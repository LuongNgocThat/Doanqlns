<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');

if (!($_SESSION['quyen_them'] || $_SESSION['quyen_sua'])) {
    header("Location: /doanqlns/giaodien.php?error=access_denied");
    exit();
}

$editId = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Quản Lý Nhân Sự</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleuser.css">
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

        .name-link,
        .name-link:hover {
            text-decoration: none;
            color: #696cff;
            font-weight: 500;
            cursor: pointer;
            transition: none;
        }
        
        .name-link:hover {
            color: #5a5cff;
            text-decoration: underline;
        }
        .employee-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            /* Tối ưu hiệu suất grid */
            contain: layout style paint;
            will-change: auto;
            /* Tối ưu GPU acceleration */
            transform: translateZ(0);
            -webkit-transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
        }

        /* Responsive design for employee grid */
        @media (max-width: 1400px) {
            .employee-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .employee-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 900px) {
            .employee-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .employee-container {
                grid-template-columns: 1fr;
            }
        }
        .employee-card {
            background: linear-gradient(180deg, #f6f8ff 0%, #eef2ff 100%);
            border: 1px solid #e0e7ff;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            transition: none;
            position: relative;
            /* Cố định kích thước card để tránh giật */
            min-height: 200px;
            max-height: 200px;
            /* Tối ưu hiệu suất */
            transform: translateZ(0);
            will-change: transform;
            /* Tối ưu GPU acceleration */
            -webkit-transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            /* Tránh giật */
            contain: layout style paint;
            /* Đảm bảo layout ổn định */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .employee-card:hover {
            transform: none;
            box-shadow: 0 6px 18px rgba(63, 109, 252, 0.12);
            background: linear-gradient(180deg, #f3f6ff 0%, #ecf1ff 100%);
            border-color: #d5ddff;
        }

        /* Thanh màu trạng thái dưới thẻ */
        .status-bar {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 4px;
            border-radius: 0 0 12px 12px;
            background: #e0e7ff;
        }
        .status-bar.active {
            background:rgb(57, 241, 127);
        }
        .status-bar.maternity {
            background:rgb(240, 131, 77);
        }
        .status-bar.inactive {
            background: #ef5350;
        }
        .employee-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 0 auto 10px;
            cursor: pointer;
            /* Cố định kích thước để tránh giật */
            min-width: 100px;
            min-height: 100px;
            max-width: 100px;
            max-height: 100px;
            /* Tối ưu hiệu suất */
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
            /* Đảm bảo placeholder có cùng kích thước */
            background-color: #f8f9fa;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="%23e9ecef"/><path d="M50 30c-11.046 0-20 8.954-20 20s8.954 20 20 20 20-8.954 20-20-8.954-20-20-20zm0 6c7.732 0 14 6.268 14 14s-6.268 14-14 14-14-6.268-14-14 6.268-14 14-14z" fill="%23adb5bd"/><circle cx="50" cy="50" r="16" fill="%23adb5bd"/></svg>');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            /* Xử lý trạng thái loading */
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .employee-avatar.loaded {
            opacity: 1;
        }

        .employee-avatar.error {
            opacity: 1;
            background-color: #f8f9fa;
        }

        .employee-avatar.loading {
            opacity: 0.5;
            background-color: #f8f9fa;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="%23e9ecef"/><circle cx="50" cy="50" r="20" fill="none" stroke="%23adb5bd" stroke-width="4" stroke-dasharray="31.416" stroke-dashoffset="31.416"><animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/><animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/></circle></svg>');
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.5; }
            50% { opacity: 0.8; }
            100% { opacity: 0.5; }
        }
        .employee-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #3f6dfc;
            margin: 6px 0 4px 0;
        }
        .employee-department {
            font-size: 0.9rem;
            color: #6b7aa6;
            margin-bottom: 10px;
        }
        .employee-status {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            min-width: 100px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .employee-status.active {
            background: linear-gradient(135deg, #3f6dfc, #5a86ff);
            color: white;
            border: 1px solid #5a86ff;
        }
        .employee-status.inactive {
            background: linear-gradient(135deg, #cfd8ff, #b9c6ff);
            color: #2b3a67;
            border: 1px solid #b9c6ff;
        }
        .employee-status.maternity {
            background: linear-gradient(135deg, #8ab4ff, #a6c1ff);
            color: #213b7a;
            border: 1px solid #8ab4ff;
        }
        .employee-status:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .ellipsis-menu {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 1.2rem;
            color: #697a8d;
            transition: color 0.3s ease;
        }
        .ellipsis-menu:hover {
            color: #696cff;
        }
        .action-buttons {
            display: none;
            position: absolute;
            top: 30px;
            right: 10px;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex-direction: column;
            gap: 5px;
            padding: 5px;
            z-index: 10;
        }
        .action-buttons.show {
            display: flex;
        }
        .action-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            width: auto;
            justify-content: center;
            min-width: 150px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .action-btn.edit {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(76, 175, 80, 0.3);
        }
        .action-btn.delete {
            background: linear-gradient(135deg, #f44336, #ef5350);
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(244, 67, 54, 0.3);
        }
        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .action-btn.edit:hover {
            box-shadow: 0 4px 10px rgba(76, 175, 80, 0.4);
        }
        .action-btn.delete:hover {
            box-shadow: 0 4px 10px rgba(244, 67, 54, 0.4);
        }
        .search-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-container .action-btn {
            background: linear-gradient(135deg, #696cff, #5a5cff);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
            min-width: 160px;
        }
        
        .search-container .action-btn:hover {
            background: linear-gradient(135deg, #5a5cff, #4c4dff);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(105, 108, 255, 0.4);
        }
        
        .search-container .action-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.3);
        }
        .search-input, .filter-select {
            padding: 10px 15px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            flex: 1;
            min-width: 200px;
            transition: all 0.3s ease;
        }
        .search-input:focus, .filter-select:focus {
            border-color: #696cff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.1);
        }
        #userDetailModal.user-detail-modal {
            z-index: 2000 !important;
            display: none;
        }
        #userDetailModal .user-detail-modal-content {
            max-width: 1000px !important;
            width: 100% !important;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
        }
        #userDetailModal .user-detail-avatar {
            width: 150px !important;
            height: 200px !important;
            border-radius: 0 !important;
            object-fit: cover;
        }
        #userDetailModal .user-detail-modal-section h3 {
            font-size: 1.4rem;
            color: #2c3e50;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }
        #userDetailModal .user-detail-modal-field label {
            width: 180px;
            font-weight: 600;
            color: #34495e;
        }
        #userDetailModal .user-detail-modal-field span {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e0e0e0;
        }
        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            .search-input, .filter-select {
                min-width: 100%;
            }
            .filter-select {
                max-width: 100%;
            }
            .employee-card {
                min-width: 100%;
            }
        }
        .custom-file-btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        .custom-file-btn:hover {
            background-color: #0056b3;
        }
        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        .avatar-input-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .avatar-container input[type="file"] {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            max-width: 200px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 5px;
        }
        .pagination-btn {
            padding: 8px 12px;
            border: 1px solid #e9ecef;
            background: white;
            color: #697a8d;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .pagination-btn:hover {
            background: #f8f9fa;
            border-color: #696cff;
            color: #696cff;
        }
        .pagination-btn.active {
            background: #696cff;
            border-color: #696cff;
            color: white;
        }
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .pagination-info {
            margin: 0 15px;
            color: #697a8d;
        }
        
        /* CSS đặc biệt cho modal cuộn */
        #addUserModal .modal-content {
            max-height: 95vh !important;
            overflow-y: auto !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        #addUserModal .modal-body {
            flex: 1 !important;
            overflow-y: auto !important;
            max-height: calc(95vh - 120px) !important;
            padding-right: 10px !important;
        }
        
        #addUserModal .modal-header {
            flex-shrink: 0 !important;
        }
        
        /* Scrollbar styling cho modal */
        #addUserModal .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        #addUserModal .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        #addUserModal .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        #addUserModal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* CSS cho modal chỉnh sửa */
        #editUserModal .modal-content {
            max-height: 95vh !important;
            overflow-y: auto !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        #editUserModal .modal-body {
            flex: 1 !important;
            overflow-y: auto !important;
            max-height: calc(95vh - 120px) !important;
            padding-right: 10px !important;
        }
        
        #editUserModal .modal-header {
            flex-shrink: 0 !important;
        }
        
        /* Scrollbar styling cho modal chỉnh sửa */
        #editUserModal .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        #editUserModal .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        #editUserModal .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        
        #editUserModal .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* CSS cho modal chi tiết nhân viên */
        .user-detail-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        /* Sắp xếp avatar ở cột trái và các khối thông tin (Cá nhân, Liên hệ, ...) ở cột phải để tránh khoảng trắng */
        @media (min-width: 992px) {
            .user-detail-container {
                display: grid;
                grid-template-columns: 320px 1fr;
                align-items: start;
                gap: 20px;
            }
            .user-avatar-section {
                position: sticky;
                top: 10px;
            }
            .user-info-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            }
        }
        
        .user-avatar-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .user-detail-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        
        .user-avatar-section h3 {
            margin: 10px 0 5px 0;
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .user-id {
            color: #6c757d;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .user-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-section {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-section h4 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 1.1rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        
        .info-section h4 i {
            margin-right: 8px;
            color: #696cff;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-item label {
            font-weight: 500;
            color: #495057;
            min-width: 150px;
        }
        
        .info-item span {
            color: #6c757d;
            text-align: right;
            flex: 1;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .status-badge.active {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            border: 1px solid #4caf50;
        }
        
        .status-badge.inactive {
            background: linear-gradient(135deg, #f44336, #ef5350);
            color: white;
            border: 1px solid #f44336;
        }
        
        .status-badge.maternity {
            background: linear-gradient(135deg, #ff9800, #ffb74d);
            color: white;
            border: 1px solid #ff9800;
        }
        
        /* Badge đếm ngược thai sản */
        .maternity-countdown {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            z-index: 10;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .maternity-countdown.remaining-high {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
        }
        
        .maternity-countdown.remaining-medium {
            background: linear-gradient(135deg, #ff9800, #ffb74d);
            color: white;
        }
        
        .maternity-countdown.remaining-low {
            background: linear-gradient(135deg, #ff5722, #ff7043);
            color: white;
        }
        
        .maternity-countdown.overdue {
            background: linear-gradient(135deg, #f44336, #ef5350);
            color: white;
        }
        
        .maternity-countdown.overdue::after {
            content: "!";
            margin-left: 2px;
            font-weight: bold;
        }
        
        /* Tooltip cho thông tin thai sản */
        .maternity-tooltip {
            position: relative;
            cursor: help;
        }
        
        .maternity-tooltip::after {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            pointer-events: none;
        }
        
        .maternity-tooltip:hover::after {
            opacity: 1;
            visibility: visible;
        }
        
        /* CSS cho modal chi tiết */
        #userDetailModal {
            z-index: 9999 !important;
        }
        
        #userDetailModal .modal-content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: scale(1);
            transition: none;
        }
        
        /* Đảm bảo modal hiển thị ngay lập tức */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }
        
        .modal.show {
            display: flex !important;
            opacity: 1;
        }
        
        
        /* Đảm bảo modal body có thể cuộn được */
        #addModalBody {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scroll-behavior: smooth !important;
        }
        
        /* Fallback cho trình duyệt không hỗ trợ flexbox */
        @supports not (display: flex) {
            #addUserModal .modal-content {
                height: 90vh;
                overflow-y: auto;
            }
            
            #addUserModal .modal-body {
                height: calc(90vh - 120px);
                overflow-y: auto;
            }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include('../includes/sidebar.php'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
        <?php 
        $page_title = "Danh Sách Nhân Viên";
        include('../includes/header.php'); 
        ?>

        <div class="page-header">
            <h1 class="page-title">Danh Sách Nhân Viên</h1>
            <div class="search-container">
                <select id="filterPhongBan" class="filter-select" aria-label="Lọc theo phòng ban">
                    <option value="">Tất cả phòng ban</option>
                </select>
                <select id="filterMaternity" class="filter-select" aria-label="Lọc theo trạng thái thai sản">
                    <option value="">Tất cả trạng thái</option>
                    <option value="maternity">Đang nghỉ thai sản</option>
                    <option value="maternity_ending_soon">Sắp hết thai sản (≤30 ngày)</option>
                    <option value="maternity_overdue">Quá hạn thai sản</option>
                </select>
                <input type="text" id="searchInput" class="search-input" placeholder="Nhập tên nhân viên...">
                <?php if ($_SESSION['quyen_them']): ?>
                    <button class="action-btn" onclick="showAddUserModal()">
                        <i class="fas fa-user-plus"></i> Thêm nhân viên
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="employee-container" id="userContainer">
            <!-- Employee cards will be rendered here -->
        </div>
        <div class="pagination" id="pagination">
            <!-- Pagination will be generated here -->
        </div>

        <div id="editUserModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Chỉnh Sửa Nhân Viên</h2>
                    <button class="modal-close">×</button>
                </div>
                <div class="modal-body" id="editModalBody">
                </div>
            </div>
        </div>

        <div id="addUserModal" class="modal">
            <div class="modal-content" style="max-height: 95vh; overflow-y: auto; display: flex; flex-direction: column;">
                <div class="modal-header" style="flex-shrink: 0;">
                    <h2 class="modal-title">Thêm Nhân Viên</h2>
                    <button class="modal-close">×</button>
                </div>
                <div class="modal-body" id="addModalBody" style="flex: 1; overflow-y: auto; max-height: calc(95vh - 120px); padding-right: 10px;">
                    <div class="modal-section">
                        <h3>Thông Tin Cá Nhân</h3>
                        <div class="avatar-input-wrapper">
                            <label for="add_hinh_anh" class="custom-file-btn">Choose File</label>
                            <input type="file" id="add_hinh_anh" accept="image/jpeg,image/png" style="display: none;">
                            <img id="add_hinh_anh_preview" class="avatar" src="https://via.placeholder.com/150x200" alt="Avatar Preview">
                        </div>
                        <div class="modal-field">
                            <label>Họ Tên <span style="color: red;">*</span></label>
                            <input type="text" id="add_ho_ten">
                        </div>
                        <div class="modal-field">
                            <label>Giới Tính</label>
                            <select id="add_gioi_tinh">
                                <option value="Nam">Nam</option>
                                <option value="Nữ">Nữ</option>
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Sinh <span style="color: red;">*</span></label>
                            <input type="date" id="add_ngay_sinh">
                        </div>
                        <div class="modal-field">
                            <label>Căn Cước Công Dân</label>
                            <input type="text" id="add_can_cuoc_cong_dan">
                        </div>
                        <div class="modal-field">
                            <label>Ngày Cấp</label>
                            <input type="date" id="add_ngay_cap">
                        </div>
                        <div class="modal-field">
                            <label>Nơi Cấp</label>
                            <input type="text" id="add_noi_cap">
                        </div>
                        <div class="modal-field">
                            <label>Quê Quán</label>
                            <input type="text" id="add_que_quan">
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Liên Hệ</h3>
                        <div class="modal-field">
                            <label>Email <span style="color: red;">*</span></label>
                            <input type="email" id="add_email">
                        </div>
                        <div class="modal-field">
                            <label>Số Điện Thoại</label>
                            <input type="text" id="add_so_dien_thoai">
                        </div>
                        <div class="modal-field">
                            <label>Địa Chỉ</label>
                            <input type="text" id="add_dia_chi">
                        </div>
                        <div class="modal-field">
                            <label>Nơi Thường Trú</label>
                            <input type="text" id="add_noi_thuong_tru" placeholder="Nhập nơi thường trú">
                        </div>
                        <div class="modal-field">
                            <label>Chỗ Ở Hiện Tại</label>
                            <input type="text" id="add_cho_o_hien_tai" placeholder="Nhập chỗ ở hiện tại">
                        </div>
                        <div class="modal-field">
                            <label>Dân Tộc</label>
                            <input type="text" id="add_dan_toc" placeholder="Nhập dân tộc">
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Công Việc</h3>
                        <div class="modal-field">
                            <label>Phòng Ban <span style="color: red;">*</span></label>
                            <select id="add_ten_phong_ban">
                                <option value="">Chọn phòng ban</option>
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Chức Vụ <span style="color: red;">*</span></label>
                            <select id="add_ten_chuc_vu">
                                <option value="">Chọn chức vụ</option>
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Loại Hợp Đồng <span style="color: red;">*</span></label>
                            <select id="add_loai_hop_dong">
                                <option value="">Chọn loại hợp đồng</option>
                                <option value="Thực tập">Thực tập</option>
                                <option value="Toàn thời gian">Toàn thời gian</option>
                            </select>
                        </div>
                        <div class="modal-field">
                            <label>Ngày Vào Làm <span style="color: red;">*</span></label>
                            <input type="date" id="add_ngay_vao_lam">
                        </div>
                        <div class="modal-field">
                            <label>Trạng Thái</label>
                            <select id="add_trang_thai" onchange="toggleMaternityFields()">
                                <option value="Đang làm việc">Đang làm việc</option>
                                <option value="Nghỉ phép">Nghỉ phép</option>
                                <option value="Đã nghỉ việc">Đã nghỉ việc</option>
                                <option value="Nghỉ thai sản">Nghỉ thai sản</option>
                            </select>
                        </div>
                        <div class="modal-field" id="maternity_fields" style="display: none;">
                            <label>Ngày Bắt Đầu Nghỉ Thai Sản</label>
                            <input type="date" id="add_ngay_bat_dau_thai_san" onchange="calculateMaternityEndDate()">
                        </div>
                        <div class="modal-field" id="maternity_end_field" style="display: none;">
                            <label>Ngày Kết Thúc Nghỉ Thai Sản (Tự động tính)</label>
                            <input type="date" id="add_ngay_ket_thuc_thai_san" readonly style="background-color: #f8f9fa; color: #6c757d;">
                            <small class="text-muted">Tự động tính = Ngày bắt đầu + 6 tháng</small>
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Tài Chính</h3>
                        <div class="modal-field">
                            <label>Lương Cơ Bản <span style="color: red;">*</span></label>
                            <input type="number" id="add_luong_co_ban">
                        </div>
                        <div class="modal-field">
                            <label>Phụ Cấp Chức Vụ</label>
                            <select id="add_phu_cap_chuc_vu" style="width: 200px;">
                                <option value="0">Chọn chức vụ để xem phụ cấp</option>
                            </select>
                            
                        </div>
                        <div class="modal-field">
                            <label>Phụ Cấp Bằng Cấp</label>
                            <input type="number" id="add_phu_cap_bang_cap" value="0">
                        </div>
                        <div class="modal-field">
                            <label>Phụ Cấp Khác</label>
                            <input type="number" id="add_phu_cap_khac" value="0">
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Thông Tin Phụ Thuộc</h3>
                        <div class="modal-field">
                            <label>Số Người Phụ Thuộc</label>
                            <input type="number" id="add_so_nguoi_phu_thuoc" value="0" min="0" max="10" readonly style="width: 100px; background-color: #f8f9fa; color: #6c757d;">
                            
                        </div>
                        <div class="modal-field">
                            <label>Tình Trạng Hôn Nhân</label>
                            <select id="add_tinh_trang_hon_nhan">
                                <option value="Độc thân">Độc thân</option>
                                <option value="Đã kết hôn">Đã kết hôn</option>
                                <option value="Ly hôn">Ly hôn</option>
                                <option value="Góa">Góa</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Thông Tin Bảo Hiểm</h3>
                        <div class="modal-field">
                            <label>Số BHXH</label>
                            <input type="text" id="add_so_bhxh">
                        </div>
                        <div class="modal-field">
                            <label>Số BHYT</label>
                            <input type="text" id="add_so_bhyt">
                        </div>
                        <div class="modal-field">
                            <label>Số BHTN</label>
                            <input type="text" id="add_so_bhtn">
                        </div>
                        <div class="modal-field">
                            <label>Ngày Tham Gia BHXH</label>
                            <input type="date" id="add_ngay_tham_gia_bhxh">
                        </div>
                    </div>
                    <div class="modal-section">
                        <h3>Thông Tin Ngân Hàng</h3>
                        <div class="modal-field">
                            <label>Số Tài Khoản</label>
                            <input type="text" id="add_so_tai_khoan">
                        </div>
                        <div class="modal-field">
                            <label>Tên Ngân Hàng</label>
                            <input type="text" id="add_ten_ngan_hang">
                        </div>
                        <div class="modal-field">
                            <label>Chi Nhánh Ngân Hàng</label>
                            <input type="text" id="add_chi_nhanh_ngan_hang">
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="modal-btn modal-btn-save" onclick="addUser()">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                        <button class="modal-btn modal-btn-cancel" onclick="closeAddUserModal()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div id="userDetailModalContainer"></div>
    </div>

    <script>
        let usersData = [];
        let phongBanList = [];
        let chucVuList = [];
        let currentPage = 1;
        const itemsPerPage = 15;
        let filteredData = [];
        let nguoiPhuThuocData = [];

        // Lấy danh sách phòng ban từ API
        fetch("http://localhost/doanqlns/index.php/api/phongban")
            .then(response => response.json())
            .then(data => {
                phongBanList = data;
                populatePhongBanSelect('add_ten_phong_ban');
                populatePhongBanSelect('edit_ten_phong_ban');
                populatePhongBanSelect('filterPhongBan');
            })
            .catch(error => {
                console.error("Lỗi khi tải danh sách phòng ban:", error);
            });

        // Lấy danh sách chức vụ từ API
        fetch("http://localhost/doanqlns/index.php/api/chucvu")
            .then(response => {
                console.log("Response status cho chức vụ:", response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Dữ liệu chức vụ:", data);
                chucVuList = data;
                populateChucVuSelect('add_ten_chuc_vu');
                populateChucVuSelect('edit_ten_chuc_vu');
                populatePhuCapChucVuSelect('add_phu_cap_chuc_vu');
                populatePhuCapChucVuSelect('edit_phu_cap_chuc_vu');
            })
            .catch(error => {
                console.error("Lỗi khi tải danh sách chức vụ:", error);
            });

        // Lấy danh sách người phụ thuộc từ API
        fetch("http://localhost/doanqlns/index.php/api/nguoiphuthuoc")
            .then(response => response.json())
            .then(data => {
                console.log("Dữ liệu người phụ thuộc:", data);
                nguoiPhuThuocData = data;
            })
            .catch(error => {
                console.error("Lỗi khi tải danh sách người phụ thuộc:", error);
            });

        // Hàm điền danh sách phòng ban vào select
        function populatePhongBanSelect(selectId) {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = selectId === 'filterPhongBan' ? '<option value="">Tất cả phòng ban</option>' : '<option value="">Chọn phòng ban</option>';
                phongBanList.forEach(pb => {
                    const option = document.createElement('option');
                    option.value = pb.ten_phong_ban;
                    option.textContent = pb.ten_phong_ban;
                    select.appendChild(option);
                });
            }
        }

        // Hàm điền danh sách chức vụ vào select
        function populateChucVuSelect(selectId) {
            const select = document.getElementById(selectId);
            if (select) {
                console.log('Populating chức vụ select:', selectId);
                select.innerHTML = '<option value="">Chọn chức vụ</option>';
                
                chucVuList.forEach(cv => {
                    const option = document.createElement('option');
                    option.value = cv.ten_chuc_vu;
                    option.textContent = cv.ten_chuc_vu;
                    option.dataset.phuCap = cv.phu_cap || 0;
                    option.dataset.phuCapKhac = cv.phu_cap_khac || 0;
                    select.appendChild(option);
                    console.log('Added option:', cv.ten_chuc_vu, 'Phụ cấp:', cv.phu_cap, 'Phụ cấp khác:', cv.phu_cap_khac);
                });
                
                // Xóa event listener cũ nếu có
                const oldHandler = select.getAttribute('data-change-handler');
                if (oldHandler) {
                    select.removeEventListener('change', select._changeHandler);
                }
                
                // Thêm event listener mới để cập nhật phụ cấp chức vụ và phụ cấp khác
                select._changeHandler = function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const phuCap = selectedOption.dataset.phuCap || 0;
                    const phuCapKhac = selectedOption.dataset.phuCapKhac || 0;
                    
                    console.log('Chọn chức vụ:', selectedOption.textContent, 'Phụ cấp:', phuCap, 'Phụ cấp khác:', phuCapKhac);
                    
                    // Cập nhật phụ cấp chức vụ tương ứng
                    const phuCapSelect = document.getElementById(selectId.replace('ten_chuc_vu', 'phu_cap_chuc_vu'));
                    if (phuCapSelect) {
                        phuCapSelect.value = phuCap;
                        console.log('Đã cập nhật phụ cấp chức vụ:', phuCap);
                    } else {
                        console.log('Không tìm thấy select phụ cấp chức vụ cho:', selectId);
                    }
                    
                    // Cập nhật phụ cấp khác tương ứng
                    const phuCapKhacInput = document.getElementById(selectId.replace('ten_chuc_vu', 'phu_cap_khac'));
                    if (phuCapKhacInput) {
                        phuCapKhacInput.value = phuCapKhac;
                        console.log('Đã cập nhật phụ cấp khác:', phuCapKhac);
                    } else {
                        console.log('Không tìm thấy input phụ cấp khác cho:', selectId);
                    }
                };
                
                select.addEventListener('change', select._changeHandler);
                select.setAttribute('data-change-handler', 'true');
                console.log('Event listener đã được thêm cho:', selectId);
            } else {
                console.log('Không tìm thấy select element:', selectId);
            }
        }

        // Hàm điền danh sách phụ cấp chức vụ vào select
        function populatePhuCapChucVuSelect(selectId) {
            const select = document.getElementById(selectId);
            if (select) {
                console.log('Populating phụ cấp chức vụ select:', selectId);
                select.innerHTML = '<option value="0">Chọn chức vụ để xem phụ cấp</option>';
                
                chucVuList.forEach(cv => {
                    const option = document.createElement('option');
                    option.value = cv.phu_cap || 0;
                    option.textContent = `${cv.ten_chuc_vu} - ${new Intl.NumberFormat('vi-VN').format(cv.phu_cap || 0)} VNĐ`;
                    select.appendChild(option);
                    console.log('Added phụ cấp option:', cv.ten_chuc_vu, 'Phụ cấp:', cv.phu_cap);
                });
            } else {
                console.log('Không tìm thấy select phụ cấp chức vụ element:', selectId);
            }
        }

        // Lấy danh sách nhân viên từ API
        fetch("http://localhost/doanqlns/index.php/api/users")
            .then(response => {
                console.log("Response status:", response.status);
                return response.json();
            })
            .then(data => {
                console.log("API data:", data);
                usersData = data;
                
                // Sắp xếp dữ liệu ban đầu theo ID nhân viên (tăng dần)
                usersData.sort((a, b) => {
                    return a.id_nhan_vien - b.id_nhan_vien;
                });
                
                filterAndRenderTable();
                <?php if ($editId): ?>
                    fetchUserAndShowEditModal(<?php echo $editId; ?>);
                <?php endif; ?>
                // Kiểm tra và mở modal thêm nếu có tham số action=add
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('action') === 'add' && <?php echo $_SESSION['quyen_them'] ? 'true' : 'false'; ?>) {
                    showAddUserModal();
                }
                
                // Kiểm tra filter từ URL parameter
                const filterParam = urlParams.get('filter');
                if (filterParam === 'maternity_ending_soon') {
                    document.getElementById('filterMaternity').value = 'maternity_ending_soon';
                    filterAndRenderTable();
                } else if (filterParam === 'maternity_overdue') {
                    document.getElementById('filterMaternity').value = 'maternity_overdue';
                    filterAndRenderTable();
                }
            })
            .catch(error => {
                console.error("Lỗi khi tải dữ liệu:", error);
                document.getElementById("userContainer").innerHTML = '<p>Không có dữ liệu</p>';
            });

        function filterAndRenderTable() {
            const keyword = document.getElementById("searchInput").value.toLowerCase();
            const selectedPhongBan = document.getElementById("filterPhongBan").value;
            const selectedMaternity = document.getElementById("filterMaternity").value;

            filteredData = usersData;
            if (selectedPhongBan) {
                filteredData = filteredData.filter(user => user.ten_phong_ban === selectedPhongBan);
            }
            if (keyword) {
                filteredData = filteredData.filter(user => user.ho_ten.toLowerCase().includes(keyword));
            }
            if (selectedMaternity) {
                filteredData = filteredData.filter(user => {
                    if (selectedMaternity === 'maternity') {
                        return user.trang_thai === 'Nghỉ thai sản';
                    } else if (selectedMaternity === 'maternity_ending_soon') {
                        return user.trang_thai === 'Nghỉ thai sản' && getMaternityDaysRemaining(user) <= 30 && getMaternityDaysRemaining(user) > 0;
                    } else if (selectedMaternity === 'maternity_overdue') {
                        return user.trang_thai === 'Nghỉ thai sản' && getMaternityDaysRemaining(user) < 0;
                    }
                    return false;
                });
            }

            // Sắp xếp theo ID nhân viên (tăng dần)
            filteredData.sort((a, b) => {
                return a.id_nhan_vien - b.id_nhan_vien;
            });

            // Reset về trang đầu khi filter
            currentPage = 1;
            renderPagination();
            renderCurrentPage();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const pagination = document.getElementById('pagination');
            
            let paginationHTML = `
                <button class="pagination-btn" onclick="changePage(1)" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <button class="pagination-btn" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-angle-left"></i>
                </button>
                <span class="pagination-info">
                    Trang ${currentPage} / ${totalPages} (${filteredData.length} nhân viên)
                </span>
                <button class="pagination-btn" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-right"></i>
                </button>
                <button class="pagination-btn" onclick="changePage(${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-angle-double-right"></i>
                </button>
            `;
            
            pagination.innerHTML = paginationHTML;
        }

        function changePage(newPage) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                renderPagination();
                renderCurrentPage();
            }
        }

        function renderCurrentPage() {
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            const currentItems = filteredData.slice(start, end);
            renderCards(currentItems);
        }
        
        // Hàm tính số ngày còn lại của thai sản
        function getMaternityDaysRemaining(user) {
            if (user.trang_thai !== 'Nghỉ thai sản' || !user.ngay_ket_thuc_thai_san) {
                return null;
            }
            
            const today = new Date();
            const endDate = new Date(user.ngay_ket_thuc_thai_san);
            const diffTime = endDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            return diffDays;
        }
        
        // Hàm tạo badge đếm ngược thai sản
        function createMaternityCountdownBadge(user) {
            const daysRemaining = getMaternityDaysRemaining(user);
            if (daysRemaining === null) return '';
            
            let badgeClass = '';
            let badgeText = '';
            
            if (daysRemaining > 60) {
                badgeClass = 'remaining-high';
                badgeText = `Còn ${daysRemaining} ngày`;
            } else if (daysRemaining > 30) {
                badgeClass = 'remaining-medium';
                badgeText = `Còn ${daysRemaining} ngày`;
            } else if (daysRemaining > 0) {
                badgeClass = 'remaining-low';
                badgeText = `Còn ${daysRemaining} ngày`;
            } else {
                badgeClass = 'overdue';
                badgeText = `Quá ${Math.abs(daysRemaining)} ngày`;
            }
            
            const tooltipText = `Bắt đầu: ${new Date(user.ngay_bat_dau_thai_san).toLocaleDateString('vi-VN')}\nKết thúc: ${new Date(user.ngay_ket_thuc_thai_san).toLocaleDateString('vi-VN')}`;
            
            return `<div class="maternity-countdown ${badgeClass} maternity-tooltip" data-tooltip="${tooltipText}">${badgeText}</div>`;
        }
        
        // Hàm lấy class cho đếm ngược thai sản (cho modal chi tiết)
        function getMaternityCountdownClass(user) {
            const daysRemaining = getMaternityDaysRemaining(user);
            if (daysRemaining === null) return '';
            
            if (daysRemaining > 60) return 'remaining-high';
            if (daysRemaining > 30) return 'remaining-medium';
            if (daysRemaining > 0) return 'remaining-low';
            return 'overdue';
        }
        
        // Hàm lấy text cho đếm ngược thai sản (cho modal chi tiết)
        function getMaternityCountdownText(user) {
            const daysRemaining = getMaternityDaysRemaining(user);
            if (daysRemaining === null) return '';
            
            if (daysRemaining > 0) return `Còn ${daysRemaining} ngày`;
            return `Quá ${Math.abs(daysRemaining)} ngày`;
        }
        
        // Hàm hiển thị/ẩn trường thai sản
        function toggleMaternityFields() {
            const statusSelect = document.getElementById('add_trang_thai');
            const maternityFields = document.getElementById('maternity_fields');
            const maternityEndField = document.getElementById('maternity_end_field');
            
            if (statusSelect.value === 'Nghỉ thai sản') {
                maternityFields.style.display = 'block';
                maternityEndField.style.display = 'block';
            } else {
                maternityFields.style.display = 'none';
                maternityEndField.style.display = 'none';
                // Reset values
                document.getElementById('add_ngay_bat_dau_thai_san').value = '';
                document.getElementById('add_ngay_ket_thuc_thai_san').value = '';
            }
        }
        
        // Hàm tính ngày kết thúc thai sản (6 tháng sau)
        function calculateMaternityEndDate() {
            const startDate = document.getElementById('add_ngay_bat_dau_thai_san').value;
            if (startDate) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setMonth(end.getMonth() + 6);
                
                const endDateString = end.toISOString().split('T')[0];
                document.getElementById('add_ngay_ket_thuc_thai_san').value = endDateString;
            }
        }
        
        // Hàm hiển thị/ẩn trường thai sản cho form chỉnh sửa
        function toggleEditMaternityFields() {
            const statusSelect = document.getElementById('edit_trang_thai');
            const maternityFields = document.getElementById('edit_maternity_fields');
            const maternityEndField = document.getElementById('edit_maternity_end_field');
            
            if (statusSelect.value === 'Nghỉ thai sản') {
                maternityFields.style.display = 'block';
                maternityEndField.style.display = 'block';
            } else {
                maternityFields.style.display = 'none';
                maternityEndField.style.display = 'none';
                // Reset values
                document.getElementById('edit_ngay_bat_dau_thai_san').value = '';
                document.getElementById('edit_ngay_ket_thuc_thai_san').value = '';
            }
        }
        
        // Hàm tính ngày kết thúc thai sản cho form chỉnh sửa
        function calculateEditMaternityEndDate() {
            const startDate = document.getElementById('edit_ngay_bat_dau_thai_san').value;
            if (startDate) {
                const start = new Date(startDate);
                const end = new Date(start);
                end.setMonth(end.getMonth() + 6);
                
                const endDateString = end.toISOString().split('T')[0];
                document.getElementById('edit_ngay_ket_thuc_thai_san').value = endDateString;
            }
        }

        function renderCards(data) {
            const container = document.getElementById("userContainer");
            container.innerHTML = "";
            if (data && data.length) {
                data.forEach(user => {
                    const card = document.createElement("div");
                    card.className = "employee-card";
                    let statusClass = "inactive";
                    let statusText = user.trang_thai || "Chưa xác định";
                    
                    if (user.trang_thai === "Đang làm việc") {
                        statusClass = "active";
                        statusText = "Đang làm việc";
                    } else if (user.trang_thai === "Nghỉ thai sản") {
                        statusClass = "maternity";
                        statusText = "Nghỉ thai sản";
                    } else if (user.trang_thai === "Nghỉ phép") {
                        statusClass = "maternity";
                        statusText = "Nghỉ phép";
                    } else if (user.trang_thai === "Đã nghỉ việc") {
                        statusClass = "inactive";
                        statusText = "Đã nghỉ việc";
                    }
                    // Build robust avatar URL with normalization and cache-busting
                    let avatarPath = user.hinh_anh || '';
                    let normalizedAvatar;
                    if (!avatarPath) {
                        normalizedAvatar = 'https://via.placeholder.com/100x100';
                    } else if (avatarPath.startsWith('http')) {
                        normalizedAvatar = avatarPath;
                    } else if (avatarPath.startsWith('/doanqlns')) {
                        normalizedAvatar = avatarPath;
                    } else if (avatarPath.startsWith('/')) {
                        normalizedAvatar = '/doanqlns' + avatarPath;
                    } else {
                        normalizedAvatar = '/doanqlns/' + avatarPath;
                    }
                    const cacheBuster = Date.now(); // Use current timestamp for better cache busting
                    const avatarSrc = normalizedAvatar + (normalizedAvatar.includes('?') ? '&' : '?') + 'v=' + cacheBuster;
                    
                    // Debug logging
                    console.log('Employee:', user.ho_ten, 'Avatar Path:', avatarPath, 'Normalized:', normalizedAvatar, 'Final URL:', avatarSrc);
                    
                    card.innerHTML = `
                        <div class="ellipsis-menu" onclick="toggleMenu(this)">
                            <i class="fas fa-ellipsis-v"></i>
                        </div>
                        <div class="action-buttons">
                            <?php if ($_SESSION['quyen_sua']): ?>
                                <button class="action-btn edit" onclick="fetchUserAndShowEditModal(${user.id_nhan_vien})">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                            <?php endif; ?>
                            <?php if ($_SESSION['quyen_xoa']): ?>
                                <button class="action-btn delete" onclick="confirmDelete(${user.id_nhan_vien})">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            <?php endif; ?>
                        </div>
                        ${createMaternityCountdownBadge(user)}
                        <img src="${avatarSrc}" alt="${user.ho_ten}" class="employee-avatar" 
                             onload="this.classList.add('loaded')" 
                             onerror="this.src='https://via.placeholder.com/100x100'; this.classList.add('error')">
                        <div class="employee-name">
                            <a href="#" class="name-link" data-id="${user.id_nhan_vien}">${user.ho_ten}</a>
                        </div>
                        <div class="employee-department">${user.ten_phong_ban || 'N/A'}</div>
                        <div class="employee-status ${statusClass}">${statusText}</div>
                        <div class="status-bar ${statusClass}"></div>
                    `;
                    container.appendChild(card);
                });

                // Thêm lightbox cho hình ảnh
                // Xử lý ảnh load mượt mà
                document.querySelectorAll('.employee-avatar').forEach(img => {
                    // Thêm loading state
                    img.addEventListener('loadstart', function() {
                        this.classList.add('loading');
                    });
                    
                    // Xử lý khi ảnh load thành công
                    img.addEventListener('load', function() {
                        this.classList.remove('loading');
                        this.classList.add('loaded');
                    });
                    
                    // Xử lý khi ảnh load lỗi
                    img.addEventListener('error', function() {
                        this.classList.remove('loading');
                        this.classList.add('error');
                    });
                    
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
                        enlargedImg.style.cssText = `
                            max-width: 90%;
                            max-height: 90vh;
                            object-fit: contain;
                            border-radius: 8px;
                            border: 3px solid white;
                        `;

                        lightbox.appendChild(enlargedImg);
                        document.body.appendChild(lightbox);

                        lightbox.addEventListener('click', () => {
                            lightbox.remove();
                        });
                    });
                });

                // Event listener sẽ được thêm ở cuối file để tránh duplicate
            } else {
                container.innerHTML = '<p>Không có dữ liệu</p>';
            }
        }

        function toggleMenu(element) {
            const menu = element.nextElementSibling;
            document.querySelectorAll('.action-buttons').forEach(btn => {
                if (btn !== menu) btn.classList.remove('show');
            });
            menu.classList.toggle('show');
        }

        // Đóng menu khi click ra ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.ellipsis-menu') && !e.target.closest('.action-buttons')) {
                document.querySelectorAll('.action-buttons').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });


        function fetchUserAndShowEditModal(userId) {
            fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`)
                .then(response => response.json())
                .then(user => {
                    if (user.message) {
                        alert(user.message);
                        return;
                    }
                    showEditUserModal(user);
                })
                .catch(error => {
                    console.error("Lỗi khi tải chi tiết nhân viên:", error);
                    alert("Lỗi khi tải thông tin nhân viên");
                });
        }

        function confirmDelete(userId) {
            if (confirm('Bạn có chắc chắn muốn xóa nhân viên này?')) {
                deleteUser(userId);
            }
        }
        
        function closeUserDetailModal() {
            const modal = document.getElementById('userDetailModal');
            if (modal) {
                modal.remove();
            }
        }

        function showEditUserModal(user) {
            const editModalBody = document.getElementById('editModalBody');
            editModalBody.innerHTML = `
                <div class="modal-section">
                    <h3>Thông Tin Cá Nhân</h3>
                    <div class="modal-field">
                        <label>Mã Nhân Viên</label>
                        <span>${user.id_nhan_vien}</span>
                    </div>
                    <div class="modal-field">
                        <label>Họ Tên <span style="color: red;">*</span></label>
                        <input type="text" id="edit_ho_ten" value="${user.ho_ten || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Giới Tính</label>
                        <select id="edit_gioi_tinh">
                            <option value="Nam" ${user.gioi_tinh === 'Nam' ? 'selected' : ''}>Nam</option>
                            <option value="Nữ" ${user.gioi_tinh === 'Nữ' ? 'selected' : ''}>Nữ</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Ngày Sinh <span style="color: red;">*</span></label>
                        <input type="date" id="edit_ngay_sinh" value="${user.ngay_sinh || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Căn Cước Công Dân</label>
                        <input type="text" id="edit_can_cuoc_cong_dan" value="${user.can_cuoc_cong_dan || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Ngày Cấp</label>
                        <input type="date" id="edit_ngay_cap" value="${user.ngay_cap || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Nơi Cấp</label>
                        <input type="text" id="edit_noi_cap" value="${user.noi_cap || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Quê Quán</label>
                        <input type="text" id="edit_que_quan" value="${user.que_quan || ''}">
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Liên Hệ</h3>
                    <div class="modal-field">
                        <label>Email <span style="color: red;">*</span></label>
                        <input type="email" id="edit_email" value="${user.email || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Số Điện Thoại</label>
                        <input type="text" id="edit_so_dien_thoai" value="${user.so_dien_thoai || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Địa Chỉ</label>
                        <input type="text" id="edit_dia_chi" value="${user.dia_chi || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Nơi Thường Trú</label>
                        <input type="text" id="edit_noi_thuong_tru" value="${user.noi_thuong_tru || ''}" placeholder="Nhập nơi thường trú">
                    </div>
                    <div class="modal-field">
                        <label>Chỗ Ở Hiện Tại</label>
                        <input type="text" id="edit_cho_o_hien_tai" value="${user.cho_o_hien_tai || ''}" placeholder="Nhập chỗ ở hiện tại">
                    </div>
                    <div class="modal-field">
                        <label>Dân Tộc</label>
                        <input type="text" id="edit_dan_toc" value="${user.dan_toc || ''}" placeholder="Nhập dân tộc">
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Công Việc</h3>
                    <div class="modal-field">
                        <label>Phòng Ban <span style="color: red;">*</span></label>
                        <select id="edit_ten_phong_ban">
                            <option value="">Chọn phòng ban</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Chức Vụ <span style="color: red;">*</span></label>
                        <select id="edit_ten_chuc_vu">
                            <option value="">Chọn chức vụ</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Loại Hợp Đồng <span style="color: red;">*</span></label>
                        <select id="edit_loai_hop_dong">
                            <option value="">Chọn loại hợp đồng</option>
                            <option value="Thực tập" ${user.loai_hop_dong === 'Thực tập' ? 'selected' : ''}>Thực tập</option>
                            <option value="Toàn thời gian" ${user.loai_hop_dong === 'Toàn thời gian' ? 'selected' : ''}>Toàn thời gian</option>
                            <option value="Bán thời gian" ${user.loai_hop_dong === 'Bán thời gian' ? 'selected' : ''}>Bán thời gian</option>
                            <option value="Hợp đồng không xác định" ${user.loai_hop_dong === 'Hợp đồng không xác định' ? 'selected' : ''}>Hợp đồng không xác định</option>
                        </select>
                    </div>
                    <div class="modal-field">
                        <label>Ngày Vào Làm <span style="color: red;">*</span></label>
                        <input type="date" id="edit_ngay_vao_lam" value="${user.ngay_vao_lam || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Ngày Nghỉ Việc</label>
                        <input type="date" id="edit_ngay_nghi_viec" value="${user.ngay_nghi_viec || ''}">
                    </div>
                    <div class="modal-field">
                        <label>Trạng Thái</label>
                        <select id="edit_trang_thai" onchange="toggleEditMaternityFields()">
                            <option value="Đang làm việc" ${user.trang_thai === 'Đang làm việc' ? 'selected' : ''}>Đang làm việc</option>
                            <option value="Nghỉ phép" ${user.trang_thai === 'Nghỉ phép' ? 'selected' : ''}>Nghỉ phép</option>
                            <option value="Đã nghỉ việc" ${user.trang_thai === 'Đã nghỉ việc' ? 'selected' : ''}>Đã nghỉ việc</option>
                            <option value="Nghỉ thai sản" ${user.trang_thai === 'Nghỉ thai sản' ? 'selected' : ''}>Nghỉ thai sản</option>
                        </select>
                    </div>
                    <div class="modal-field" id="edit_maternity_fields" style="display: ${user.trang_thai === 'Nghỉ thai sản' ? 'block' : 'none'};">
                        <label>Ngày Bắt Đầu Nghỉ Thai Sản</label>
                        <input type="date" id="edit_ngay_bat_dau_thai_san" value="${user.ngay_bat_dau_thai_san || ''}" onchange="calculateEditMaternityEndDate()">
                    </div>
                    <div class="modal-field" id="edit_maternity_end_field" style="display: ${user.trang_thai === 'Nghỉ thai sản' ? 'block' : 'none'};">
                        <label>Ngày Kết Thúc Nghỉ Thai Sản (Tự động tính)</label>
                        <input type="date" id="edit_ngay_ket_thuc_thai_san" value="${user.ngay_ket_thuc_thai_san || ''}" readonly style="background-color: #f8f9fa; color: #6c757d;">
                        <small class="text-muted">Tự động tính = Ngày bắt đầu + 6 tháng</small>
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Tài Chính</h3>
                    <div class="modal-field">
                        <label>Lương Cơ Bản <span style="color: red;">*</span></label>
                        <input type="number" id="edit_luong_co_ban" value="${user.luong_co_ban || ''}" step="1000">
                    </div>
                    <div class="modal-field">
                        <label>Phụ Cấp Chức Vụ</label>
                        <select id="edit_phu_cap_chuc_vu" style="width: 200px;">
                            <option value="0">Chọn chức vụ để xem phụ cấp</option>
                        </select>
                        
                    </div>
                    <div class="modal-field">
                        <label>Phụ Cấp Bằng Cấp</label>
                        <input type="number" id="edit_phu_cap_bang_cap" value="${user.phu_cap_bang_cap || 0}" step="1000">
                    </div>
                    <div class="modal-field">
                        <label>Phụ Cấp Khác</label>
                        <input type="number" id="edit_phu_cap_khac" value="${user.phu_cap_khac || 0}" step="1000">
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Thông Tin Phụ Thuộc</h3>
                    <div class="modal-field">
                        <label>Số Người Phụ Thuộc</label>
                        <input type="number" id="edit_so_nguoi_phu_thuoc" value="${countNguoiPhuThuoc(user.id_nhan_vien)}" min="0" max="10" readonly style="width: 100px; background-color: #f8f9fa; color: #6c757d;">
                        
                    </div>
                    <div class="modal-field">
                        <label>Tình Trạng Hôn Nhân</label>
                        <select id="edit_tinh_trang_hon_nhan">
                            <option value="Độc thân" ${user.tinh_trang_hon_nhan === 'Độc thân' ? 'selected' : ''}>Độc thân</option>
                            <option value="Đã kết hôn" ${user.tinh_trang_hon_nhan === 'Đã kết hôn' ? 'selected' : ''}>Đã kết hôn</option>
                            <option value="Ly hôn" ${user.tinh_trang_hon_nhan === 'Ly hôn' ? 'selected' : ''}>Ly hôn</option>
                            <option value="Góa" ${user.tinh_trang_hon_nhan === 'Góa' ? 'selected' : ''}>Góa</option>
                        </select>
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Thông Tin Bảo Hiểm</h3>
                    <div class="modal-field">
                        <label>Số BHXH</label>
                        <input type="text" id="edit_so_bhxh" value="${user.so_bhxh || ''}" placeholder="Ví dụ: 1234567890">
                    </div>
                    <div class="modal-field">
                        <label>Số BHYT</label>
                        <input type="text" id="edit_so_bhyt" value="${user.so_bhyt || ''}" placeholder="Ví dụ: 1234567890">
                    </div>
                    <div class="modal-field">
                        <label>Số BHTN</label>
                        <input type="text" id="edit_so_bhtn" value="${user.so_bhtn || ''}" placeholder="Ví dụ: 1234567890">
                    </div>
                    <div class="modal-field">
                        <label>Ngày Tham Gia BHXH</label>
                        <input type="date" id="edit_ngay_tham_gia_bhxh" value="${user.ngay_tham_gia_bhxh || ''}">
                    </div>
                </div>
                <div class="modal-section">
                    <h3>Thông Tin Ngân Hàng</h3>
                    <div class="modal-field">
                        <label>Số Tài Khoản</label>
                        <input type="text" id="edit_so_tai_khoan" value="${user.so_tai_khoan || ''}" placeholder="Ví dụ: 1234567890">
                    </div>
                    <div class="modal-field">
                        <label>Tên Ngân Hàng</label>
                        <input type="text" id="edit_ten_ngan_hang" value="${user.ten_ngan_hang || ''}" placeholder="Ví dụ: Vietcombank">
                    </div>
                    <div class="modal-field">
                        <label>Chi Nhánh Ngân Hàng</label>
                        <input type="text" id="edit_chi_nhanh_ngan_hang" value="${user.chi_nhanh_ngan_hang || ''}" placeholder="Ví dụ: Chi nhánh Hà Nội">
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="modal-btn modal-btn-save" onclick="updateUser(${user.id_nhan_vien})">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                    <button class="modal-btn modal-btn-cancel" onclick="closeEditUserModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                </div>
            `;

            populatePhongBanSelect('edit_ten_phong_ban');
            populateChucVuSelect('edit_ten_chuc_vu');
            populatePhuCapChucVuSelect('edit_phu_cap_chuc_vu');

            document.getElementById('edit_ten_phong_ban').value = user.ten_phong_ban || '';
            document.getElementById('edit_ten_chuc_vu').value = user.ten_chuc_vu || '';
            
            // Cập nhật phụ cấp chức vụ sau khi set giá trị chức vụ
            setTimeout(() => {
                const chucVuSelect = document.getElementById('edit_ten_chuc_vu');
                if (chucVuSelect) {
                    chucVuSelect.dispatchEvent(new Event('change'));
                }
            }, 100);

            document.getElementById('editUserModal').style.display = 'flex';
        }

        function addUser() {
            const formData = new FormData();
            formData.append('ho_ten', document.getElementById('add_ho_ten').value);
            formData.append('gioi_tinh', document.getElementById('add_gioi_tinh').value);
            formData.append('ngay_sinh', document.getElementById('add_ngay_sinh').value);
            formData.append('can_cuoc_cong_dan', document.getElementById('add_can_cuoc_cong_dan').value);
            formData.append('ngay_cap', document.getElementById('add_ngay_cap').value);
            formData.append('noi_cap', document.getElementById('add_noi_cap').value);
            formData.append('que_quan', document.getElementById('add_que_quan').value);
            formData.append('email', document.getElementById('add_email').value);
            formData.append('so_dien_thoai', document.getElementById('add_so_dien_thoai').value);
            formData.append('dia_chi', document.getElementById('add_dia_chi').value);
            formData.append('noi_thuong_tru', document.getElementById('add_noi_thuong_tru').value);
            formData.append('cho_o_hien_tai', document.getElementById('add_cho_o_hien_tai').value);
            formData.append('dan_toc', document.getElementById('add_dan_toc').value);
            formData.append('ten_phong_ban', document.getElementById('add_ten_phong_ban').value);
            formData.append('ten_chuc_vu', document.getElementById('add_ten_chuc_vu').value);
            formData.append('loai_hop_dong', document.getElementById('add_loai_hop_dong').value);
            formData.append('ngay_vao_lam', document.getElementById('add_ngay_vao_lam').value);
            formData.append('trang_thai', document.getElementById('add_trang_thai').value);
            formData.append('ngay_bat_dau_thai_san', document.getElementById('add_ngay_bat_dau_thai_san').value);
            formData.append('ngay_ket_thuc_thai_san', document.getElementById('add_ngay_ket_thuc_thai_san').value);
            formData.append('luong_co_ban', document.getElementById('add_luong_co_ban').value);
            formData.append('phu_cap_chuc_vu', document.getElementById('add_phu_cap_chuc_vu').value);
            formData.append('phu_cap_bang_cap', document.getElementById('add_phu_cap_bang_cap').value);
            formData.append('phu_cap_khac', document.getElementById('add_phu_cap_khac').value);
            // so_nguoi_phu_thuoc sẽ được tính tự động từ bảng nguoi_phu_thuoc
            formData.append('tinh_trang_hon_nhan', document.getElementById('add_tinh_trang_hon_nhan').value);
            formData.append('so_bhxh', document.getElementById('add_so_bhxh').value);
            formData.append('so_bhyt', document.getElementById('add_so_bhyt').value);
            formData.append('so_bhtn', document.getElementById('add_so_bhtn').value);
            formData.append('ngay_tham_gia_bhxh', document.getElementById('add_ngay_tham_gia_bhxh').value);
            formData.append('so_tai_khoan', document.getElementById('add_so_tai_khoan').value);
            formData.append('ten_ngan_hang', document.getElementById('add_ten_ngan_hang').value);
            formData.append('chi_nhanh_ngan_hang', document.getElementById('add_chi_nhanh_ngan_hang').value);
            const hinhAnhInput = document.getElementById('add_hinh_anh');
            if (hinhAnhInput.files.length > 0) {
                formData.append('hinh_anh', hinhAnhInput.files[0]);
            }

            const requiredFields = ['ho_ten', 'ngay_sinh', 'email', 'ten_phong_ban', 'ten_chuc_vu', 'loai_hop_dong', 'ngay_vao_lam', 'luong_co_ban'];
            for (let field of requiredFields) {
                if (!formData.get(field)) {
                    alert(`Vui lòng điền trường bắt buộc: ${field.replace('_', ' ')}`);
                    return;
                }
            }

            if (hinhAnhInput.files.length > 0) {
                const file = hinhAnhInput.files[0];
                const validTypes = ['image/jpeg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('Vui lòng chọn file ảnh định dạng JPG hoặc PNG.');
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    alert('Kích thước file không được vượt quá 2MB.');
                    return;
                }
            }

            fetch(`http://localhost/doanqlns/index.php/api/user`, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        document.getElementById('addUserModal').style.display = 'none';
                        fetch("http://localhost/doanqlns/index.php/api/users")
                            .then(response => response.json())
                            .then(data => {
                                usersData = data;
                                // Sắp xếp lại dữ liệu sau khi thêm
                                usersData.sort((a, b) => {
                                    return a.id_nhan_vien - b.id_nhan_vien;
                                });
                                filterAndRenderTable();
                                alert('Thêm nhân viên thành công!');
                            });
                    } else {
                        alert('Lỗi khi thêm nhân viên: ' + (result.message || 'Không rõ nguyên nhân'));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi thêm nhân viên:", error);
                    alert("Lỗi khi thêm nhân viên");
                });
        }

        function updateUser(userId) {
            const updatedUser = {
                ho_ten: document.getElementById('edit_ho_ten').value,
                gioi_tinh: document.getElementById('edit_gioi_tinh').value,
                ngay_sinh: document.getElementById('edit_ngay_sinh').value,
                can_cuoc_cong_dan: document.getElementById('edit_can_cuoc_cong_dan').value,
                ngay_cap: document.getElementById('edit_ngay_cap').value,
                noi_cap: document.getElementById('edit_noi_cap').value,
                que_quan: document.getElementById('edit_que_quan').value,
                email: document.getElementById('edit_email').value,
                so_dien_thoai: document.getElementById('edit_so_dien_thoai').value,
                dia_chi: document.getElementById('edit_dia_chi').value,
                noi_thuong_tru: document.getElementById('edit_noi_thuong_tru').value,
                cho_o_hien_tai: document.getElementById('edit_cho_o_hien_tai').value,
                dan_toc: document.getElementById('edit_dan_toc').value,
                ten_phong_ban: document.getElementById('edit_ten_phong_ban').value,
                ten_chuc_vu: document.getElementById('edit_ten_chuc_vu').value,
                loai_hop_dong: document.getElementById('edit_loai_hop_dong').value,
                ngay_vao_lam: document.getElementById('edit_ngay_vao_lam').value,
                ngay_nghi_viec: document.getElementById('edit_ngay_nghi_viec').value,
                trang_thai: document.getElementById('edit_trang_thai').value,
                ngay_bat_dau_thai_san: document.getElementById('edit_ngay_bat_dau_thai_san').value,
                ngay_ket_thuc_thai_san: document.getElementById('edit_ngay_ket_thuc_thai_san').value,
                luong_co_ban: document.getElementById('edit_luong_co_ban').value,
                phu_cap_chuc_vu: document.getElementById('edit_phu_cap_chuc_vu').value,
                phu_cap_bang_cap: document.getElementById('edit_phu_cap_bang_cap').value,
                phu_cap_khac: document.getElementById('edit_phu_cap_khac').value,
                // so_nguoi_phu_thuoc sẽ được tính tự động từ bảng nguoi_phu_thuoc
                tinh_trang_hon_nhan: document.getElementById('edit_tinh_trang_hon_nhan').value,
                so_bhxh: document.getElementById('edit_so_bhxh').value,
                so_bhyt: document.getElementById('edit_so_bhyt').value,
                so_bhtn: document.getElementById('edit_so_bhtn').value,
                ngay_tham_gia_bhxh: document.getElementById('edit_ngay_tham_gia_bhxh').value,
                so_tai_khoan: document.getElementById('edit_so_tai_khoan').value,
                ten_ngan_hang: document.getElementById('edit_ten_ngan_hang').value,
                chi_nhanh_ngan_hang: document.getElementById('edit_chi_nhanh_ngan_hang').value
            };

            const requiredFields = ['ho_ten', 'ngay_sinh', 'email', 'ten_phong_ban', 'ten_chuc_vu', 'loai_hop_dong', 'ngay_vao_lam', 'luong_co_ban'];
            for (let field of requiredFields) {
                if (!updatedUser[field]) {
                    const fieldNames = {
                        'ho_ten': 'Họ tên',
                        'ngay_sinh': 'Ngày sinh',
                        'email': 'Email',
                        'ten_phong_ban': 'Phòng ban',
                        'ten_chuc_vu': 'Chức vụ',
                        'loai_hop_dong': 'Loại hợp đồng',
                        'ngay_vao_lam': 'Ngày vào làm',
                        'luong_co_ban': 'Lương cơ bản'
                    };
                    alert(`Vui lòng điền trường bắt buộc: ${fieldNames[field] || field}`);
                    return;
                }
            }

            fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updatedUser)
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    
                    // Kiểm tra nếu response không OK
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    
                    // Kiểm tra content-type
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Response không phải JSON. Content-Type: ' + contentType);
                    }
                    
                    return response.text().then(text => {
                        console.log('Raw response length:', text.length);
                        console.log('Raw response:', text);
                        
                        // Kiểm tra nếu response rỗng
                        if (!text || text.trim() === '') {
                            throw new Error('Response rỗng');
                        }
                        
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            console.error('Response text:', text);
                            throw new Error('Không thể parse JSON: ' + e.message + '. Response: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(result => {
                    console.log('Response result:', result);
                    if (result.success) {
                        document.getElementById('editUserModal').style.display = 'none';
                        fetch("http://localhost/doanqlns/index.php/api/users")
                            .then(response => response.json())
                            .then(data => {
                                usersData = data;
                                // Sắp xếp lại dữ liệu sau khi cập nhật
                                usersData.sort((a, b) => {
                                    return a.id_nhan_vien - b.id_nhan_vien;
                                });
                                filterAndRenderTable();
                                alert('Cập nhật nhân viên thành công!');
                            });
                    } else {
                        alert('Lỗi khi cập nhật nhân viên: ' + (result.message || 'Không rõ nguyên nhân'));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi cập nhật nhân viên:", error);
                    alert("Lỗi khi cập nhật nhân viên: " + error.message);
                });
        }

        function deleteUser(userId) {
            fetch(`http://localhost/doanqlns/index.php/api/user?id=${userId}`, {
                method: 'DELETE'
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        usersData = usersData.filter(user => user.id_nhan_vien != userId);
                        // Sắp xếp lại dữ liệu sau khi xóa
                        usersData.sort((a, b) => {
                            return a.id_nhan_vien - b.id_nhan_vien;
                        });
                        filterAndRenderTable();
                        alert('Xóa nhân viên thành công!');
                    } else {
                        alert('Lỗi khi xóa nhân viên: ' + (result.message || 'Không rõ nguyên nhân'));
                    }
                })
                .catch(error => {
                    console.error("Lỗi khi xóa nhân viên:", error);
                    alert("Lỗi khi xóa nhân viên");
                });
        }

        function showAddUserModal() {
            const modal = document.getElementById('addUserModal');
            modal.style.display = 'flex';
            modal.classList.add('show');
            
            // Reset form
            document.getElementById('add_hinh_anh_preview').src = 'https://via.placeholder.com/150x200';
            document.getElementById('add_ho_ten').value = '';
            document.getElementById('add_gioi_tinh').value = 'Nam';
            document.getElementById('add_ngay_sinh').value = '';
            document.getElementById('add_can_cuoc_cong_dan').value = '';
            document.getElementById('add_ngay_cap').value = '';
            document.getElementById('add_noi_cap').value = '';
            document.getElementById('add_que_quan').value = '';
            document.getElementById('add_email').value = '';
            document.getElementById('add_so_dien_thoai').value = '';
            document.getElementById('add_dia_chi').value = '';
            document.getElementById('add_ten_phong_ban').value = '';
            document.getElementById('add_ten_chuc_vu').value = '';
            document.getElementById('add_loai_hop_dong').value = '';
            document.getElementById('add_ngay_vao_lam').value = '';
            document.getElementById('add_trang_thai').value = 'Đang làm việc';
            document.getElementById('add_luong_co_ban').value = '';
            document.getElementById('add_hinh_anh').value = '';
            
            // Scroll to top of modal body
            const modalBody = document.getElementById('addModalBody');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        }

        function closeAddUserModal() {
            const modal = document.getElementById('addUserModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        document.querySelector('#editUserModal .modal-close').addEventListener('click', () => {
            closeEditUserModal();
        });

        document.querySelector('#addUserModal .modal-close').addEventListener('click', () => {
            closeAddUserModal();
        });

        document.getElementById('editUserModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('editUserModal')) {
                closeEditUserModal();
            }
        });

        document.getElementById('addUserModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('addUserModal')) {
                closeAddUserModal();
            }
        });

        document.getElementById("searchInput").addEventListener("keyup", filterAndRenderTable);
        document.getElementById("filterPhongBan").addEventListener("change", filterAndRenderTable);
        document.getElementById("filterMaternity").addEventListener("change", filterAndRenderTable);

        document.getElementById('add_hinh_anh').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('add_hinh_anh_preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <script>
        // Hàm xử lý đường dẫn ảnh avatar
        function getAvatarUrl(avatarPath) {
            if (!avatarPath) {
                return 'https://via.placeholder.com/120x120';
            }
            
            let normalizedAvatar;
            if (avatarPath.startsWith('http')) {
                normalizedAvatar = avatarPath;
            } else if (avatarPath.startsWith('/doanqlns')) {
                normalizedAvatar = avatarPath;
            } else if (avatarPath.startsWith('/')) {
                normalizedAvatar = '/doanqlns' + avatarPath;
            } else {
                normalizedAvatar = '/doanqlns/' + avatarPath;
            }
            
            const cacheBuster = Date.now();
            return normalizedAvatar + (normalizedAvatar.includes('?') ? '&' : '?') + 'v=' + cacheBuster;
        }

        // Hàm đếm số người phụ thuộc
        function countNguoiPhuThuoc(userId) {
            if (!nguoiPhuThuocData || nguoiPhuThuocData.length === 0) {
                return 0;
            }
            
            const count = nguoiPhuThuocData.filter(nguoi => 
                nguoi.id_nhan_vien == userId && nguoi.trang_thai === 'Đang phụ thuộc'
            ).length;
            
            console.log('Đếm người phụ thuộc cho userId:', userId, 'Kết quả:', count);
            return count;
        }

        // Hàm hiển thị chi tiết nhân viên với thông tin đầy đủ
        function showUserDetails(userId) {
            console.log('showUserDetails được gọi với userId:', userId);
            
            // Kiểm tra nếu modal đã tồn tại và đang hiển thị
            const existingModal = document.getElementById('userDetailModal');
            if (existingModal) {
                console.log('Modal đã tồn tại, đóng modal cũ');
                existingModal.remove();
            }
            
            // Tìm nhân viên trong danh sách hiện tại
            const user = usersData.find(u => u.id_nhan_vien == userId || u.id_nhan_vien == parseInt(userId));
            if (!user) {
                console.log('Không tìm thấy nhân viên với ID:', userId);
                alert("Không tìm thấy thông tin nhân viên");
                return;
            }
            
            console.log('Tìm thấy nhân viên:', user);
            
            // Tạo modal chi tiết nhân viên
            const detailModal = document.createElement('div');
            detailModal.className = 'modal';
            detailModal.id = 'userDetailModal';
            detailModal.innerHTML = `
                <div class="modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
                    <div class="modal-header">
                        <h2>Chi Tiết Nhân Viên</h2>
                        <button class="modal-close" onclick="closeUserDetailModal()">&times;</button>
                    </div>
                    <div class="modal-body" style="padding: 20px;">
                        <div class="user-detail-container">
                            <div class="user-avatar-section">
                                <img src="${getAvatarUrl(user.hinh_anh)}" 
                                     alt="Ảnh đại diện" 
                                     class="user-detail-avatar"
                                     onerror="this.src='https://via.placeholder.com/120x120'">
                                <h3>${user.ho_ten}</h3>
                                <p class="user-id">Mã NV: ${user.id_nhan_vien}</p>
                            </div>
                            
                            <div class="user-info-grid">
                                <div class="info-section">
                                    <h4><i class="fas fa-user"></i> Thông Tin Cá Nhân</h4>
                                    <div class="info-item">
                                        <label>Giới tính:</label>
                                        <span>${user.gioi_tinh || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày sinh:</label>
                                        <span>${user.ngay_sinh ? new Date(user.ngay_sinh).toLocaleDateString('vi-VN') : 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>CCCD:</label>
                                        <span>${user.can_cuoc_cong_dan || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày cấp:</label>
                                        <span>${user.ngay_cap ? new Date(user.ngay_cap).toLocaleDateString('vi-VN') : 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Nơi cấp:</label>
                                        <span>${user.noi_cap || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Quê quán:</label>
                                        <span>${user.que_quan || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Nơi thường trú:</label>
                                        <span>${user.noi_thuong_tru || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Chỗ ở hiện tại:</label>
                                        <span>${user.cho_o_hien_tai || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Dân tộc:</label>
                                        <span>${user.dan_toc || 'Chưa cập nhật'}</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-phone"></i> Liên Hệ</h4>
                                    <div class="info-item">
                                        <label>Email:</label>
                                        <span>${user.email || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Số điện thoại:</label>
                                        <span>${user.so_dien_thoai || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Địa chỉ:</label>
                                        <span>${user.dia_chi || 'Chưa cập nhật'}</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-briefcase"></i> Công Việc</h4>
                                    <div class="info-item">
                                        <label>Phòng ban:</label>
                                        <span>${user.ten_phong_ban || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Chức vụ:</label>
                                        <span>${user.ten_chuc_vu || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Loại hợp đồng:</label>
                                        <span>${user.loai_hop_dong || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày vào làm:</label>
                                        <span>${user.ngay_vao_lam ? new Date(user.ngay_vao_lam).toLocaleDateString('vi-VN') : 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày nghỉ việc:</label>
                                        <span>${user.ngay_nghi_viec ? new Date(user.ngay_nghi_viec).toLocaleDateString('vi-VN') : 'Chưa làm việc'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Trạng thái:</label>
                                        <span class="status-badge ${user.trang_thai === 'Đang làm việc' ? 'active' : user.trang_thai === 'Nghỉ thai sản' ? 'maternity' : 'inactive'}">${user.trang_thai || 'Chưa cập nhật'}</span>
                                    </div>
                                    ${user.trang_thai === 'Nghỉ thai sản' && user.ngay_bat_dau_thai_san ? `
                                    <div class="info-item">
                                        <label>Nghỉ thai sản:</label>
                                        <span>${new Date(user.ngay_bat_dau_thai_san).toLocaleDateString('vi-VN')} → ${new Date(user.ngay_ket_thuc_thai_san).toLocaleDateString('vi-VN')}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Còn lại:</label>
                                        <span class="maternity-countdown ${getMaternityCountdownClass(user)}">${getMaternityCountdownText(user)}</span>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-money-bill-wave"></i> Tài Chính</h4>
                                    <div class="info-item">
                                        <label>Lương cơ bản:</label>
                                        <span>${user.luong_co_ban ? new Intl.NumberFormat('vi-VN').format(user.luong_co_ban) + ' VNĐ' : 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Phụ cấp chức vụ:</label>
                                        <span>${user.phu_cap_chuc_vu ? new Intl.NumberFormat('vi-VN').format(user.phu_cap_chuc_vu) + ' VNĐ' : '0 VNĐ'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Phụ cấp bằng cấp:</label>
                                        <span>${user.phu_cap_bang_cap ? new Intl.NumberFormat('vi-VN').format(user.phu_cap_bang_cap) + ' VNĐ' : '0 VNĐ'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Phụ cấp khác:</label>
                                        <span>${user.phu_cap_khac ? new Intl.NumberFormat('vi-VN').format(user.phu_cap_khac) + ' VNĐ' : '0 VNĐ'}</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-users"></i> Thông Tin Phụ Thuộc</h4>
                                    <div class="info-item">
                                        <label>Số người phụ thuộc:</label>
                                        <span>${countNguoiPhuThuoc(user.id_nhan_vien)} người</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Tình trạng hôn nhân:</label>
                                        <span>${user.tinh_trang_hon_nhan || 'Chưa cập nhật'}</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-shield-alt"></i> Bảo Hiểm</h4>
                                    <div class="info-item">
                                        <label>Số BHXH:</label>
                                        <span>${user.so_bhxh || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Số BHYT:</label>
                                        <span>${user.so_bhyt || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Số BHTN:</label>
                                        <span>${user.so_bhtn || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Ngày tham gia BHXH:</label>
                                        <span>${user.ngay_tham_gia_bhxh ? new Date(user.ngay_tham_gia_bhxh).toLocaleDateString('vi-VN') : 'Chưa cập nhật'}</span>
                                    </div>
                                </div>
                                
                                <div class="info-section">
                                    <h4><i class="fas fa-university"></i> Ngân Hàng</h4>
                                    <div class="info-item">
                                        <label>Số tài khoản:</label>
                                        <span>${user.so_tai_khoan || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Tên ngân hàng:</label>
                                        <span>${user.ten_ngan_hang || 'Chưa cập nhật'}</span>
                                    </div>
                                    <div class="info-item">
                                        <label>Chi nhánh:</label>
                                        <span>${user.chi_nhanh_ngan_hang || 'Chưa cập nhật'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Thêm modal vào body
            document.body.appendChild(detailModal);
            
            // Hiển thị modal ngay lập tức
            detailModal.style.display = 'flex';
            detailModal.classList.add('show');
            console.log('Modal đã được thêm vào DOM và hiển thị');
            
            // Thêm event listener để đóng modal khi click ra ngoài
            detailModal.addEventListener('click', function(e) {
                if (e.target === detailModal) {
                    closeUserDetailModal();
                }
            });
        }

        function closeUserDetailModal() {
            const modal = document.getElementById('userDetailModal');
            if (modal) {
                modal.remove();
            }
        }

        // Event delegation để tránh duplicate event listeners
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('name-link')) {
                e.preventDefault();
                e.stopPropagation();
                
                const userId = e.target.getAttribute('data-id');
                console.log('Click vào tên nhân viên ID:', userId);
                
                // Throttle để tránh click liên tục
                if (e.target.dataset.clicked === 'true') {
                    console.log('Đang xử lý click trước đó, bỏ qua');
                    return;
                }
                
                e.target.dataset.clicked = 'true';
                setTimeout(() => {
                    e.target.dataset.clicked = 'false';
                }, 500);
                
                showUserDetails(userId);
            }
        });
    </script>

    <!-- <?php include(__DIR__ . '/../includes/footer.php'); ?> -->
            </div>
        </div>
    </div>
</body>
</html>