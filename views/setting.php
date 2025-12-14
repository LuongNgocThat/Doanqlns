<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../includes/network_check.php';
include(__DIR__ . '/../includes/header.php');

// Kiểm tra quyền cài đặt
if (!isset($_SESSION['user_id']) || !$_SESSION['quyen_them'] || !$_SESSION['quyen_sua'] || !$_SESSION['quyen_xoa']) {
    header("Location: /doanqlns/views/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt Tài khoản Người Dùng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background: var(--bs-body-bg);
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .layout-page {
            padding-left: 260px;
            width: 100%;
            padding-top: 1rem;
        }

        .content-wrapper {
            padding: 0 1.5rem 1.5rem;
        }

        @media (max-width: 1199.98px) {
            .layout-page {
                padding-left: 0;
            }
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            line-height: 1.6;
        }

        .main-content {
            padding: 20px;
        }

        .separate-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0 30px 0;
            flex-wrap: wrap;
        }

        .separate-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-width: 180px;
            justify-content: center;
        }

        .separate-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .separate-btn.active {
            background: linear-gradient(135deg, #007bff, #0056b3);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3);
        }

        .separate-btn.active:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
        }

        .settings-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .settings-container h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .form-container {
            display: none;
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease-in-out;
        }

        .form-container.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .form-container h3 {
            color: #34495e;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-group input:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        .form-group input::placeholder {
            color: #95a5a6;
        }

        .role-buttons {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .role-buttons button {
            padding: 10px 20px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 20px;
            background-color: #ecf0f1;
            color: #7f8c8d;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .role-buttons button:hover {
            background-color: #bdc3c7;
            color: #2c3e50;
            transform: translateY(-1px);
        }

        .role-buttons button.selected {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .form-container button[type="submit"] {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            position: relative;
            overflow: hidden;
        }

        .form-container button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .form-container button[type="submit"]:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .form-container button[type="submit"]:hover:before {
            left: 100%;
        }

        .message {
            padding: 12px;
            margin-top: 15px;
            border-radius: 8px;
            font-size: 14px;
            text-align: center;
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .message.active {
            display: block;
        }

        .message.error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .message.success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .message.info {
            background-color: #e7f3fe;
            color: #1e4976;
            border: 1px solid #b3d4fc;
        }

        .loading {
            position: relative;
            pointer-events: none;
        }

        .loading:after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin: -10px 0 0 -10px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-left-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .settings-container {
                margin: 20px;
                padding: 20px;
            }
            .separate-buttons {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            .separate-btn {
                width: 100%;
                max-width: 300px;
                min-width: auto;
            }
        }

        @media (max-width: 480px) {
            .separate-btn {
                width: 100%;
                max-width: 280px;
                padding: 10px 20px;
                font-size: 14px;
            }
            .role-buttons button {
                width: calc(33% - 10px);
            }
        }

        .table-responsive {
            overflow-x: auto;
            margin-top: 20px;
            /* Tối ưu hiệu suất bảng */
            contain: layout style paint;
            will-change: scroll-position;
        }

        .table-responsive table {
            width: 100%;
            border-collapse: collapse;
            /* Tối ưu rendering bảng */
            table-layout: fixed;
        }

        .table-responsive td, .table-responsive th {
            /* Cố định kích thước cell để tránh giật */
            min-width: 120px;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .user-table th,
        .user-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-table th {
            background: #f8f9fa;
            font-weight: 500;
            color: #2c3e50;
        }

        .user-table tr:hover {
            background-color: #f5f6f7;
        }

        .user-table td:hover {
            overflow: visible;
            white-space: normal;
            word-break: break-all;
        }

        .action-btn {
            padding: 6px 12px;
            margin: 0 4px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: #3498db;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .permission-toggle, .status-toggle {
            width: 46px;
            height: 24px;
            background-color: #e0e0e0;
            border-radius: 12px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        .permission-toggle::after, .status-toggle::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: transform 0.3s cubic-bezier(0.4, 0.0, 0.2, 1), background-color 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .permission-toggle.active {
            background-color: #4CAF50;
        }

        .status-toggle.active {
            background-color: #2196F3;
        }

        .permission-toggle.active::after, .status-toggle.active::after {
            transform: translateX(22px);
        }

        .permission-toggle:hover::after, .status-toggle:hover::after {
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        }

        .permission-toggle.disabled, .status-toggle.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .permission-toggle.processing, .status-toggle.processing {
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }

        .status-label {
            margin-left: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .status-label[data-status="Hoạt động"] {
            color: #2196F3;
        }

        .status-label[data-status="Không hoạt động"] {
            color: #757575;
        }

        .role-btn {
            position: relative;
            overflow: hidden;
            transform: translate3d(0, 0, 0);
            padding: 8px 16px;
            background: #f5f5f5;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-btn:hover {
            background: #e0e0e0;
        }

        .role-btn.selected {
            background: #2196F3;
            color: white;
        }

        .role-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, .5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .role-btn:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }
            100% {
                transform: scale(100, 100);
                opacity: 0;
            }
        }

        /* Face Recognition Styles */
        .face-scanner {
            display: none;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 2px solid #e3f2fd;
        }

        .face-scanner.active {
            display: flex;
        }

        .video-container {
            position: relative;
            width: 400px;
            height: 400px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 15px;
            /* Cố định kích thước để tránh giật */
            min-width: 400px;
            min-height: 400px;
            max-width: 400px;
            max-height: 400px;
            /* Tối ưu hiệu suất */
            transform: translateZ(0);
            will-change: transform;
            /* Tối ưu GPU acceleration */
            -webkit-transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            /* Tránh giật */
            contain: layout style paint;
        }

        #faceVideo {
            width: 400px;
            height: 400px;
            object-fit: cover;
            /* Cố định kích thước video */
            min-width: 400px;
            min-height: 400px;
            max-width: 400px;
            max-height: 400px;
            /* Tối ưu hiệu suất video */
            transform: translateZ(0);
            will-change: transform;
            /* Tránh giật khi load */
            background-color: #000;
            /* Tối ưu GPU acceleration */
            -webkit-transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            /* Tối ưu rendering */
            image-rendering: optimizeSpeed;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            /* Tránh giật */
            contain: layout style paint;
        }

        #faceCanvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 400px;
            height: 400px;
            z-index: 1;
            /* Tối ưu hiệu suất canvas */
            transform: translateZ(0);
            will-change: transform;
            /* Tối ưu GPU acceleration */
            -webkit-transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            /* Tối ưu rendering */
            image-rendering: optimizeSpeed;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            /* Tránh giật */
            contain: layout style paint;
        }

        .face-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            z-index: 2;
        }

        .face-circle {
            width: 200px;
            height: 200px;
            border: 3px solid #4CAF50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .face-success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 8px;
            z-index: 3;
        }

        .face-success-circle {
            width: 100px;
            height: 100px;
            border: 5px solid #28a745;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
            animation: successPulse 1s ease-out;
        }

        .face-success-circle i {
            font-size: 40px;
            color: #28a745;
        }

        @keyframes successPulse {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.2);
                opacity: 1;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .face-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .face-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .face-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .face-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .face-icon {
            color: #2196F3;
            font-size: 24px;
        }

        .face-details {
            flex-grow: 1;
        }

        .face-name {
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .face-date {
            font-size: 12px;
            color: #6c757d;
        }

        .face-actions button {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .face-delete {
            background: #dc3545;
            color: white;
        }

        .face-delete:hover {
            background: #c82333;
            transform: scale(1.05);
        }

        .add-face-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #2196F3, #1976d2);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .add-face-btn:hover {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }

        .capture-face-btn {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .capture-face-btn:hover {
            background: linear-gradient(135deg, #45a049, #3d8b40);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        }

        .cancel-face-scan-btn {
            margin-top: 15px;
            padding: 8px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .cancel-face-scan-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
        }

        .scanner-status {
            text-align: center;
            margin: 15px 0 10px 0;
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .scanner-instructions {
            text-align: center;
            margin-bottom: 15px;
            color: #666;
            font-size: 14px;
        }

        .face-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            cursor: pointer;
            /* Cố định kích thước để tránh giật */
            min-width: 100px;
            min-height: 100px;
            max-width: 100px;
            max-height: 100px;
            /* Tối ưu hiệu suất */
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
            will-change: transform;
            transition: transform 0.3s ease-in-out;
            /* Tối ưu rendering */
            image-rendering: optimizeSpeed;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            /* Tránh giật */
            contain: layout style paint;
        }

        .face-image:hover {
            transform: scale(1.1) translate3d(0, 0, 0);
        }

        .change-face-btn {
            background: #3498db;
            color: white;
        }

        .change-face-btn:hover {
            background: #2980b9;
            transform: scale(1.05);
        }

        #lightbox img {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            border: 3px solid white;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
            will-change: transform;
            transition: transform 0.3s ease-in-out;
        }

        #lightbox img:hover {
            transform: scale(1.1);
        }

        /* Network Debug Styles */
        .network-info-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .network-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .network-info-row:last-child {
            border-bottom: none;
        }

        .network-label {
            font-weight: 600;
            color: #495057;
        }

        .network-value {
            color: #6c757d;
            font-family: monospace;
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .network-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            text-align: center;
        }

        .network-status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .network-status.danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .network-details {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .network-details h4 {
            color: #1976d2;
            margin-top: 0;
        }

        .network-test-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .test-ip-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin: 15px 0;
            flex-wrap: wrap;
        }

        .test-ip-field {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            min-width: 200px;
        }

        .test-ip-btn, .clear-test-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .test-ip-btn {
            background: #28a745;
            color: white;
        }

        .test-ip-btn:hover {
            background: #218838;
        }

        .clear-test-btn {
            background: #dc3545;
            color: white;
        }

        .clear-test-btn:hover {
            background: #c82333;
        }

        .test-ip-examples {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 15px;
        }

        .test-ip-examples ul {
            margin: 5px 0;
            padding-left: 20px;
        }

        .test-ip-examples li {
            margin: 5px 0;
        }

        .test-ip-examples code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }

        .network-help {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 8px;
            padding: 15px;
        }

        .network-help h4 {
            color: #0c5460;
            margin-top: 0;
        }

        .network-help ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .network-help li {
            margin: 8px 0;
            line-height: 1.5;
        }

        .text-muted {
            color: #6c757d;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Attendance Time Settings Styles */
        .attendance-time-settings {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        /* Cấu Hình GLV Styles */
        .cau-hinh-glv-settings {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        
        .glv-setting-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
            transition: box-shadow .2s ease;
        }
        
        .glv-setting-card:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        }
        
        .glv-setting-card h4 {
            font-size: 16px;
            margin: 0 0 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #374151;
        }
        
        .glv-setting-card .form-group {
            margin-bottom: 10px;
        }
        
        .glv-setting-card label {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 6px;
            display: block;
            color: #4b5563;
        }
        
        .glv-setting-card input[type="time"] {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px 10px;
            font-size: 14px;
        }
        
        .glv-setting-card input[type="time"]:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-save-glv {
            margin-top: 12px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .btn-save-glv:hover {
            background: #1d4ed8;
        }
        
        .glv-section-title {
            margin: 10px 0 6px;
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }
        
        .glv-subtext-muted {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* Dropdown styles */
        .dropdown-menu {
            max-height: 300px;
            overflow-y: auto;
            min-width: 300px;
        }

        .dropdown-item {
            padding: 8px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .cau-hinh-dropdown-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .cau-hinh-ten {
            font-weight: 600;
            color: #374151;
            margin-bottom: 2px;
        }

        .cau-hinh-thoi-gian {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
        }

        .cau-hinh-ngay {
            font-size: 11px;
            color: #9ca3af;
        }

        .cau-hinh-trang-thai {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 500;
            margin-top: 2px;
        }

        .cau-hinh-trang-thai.active {
            background: #dcfce7;
            color: #166534;
        }

        .cau-hinh-trang-thai.inactive {
            background: #fef2f2;
            color: #dc2626;
        }

        .time-setting-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .time-setting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .time-setting-card h4 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .time-setting-card .form-group {
            margin-bottom: 15px;
        }

        .time-setting-card label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
        }

        .time-setting-card input[type="time"] {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            text-align: center;
        }

        .time-setting-card input[type="time"]:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
            outline: none;
        }

        /* Đảm bảo hiển thị định dạng 24 giờ */
        .time-setting-card input[type="time"]::-webkit-datetime-edit-ampm-field {
            display: none;
        }

        .time-setting-card input[type="time"]::-webkit-datetime-edit-hour-field {
            font-weight: bold;
            color: #2c3e50;
        }

        .time-setting-card input[type="time"]::-webkit-datetime-edit-minute-field {
            font-weight: bold;
            color: #2c3e50;
        }

        .btn-save-settings {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-save-settings:hover {
            background: linear-gradient(135deg, #20c997, #17a2b8);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .current-settings-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .current-settings-info h5 {
            color: #1976d2;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .current-settings-info p {
            margin-bottom: 8px;
            color: #424242;
        }

        .current-settings-info span {
            font-weight: 500;
            color: #1976d2;
        }
        
        .time-status-message {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .time-status-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .time-status-message.danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        .time-format-help {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .time-format-help h5 {
            color: #856404;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .help-content p {
            margin-bottom: 10px;
            color: #856404;
        }

        .time-examples {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
        }

        .time-examples p {
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }

        .time-examples ul {
            margin: 0;
            padding-left: 20px;
        }

        .time-examples li {
            margin-bottom: 5px;
            color: #495057;
        }

        .time-examples code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #d63384;
        }

        @media (max-width: 768px) {
            .attendance-time-settings {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .cau-hinh-glv-settings {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
<div class="layout-wrapper">
    <?php include('../includes/sidebar.php'); ?>
    <div class="layout-page">
        <div class="content-wrapper">
    <!-- Separate buttons outside of the settings container -->
    <div class="separate-buttons">
        <button id="registerBtn" onclick="showForm('register')" class="separate-btn">
            <i class="fas fa-user-plus"></i> Đăng ký tài khoản
        </button>
        <button id="manageBtn" onclick="showForm('manage')" class="separate-btn">
            <i class="fas fa-users-cog"></i> Quản lý người dùng
        </button>
        <button id="faceBtn" onclick="showForm('face')" class="separate-btn">
            <i class="fas fa-user-circle"></i> Gương mặt
        </button>
        <button id="networkBtn" onclick="showForm('network')" class="separate-btn">
            <i class="fas fa-network-wired"></i> Network Debug
        </button>
        <button id="attendanceTimeBtn" onclick="showForm('attendanceTime')" class="separate-btn">
            <i class="fas fa-clock"></i> Hiển thị nút điểm danh
        </button>
        <button id="cauHinhGLVBtn" onclick="showForm('cauHinhGLV')" class="separate-btn">
            <i class="fas fa-cogs"></i> Cấu Hình GLV
        </button>
    </div>

    <div class="settings-container">
        <h2><i class="fas fa-cog"></i> Cài đặt Hệ thống</h2>

        <div id="registerForm" class="form-container">
            <h3><i class="fas fa-user"></i> Đăng ký tài khoản</h3>
            <form id="form-regular" method="POST">
                <div class="form-group">
                    <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="mat_khau" placeholder="Mật khẩu" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_mat_khau" placeholder="Xác nhận mật khẩu" required>
                </div>
                <div class="role-buttons">
                    <button type="button" class="role-btn" onclick="selectRole('admin', this)">Admin</button>
                    <button type="button" class="role-btn" onclick="selectRole('manager', this)">Quản Lý</button>
                    <button type="button" class="role-btn" onclick="selectRole('employee', this)">Nhân Viên</button>
                </div>
                <input type="hidden" name="role">
                <button type="submit"><i class="fas fa-paper-plane"></i> Đăng ký</button>
            </form>
            <p id="register-regular-message" class="message"></p>
        </div>

        <div id="manageForm" class="form-container">
            <h3><i class="fas fa-users"></i> Danh sách người dùng</h3>
            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ID Nhân viên</th>
                            <th>Tên đăng nhập</th>
                            <th>Email</th>
                            <th>Quyền thêm</th>
                            <th>Quyền sửa</th>
                            <th>Quyền xóa</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="user-list-body">
                        <!-- Dữ liệu sẽ được thêm vào đây bằng JavaScript -->
                    </tbody>
                </table>
            </div>
            <p id="user-list-message" class="message"></p>
        </div>

        <div id="faceForm" class="form-container">
            <h3><i class="fas fa-user-circle"></i> Quản lý Gương mặt</h3>
            
            <div class="form-group">
                <select id="faceEmployeeSelect" class="form-control">
                    <option value="">-- Chọn nhân viên --</option>
                </select>
            </div>
            
            <!-- <button class="add-face-btn" onclick="startFaceEnrollment()">
                <i class="fas fa-plus"></i>
                Cập nhật
            </button> -->

            <div id="face-enrollment-status" class="message" style="display: none;"></div>

            <div id="faceScanner" class="face-scanner">
                <div class="video-container">
                    <video id="faceVideo" width="400" height="400" autoplay muted></video>
                    <canvas id="faceCanvas" style="display:none;"></canvas>
                    <div id="face-overlay" class="face-overlay">
                        <div class="face-circle"></div>
                    </div>
                    <div id="face-success-overlay" class="face-success-overlay" style="display:none;">
                        <div class="face-success-circle">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                <div class="scanner-status">Đang chờ gương mặt...</div>
                <div class="scanner-instructions">Vui lòng nhìn thẳng vào camera</div>
                <button class="cancel-face-scan-btn" onclick="cancelFaceScanning()">
                    <i class="fas fa-times"></i> Hủy
                </button>
            </div>

            <div class="table-responsive">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID Nhân viên</th>
                            <th>Tên nhân viên</th>
                            <th>Hình ảnh</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="face-manage-list">
                        <!-- Dữ liệu sẽ được thêm vào đây bằng JavaScript -->
                    </tbody>
                </table>
            </div>
            <p id="face-manage-message" class="message"></p>
        </div>

        <div id="networkForm" class="form-container">
            <h3><i class="fas fa-network-wired"></i> Network Debug Information</h3>
            <p class="text-muted">Thông tin kiểm tra mạng và WiFi công ty</p>
            
            <!-- <div class="network-info-card">
                <h4><i class="fas fa-info-circle"></i> Thông tin mạng hiện tại</h4> -->
                
                <!-- <div class="network-info-row">
                    <span class="network-label">IP của người dùng:</span>
                    <span class="network-value" id="current-ip">Đang kiểm tra...</span>
                </div>
                
                <div class="network-info-row">
                    <span class="network-label">Trạng thái mạng công ty:</span>
                    <span class="network-status" id="network-status">Đang kiểm tra...</span>
                </div>
                
                <div class="network-info-row">
                    <span class="network-label">Remote Address:</span>
                    <span class="network-value" id="remote-addr">Đang kiểm tra...</span>
                </div>
                
                <div class="network-info-row">
                    <span class="network-label">X-Forwarded-For:</span>
                    <span class="network-value" id="x-forwarded-for">Đang kiểm tra...</span>
                </div>
                
                <div class="network-info-row">
                    <span class="network-label">Client IP:</span>
                    <span class="network-value" id="client-ip">Đang kiểm tra...</span>
                </div>
            </div> -->

            <div class="network-details">
                <h4><i class="fas fa-cog"></i> Cấu hình mạng công ty</h4>
                <p><strong>SSID công ty:</strong> NGOC THAT</p>
                <p><strong>Trạng thái mạng:</strong> <span id="network-status">Đang kiểm tra...</span></p>
                <p><strong>Trạng thái thời gian:</strong> <span id="time-status">Đang kiểm tra...</span></p>
                <p><strong>Trạng thái tổng hợp:</strong> <span id="button-status">Đang kiểm tra...</span></p>
            </div>

            <?php
                // Chỉ kiểm tra mạng, không kiểm tra giờ
                $serverOk = function_exists('isServerOnCompanyNetwork') ? isServerOnCompanyNetwork() : false;
                $clientOk = function_exists('isCompanyNetwork') ? isCompanyNetwork() : false;
                $networkOk = $serverOk && $clientOk;
                $message = $networkOk ? 'Đang ở trong mạng Wi‑Fi công ty. Bạn có thể điểm danh.' : 'Thiết bị hoặc máy chủ không kết nối đúng Wi‑Fi công ty.';
            ?>
            <div class="network-info-card">
                <h4><i class="fas fa-shield-alt"></i> Trạng thái điểm danh theo mạng</h4>
                <p id="gate-message" class="message <?php echo $networkOk ? 'success' : 'error'; ?> active" style="display:block;">
                    <?php echo htmlspecialchars($message); ?>
                </p>
                <div style="text-align:center; margin-top:10px;">
                    <button id="attendance-action-btn" class="separate-btn" style="display: none;">
                        <i class="fas fa-check-circle"></i> Điểm danh
                    </button>
                </div>
                <p class="text-muted" style="margin-top:10px;">
                    Nút "Điểm danh" chỉ hiển thị khi máy chủ và thiết bị cùng kết nối Wi‑Fi công ty.
                </p>
            </div>

            <!-- <div class="network-test-section">
                <h4><i class="fas fa-tools"></i> Test với IP khác</h4>
                <p><strong>IP hiện tại đang test:</strong> 
                    <span id="test-ip-display">Không có IP test</span>
                </p>
                
                <div class="test-ip-form">
                    <input type="text" id="test-ip-input" placeholder="Nhập IP để test (VD: 192.168.0.118)" 
                           class="test-ip-field">
                    <button type="button" onclick="testWithIP()" class="test-ip-btn">
                        <i class="fas fa-play"></i> Test IP
                    </button>
                    <button type="button" onclick="clearTestIP()" class="clear-test-btn">
                        <i class="fas fa-times"></i> Xóa Test
                    </button>
                </div>
            </div> -->
        </div>

        <div id="attendanceTimeForm" class="form-container">
            <h3><i class="fas fa-clock"></i> Hiển thị nút điểm danh</h3>
            <p class="text-muted">Thiết lập khung giờ cho phép điểm danh bằng webcam</p>
            
            <div class="attendance-time-settings">
                <div class="time-setting-card">
                    <h4><i class="fas fa-sun"></i> Điểm danh sáng</h4>
                    <div class="form-group">
                        <label for="morningStartTime">Giờ bắt đầu:</label>
                        <input type="time" id="morningStartTime" class="form-control" step="3600">
                    </div>
                    <div class="form-group">
                        <label for="morningEndTime">Giờ kết thúc:</label>
                        <input type="time" id="morningEndTime" class="form-control" step="3600">
                    </div>
                </div>
                
                <div class="time-setting-card">
                    <h4><i class="fas fa-sun"></i> Điểm danh trưa</h4>
                    <div class="form-group">
                        <label for="lunchStartTime">Giờ bắt đầu:</label>
                        <input type="time" id="lunchStartTime" class="form-control" step="3600">
                    </div>
                    <div class="form-group">
                        <label for="lunchEndTime">Giờ kết thúc:</label>
                        <input type="time" id="lunchEndTime" class="form-control" step="3600">
                    </div>
                </div>
                
                <div class="time-setting-card">
                    <h4><i class="fas fa-moon"></i> Điểm danh chiều</h4>
                    <div class="form-group">
                        <label for="afternoonStartTime">Giờ bắt đầu:</label>
                        <input type="time" id="afternoonStartTime" class="form-control" step="3600">
                    </div>
                    <div class="form-group">
                        <label for="afternoonEndTime">Giờ kết thúc:</label>
                        <input type="time" id="afternoonEndTime" class="form-control" step="3600">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" id="enableTimeRestriction" checked>
                    Bật kiểm tra giờ điểm danh
                </label>
                <small class="form-text text-muted">
                    Khi bật, webcam chỉ hoạt động trong khung giờ đã thiết lập
                </small>
            </div>
            
            <button type="button" class="btn-save-settings" onclick="saveAttendanceTimeSettings()">
                <i class="fas fa-save"></i> Lưu cài đặt
            </button>
            
            <div id="attendance-time-message" class="message"></div>
            
            <div class="current-settings-info">
                <h5><i class="fas fa-info-circle"></i> Cài đặt hiện tại</h5>
                <div id="current-time-settings">
                    <p><strong>Điểm danh sáng:</strong> <span id="current-morning-time">Chưa cài đặt</span></p>
                    <p><strong>Điểm danh trưa:</strong> <span id="current-lunch-time">Chưa cài đặt</span></p>
                    <p><strong>Điểm danh chiều:</strong> <span id="current-afternoon-time">Chưa cài đặt</span></p>
                    <p><strong>Trạng thái:</strong> <span id="current-restriction-status">Chưa cài đặt</span></p>
                    <p><strong>Khung giờ hiện tại:</strong> <span id="current-time-status" class="time-status-message">Đang kiểm tra...</span></p>
                </div>
            </div>
            
        </div>

        <div id="cauHinhGLVForm" class="form-container">
            <h3><i class="fas fa-cogs"></i> Cấu Hình Giờ Làm Việc</h3>
            <p class="glv-subtext-muted">Thiết lập khung giờ chi tiết cho điểm danh sáng, trưa, chiều với trạng thái đúng giờ/đi trễ/ra sớm</p>
            
            <div class="cau-hinh-glv-settings">
                <div class="glv-setting-card">
                    <h4><i class="fas fa-sun"></i> Điểm danh sáng</h4>
                    <div class="form-group">
                        <label for="glv_gio_sang_bat_dau">Bắt đầu đúng giờ</label>
                        <input type="time" id="glv_gio_sang_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_sang_ket_thuc">Kết thúc đúng giờ</label>
                        <input type="time" id="glv_gio_sang_ket_thuc">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_sang_tre_bat_dau">Bắt đầu đi trễ</label>
                        <input type="time" id="glv_gio_sang_tre_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_sang_tre_ket_thuc">Kết thúc đi trễ</label>
                        <input type="time" id="glv_gio_sang_tre_ket_thuc">
                    </div>
                </div>

                <div class="glv-setting-card">
                    <h4><i class="fas fa-utensils"></i> Điểm danh trưa</h4>
                    <div class="form-group">
                        <label for="glv_gio_trua_bat_dau">Bắt đầu đúng giờ</label>
                        <input type="time" id="glv_gio_trua_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_trua_ket_thuc">Kết thúc đúng giờ</label>
                        <input type="time" id="glv_gio_trua_ket_thuc">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_trua_tre_bat_dau">Bắt đầu đi trễ</label>
                        <input type="time" id="glv_gio_trua_tre_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_trua_tre_ket_thuc">Kết thúc đi trễ</label>
                        <input type="time" id="glv_gio_trua_tre_ket_thuc">
                    </div>
                </div>

                <div class="glv-setting-card">
                    <h4><i class="fas fa-moon"></i> Điểm danh chiều</h4>
                    <div class="form-group">
                        <label for="glv_gio_chieu_ra_som_bat_dau">Bắt đầu ra sớm</label>
                        <input type="time" id="glv_gio_chieu_ra_som_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_chieu_ra_som_ket_thuc">Kết thúc ra sớm</label>
                        <input type="time" id="glv_gio_chieu_ra_som_ket_thuc">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_chieu_bat_dau">Bắt đầu đúng giờ</label>
                        <input type="time" id="glv_gio_chieu_bat_dau">
                    </div>
                    <div class="form-group">
                        <label for="glv_gio_chieu_ket_thuc">Kết thúc</label>
                        <input type="time" id="glv_gio_chieu_ket_thuc">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="glv_ten_cau_hinh">Tên cấu hình</label>
                <input type="text" id="glv_ten_cau_hinh" class="form-control" placeholder="VD: Giờ làm việc tiêu chuẩn 2025">
            </div>

            <div class="form-group">
                <label for="glv_ghi_chu">Ghi chú</label>
                <textarea id="glv_ghi_chu" class="form-control" rows="2" placeholder="Mô tả về cấu hình giờ làm việc này..."></textarea>
            </div>

            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn-save-glv" onclick="luuCauHinhGLV()">
                    <i class="fas fa-save"></i> Lưu cấu hình GLV
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="chonCauHinhDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-list"></i> Chọn cấu hình
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="chonCauHinhDropdown" id="danhSachCauHinhDropdown">
                        <li><span class="dropdown-item-text"><i class="fas fa-spinner fa-spin"></i> Đang tải...</span></li>
                    </ul>
                </div>
            </div>
            
            <div id="glv-message" class="message"></div>
            
            <div class="current-settings-info">
                <h5><i class="fas fa-info-circle"></i> Cấu hình hiện tại</h5>
                <div id="current-glv-settings">
                    <p><i class="fas fa-spinner fa-spin"></i> Đang tải...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('Settings script loaded');

    document.addEventListener('DOMContentLoaded', () => {
        const savedTab = localStorage.getItem('settings_active_tab');
        const hashTab = (location.hash || '').replace('#', '');
        const initialTab = hashTab || savedTab || 'register';
        showForm(initialTab);
        
        // Tự động load dữ liệu attendance time settings khi trang được tải
        loadAttendanceTimeSettings();

        const formRegular = document.getElementById('form-regular');
        if (formRegular) {
            formRegular.addEventListener('submit', async function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.classList.add('loading');
                await handleFormSubmit(this, document.getElementById('register-regular-message'));
                submitBtn.classList.remove('loading');
            });
        }

        const buttons = document.querySelectorAll('button');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const x = e.clientX - e.target.offsetLeft;
                const y = e.clientY - e.target.offsetTop;

                const ripples = document.createElement('span');
                ripples.style.left = x + 'px';
                ripples.style.top = y + 'px';
                this.appendChild(ripples);

                setTimeout(() => {
                    ripples.remove();
                }, 1000);
            });
        });

        document.getElementById('manageBtn').addEventListener('click', loadUserList);
        document.getElementById('faceBtn').addEventListener('click', () => {
            showForm('face');
            loadFaceEmployeeList();
            loadFaceManageList();
        });
        document.getElementById('networkBtn').addEventListener('click', () => {
            showForm('network');
            loadNetworkInfo();
        });
        document.getElementById('attendanceTimeBtn').addEventListener('click', () => {
            showForm('attendanceTime');
            loadAttendanceTimeSettings();
        });
        document.getElementById('cauHinhGLVBtn').addEventListener('click', () => {
            showForm('cauHinhGLV');
            loadCauHinhGLV();
            loadDanhSachCauHinhDropdown();
        });
    });

    function showForm(formType) {
        document.querySelectorAll('.form-container').forEach(form => {
            form.classList.remove('active');
            form.style.opacity = '0';
        });
        
        document.querySelectorAll('.separate-btn').forEach(button => {
            button.classList.remove('active');
        });
        
        const targetForm = document.getElementById(formType + 'Form');
        const targetButton = document.getElementById(formType + 'Btn');
        
        targetForm.classList.add('active');
        targetButton.classList.add('active');

        requestAnimationFrame(() => {
            targetForm.style.transition = 'opacity 0.3s ease';
            targetForm.style.opacity = '1';
        });

        // Lưu tab hiện tại và cập nhật URL để tránh bị nhảy trang khi reload
        try {
            localStorage.setItem('settings_active_tab', formType);
        } catch (e) {}
        if (history && history.replaceState) {
            history.replaceState(null, '', '#' + formType);
        } else {
            location.hash = formType;
        }
    }

    function selectRole(role, clickedButton) {
        const formContainer = clickedButton.closest('.form-container');
        if (!formContainer) return;

        formContainer.querySelectorAll('.role-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
        clickedButton.classList.add('selected');

        const roleInput = formContainer.querySelector('input[name="role"]');
        if (roleInput) {
            roleInput.value = role;
        }
    }

    async function handleFormSubmit(form, messageElement) {
        try {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');

            const roleInput = form.querySelector('input[name="role"]');
            if (!roleInput || !roleInput.value) {
                showMessage(messageElement, 'Vui lòng chọn vai trò người dùng', 'error');
                return;
            }

            if (form.id === 'form-regular') {
                const password = form.querySelector('input[name="mat_khau"]').value;
                const confirmPassword = form.querySelector('input[name="confirm_mat_khau"]').value;
                if (password !== confirmPassword) {
                    showMessage(messageElement, 'Mật khẩu xác nhận không khớp', 'error');
                    return;
                }
            }

            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                if (key !== 'confirm_mat_khau') {
                    data[key] = value;
                }
            });

            const endpoint = '/doanqlns/index.php/api/settings/register-regular';

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                showMessage(messageElement, result.message, 'success');
                form.reset();
                form.querySelectorAll('.role-btn').forEach(btn => btn.classList.remove('selected'));
                if (roleInput) roleInput.value = '';
                
                if (document.getElementById('manageForm').classList.contains('active')) {
                    loadUserList();
                }
            } else {
                showMessage(messageElement, result.message || 'Lỗi khi đăng ký', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage(messageElement, 'Lỗi khi xử lý yêu cầu: ' + error.message, 'error');
        } finally {
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
        }
    }

    function showMessage(element, message, type) {
        element.textContent = message;
        element.className = `message ${type}`;
        
        element.style.opacity = '0';
        element.classList.add('active');
        
        requestAnimationFrame(() => {
            element.style.transition = 'opacity 0.3s ease';
            element.style.opacity = '1';
        });

        setTimeout(() => {
            element.style.opacity = '0';
            setTimeout(() => {
                element.classList.remove('active');
            }, 300);
        }, 3000);
    }

    async function loadUserList() {
        try {
            console.log('Loading user list...');
            const response = await fetch('/doanqlns/index.php/api/settings/users');

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                const tbody = document.getElementById('user-list-body');
                tbody.innerHTML = '';

                result.data.forEach(user => {
                    // Bỏ qua user có ID = 1 (admin)
                    if (user.id === 1) return;
                    
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.id_nhan_vien || 'N/A'}</td>
                        <td>${user.ten_dang_nhap || 'N/A'}</td>
                        <td>${user.email || 'N/A'}</td>
                        <td>
                            <div class="permission-toggle ${user.quyen_them ? 'active' : ''}" 
                                 onclick="togglePermission(this, ${user.id}, 'quyen_them')"
                                 title="Quyền thêm"></div>
                        </td>
                        <td>
                            <div class="permission-toggle ${user.quyen_sua ? 'active' : ''}"
                                 onclick="togglePermission(this, ${user.id}, 'quyen_sua')"
                                 title="Quyền sửa"></div>
                        </td>
                        <td>
                            <div class="permission-toggle ${user.quyen_xoa ? 'active' : ''}"
                                 onclick="togglePermission(this, ${user.id}, 'quyen_xoa')"
                                 title="Quyền xóa"></div>
                        </td>
                        <td>
                            <div class="status-toggle ${user.trang_thai === 'Hoạt động' ? 'active' : ''}"
                                 onclick="toggleUserStatus(this, ${user.id})"
                                 data-user-id="${user.id}"
                                 title="Click để thay đổi trạng thái"></div>
                            <span class="status-label" data-status="${user.trang_thai}">${user.trang_thai}</span>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                showMessage(document.getElementById('user-list-message'), result.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage(document.getElementById('user-list-message'), 'Lỗi khi tải danh sách người dùng: ' + error.message, 'error');
        }
    }

    async function togglePermission(element, userId, permission) {
        try {
            if (element.classList.contains('processing')) {
                return;
            }

            element.classList.add('processing', 'disabled');
            
            const isActive = !element.classList.contains('active');
            const data = {
                [permission]: isActive ? 1 : 0
            };

            const response = await fetch(`/doanqlns/index.php/api/settings/permissions/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            
            if (result.success) {
                element.classList.toggle('active', isActive);
                showMessage(document.getElementById('user-list-message'), 
                    `${permission.replace('quyen_', 'Quyền ')} đã được ${isActive ? 'bật' : 'tắt'}`, 
                    'success'
                );
            } else {
                showMessage(document.getElementById('user-list-message'), 
                    result.message || 'Lỗi khi cập nhật quyền', 
                    'error'
                );
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage(document.getElementById('user-list-message'), 
                'Lỗi khi cập nhật quyền: ' + error.message, 
                'error'
            );
        } finally {
            setTimeout(() => {
                element.classList.remove('processing', 'disabled');
            }, 300);
        }
    }

    async function toggleUserStatus(element, userId) {
        try {
            const toggleElement = element;
            const statusLabel = toggleElement.nextElementSibling;

            if (toggleElement.classList.contains('processing')) {
                return;
            }

            const isCurrentlyActive = toggleElement.classList.contains('active');
            const newStatus = isCurrentlyActive ? 'Không hoạt động' : 'Hoạt động';

            if (!confirm(`Bạn có muốn thay đổi trạng thái thành "${newStatus}" không?`)) {
                return;
            }

            toggleElement.classList.add('processing', 'disabled');
            
            const response = await fetch(`/doanqlns/index.php/api/settings/user-status/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ trang_thai: newStatus })
            });

            let result;
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                result = await response.json();
            } else {
                throw new Error("Phản hồi không phải định dạng JSON");
            }
            
            if (result.success) {
                toggleElement.classList.toggle('active');
                statusLabel.textContent = newStatus;
                statusLabel.setAttribute('data-status', newStatus);
                showMessage(document.getElementById('user-list-message'), 
                    `Trạng thái đã được chuyển thành ${newStatus}`, 
                    'success'
                );
            } else {
                throw new Error(result.message || 'Lỗi khi cập nhật trạng thái');
            }
        } catch (error) {
            console.error('Error:', error);
            showMessage(document.getElementById('user-list-message'), 
                'Lỗi khi cập nhật trạng thái: ' + error.message, 
                'error'
            );
            // Khôi phục trạng thái UI
            element.classList.remove('active');
            const statusLabel = element.nextElementSibling;
            statusLabel.textContent = element.classList.contains('active') ? 'Hoạt động' : 'Không hoạt động';
            statusLabel.setAttribute('data-status', element.classList.contains('active') ? 'Hoạt động' : 'Không hoạt động');
        } finally {
            setTimeout(() => {
                element.classList.remove('processing', 'disabled');
            }, 300);
        }
    }

    async function loadFaceEmployeeList() {
        try {
            const response = await fetch('/doanqlns/index.php/api/users');
            if (!response.ok) {
                throw new Error(`Lỗi khi tải danh sách nhân viên: ${response.status}`);
            }
            const result = await response.json();
            
            if (!Array.isArray(result)) {
                throw new Error('Dữ liệu nhân viên không hợp lệ');
            }

            const select = document.getElementById('faceEmployeeSelect');
            select.innerHTML = '<option value="">-- Chọn nhân viên --</option>';

            result.forEach(user => {
                if (user.id_nhan_vien && user.ho_ten) {
                    const option = document.createElement('option');
                    option.value = user.id_nhan_vien;
                    option.textContent = `${user.ho_ten} (ID: ${user.id_nhan_vien})`;
                    select.appendChild(option);
                }
            });
        } catch (error) {
            console.error('Error:', error);
            showMessage(document.getElementById('face-enrollment-status'), 
                'Lỗi khi tải danh sách nhân viên: ' + error.message, 'error');
        }
    }

    async function startFaceEnrollment() {
        try {
            const employeeSelect = document.getElementById('faceEmployeeSelect');
            const employeeId = employeeSelect.value;
            
            if (!employeeId) {
                showMessage(document.getElementById('face-enrollment-status'), 
                    'Vui lòng chọn nhân viên trước khi thêm gương mặt', 'error');
                return;
            }

            const statusElement = document.getElementById('face-enrollment-status');
            const scannerElement = document.getElementById('faceScanner');
            const videoElement = document.getElementById('faceVideo');
            
            scannerElement.classList.add('active');
            statusElement.style.display = 'block';
            showMessage(statusElement, 'Đang khởi tạo camera...', 'info');

            // Khởi tạo webcam với cấu hình tối ưu
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 400, max: 400 },
                        height: { ideal: 400, max: 400 },
                        facingMode: 'user',
                        frameRate: { ideal: 30, max: 30 }
                    } 
                });
                
                // Cố định kích thước video trước khi gán stream
                videoElement.width = 400;
                videoElement.height = 400;
                videoElement.srcObject = stream;
                
                // Đợi video load xong trước khi play
                videoElement.onloadedmetadata = () => {
                    videoElement.play().catch(console.error);
                };
                
                // Tối ưu hiệu suất video
                videoElement.addEventListener('loadeddata', () => {
                    console.log('Video loaded successfully');
                });
                
                // Xử lý lỗi video
                videoElement.addEventListener('error', (e) => {
                    console.error('Video error:', e);
                    showMessage(statusElement, 'Lỗi khi tải video: ' + e.message, 'error');
                });
                
                showMessage(statusElement, 'Camera đã sẵn sàng. Vui lòng nhìn thẳng vào camera và nhấn "Chụp ảnh"', 'info');
                
                // Hiển thị nút chụp ảnh
                const captureButton = document.createElement('button');
                captureButton.className = 'capture-face-btn';
                captureButton.innerHTML = '<i class="fas fa-camera"></i> Chụp ảnh gương mặt';
                captureButton.onclick = () => captureFaceImage(employeeId, statusElement);
                
                const existingButton = scannerElement.querySelector('.capture-face-btn');
                if (existingButton) {
                    existingButton.remove();
                }
                scannerElement.appendChild(captureButton);
                
            } catch (error) {
                console.error('Webcam error:', error);
                showMessage(statusElement, 'Không thể truy cập camera: ' + error.message, 'error');
                stopFaceScanning();
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage(document.getElementById('face-enrollment-status'), 
                'Lỗi khi khởi tạo camera: ' + error.message, 'error');
            stopFaceScanning();
        }
    }

    async function captureFaceImage(employeeId, statusElement) {
        try {
            const videoElement = document.getElementById('faceVideo');
            const canvasElement = document.getElementById('faceCanvas');
            const context = canvasElement.getContext('2d');
            
            // Cố định kích thước canvas để tránh giật
            canvasElement.width = 400;
            canvasElement.height = 400;
            
            // Vẽ frame từ video lên canvas với kích thước cố định
            context.drawImage(videoElement, 0, 0, 400, 400);
            
            // Chuyển đổi canvas thành blob
            const blob = await new Promise(resolve => {
                canvasElement.toBlob(resolve, 'image/jpeg', 0.8);
            });
            
            // Tạo FormData để gửi ảnh
            const formData = new FormData();
            formData.append('image', blob, 'face.jpg');
            formData.append('id_nhan_vien', employeeId);
            
            showMessage(statusElement, 'Đang xử lý ảnh gương mặt...', 'info');
            
            // Gửi ảnh lên server
            const response = await fetch('/doanqlns/index.php/api/settings/face/enroll', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(statusElement, 'Đã lưu gương mặt thành công!', 'success');
                
                // Hiển thị overlay thành công
                const successOverlay = document.getElementById('face-success-overlay');
                successOverlay.style.display = 'flex';
                
                setTimeout(() => {
                    stopFaceScanning();
                    loadFaceManageList();
                    successOverlay.style.display = 'none';
                }, 2000);
            } else {
                throw new Error(result.message || 'Không thể lưu gương mặt');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showMessage(statusElement, 'Lỗi khi xử lý ảnh gương mặt: ' + error.message, 'error');
        }
    }

    function stopFaceScanning() {
        const scannerElement = document.getElementById('faceScanner');
        const videoElement = document.getElementById('faceVideo');
        const captureButton = scannerElement.querySelector('.capture-face-btn');
        
        // Dừng webcam stream
        if (videoElement.srcObject) {
            videoElement.srcObject.getTracks().forEach(track => track.stop());
            videoElement.srcObject = null;
        }
        
        // Xóa nút chụp ảnh
        if (captureButton) {
            captureButton.remove();
        }
        
        scannerElement.classList.remove('active');
    }

    function cancelFaceScanning() {
        if (confirm('Bạn có chắc muốn hủy quá trình lấy gương mặt?')) {
            stopFaceScanning();
            showMessage(document.getElementById('face-enrollment-status'), 
                'Đã hủy quá trình lấy gương mặt', 'info');
        }
    }

    async function loadFaceManageList() {
        try {
            console.log('Loading face management list...');
            const response = await fetch('/doanqlns/index.php/api/users');
            const result = await response.json();
            console.log('Face data:', result);

            if (Array.isArray(result)) {
                const tbody = document.getElementById('face-manage-list');
                tbody.innerHTML = '';
                
                // Lọc các nhân viên có hình ảnh
                const faceData = result.filter(user => user.hinh_anh && user.hinh_anh.trim() !== '');
                
                if (!faceData.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Không có nhân viên nào có hình ảnh</td></tr>';
                    return;
                }

                faceData.forEach(employee => {
                    // Chuẩn hóa đường dẫn hình ảnh giống như trong users.php
                    let avatarPath = employee.hinh_anh || '';
                    let normalizedAvatar;
                    if (!avatarPath) {
                        normalizedAvatar = 'https://via.placeholder.com/60x80';
                    } else if (avatarPath.startsWith('http')) {
                        normalizedAvatar = avatarPath;
                    } else if (avatarPath.startsWith('/doanqlns')) {
                        normalizedAvatar = avatarPath;
                    } else if (avatarPath.startsWith('/')) {
                        normalizedAvatar = '/doanqlns' + avatarPath;
                    } else {
                        normalizedAvatar = '/doanqlns/' + avatarPath;
                    }
                    const cacheBuster = Date.now();
                    const avatarSrc = normalizedAvatar + (normalizedAvatar.includes('?') ? '&' : '?') + 'v=' + cacheBuster;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${employee.id_nhan_vien || 'N/A'}</td>
                        <td>${employee.ho_ten || 'Chưa có'}</td>
                        <td class="image-cell">
                            <img src="${avatarSrc}" alt="${employee.ho_ten}" class="face-image" 
                                 onerror="this.src='https://via.placeholder.com/60x80'; console.error('Failed to load image: ${avatarSrc}');">
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn change-face" onclick="changeFaceImage(${employee.id_nhan_vien})">
                                    <i class="fas fa-camera"></i> Đổi ảnh
                                </button>
                                <button class="action-btn delete" onclick="deleteFace(${employee.id_nhan_vien})">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Thêm lightbox cho hình ảnh
                document.querySelectorAll('.face-image').forEach(img => {
                    img.addEventListener('click', function() {
                        const lightbox = document.createElement('div');
                        lightbox.id = 'lightbox';
                        lightbox.style.cssText = `
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(0, 0, 0, 0.8);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            z-index: 1000;
                            cursor: pointer;
                        `;

                        const enlargedImg = document.createElement('img');
                        enlargedImg.src = this.src;
                        enlargedImg.className = 'enlarged-img';
                        lightbox.appendChild(enlargedImg);
                        document.body.appendChild(lightbox);

                        lightbox.addEventListener('click', () => {
                            lightbox.remove();
                        });
                    });
                });

                console.log('Face management list loaded successfully');
            } else {
                showMessage(document.getElementById('face-manage-message'), 
                    result.message || 'Không thể tải danh sách nhân viên', 'error');
            }
        } catch (error) {
            console.error('Error loading face manage list:', error);
            showMessage(document.getElementById('face-manage-message'), 
                'Lỗi khi tải danh sách nhân viên: ' + error.message, 'error');
        }
    }

    async function changeFaceImage(employeeId) {
        try {
            const statusElement = document.getElementById('face-enrollment-status');
            const scannerElement = document.getElementById('faceScanner');
            const videoElement = document.getElementById('faceVideo');
            
            // Chọn nhân viên tự động trong dropdown
            const employeeSelect = document.getElementById('faceEmployeeSelect');
            employeeSelect.value = employeeId;
            
            // Hiển thị giao diện quét gương mặt
            showForm('face');
            scannerElement.classList.add('active');
            statusElement.style.display = 'block';
            showMessage(statusElement, 'Đang khởi tạo camera để đổi ảnh...', 'info');

            // Khởi tạo webcam với cấu hình tối ưu
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 400, max: 400 },
                        height: { ideal: 400, max: 400 },
                        facingMode: 'user',
                        frameRate: { ideal: 30, max: 30 }
                    } 
                });
                
                // Cố định kích thước video trước khi gán stream
                videoElement.width = 400;
                videoElement.height = 400;
                videoElement.srcObject = stream;
                
                // Đợi video load xong trước khi play
                videoElement.onloadedmetadata = () => {
                    videoElement.play().catch(console.error);
                };
                
                // Tối ưu hiệu suất video
                videoElement.addEventListener('loadeddata', () => {
                    console.log('Video loaded successfully');
                });
                
                // Xử lý lỗi video
                videoElement.addEventListener('error', (e) => {
                    console.error('Video error:', e);
                    showMessage(statusElement, 'Lỗi khi tải video: ' + e.message, 'error');
                });
                
                showMessage(statusElement, 'Camera đã sẵn sàng. Vui lòng nhìn thẳng vào camera và nhấn "Chụp ảnh"', 'info');
                
                // Tạo nút chụp ảnh mới
                const captureButton = document.createElement('button');
                captureButton.className = 'capture-face-btn';
                captureButton.innerHTML = '<i class="fas fa-camera"></i> Chụp ảnh gương mặt';
                captureButton.onclick = async () => {
                    try {
                        const canvasElement = document.getElementById('faceCanvas');
                        const context = canvasElement.getContext('2d');
                        
                        // Cố định kích thước canvas để tránh giật
                        canvasElement.width = 400;
                        canvasElement.height = 400;
                        
                        // Vẽ frame từ video lên canvas với kích thước cố định
                        context.drawImage(videoElement, 0, 0, 400, 400);
                        
                        const blob = await new Promise(resolve => {
                            canvasElement.toBlob(resolve, 'image/jpeg', 0.8);
                        });
                        
                        // Kiểm tra kích thước file
                        if (blob.size > 2 * 1024 * 1024) {
                            showMessage(statusElement, 'Kích thước ảnh vượt quá 2MB', 'error');
                            return;
                        }

                        const formData = new FormData();
                        formData.append('image', blob, `nv${employeeId}.jpg`);
                        formData.append('id_nhan_vien', employeeId);
                        
                        console.log('Uploading image for employee ' + employeeId + ' with size ' + blob.size + ' bytes');
                        
                        showMessage(statusElement, 'Đang tải ảnh lên server...', 'info');
                        
                        const response = await fetch('/doanqlns/index.php/api/settings/face/enroll', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const result = await response.json();
                        
                        if (result.success) {
                            showMessage(statusElement, 'Cập nhật ảnh gương mặt thành công!', 'success');
                            
                            const successOverlay = document.getElementById('face-success-overlay');
                            successOverlay.style.display = 'flex';
                            
                            setTimeout(() => {
                                stopFaceScanning();
                                loadFaceManageList();
                                successOverlay.style.display = 'none';
                            }, 2000);
                        } else {
                            throw new Error(result.message || 'Không thể cập nhật ảnh gương mặt');
                        }
                    } catch (error) {
                        console.error('Error capturing face image:', error);
                        showMessage(statusElement, 'Lỗi khi tải ảnh lên: ' + error.message, 'error');
                    }
                };
                
                const existingButton = scannerElement.querySelector('.capture-face-btn');
                if (existingButton) {
                    existingButton.remove();
                }
                scannerElement.appendChild(captureButton);
                
            } catch (error) {
                console.error('Webcam error:', error);
                showMessage(statusElement, 'Không thể truy cập camera: ' + error.message, 'error');
                stopFaceScanning();
            }
            
        } catch (error) {
            console.error('Error initializing face change:', error);
            showMessage(document.getElementById('face-enrollment-status'), 
                'Lỗi khi khởi tạo camera: ' + error.message, 'error');
            stopFaceScanning();
        }
    }

    async function deleteFace(employeeId) {
        if (!confirm('Bạn có chắc chắn muốn xóa ảnh gương mặt của nhân viên này không?')) {
            return;
        }

        try {
            const formData = new FormData();
            // Tạo một file rỗng để gửi
            const emptyFile = new File([''], 'empty.jpg', { type: 'image/jpeg' });
            formData.append('image', emptyFile);
            formData.append('id_nhan_vien', employeeId);

            console.log('Deleting image for employee ' + employeeId);

            const response = await fetch('/doanqlns/index.php/api/settings/face/enroll', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.success) {
                showMessage(document.getElementById('face-enrollment-status'), 
                    'Đã xóa ảnh gương mặt thành công', 'success');
                loadFaceManageList();
            } else {
                showMessage(document.getElementById('face-enrollment-status'), 
                    result.message || 'Không thể xóa ảnh gương mặt', 'error');
            }
        } catch (error) {
            console.error('Error deleting face:', error);
            showMessage(document.getElementById('face-enrollment-status'), 
                'Lỗi khi xóa ảnh gương mặt: ' + error.message, 'error');
        }
    }

    // Network Debug Functions
    async function loadNetworkInfo() {
        try {
            // Lấy thời gian hiện tại từ máy tính người dùng
            const now = new Date();
            const clientTime = now.toTimeString().slice(0, 8); // HH:MM:SS
            
            console.log('Client time:', clientTime);
            
            // Gửi thời gian client lên server
            const response = await fetch('/doanqlns/api_network_info.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    client_time: clientTime
                })
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    console.log('Server response:', data.data);
                    updateNetworkDisplay(data.data);
                } else {
                    console.error('Error loading network info:', data.message);
                    updateNetworkDisplayWithDefaults();
                }
            } else {
                console.error('HTTP error loading network info');
                updateNetworkDisplayWithDefaults();
            }
        } catch (error) {
            console.error('Error loading network info:', error);
            updateNetworkDisplayWithDefaults();
        }
    }

    function updateNetworkDisplay(networkInfo) {
        // Cập nhật trạng thái mạng
        const networkStatus = document.getElementById('network-status');
        if (networkInfo.is_company_network) {
            networkStatus.textContent = 'Đang ở mạng công ty';
            networkStatus.className = 'network-status success';
        } else {
            networkStatus.textContent = 'Không ở mạng công ty';
            networkStatus.className = 'network-status danger';
        }
        
        // Cập nhật trạng thái thời gian
        const timeStatus = document.getElementById('time-status');
        if (networkInfo.time_ok !== undefined) {
            if (networkInfo.time_ok) {
                timeStatus.textContent = 'Trong khung giờ điểm danh';
                timeStatus.className = 'network-status success';
            } else {
                timeStatus.textContent = 'Ngoài khung giờ điểm danh';
                timeStatus.className = 'network-status danger';
            }
        } else {
            timeStatus.textContent = 'Không giới hạn thời gian';
            timeStatus.className = 'network-status success';
        }
        
        // Cập nhật trạng thái nút điểm danh tổng hợp
        const buttonStatus = document.getElementById('button-status');
        const networkOk = networkInfo.is_company_network;
        const timeOk = networkInfo.time_ok !== undefined ? networkInfo.time_ok : true;
        
        if (networkOk && timeOk) {
            buttonStatus.innerHTML = '<span class="network-status success">✓ Nút "Điểm danh" sẽ hiển thị</span>';
        } else if (!networkOk) {
            buttonStatus.innerHTML = '<span class="network-status danger">✗ Không ở mạng công ty</span>';
        } else if (!timeOk) {
            buttonStatus.innerHTML = '<span class="network-status danger">✗ Ngoài khung giờ điểm danh</span>';
        } else {
            buttonStatus.innerHTML = '<span class="network-status danger">✗ Nút "Điểm danh" sẽ ẩn</span>';
        }

        // Cập nhật hiển thị nút điểm danh theo gate tổng hợp (server + client + time)
        const attendanceBtn = document.getElementById('attendance-action-btn');
        const gateMsg = document.getElementById('gate-message');
        if (attendanceBtn && gateMsg) {
            // Nếu API cung cấp thêm server_on_company_network thì dùng, nếu không fallback theo client
            const serverOk = typeof networkInfo.server_on_company_network !== 'undefined' ? !!networkInfo.server_on_company_network : true;
            const allowed = !!networkInfo.is_company_network && serverOk && timeOk;
            attendanceBtn.style.display = allowed ? 'inline-flex' : 'none';
            gateMsg.className = 'message ' + (allowed ? 'success' : 'error') + ' active';
            gateMsg.style.display = 'block';
            
            if (networkOk) {
                gateMsg.textContent = 'Đang ở trong mạng Wi‑Fi công ty. Bạn có thể điểm danh.';
            } else {
                gateMsg.textContent = 'Thiết bị hoặc máy chủ không kết nối đúng Wi‑Fi công ty.';
            }
        }
    }

    function updateNetworkDisplayWithDefaults() {
        // Hiển thị thông tin mặc định khi không thể lấy từ server
        document.getElementById('network-status').textContent = 'Không thể kiểm tra';
        document.getElementById('network-status').className = 'network-status danger';
        document.getElementById('time-status').textContent = 'Không thể kiểm tra';
        document.getElementById('time-status').className = 'network-status danger';
        document.getElementById('button-status').innerHTML = '<span class="network-status danger">Không thể kiểm tra</span>';
    }

    async function testWithIP() {
        const testIP = document.getElementById('test-ip-input').value.trim();
        if (!testIP) {
            alert('Vui lòng nhập IP để test');
            return;
        }

        try {
            // Gửi IP test lên server
            const response = await fetch('/doanqlns/api_network.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ test_ip: testIP })
            });

            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Cập nhật hiển thị
                    document.getElementById('test-ip-display').textContent = testIP;
                    document.getElementById('test-ip-display').className = 'network-status success';
                    
                    // Reload thông tin mạng
                    setTimeout(() => {
                        loadNetworkInfo();
                    }, 500);
                    
                    alert('Đã test với IP: ' + testIP);
                } else {
                    alert('Lỗi khi test IP: ' + result.message);
                }
            } else {
                alert('Lỗi khi gửi yêu cầu test IP');
            }
        } catch (error) {
            console.error('Error testing IP:', error);
            alert('Lỗi khi test IP: ' + error.message);
        }
    }

    async function clearTestIP() {
        try {
            const response = await fetch('/doanqlns/api_network.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ clear: true })
            });

            if (response.ok) {
                result = await response.json();
                if (result.success) {
                    // Cập nhật hiển thị
                    document.getElementById('test-ip-display').textContent = 'Không có IP test';
                    document.getElementById('test-ip-display').className = 'network-status danger';
                    document.getElementById('test-ip-input').value = '';
                    
                    // Reload thông tin mạng
                    setTimeout(() => {
                        loadNetworkInfo();
                    }, 500);
                    
                    alert('Đã xóa IP test thành công');
                } else {
                    alert('Lỗi khi xóa IP test: ' + result.message);
                }
            } else {
                alert('Lỗi khi gửi yêu cầu xóa IP test');
            }
        } catch (error) {
            console.error('Error clearing test IP:', error);
            alert('Lỗi khi xóa IP test: ' + error.message);
        }
    }


    // Attendance Time Settings Functions
    async function loadAttendanceTimeSettings() {
        try {
            console.log('Loading attendance time settings...');
            const response = await fetch('/doanqlns/index.php/api/settings/attendance-time');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Settings response:', result);
            
            if (result.success) {
                const settings = result.data;
                console.log('Settings data:', settings);
                
                // Cập nhật form với dữ liệu từ database
                document.getElementById('morningStartTime').value = settings.morning_start_time || '08:00';
                document.getElementById('morningEndTime').value = settings.morning_end_time || '08:30';
                document.getElementById('lunchStartTime').value = settings.lunch_start_time || '12:00';
                document.getElementById('lunchEndTime').value = settings.lunch_end_time || '12:30';
                document.getElementById('afternoonStartTime').value = settings.afternoon_start_time || '17:00';
                document.getElementById('afternoonEndTime').value = settings.afternoon_end_time || '17:30';
                document.getElementById('enableTimeRestriction').checked = settings.enable_time_restriction !== 0;
                
                // Cập nhật hiển thị cài đặt hiện tại
                document.getElementById('current-morning-time').textContent = 
                    `${settings.morning_start_time || '08:00'} - ${settings.morning_end_time || '08:30'}`;
                document.getElementById('current-lunch-time').textContent = 
                    `${settings.lunch_start_time || '12:00'} - ${settings.lunch_end_time || '12:30'}`;
                document.getElementById('current-afternoon-time').textContent = 
                    `${settings.afternoon_start_time || '17:00'} - ${settings.afternoon_end_time || '17:30'}`;
                document.getElementById('current-restriction-status').textContent = 
                    settings.enable_time_restriction !== 0 ? 'Đã bật' : 'Đã tắt';
                
                // Cập nhật thông báo khung giờ hiện tại
                updateCurrentTimeStatus(settings);
                
                console.log('Form updated with settings:', {
                    morningStart: settings.morning_start_time,
                    morningEnd: settings.morning_end_time,
                    afternoonStart: settings.afternoon_start_time,
                    afternoonEnd: settings.afternoon_end_time,
                    restriction: settings.enable_time_restriction
                });
            } else {
                showMessage(document.getElementById('attendance-time-message'), 
                    result.message || 'Không thể tải cài đặt giờ điểm danh', 'error');
            }
        } catch (error) {
            console.error('Error loading attendance time settings:', error);
            showMessage(document.getElementById('attendance-time-message'), 
                'Lỗi khi tải cài đặt giờ điểm danh: ' + error.message, 'error');
        }
    }

    function updateCurrentTimeStatus(settings) {
        const timeStatusElement = document.getElementById('current-time-status');
        if (!timeStatusElement) return;
        
        // Nếu không bật kiểm tra giờ
        if (!settings.enable_time_restriction) {
            timeStatusElement.textContent = 'Không giới hạn thời gian';
            timeStatusElement.className = 'time-status-message success';
            return;
        }
        
        // Lấy thời gian hiện tại từ máy tính người dùng
        const now = new Date();
        const currentTime = now.toTimeString().slice(0, 8); // HH:MM:SS
        
        // Kiểm tra xem có trong khung giờ không
        const isMorningTime = currentTime >= settings.morning_start_time && currentTime <= settings.morning_end_time;
        const isLunchTime = currentTime >= settings.lunch_start_time && currentTime <= settings.lunch_end_time;
        const isAfternoonTime = currentTime >= settings.afternoon_start_time && currentTime <= settings.afternoon_end_time;
        const isInTimeRange = isMorningTime || isLunchTime || isAfternoonTime;
        
        if (isInTimeRange) {
            timeStatusElement.textContent = 'Trong khung giờ điểm danh';
            timeStatusElement.className = 'time-status-message success';
        } else {
            timeStatusElement.textContent = 'Hiện tại không trong khung giờ điểm danh';
            timeStatusElement.className = 'time-status-message danger';
        }
    }

    async function saveAttendanceTimeSettings() {
        try {
            const morningStart = document.getElementById('morningStartTime').value;
            const morningEnd = document.getElementById('morningEndTime').value;
            const lunchStart = document.getElementById('lunchStartTime').value;
            const lunchEnd = document.getElementById('lunchEndTime').value;
            const afternoonStart = document.getElementById('afternoonStartTime').value;
            const afternoonEnd = document.getElementById('afternoonEndTime').value;
            const enableRestriction = document.getElementById('enableTimeRestriction').checked;
            
            // Kiểm tra dữ liệu đầu vào
            if (!morningStart || !morningEnd || !lunchStart || !lunchEnd || !afternoonStart || !afternoonEnd) {
                showMessage(document.getElementById('attendance-time-message'), 
                    'Vui lòng nhập đầy đủ thời gian', 'error');
                return;
            }
            
            // Kiểm tra logic thời gian
            if (morningStart >= morningEnd) {
                showMessage(document.getElementById('attendance-time-message'), 
                    'Giờ kết thúc điểm danh sáng phải sau giờ bắt đầu', 'error');
                return;
            }
            
            if (lunchStart >= lunchEnd) {
                showMessage(document.getElementById('attendance-time-message'), 
                    'Giờ kết thúc điểm danh trưa phải sau giờ bắt đầu', 'error');
                return;
            }
            
            if (afternoonStart >= afternoonEnd) {
                showMessage(document.getElementById('attendance-time-message'), 
                    'Giờ kết thúc điểm danh chiều phải sau giờ bắt đầu', 'error');
                return;
            }
            
            const data = {
                morning_start_time: morningStart,
                morning_end_time: morningEnd,
                lunch_start_time: lunchStart,
                lunch_end_time: lunchEnd,
                afternoon_start_time: afternoonStart,
                afternoon_end_time: afternoonEnd,
                enable_time_restriction: enableRestriction ? 1 : 0
            };
            
            const response = await fetch('/doanqlns/index.php/api/settings/attendance-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(document.getElementById('attendance-time-message'), 
                    'Đã lưu cài đặt giờ điểm danh thành công!', 'success');
                
                // Cập nhật hiển thị cài đặt hiện tại
                document.getElementById('current-morning-time').textContent = `${morningStart} - ${morningEnd}`;
                document.getElementById('current-lunch-time').textContent = `${lunchStart} - ${lunchEnd}`;
                document.getElementById('current-afternoon-time').textContent = `${afternoonStart} - ${afternoonEnd}`;
                document.getElementById('current-restriction-status').textContent = enableRestriction ? 'Đã bật' : 'Đã tắt';
            } else {
                showMessage(document.getElementById('attendance-time-message'), 
                    result.message || 'Không thể lưu cài đặt giờ điểm danh', 'error');
            }
        } catch (error) {
            console.error('Error saving attendance time settings:', error);
            showMessage(document.getElementById('attendance-time-message'), 
                'Lỗi khi lưu cài đặt giờ điểm danh: ' + error.message, 'error');
        }
    }

    // Cấu Hình GLV Functions
    async function loadDanhSachCauHinhDropdown() {
        try {
            console.log('Loading danh sách cấu hình for dropdown...');
            const response = await fetch('/doanqlns/index.php/api/cau-hinh-gio-lam-viec');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Dropdown cấu hình response:', result);
            
            if (result.success && result.data) {
                hienThiDanhSachCauHinhTrongDropdown(result.data);
            } else {
                document.getElementById('danhSachCauHinhDropdown').innerHTML = 
                    '<li><span class="dropdown-item-text text-muted">Không có cấu hình nào</span></li>';
            }
        } catch (error) {
            console.error('Error loading danh sách cấu hình dropdown:', error);
            document.getElementById('danhSachCauHinhDropdown').innerHTML = 
                '<li><span class="dropdown-item-text text-danger">Lỗi khi tải danh sách</span></li>';
        }
    }

    function hienThiDanhSachCauHinhTrongDropdown(danhSach) {
        let html = '';
        
        danhSach.forEach((cauHinh) => {
            const ngayTao = new Date(cauHinh.ngay_tao).toLocaleString('vi-VN');
            const trangThaiClass = cauHinh.trang_thai === 'active' ? 'active' : 'inactive';
            const trangThaiText = cauHinh.trang_thai === 'active' ? 'Hoạt động' : 'Không hoạt động';
            
            html += `
                <li>
                    <a class="dropdown-item cau-hinh-dropdown-item" href="#" onclick="chonCauHinhTuDropdown(${cauHinh.id})" data-id="${cauHinh.id}">
                        <div class="cau-hinh-ten">${cauHinh.ten_cau_hinh || 'Chưa đặt tên'}</div>
                        <div class="cau-hinh-thoi-gian">
                            Sáng: ${cauHinh.gio_sang_bat_dau} - ${cauHinh.gio_sang_ket_thuc} | 
                            Trưa: ${cauHinh.gio_trua_bat_dau} - ${cauHinh.gio_trua_ket_thuc} | 
                            Chiều: ${cauHinh.gio_chieu_bat_dau} - ${cauHinh.gio_chieu_ket_thuc}
                        </div>
                        <div class="cau-hinh-ngay">Tạo: ${ngayTao}</div>
                        <div class="cau-hinh-trang-thai ${trangThaiClass}">${trangThaiText}</div>
                    </a>
                </li>
            `;
        });
        
        document.getElementById('danhSachCauHinhDropdown').innerHTML = html;
    }

    async function chonCauHinhTuDropdown(id) {
        try {
            console.log('Chọn cấu hình ID:', id);
            
            // Tải dữ liệu cấu hình được chọn
            const response = await fetch(`/doanqlns/index.php/api/cau-hinh-gio-lam-viec/${id}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success && result.data) {
                const cauHinh = result.data;
                
                // Điền dữ liệu vào form
                document.getElementById('glv_ten_cau_hinh').value = cauHinh.ten_cau_hinh || '';
                document.getElementById('glv_gio_sang_bat_dau').value = (cauHinh.gio_sang_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_sang_ket_thuc').value = (cauHinh.gio_sang_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_sang_tre_bat_dau').value = (cauHinh.gio_sang_tre_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_sang_tre_ket_thuc').value = (cauHinh.gio_sang_tre_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_gio_trua_bat_dau').value = (cauHinh.gio_trua_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_trua_ket_thuc').value = (cauHinh.gio_trua_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_trua_tre_bat_dau').value = (cauHinh.gio_trua_tre_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_trua_tre_ket_thuc').value = (cauHinh.gio_trua_tre_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_gio_chieu_ra_som_bat_dau').value = (cauHinh.gio_chieu_ra_som_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_ra_som_ket_thuc').value = (cauHinh.gio_chieu_ra_som_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_bat_dau').value = (cauHinh.gio_chieu_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_ket_thuc').value = (cauHinh.gio_chieu_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_ghi_chu').value = cauHinh.ghi_chu || '';
                
                // Hiển thị cấu hình hiện tại
                hienThiCauHinhGLVHienTai(cauHinh);
                
                showMessage(document.getElementById('glv-message'), 
                    'Đã tải cấu hình: ' + (cauHinh.ten_cau_hinh || 'Chưa đặt tên'), 'success');
            } else {
                showMessage(document.getElementById('glv-message'), 
                    result.message || 'Không thể tải cấu hình', 'error');
            }
        } catch (error) {
            console.error('Error loading cấu hình:', error);
            showMessage(document.getElementById('glv-message'), 
                'Lỗi khi tải cấu hình: ' + error.message, 'error');
        }
    }

    async function loadCauHinhGLV() {
        try {
            console.log('Loading cấu hình GLV...');
            const response = await fetch('/doanqlns/index.php/api/cau-hinh-gio-lam-viec/hien-tai');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('Cấu hình GLV response:', result);
            
            if (result.success && result.data) {
                const cauHinh = result.data;
                
                // Điền dữ liệu vào form
                document.getElementById('glv_gio_sang_bat_dau').value = (cauHinh.gio_sang_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_sang_ket_thuc').value = (cauHinh.gio_sang_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_sang_tre_bat_dau').value = (cauHinh.gio_sang_tre_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_sang_tre_ket_thuc').value = (cauHinh.gio_sang_tre_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_gio_trua_bat_dau').value = (cauHinh.gio_trua_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_trua_ket_thuc').value = (cauHinh.gio_trua_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_trua_tre_bat_dau').value = (cauHinh.gio_trua_tre_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_trua_tre_ket_thuc').value = (cauHinh.gio_trua_tre_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_gio_chieu_ra_som_bat_dau').value = (cauHinh.gio_chieu_ra_som_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_ra_som_ket_thuc').value = (cauHinh.gio_chieu_ra_som_ket_thuc || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_bat_dau').value = (cauHinh.gio_chieu_bat_dau || '').substring(0, 5);
                document.getElementById('glv_gio_chieu_ket_thuc').value = (cauHinh.gio_chieu_ket_thuc || '').substring(0, 5);
                
                document.getElementById('glv_ten_cau_hinh').value = cauHinh.ten_cau_hinh || '';
                document.getElementById('glv_ghi_chu').value = cauHinh.ghi_chu || '';
                
                // Hiển thị cấu hình hiện tại
                hienThiCauHinhGLVHienTai(cauHinh);
            } else {
                showMessage(document.getElementById('glv-message'), 
                    result.message || 'Không có cấu hình GLV', 'warning');
                document.getElementById('current-glv-settings').innerHTML = 
                    '<p class="text-muted">Chưa có cấu hình nào</p>';
            }
        } catch (error) {
            console.error('Error loading cấu hình GLV:', error);
            showMessage(document.getElementById('glv-message'), 
                'Lỗi khi tải cấu hình GLV: ' + error.message, 'error');
        }
    }

    function hienThiCauHinhGLVHienTai(cauHinh) {
        const html = `
            <div class="row">
                <div class="col-md-4">
                    <strong>Giờ sáng:</strong><br>
                    Đúng giờ: ${cauHinh.gio_sang_bat_dau} - ${cauHinh.gio_sang_ket_thuc}<br>
                    Đi trễ: ${cauHinh.gio_sang_tre_bat_dau} - ${cauHinh.gio_sang_tre_ket_thuc}
                </div>
                <div class="col-md-4">
                    <strong>Giờ trưa:</strong><br>
                    Đúng giờ: ${cauHinh.gio_trua_bat_dau} - ${cauHinh.gio_trua_ket_thuc}<br>
                    Đi trễ: ${cauHinh.gio_trua_tre_bat_dau} - ${cauHinh.gio_trua_tre_ket_thuc}
                </div>
                <div class="col-md-4">
                    <strong>Giờ chiều:</strong><br>
                    Ra sớm: ${cauHinh.gio_chieu_ra_som_bat_dau} - ${cauHinh.gio_chieu_ra_som_ket_thuc}<br>
                    Đúng giờ: ${cauHinh.gio_chieu_bat_dau} - ${cauHinh.gio_chieu_ket_thuc}
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Tên cấu hình:</strong> ${cauHinh.ten_cau_hinh || 'Chưa đặt tên'}<br>
                    <strong>Ghi chú:</strong> ${cauHinh.ghi_chu || 'Không có'}
                </div>
            </div>
        `;
        document.getElementById('current-glv-settings').innerHTML = html;
    }

    async function luuCauHinhGLV() {
        try {
            const data = {
                ten_cau_hinh: document.getElementById('glv_ten_cau_hinh').value || 'Cấu hình GLV ' + new Date().toLocaleString('vi-VN'),
                gio_sang_bat_dau: (document.getElementById('glv_gio_sang_bat_dau').value || '') + ':00',
                gio_sang_ket_thuc: (document.getElementById('glv_gio_sang_ket_thuc').value || '') + ':00',
                gio_sang_tre_bat_dau: (document.getElementById('glv_gio_sang_tre_bat_dau').value || '') + ':00',
                gio_sang_tre_ket_thuc: (document.getElementById('glv_gio_sang_tre_ket_thuc').value || '') + ':00',
                gio_trua_bat_dau: (document.getElementById('glv_gio_trua_bat_dau').value || '') + ':00',
                gio_trua_ket_thuc: (document.getElementById('glv_gio_trua_ket_thuc').value || '') + ':00',
                gio_trua_tre_bat_dau: (document.getElementById('glv_gio_trua_tre_bat_dau').value || '') + ':00',
                gio_trua_tre_ket_thuc: (document.getElementById('glv_gio_trua_tre_ket_thuc').value || '') + ':00',
                gio_chieu_ra_som_bat_dau: (document.getElementById('glv_gio_chieu_ra_som_bat_dau').value || '') + ':00',
                gio_chieu_ra_som_ket_thuc: (document.getElementById('glv_gio_chieu_ra_som_ket_thuc').value || '') + ':00',
                gio_chieu_bat_dau: (document.getElementById('glv_gio_chieu_bat_dau').value || '') + ':00',
                gio_chieu_ket_thuc: (document.getElementById('glv_gio_chieu_ket_thuc').value || '') + ':00',
                ghi_chu: document.getElementById('glv_ghi_chu').value || ''
            };

            // Kiểm tra dữ liệu đầu vào
            if (!data.gio_sang_bat_dau || !data.gio_sang_ket_thuc || !data.gio_trua_bat_dau || !data.gio_trua_ket_thuc || 
                !data.gio_chieu_bat_dau || !data.gio_chieu_ket_thuc) {
                showMessage(document.getElementById('glv-message'), 
                    'Vui lòng nhập đầy đủ thời gian', 'error');
                return;
            }

            const response = await fetch('/doanqlns/index.php/api/cau-hinh-gio-lam-viec', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(document.getElementById('glv-message'), 
                    'Lưu cấu hình GLV thành công!', 'success');
                loadCauHinhGLV(); // Tải lại dữ liệu
            } else {
                showMessage(document.getElementById('glv-message'), 
                    result.message || 'Lỗi khi lưu cấu hình GLV', 'error');
            }
        } catch (error) {
            console.error('Error saving cấu hình GLV:', error);
            showMessage(document.getElementById('glv-message'), 
                'Lỗi khi lưu cấu hình GLV: ' + error.message, 'error');
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
        </div>
    </div>
</div>
</body>
</html>