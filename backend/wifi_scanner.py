#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
wifi_scanner.py - WiFi scanning utility for attendance system
"""

import subprocess
import platform
import re
import logging

logger = logging.getLogger(__name__)

def get_wifi_info():
    """
    Get current WiFi information based on the operating system
    Returns dict with ssid, signal_strength, security, etc.
    """
    try:
        system = platform.system().lower()
        
        if system == "windows":
            return _get_wifi_windows()
        elif system == "linux":
            return _get_wifi_linux()
        elif system == "darwin":  # macOS
            return _get_wifi_macos()
        else:
            logger.warning(f"Unsupported operating system: {system}")
            return {"ssid": None, "error": f"Unsupported OS: {system}"}
            
    except Exception as e:
        logger.error(f"Error getting WiFi info: {e}")
        return {"ssid": None, "error": str(e)}

def _get_wifi_windows():
    """Get WiFi info on Windows using netsh"""
    try:
        # Get current connection
        result = subprocess.run(
            ['netsh', 'wlan', 'show', 'interfaces'],
            capture_output=True,
            text=True,
            timeout=10
        )
        
        if result.returncode != 0:
            return {"ssid": None, "error": "Failed to run netsh command"}
        
        output = result.stdout
        
        # Extract SSID
        ssid_match = re.search(r'SSID\s+:\s+(.+)', output)
        ssid = ssid_match.group(1).strip() if ssid_match else None
        
        if not ssid or ssid == '':
            return {"ssid": None, "error": "No WiFi connection detected"}
        
        # Extract signal strength
        signal_match = re.search(r'Signal\s+:\s+(\d+)%', output)
        signal_strength = int(signal_match.group(1)) if signal_match else None
        
        # Extract security
        security_match = re.search(r'Authentication\s+:\s+(.+)', output)
        security = security_match.group(1).strip() if security_match else "Unknown"
        
        # Extract BSSID (MAC address of access point)
        bssid_match = re.search(r'BSSID\s+:\s+([0-9a-fA-F:]{17})', output)
        bssid = bssid_match.group(1) if bssid_match else None
        
        return {
            "ssid": ssid,
            "signal_strength": signal_strength,
            "security": security,
            "bssid": bssid,
            "error": None
        }
        
    except subprocess.TimeoutExpired:
        return {"ssid": None, "error": "WiFi scan timeout"}
    except Exception as e:
        logger.error(f"Error in Windows WiFi scan: {e}")
        return {"ssid": None, "error": str(e)}

def _get_wifi_linux():
    """Get WiFi info on Linux using iwconfig or nmcli"""
    try:
        # Try nmcli first (NetworkManager)
        try:
            result = subprocess.run(
                ['nmcli', '-t', '-f', 'SSID,SIGNAL,SECURITY', 'device', 'wifi', 'list', '--rescan', 'no'],
                capture_output=True,
                text=True,
                timeout=10
            )
            
            if result.returncode == 0:
                lines = result.stdout.strip().split('\n')
                for line in lines:
                    parts = line.split(':')
                    if len(parts) >= 3:
                        ssid = parts[0]
                        signal = parts[1]
                        security = parts[2]
                        
                        # Check if this is the connected network
                        if ssid and signal and signal != '0':
                            return {
                                "ssid": ssid,
                                "signal_strength": int(signal) if signal.isdigit() else None,
                                "security": security,
                                "bssid": None,
                                "error": None
                            }
        except FileNotFoundError:
            pass
        
        # Fallback to iwconfig
        result = subprocess.run(
            ['iwconfig'],
            capture_output=True,
            text=True,
            timeout=10
        )
        
        if result.returncode != 0:
            return {"ssid": None, "error": "Failed to run iwconfig"}
        
        output = result.stdout
        
        # Extract SSID
        ssid_match = re.search(r'ESSID:"([^"]*)"', output)
        ssid = ssid_match.group(1) if ssid_match else None
        
        if not ssid or ssid == 'off/any':
            return {"ssid": None, "error": "No WiFi connection detected"}
        
        # Extract signal strength
        signal_match = re.search(r'Signal level=(-?\d+)', output)
        signal_strength = int(signal_match.group(1)) if signal_match else None
        
        # Extract security
        security_match = re.search(r'Encryption key:(\w+)', output)
        security = "WEP" if security_match and security_match.group(1) == "on" else "WPA/WPA2"
        
        return {
            "ssid": ssid,
            "signal_strength": signal_strength,
            "security": security,
            "bssid": None,
            "error": None
        }
        
    except subprocess.TimeoutExpired:
        return {"ssid": None, "error": "WiFi scan timeout"}
    except Exception as e:
        logger.error(f"Error in Linux WiFi scan: {e}")
        return {"ssid": None, "error": str(e)}

def _get_wifi_macos():
    """Get WiFi info on macOS using system_profiler"""
    try:
        result = subprocess.run(
            ['system_profiler', 'SPAirPortDataType', '-json'],
            capture_output=True,
            text=True,
            timeout=10
        )
        
        if result.returncode != 0:
            return {"ssid": None, "error": "Failed to run system_profiler"}
        
        import json
        data = json.loads(result.stdout)
        
        # Extract WiFi info from JSON
        if 'SPAirPortDataType' in data and data['SPAirPortDataType']:
            wifi_info = data['SPAirPortDataType'][0]
            
            ssid = wifi_info.get('SSID_STR', '')
            if not ssid:
                return {"ssid": None, "error": "No WiFi connection detected"}
            
            return {
                "ssid": ssid,
                "signal_strength": None,  # Not easily available from system_profiler
                "security": wifi_info.get('AUTH_WPA2_PSK', 'Unknown'),
                "bssid": wifi_info.get('BSSID', ''),
                "error": None
            }
        
        return {"ssid": None, "error": "No WiFi data found"}
        
    except subprocess.TimeoutExpired:
        return {"ssid": None, "error": "WiFi scan timeout"}
    except Exception as e:
        logger.error(f"Error in macOS WiFi scan: {e}")
        return {"ssid": None, "error": str(e)}

def is_company_wifi(ssid, company_ssids=None):
    """
    Check if the current WiFi is a company network
    """
    if not ssid:
        return False
    
    if company_ssids is None:
        # Default company SSIDs - you can modify this list
        company_ssids = [
            "CompanyWiFi",
            "Office-WiFi", 
            "Corporate",
            "Work-WiFi",
            "Company-5G",
            "Office-5G"
        ]
    
    return ssid in company_ssids

if __name__ == "__main__":
    # Test the WiFi scanner
    logging.basicConfig(level=logging.INFO)
    
    print("Testing WiFi Scanner...")
    wifi_info = get_wifi_info()
    print(f"WiFi Info: {wifi_info}")
    
    if wifi_info.get("ssid"):
        is_company = is_company_wifi(wifi_info["ssid"])
        print(f"Is Company WiFi: {is_company}")

