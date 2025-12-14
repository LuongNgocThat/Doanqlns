#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
mysql_config.py - Cấu hình kết nối MySQL cho hệ thống nhận diện gương mặt
"""

import pymysql
import pymysql.cursors
import logging

# Cấu hình logging
logger = logging.getLogger(__name__)

# Cấu hình MySQL
MYSQL_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # Để trống nếu không có password
    'database': 'doanqlns',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor,
    'autocommit': True
}

def get_mysql_connection():
    """Tạo kết nối MySQL"""
    try:
        connection = pymysql.connect(**MYSQL_CONFIG)
        logger.info("Kết nối MySQL thành công")
        return connection
    except Exception as e:
        logger.error(f"Lỗi kết nối MySQL: {e}")
        return None

def test_mysql_connection():
    """Test kết nối MySQL"""
    try:
        conn = get_mysql_connection()
        if conn:
            with conn.cursor() as cursor:
                cursor.execute("SELECT 1")
                result = cursor.fetchone()
                logger.info("Test kết nối MySQL thành công")
            conn.close()
            return True
        else:
            logger.error("Không thể kết nối MySQL")
            return False
    except Exception as e:
        logger.error(f"Lỗi test kết nối MySQL: {e}")
        return False

def create_database_if_not_exists():
    """Tạo database nếu chưa tồn tại"""
    try:
        # Kết nối không chỉ định database
        config_without_db = MYSQL_CONFIG.copy()
        del config_without_db['database']
        
        connection = pymysql.connect(**config_without_db)
        with connection.cursor() as cursor:
            cursor.execute(f"CREATE DATABASE IF NOT EXISTS {MYSQL_CONFIG['database']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")
            logger.info(f"Database {MYSQL_CONFIG['database']} đã sẵn sàng")
        connection.close()
        return True
    except Exception as e:
        logger.error(f"Lỗi tạo database: {e}")
        return False

if __name__ == "__main__":
    # Test cấu hình MySQL
    logging.basicConfig(level=logging.INFO)
    
    print("=== KIỂM TRA CẤU HÌNH MYSQL ===")
    
    # Tạo database nếu chưa có
    if create_database_if_not_exists():
        print("✓ Database đã sẵn sàng")
    else:
        print("✗ Không thể tạo database")
    
    # Test kết nối
    if test_mysql_connection():
        print("✓ Kết nối MySQL thành công")
    else:
        print("✗ Kết nối MySQL thất bại")
    
    print("=== HOÀN THÀNH KIỂM TRA ===")

