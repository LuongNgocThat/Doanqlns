<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
?>
<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Bảng Lương</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS riêng -->
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
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .main-content {
            margin-left: 240px;
            padding: 20px;
        }
        h3 {
            font-size: 26px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        .filter-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
            align-items: center;
        }
        .filter-container select,
        .filter-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .filter-container select:focus,
        .filter-container input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.2);
        }
        .search-input {
            min-width: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .filter-select {
            min-width: 150px;
        }
        .btn-export {
            padding: 10px 20px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity 0.3s;
        }
        .btn-export:hover {
            opacity: 0.9;
        }
        .table-container {
            overflow-x: auto;
            margin: 0 auto 20px;
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 2000px;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            word-wrap: break-word;
        }
        th {
            background: #007bff;
            color: #fff;
            font-weight: 500;
            writing-mode: horizontal-tb;
            text-orientation: mixed;
            white-space: nowrap;
            height: 60px;
            vertical-align: middle;
            text-align: center;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #eef3f7;
        }
        .btn-edit, .btn-delete {
            border: none;
            background: none;
            cursor: pointer;
            margin: 0 5px;
            font-size: 1rem;
        }
        .btn-edit {
            color: #007bff;
        }
        .btn-delete {
            color: #dc3545;
        }
        .btn-edit:hover, .btn-delete:hover {
            opacity: 0.8;
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .loading::after {
            content: '';
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 15px;
            }
            .filter-container {
                flex-direction: column;
                align-items: center;
            }
            .table-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            table {
                min-width: 2000px;
            }
            th, td {
                padding: 8px;
            }
            th {
                height: 50px;
                font-size: 11px;
            }
            h3 {
                font-size: 22px;
            }
        }
        
        /* CSS cho tiêu đề cột ngang */
        .table-container th {
            position: relative;
        }
        
        /* Đảm bảo text không bị cắt */
        .table-container th span {
            display: inline-block;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* CSS cho header phòng ban */
        .department-header {
            background: linear-gradient(135deg, #fff8e1, #ffe082) !important;
            color: #000 !important;
            font-weight: bold !important;
            text-align: center !important;
            padding: 15px 12px !important;
            border: none !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .department-header td {
            border: none !important;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .department-header i {
            margin-right: 8px;
            font-size: 18px;
        }
        
        /* CSS cho dòng nhân viên trong phòng ban */
        tr:not(.department-header) {
            transition: background-color 0.2s ease;
        }
        
        tr:not(.department-header):hover {
            background-color: #f8f9fa !important;
        }
        
        /* CSS cho bảng có header phòng ban */
        .table-with-departments {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table-with-departments thead th {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #007bff;
            color: #fff;
        }
        
        /* CSS cho header nhóm */
        .header-group-row th {
            background: linear-gradient(135deg, #4a90e2, #357abd) !important;
            color: #fff !important;
            font-weight: bold !important;
            text-align: center !important;
            padding: 16px 18px !important;
            border: 1px solid #5a67d8 !important;
            border-right: 2px solid rgba(255, 255, 255, 0.3) !important;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
            position: relative;
            min-width: 100px;
        }
        
        .header-group-row th:last-child {
            border-right: 1px solid #5a67d8 !important;
        }
        
        /* CSS cho header chi tiết */
        .header-detail-row th {
            background: linear-gradient(135deg,rgb(188, 218, 238),rgb(184, 213, 243)) !important;
            color: #4a5568 !important;
            font-weight: 600 !important;
            text-align: center !important;
            padding: 14px 16px !important;
            border: 1px solid #e2e8f0 !important;
            border-right: 1px solid rgba(74, 85, 104, 0.2) !important;
            font-size: 13px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: relative;
            min-width: 100px;
        }
        
        .header-detail-row th:last-child {
            border-right: 1px solid #e2e8f0 !important;
        }
        
        /* CSS cho font header số */
        .header-detail-row th {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        
        .header-group-row th {
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-weight: 800;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        /* CSS cho sticky header */
        .header-group-row {
            position: sticky;
            top: 0;
            z-index: 30;
        }
        
        .header-detail-row {
            position: sticky;
            top: 45px; /* Chiều cao của header-group-row */
            z-index: 25;
        }
        
        /* Hiệu ứng hover cho header */
        .header-group-row th:hover {
            background: linear-gradient(135deg, #5a67d8, #6b46c1) !important;
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }
        
        .header-detail-row th:hover {
            background: linear-gradient(135deg, #edf2f7, #e2e8f0) !important;
            color: #2d3748 !important;
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }
        
        /* CSS cho đường kẻ phân nhóm */
        .header-group-row th[colspan="3"] {
            border-right: 3px solid rgba(255, 255, 255, 0.5) !important;
        }
        
        .header-group-row th[colspan="4"] {
            border-right: 3px solid rgba(255, 255, 255, 0.5) !important;
        }
        
        .header-group-row th[colspan="2"] {
            border-right: 3px solid rgba(255, 255, 255, 0.5) !important;
        }
        
        .header-detail-row th:nth-child(3) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(4) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(5) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(6) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(9) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(10) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(11) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(12) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(13) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(14) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(15) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(16) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(17) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(18) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(19) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(20) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(21) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(22) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(23) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        .header-detail-row th:nth-child(24) {
            border-right: 2px solid rgba(74, 85, 104, 0.4) !important;
        }
        
        /* CSS cho khoảng cách các ô trong bảng */
        .table-with-departments td {
            padding: 12px 16px !important;
            border-right: 1px solid rgba(0, 0, 0, 0.1) !important;
            min-width: 100px;
        }
        
        .table-with-departments td:last-child {
            border-right: none !important;
        }
        
        /* CSS tăng khoảng cách giữa các cột */
        .table-with-departments {
            border-spacing: 0;
            border-collapse: separate;
        }
        
        .table-with-departments th,
        .table-with-departments td {
            border-spacing: 2px;
        }
        
        /* CSS cho các cột số để dễ đọc hơn */
        .table-with-departments td:nth-child(4),
        .table-with-departments td:nth-child(5),
        .table-with-departments td:nth-child(6),
        .table-with-departments td:nth-child(7),
        .table-with-departments td:nth-child(8),
        .table-with-departments td:nth-child(9),
        .table-with-departments td:nth-child(10),
        .table-with-departments td:nth-child(11),
        .table-with-departments td:nth-child(12),
        .table-with-departments td:nth-child(13),
        .table-with-departments td:nth-child(14),
        .table-with-departments td:nth-child(15),
        .table-with-departments td:nth-child(16),
        .table-with-departments td:nth-child(17),
        .table-with-departments td:nth-child(18),
        .table-with-departments td:nth-child(19),
        .table-with-departments td:nth-child(20),
        .table-with-departments td:nth-child(21),
        .table-with-departments td:nth-child(22),
        .table-with-departments td:nth-child(23),
        .table-with-departments td:nth-child(24),
        .table-with-departments td:nth-child(25) {
            min-width: 120px;
            text-align: center;
            font-family: 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
            color: #2d3748;
            line-height: 1.4;
        }
        
        /* CSS cho cột tên nhân viên */
        .table-with-departments td:nth-child(2) {
            min-width: 150px;
            text-align: left;
            padding-left: 20px;
        }
        
        /* CSS cho cột chức vụ */
        .table-with-departments td:nth-child(3) {
            min-width: 120px;
            text-align: left;
            padding-left: 15px;
        }
        
        /* CSS đặc biệt cho các số tiền */
        .table-with-departments td {
            font-variant-numeric: tabular-nums;
        }
        
        /* CSS cho số 0 để nổi bật hơn */
        .table-with-departments td:contains("0 ₫") {
            color: #a0aec0;
            font-style: italic;
        }
        
        /* CSS cho số tiền lớn */
        .table-with-departments td:contains("000.000 ₫") {
            font-weight: 700;
            color: #1a202c;
        }
        
        /* CSS cho số tiền trung bình */
        .table-with-departments td:contains("000 ₫") {
            font-weight: 600;
            color: #2d3748;
        }
        
        /* CSS cho số tiền nhỏ */
        .table-with-departments td:contains("00 ₫") {
            font-weight: 500;
            color: #4a5568;
        }
        
        /* CSS cho đường kẻ phân nhóm trong dữ liệu */
        .table-with-departments td:nth-child(3) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(4) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(5) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(6) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(9) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(10) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(11) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(12) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(13) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(14) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(15) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(16) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(17) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(18) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(19) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(20) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(21) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(22) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(23) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        .table-with-departments td:nth-child(24) {
            border-right: 2px solid rgba(0, 0, 0, 0.2) !important;
        }
        
        /* CSS cho tên nhân viên - đảm bảo nằm ngang */
        .name-link {
            white-space: nowrap !important;
            display: inline-block !important;
            max-width: 100% !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            text-decoration: none !important;
            color: #007bff !important;
        }
        
        .name-link:hover {
            text-decoration: underline !important;
            color: #0056b3 !important;
        }
        
        /* CSS cho cột tên nhân viên */
        .table-with-departments td:nth-child(2) {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding: 8px 4px !important;
            max-width: 150px !important;
        }
        
        /* CSS cho tất cả các cột để đảm bảo không xuống dòng */
        .table-with-departments td {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding: 8px 4px !important;
            vertical-align: middle !important;
        }
        
        /* CSS cho header để đảm bảo không xuống dòng */
        .table-with-departments th {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            padding: 8px 4px !important;
            vertical-align: middle !important;
            text-align: center !important;
        }
        
        /* CSS căn giữa cho các cột số */
        .table-with-departments td:nth-child(1),
        .table-with-departments td:nth-child(4),
        .table-with-departments td:nth-child(5),
        .table-with-departments td:nth-child(6),
        .table-with-departments td:nth-child(7),
        .table-with-departments td:nth-child(8),
        .table-with-departments td:nth-child(9),
        .table-with-departments td:nth-child(10),
        .table-with-departments td:nth-child(11),
        .table-with-departments td:nth-child(12),
        .table-with-departments td:nth-child(13),
        .table-with-departments td:nth-child(14),
        .table-with-departments td:nth-child(15),
        .table-with-departments td:nth-child(16),
        .table-with-departments td:nth-child(17),
        .table-with-departments td:nth-child(18),
        .table-with-departments td:nth-child(19),
        .table-with-departments td:nth-child(20),
        .table-with-departments td:nth-child(21),
        .table-with-departments td:nth-child(22),
        .table-with-departments td:nth-child(23),
        .table-with-departments td:nth-child(24),
        .table-with-departments td:nth-child(25),
        .table-with-departments td:nth-child(26) {
            text-align: center !important;
        }
        
        /* CSS căn trái cho cột tên nhân viên */
        .table-with-departments td:nth-child(2) {
            text-align: left !important;
        }
        
        /* CSS căn trái cho cột chức vụ */
        .table-with-departments td:nth-child(3) {
            text-align: left !important;
        }
        
        /* CSS cho nút Công Thức */
        .formula-button-container {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .btn-formula {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
        }
        
        .btn-formula:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        }
        
        .btn-formula i {
            margin-right: 8px;
        }
        
        /* CSS cho modal công thức */
        .formula-modal-content {
            max-width: 1000px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .formula-modal-content::-webkit-scrollbar {
            width: 8px;
        }
        
        .formula-modal-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .formula-modal-content::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 4px;
        }
        
        .formula-modal-content::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }
        
        .formula-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
            padding-bottom: 20px;
        }
        
        .modal-body {
            max-height: calc(90vh - 120px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .modal-body::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 4px;
        }
        
        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }
        
        .formula-item {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .formula-item:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .formula-item h4 {
            color: #007bff;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }
        
        .formula-box {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 15px;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .formula-box strong {
            color: #28a745;
        }
        
        /* Responsive cho modal công thức */
        @media (max-width: 768px) {
            .formula-grid {
                grid-template-columns: 1fr;
            }
            
            .formula-modal-content {
                width: 95%;
                margin: 5% auto;
            }
        }
        
        .modal-section .avatar-container {
            text-align: center;
        }
        .modal-field input[type="file"] {
            padding: 5px;
        }
        .modal-field label {
            font-weight: 500;
            margin-bottom: 5px;
            display: block;
        }
        .modal-field select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            background: linear-gradient(90deg, #007bff, #0056b3);
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #fff;
            transition: transform 0.2s, color 0.2s;
        }
        .modal-close:hover {
            transform: scale(1.2);
            color: #e0e0e0;
        }
        .modal-body {
            padding: 25px;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .info-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-group label {
            font-weight: 500;
            color: #333;
            width: 150px;
            flex-shrink: 0;
            font-size: 14px;
        }
        .info-group .info-value {
            color: #555;
            flex-grow: 1;
            font-size: 14px;
            line-height: 1.5;
        }
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            background: #f8f9fa;
        }
        .btn-close {
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-close:hover {
            opacity: 0.85;
        }
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                max-width: 400px;
            }
            .info-group {
                flex-direction: column;
                align-items: flex-start;
            }
            .info-group label {
                width: auto;
                margin-bottom: 5px;
            }
            .info-group .info-value {
                width: 100%;
            }
            .modal-body {
                padding: 15px;
            }
            .modal-header h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
<div class="layout-wrapper">
    <?php include('../includes/sidebar.php'); ?>
    <div class="layout-page">
        <div class="content-wrapper">
    <h3>Bảng Lương</h3>
    
    <!-- Nút Công Thức -->
    <div class="formula-button-container">
        <button class="btn-formula" id="formulaBtn">
            <i class="fas fa-calculator"></i> Công Thức Tính Lương (Cập Nhật 2025)
        </button>
    </div>

    <!-- Bộ lọc tháng và năm -->
    <div class="filter-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Nhập tên nhân viên...">
        <select id="selectMonth" aria-label="Chọn tháng">
            <option value="1">Tháng 1</option>
            <option value="2">Tháng 2</option>
            <option value="3">Tháng 3</option>
            <option value="4" selected>Tháng 4</option>
            <option value="5">Tháng 5</option>
            <option value="6">Tháng 6</option>
            <option value="7">Tháng 7</option>
            <option value="8">Tháng 8</option>
            <option value="9">Tháng 9</option>
            <option value="10">Tháng 10</option>
            <option value="11">Tháng 11</option>
            <option value="12">Tháng 12</option>
        </select>
        <input type="number" id="selectYear" min="2000" max="2100" aria-label="Nhập năm" placeholder="Năm"/>
        <button class="btn-export" id="exportExcelBtn">
            <i class="fas fa-file-excel"></i> Xuất Lương
        </button>
    </div>

    <!-- Bảng lương -->
    <div class="table-container">
        <table class="table-with-departments">
        <thead>
            <!-- Hàng 1: Nhóm các cột -->
            <tr class="header-group-row">
                <th colspan="3" class="group-header">Thông tin nhân viên</th>
                <th colspan="4" class="group-header">Thu nhập cơ bản</th>
                <th colspan="3" class="group-header">Phụ cấp</th>
                <th colspan="2" class="group-header">Thưởng</th>
                <th colspan="2" class="group-header">Giảm Trừ Gia Cảnh</th>
                <th colspan="3" class="group-header">BH do Công ty đóng</th>
                <th colspan="3" class="group-header">BH do Nhân viên đóng</th>
                <th colspan="4" class="group-header">Thuế & khấu trừ</th>
                <th colspan="1" class="group-header">Lương Thực Nhận</th>
            </tr>
            <!-- Hàng 2: Tên cột chi tiết -->
            <tr class="header-detail-row">
                <th><span>STT</span></th>
                <th><span>Họ và Tên</span></th>
                <th><span>Chức Vụ</span></th>
                <th><span>Tháng</span></th>
                <th><span>Ngày Công</span></th>
                <th><span>Lương Cơ Bản</span></th>
                <th><span>Lương Theo Ngày</span></th>
                <th><span>Trách nhiệm</span></th>
                <th><span>Bằng Cấp</span></th>
                <th><span>Khác</span></th>
                <th><span>Thưởng</span></th>
                <th><span>Hoa Hồng</span></th>
                <th><span>Số Người Phụ Thuộc</span></th>
                <th><span>Tổng Giảm Trừ</span></th>
                <th><span>BHXH (17.5%)</span></th>
                <th><span>BHYT (3%)</span></th>
                <th><span>BHTN (1%)</span></th>
                <th><span>BHXH (8%)</span></th>
                <th><span>BHYT (1.5%)</span></th>
                <th><span>BHTN (1%)</span></th>
                <th><span>Thu Nhập Trước Thuế</span></th>
                <th><span>Thuế TNCN</span></th>
                <th><span>Các Khoản Trừ Khác</span></th>
                <th><span>Tổng</span></th>
                <th><span>Lương Thực Nhận</span></th>
            </tr>
        </thead>
        <tbody id="luongTableBody">
            <tr><td colspan="26">Đang tải dữ liệu...</td></tr>
        </tbody>
        </table>
    </div>

    <!-- Loading indicator -->
    <div class="loading" id="loadingIndicator"></div>

    <!-- Modal Công Thức Tính Lương -->
    <div id="formulaModal" class="modal">
        <div class="modal-content formula-modal-content">
            <div class="modal-header">
                <h2>Công Thức Tính Lương (Cập Nhật 2025)</h2>
                <span class="close" id="closeFormulaModal">&times;</span>
            </div>
            <div class="modal-body">
                <div class="formula-grid">
                    <div class="formula-item">
                        <h4>1. Lương Theo Ngày Công (2025)</h4>
                        <div class="formula-box">
                            <strong>Lương theo ngày = (Lương cơ bản / 26) × Số ngày công</strong><br><br>
                            <strong>Lưu ý:</strong> 26 ngày công chuẩn/tháng theo quy định<br><br>
                            <strong>Ví dụ:</strong><br>
                            • Lương cơ bản: 10,000,000 VNĐ<br>
                            • Số ngày công: 24 ngày<br>
                            • <strong>→ Lương theo ngày = (10,000,000 ÷ 26) × 24 = 9,230,769 VNĐ</strong>
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>2. Lương Gross (Tổng Thu Nhập)</h4>
                        <div class="formula-box">
                            <strong>Lương Gross = Lương theo ngày + Phụ cấp chức vụ + Phụ cấp bằng cấp + Phụ cấp khác + Thưởng + Hoa hồng</strong><br><br>
                            <strong>Phụ cấp prorate:</strong> (Phụ cấp × Số ngày công) ÷ 26<br><br>
                            <strong>Ví dụ:</strong><br>
                            • Lương theo ngày: 9,230,769 VNĐ<br>
                            • Phụ cấp chức vụ: 1,200,000 VNĐ<br>
                            • Phụ cấp bằng cấp: 500,000 VNĐ<br>
                            • Thưởng: 1,000,000 VNĐ<br>
                            • Hoa hồng: 500,000 VNĐ<br>
                            <strong>→ Gross = 9,230,769 + 1,200,000 + 500,000 + 1,000,000 + 500,000 = 12,430,769 VNĐ</strong>
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>3. Bảo Hiểm Xã Hội (2025)</h4>
                        <div class="formula-box">
                            <strong>Mức đóng BHXH tối đa:</strong> 36,000,000 VNĐ/tháng<br><br>
                            <strong>BHXH nhân viên:</strong> 8% × Lương đóng BH<br>
                            <strong>BHYT nhân viên:</strong> 1.5% × Lương đóng BH<br>
                            <strong>BHTN nhân viên:</strong> 1% × Lương đóng BH<br>
                            <strong>Tổng BH nhân viên:</strong> 10.5% × Lương đóng BH<br><br>
                            <strong>BHXH công ty:</strong> 17.5% × Lương đóng BH<br>
                            <strong>BHYT công ty:</strong> 3% × Lương đóng BH<br>
                            <strong>BHTN công ty:</strong> 1% × Lương đóng BH<br>
                            <strong>Tổng BH công ty:</strong> 21.5% × Lương đóng BH
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>4. Giảm Trừ Gia Cảnh (2025)</h4>
                        <div class="formula-box">
                            <strong>Giảm trừ bản thân:</strong> 11,000,000 VNĐ/tháng<br>
                            <strong>Giảm trừ người phụ thuộc:</strong> 4,400,000 VNĐ/người/tháng<br><br>
                            <strong>Công thức:</strong><br>
                            <strong>Giảm trừ = 11,000,000 + (Số người phụ thuộc × 4,400,000)</strong><br><br>
                            <strong>Ví dụ:</strong><br>
                            • Bản thân: 11,000,000 VNĐ<br>
                            • 2 người phụ thuộc: 2 × 4,400,000 = 8,800,000 VNĐ<br>
                            <strong>→ Tổng giảm trừ = 11,000,000 + 8,800,000 = 19,800,000 VNĐ</strong>
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>5. Thuế Thu Nhập Cá Nhân (2025)</h4>
                        <div class="formula-box">
                            <strong>Thu nhập chịu thuế = Lương Gross - Tổng BH nhân viên - Giảm trừ gia cảnh</strong><br><br>
                            <strong>Biểu thuế lũy tiến từng phần (2025):</strong><br>
                            • <strong>Bậc 1:</strong> 0 - 5,000,000 VNĐ: 5%<br>
                            • <strong>Bậc 2:</strong> 5,000,000 - 10,000,000 VNĐ: 10%<br>
                            • <strong>Bậc 3:</strong> 10,000,000 - 18,000,000 VNĐ: 15%<br>
                            • <strong>Bậc 4:</strong> 18,000,000 - 32,000,000 VNĐ: 20%<br>
                            • <strong>Bậc 5:</strong> 32,000,000 - 52,000,000 VNĐ: 25%<br>
                            • <strong>Bậc 6:</strong> 52,000,000 - 80,000,000 VNĐ: 30%<br>
                            • <strong>Bậc 7:</strong> Trên 80,000,000 VNĐ: 35%
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>6. Lương Thực Nhận (Net)</h4>
                        <div class="formula-box">
                            <strong>Lương Net = Lương Gross - Tổng BH nhân viên - Thuế TNCN - Các khoản trừ khác</strong><br><br>
                            <strong>Ví dụ tính toán:</strong><br>
                            • Lương Gross: 12,430,769 VNĐ<br>
                            • Tổng BH nhân viên: 1,305,231 VNĐ (10.5%)<br>
                            • Giảm trừ gia cảnh: 19,800,000 VNĐ<br>
                            • Thu nhập chịu thuế: 12,430,769 - 1,305,231 - 19,800,000 = -8,674,462 VNĐ<br>
                            • <strong>→ Thuế TNCN = 0 VNĐ (Thu nhập chịu thuế < 0)</strong><br>
                            • <strong>→ Lương Net = 12,430,769 - 1,305,231 - 0 = 11,125,538 VNĐ</strong>
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>7. Các Khoản Trừ Khác</h4>
                        <div class="formula-box">
                            <strong>Bao gồm:</strong><br>
                            • Ứng lương trước<br>
                            • Phạt kỷ luật<br>
                            • Phạt trách nhiệm công việc<br>
                            • Các khoản khấu trừ khác<br><br>
                            <strong>Lưu ý:</strong> Các khoản này được trừ trực tiếp vào lương thực nhận
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>8. Các Nghị Định Liên Quan</h4>
                        <div class="formula-box">
                            <strong>Nghị định 12/2022/NĐ-CP:</strong><br>
                            • Quy định chi tiết thi hành một số điều của Bộ luật Lao động<br>
                            • Về thời giờ làm việc, thời giờ nghỉ ngơi<br><br>
                            
                            <strong>Nghị định 15/2022/NĐ-CP:</strong><br>
                            • Quy định chi tiết thi hành Luật Bảo hiểm xã hội<br>
                            • Về mức đóng, phương thức đóng BHXH<br><br>
                            
                            <strong>Nghị định 143/2018/NĐ-CP:</strong><br>
                            • Quy định chi tiết thi hành Luật Thuế thu nhập cá nhân<br>
                            • Về giảm trừ gia cảnh, biểu thuế lũy tiến<br><br>
                            
                            <strong>Nghị định 38/2019/NĐ-CP:</strong><br>
                            • Quy định chi tiết thi hành Luật Bảo hiểm y tế<br>
                            • Về mức đóng BHYT, đối tượng tham gia
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>9. Mức Lương Tối Thiểu Vùng (2025)</h4>
                        <div class="formula-box">
                            <strong>Vùng I:</strong> 4,680,000 VNĐ/tháng<br>
                            • Hà Nội, TP.HCM, Đà Nẵng, Hải Phòng<br>
                            • Bình Dương, Đồng Nai, Cần Thơ<br><br>
                            
                            <strong>Vùng II:</strong> 4,160,000 VNĐ/tháng<br>
                            • Các tỉnh, thành phố trực thuộc TW khác<br><br>
                            
                            <strong>Vùng III:</strong> 3,640,000 VNĐ/tháng<br>
                            • Các huyện miền núi, vùng sâu, vùng xa<br><br>
                            
                            <strong>Vùng IV:</strong> 3,250,000 VNĐ/tháng<br>
                            • Các xã đặc biệt khó khăn
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>10. Quy Định Về Phụ Cấp</h4>
                        <div class="formula-box">
                            <strong>Phụ cấp cơm (Nghị định 12/2022):</strong><br>
                            • Tối đa 730,000 VNĐ/tháng được miễn thuế TNCN<br>
                            • Phải có hóa đơn, chứng từ hợp lệ<br><br>
                            
                            <strong>Phụ cấp chức vụ:</strong><br>
                            • Theo quy định của từng doanh nghiệp<br>
                            • Phải được ghi trong hợp đồng lao động<br><br>
                            
                            <strong>Phụ cấp bằng cấp:</strong><br>
                            • Khuyến khích nhân viên nâng cao trình độ<br>
                            • Mức phụ cấp do doanh nghiệp quy định
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>11. Thông Tư & Quyết Định</h4>
                        <div class="formula-box">
                            <strong>Thông tư 01/2023/TT-BLĐTBXH:</strong><br>
                            • Hướng dẫn thực hiện Nghị định 12/2022<br>
                            • Về thời giờ làm việc, nghỉ phép năm<br><br>
                            
                            <strong>Quyết định 15/2022/QĐ-TTg:</strong><br>
                            • Về mức lương tối thiểu vùng 2023-2025<br>
                            • Áp dụng từ 01/07/2023<br><br>
                            
                            <strong>Thông tư 59/2015/TT-BLĐTBXH:</strong><br>
                            • Hướng dẫn Luật Bảo hiểm xã hội<br>
                            • Về đối tượng, mức đóng BHXH<br><br>
                            
                            <strong>Thông tư 111/2013/TT-BTC:</strong><br>
                            • Hướng dẫn Luật Thuế thu nhập cá nhân<br>
                            • Về khấu trừ thuế tại nguồn
                        </div>
                    </div>
                    
                    <div class="formula-item">
                        <h4>12. Tổng Kết & Lưu Ý</h4>
                        <div class="formula-box">
                            <strong>Quy trình tính lương:</strong><br>
                            1. Tính lương theo ngày công<br>
                            2. Cộng các phụ cấp và thưởng<br>
                            3. Trừ bảo hiểm nhân viên<br>
                            4. Tính thuế TNCN (nếu có)<br>
                            5. Trừ các khoản khác<br>
                            6. <strong>= Lương thực nhận</strong><br><br>
                            
                            <strong>Lưu ý quan trọng:</strong><br>
                            • Tất cả tính toán dựa trên luật lao động 2025<br>
                            • Tuân thủ các nghị định của Chính phủ<br>
                            • Công ty chịu trách nhiệm đóng BH cho nhân viên<br>
                            • Lương không được thấp hơn mức tối thiểu vùng<br>
                            • Phải có bảng lương rõ ràng, minh bạch<br>
                            • Cập nhật thường xuyên theo quy định mới
                        </div>
                    </div>
                </div>
    </div>
    </div>
    </div>

    <!-- Modal chi tiết lương -->
    <div id="detailLuongModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Chi Tiết Bảng Lương</h2>
                <button class="modal-close" onclick="closeDetailModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="section-title">Thông Tin Nhân Viên</div>
                <div class="info-group">
                    <label>Họ và Tên:</label>
                    <span id="detailHoTen" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Giới Tính:</label>
                    <span id="detailGioiTinh" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Ngày Sinh:</label>
                    <span id="detailNgaySinh" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Email:</label>
                    <span id="detailEmail" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Số Điện Thoại:</label>
                    <span id="detailSoDienThoai" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Địa Chỉ:</label>
                    <span id="detailDiaChi" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Phòng Ban:</label>
                    <span id="detailPhongBan" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Chức Vụ:</label>
                    <span id="detailChucVu" class="info-value"></span>
                </div>
                <div class="info-group">
                        <label>Lương Cơ Bản:</label>
                        <span id="detailLuongCoBanNhanVien" class="info-value"></span>
                    </div>
                <div class="section-title">Thông Tin Lương</div>
                <div class="info-group">
                    <label>Mã Lương:</label>
                    <span id="detailIdLuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Tháng/Năm:</label>
                    <span id="detailThangNam" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Số Ngày Công:</label>
                    <span id="detailSoNgayCong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Lương Tháng:</label>
                    <span id="detailLuongCoBan" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Phụ Cấp:</label>
                    <span id="detailPhuCap" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Tiền Thưởng:</label>
                    <span id="detailTienThuong" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Các Khoản Trừ:</label>
                    <span id="detailCacKhoanTru" class="info-value"></span>
                </div>
                <div class="info-group">
                    <label>Lương Thực Nhận:</label>
                    <span id="detailLuongThucNhan" class="info-value"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-close" onclick="closeDetailModal()">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// Biến toàn cục để lưu dữ liệu
let luongData = [];
let usersData = [];
let bonusData = [];
let baoHiemThueTncnData = [];
let attendanceByEmployee = {};

// Hàm định dạng tiền tệ
function formatCurrency(value) {
    if (value == null || value == undefined) return '0 ₫';
    const numValue = Number(value);
    if (isNaN(numValue)) return '0 ₫';
    
    // Sử dụng toLocaleString để format số với dấu phẩy ngăn cách hàng nghìn
    const formattedNumber = numValue.toLocaleString('vi-VN');
    return `${formattedNumber} ₫`;
}

// Hàm định dạng số: luôn giữ số thập phân nếu có
function formatNumber(number) {
    if (typeof number !== 'number') {
        number = parseFloat(number) || 0;
    }
    return number.toString().includes('.') ? number.toString() : number.toFixed(0);
}

// Hàm hiển thị loading
function showLoading() {
    document.getElementById('loadingIndicator').style.display = 'block';
}

// Hàm ẩn loading
function hideLoading() {
    document.getElementById('loadingIndicator').style.display = 'none';
}

// Hàm tải dữ liệu chấm công và tính tổng công
async function loadAttendanceData(month, year) {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/chamcong');
        if (!response.ok) throw new Error(`Lỗi khi tải dữ liệu chấm công: ${response.status}`);
        const data = await response.json();
        if (!Array.isArray(data)) throw new Error('Dữ liệu chấm công không hợp lệ');

        // Lọc dữ liệu theo tháng/năm
        const filteredData = data.filter(record => {
            const recordDate = new Date(record.ngay_lam_viec);
            return recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year;
        });

        // Tính tổng công (totalWorkDays) cho từng nhân viên
        const localAttendanceByEmployee = {};
        const uniqueEmployeeIds = [...new Set(filteredData.map(record => record.id_nhan_vien))];
        
        uniqueEmployeeIds.forEach(userId => {
            const stats = calculateAttendanceStats(userId, month, year, filteredData);
            localAttendanceByEmployee[userId] = stats.totalWorkDays;
            console.log(`🔍 Nhân viên ${userId}: Số ngày công = ${stats.totalWorkDays}`);
        });

        return localAttendanceByEmployee; // { id_nhan_vien: totalWorkDays }
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu chấm công:', error);
        return {};
    }
}

// Hàm tính thống kê chấm công (từ chamcong.php)
function calculateAttendanceStats(userId, month, year, data) {
    const startDate = new Date(year, month - 1, 1);
    const endDate = new Date(year, month, 0);
    const records = data.filter(record => {
        const recordDate = new Date(record.ngay_lam_viec);
        return record.id_nhan_vien == userId && 
               recordDate >= startDate && 
               recordDate <= endDate;
    });

    let diemDanhDays = 0;
    let nghiDays = 0;
    let khongPhepCount = 0;

    for (let day = 1; day <= endDate.getDate(); day++) {
        const date = new Date(year, month - 1, day);
        const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const isSunday = date.getDay() === 0;
        const record = records.find(r => r.ngay_lam_viec === dateStr);

        if (isSunday) {
            // Không tính chủ nhật vào ngày công
            continue;
        } else if (record) {
            if (record.trang_thai === 'Đúng giờ' || record.trang_thai === 'Phép Năm' || record.trang_thai === 'Nghỉ Lễ') {
                diemDanhDays += 1; // Đúng giờ, Phép Năm, Nghỉ Lễ đều tính 1 ngày điểm danh
            } else if (record.trang_thai === 'Đi trễ') {
                diemDanhDays += 0.75; // Đi trễ tính 0.75 ngày
            } else if (record.trang_thai === 'Có phép') {
                diemDanhDays += 1; // Có phép tính 1 ngày điểm danh
                nghiDays -= 0.5; // Trừ 0.5 ngày nghỉ
                khongPhepCount += 0.5; // Tăng đếm không phép
            } else if (record.trang_thai === 'Không phép') {
                diemDanhDays += 1; // Không phép tính 1 ngày điểm danh
                nghiDays -= 1; // Trừ 1 ngày nghỉ
                khongPhepCount += 1; // Tăng đếm không phép
            }
        }
    }

    const totalWorkDays = diemDanhDays - khongPhepCount;
    console.log(`Nhân viên ${userId}, tháng ${month}/${year}: Điểm danh=${diemDanhDays}, Nghỉ=${nghiDays}, Không phép=${khongPhepCount}, Tổng công=${totalWorkDays}`);
    return { diemDanhDays, nghiDays, totalWorkDays };
}

// Hàm tải dữ liệu thưởng từ bảng thuong
async function loadBonusData(month, year) {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/thuong');
        if (!response.ok) throw new Error(`Lỗi khi tải dữ liệu thưởng: ${response.status}`);
        const data = await response.json();
        if (!Array.isArray(data)) throw new Error('Dữ liệu thưởng không hợp lệ');
        return data.filter(record => {
            const recordDate = new Date(record.ngay);
            return recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year;
        });
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu thưởng:', error);
        return [];
    }
}

// Hàm tính tổng tiền thưởng cho một nhân viên trong tháng/năm từ bảng thuong
function calculateTotalBonus(bonusData, userId, month, year) {
    // Suy luận loại khi cột loai rỗng để không bỏ sót thưởng
    const inferLoaiFromNoiDung = (noiDung) => {
        if (!noiDung) return '';
        const nd = (noiDung || '').toLowerCase();
        if (nd.includes('xuất sắc') || nd.includes('xuat sac') || nd.includes('xu?t s?c')) return 'thành tích cá nhân - xuất sắc';
        if (nd.includes('thưởng thành tích - tốt') || nd.includes('thuong thanh tich - tot') || nd.includes('t?t') || nd.includes('tốt')) return 'thành tích cá nhân - tốt';
        if (nd.includes('khá') || nd.includes('kha')) return 'thành tích cá nhân - khá';
        if (nd.includes('thưởng thành tích') || nd.includes('thuong thanh tich')) return 'thành tích cá nhân';
        return '';
    };

    const records = bonusData.filter(record => {
        const recordDate = new Date(record.ngay);
        if (!(recordDate.getMonth() + 1 === month && recordDate.getFullYear() === year)) return false;
        if (record.id_nhan_vien != userId) return false;

        const loai = (record.loai && record.loai.trim() !== '') ? record.loai : inferLoaiFromNoiDung(record.noi_dung_thuong);
        // Chấp nhận tất cả biến thể "thành tích cá nhân"
        const isThanhTich = loai === 'thành tích cá nhân' || loai === 'thành tích cá nhân - xuất sắc' || loai === 'thành tích cá nhân - tốt' || loai === 'thành tích cá nhân - khá';
        return isThanhTich || loai === 'thăng chức' || loai === 'nghỉ lễ';
    });

    const totalBonus = records.reduce((sum, record) => sum + (parseFloat(record.tien_thuong) || 0), 0);
    return totalBonus;
}

// Hàm này đã được xóa vì không cần thiết - sử dụng trực tiếp hoa_hong từ database

// Hàm tính lương theo ngày
function calculateSalaryByDay(luongCoBan, soNgayCong, ngayCongQuyDinh = 26) {
    // Lương theo ngày = (Lương cơ bản / Số ngày công quy định) × Số ngày công thực tế
    const luongTheoNgay = Math.round((luongCoBan / ngayCongQuyDinh) * soNgayCong);
    console.log(`🔍 calculateSalaryByDay: ${luongCoBan} / ${ngayCongQuyDinh} × ${soNgayCong} = ${luongTheoNgay}`);
    return luongTheoNgay;
}

// Hàm cập nhật dữ liệu lương khi thay đổi số ngày công
async function updateLuongNgayCong(idNhanVien, soNgayCong) {
    try {
        const response = await fetch('/doanqlns/api/update-luong-ngay-cong.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_nhan_vien: idNhanVien,
                so_ngay_cong: soNgayCong
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            console.log(`✅ Đã cập nhật dữ liệu lương cho nhân viên ${idNhanVien} với ${soNgayCong} ngày công`);
            return result.data;
        } else {
            console.error(`❌ Lỗi cập nhật dữ liệu lương: ${result.message}`);
            return null;
        }
    } catch (error) {
        console.error('❌ Lỗi khi gọi API cập nhật lương:', error);
        return null;
    }
}

// Hàm tính phụ cấp cơm theo ngày công
function calculatePhuCapCom(phuCapComTien, soNgayCong, ngayCongQuyDinh = 26) {
    // Phụ cấp cơm = (Phụ cấp cơm đầy đủ / Số ngày công quy định) × Số ngày công thực tế
    const phuCapComTheoNgay = Math.round((phuCapComTien / ngayCongQuyDinh) * soNgayCong);
    console.log(`🔍 calculatePhuCapCom: ${phuCapComTien} / ${ngayCongQuyDinh} × ${soNgayCong} = ${phuCapComTheoNgay}`);
    return phuCapComTheoNgay;
}

// Hàm tính phụ cấp chức vụ theo ngày công
function calculatePhuCapChucVu(phuCapChucVuTien, soNgayCong, ngayCongQuyDinh = 26) {
    // Phụ cấp chức vụ = (Phụ cấp chức vụ đầy đủ / Số ngày công quy định) × Số ngày công thực tế
    const phuCapChucVuTheoNgay = Math.round((phuCapChucVuTien / ngayCongQuyDinh) * soNgayCong);
    console.log(`🔍 calculatePhuCapChucVu: ${phuCapChucVuTien} / ${ngayCongQuyDinh} × ${soNgayCong} = ${phuCapChucVuTheoNgay}`);
    return phuCapChucVuTheoNgay;
}

// Hàm tính lương Gross (Lương tổng)
function calculateGrossSalary(luongTheoNgay, phuCapChucVu, phuCapBangCap, phuCapKhac, totalBonus, totalHoaHong = 0) {
    // Lương Gross = Lương theo ngày + Phụ cấp chức vụ + Phụ cấp bằng cấp + Phụ cấp khác + Thưởng + Hoa hồng
    return luongTheoNgay + phuCapChucVu + phuCapBangCap + phuCapKhac + totalBonus + totalHoaHong;
}

// Hàm tính bảo hiểm theo lương cơ bản (theo công thức mới)
function calculateBaoHiem(luongCoBan) {
    // BHXH NV = Lương cơ bản × 8%
    // BHYT NV = Lương cơ bản × 1.5%
    // BHTN NV = Lương cơ bản × 1%
    const bhxh = luongCoBan * 0.08;      // 8%
    const bhyt = luongCoBan * 0.015;     // 1.5%
    const bhtn = luongCoBan * 0.01;      // 1%
    const tongBaoHiem = bhxh + bhyt + bhtn;
    
    return {
        bhxh: Math.round(bhxh),
        bhyt: Math.round(bhyt),
        bhtn: Math.round(bhtn),
        tongBaoHiem: Math.round(tongBaoHiem)
    };
}

// Hàm lấy dữ liệu từ bảng bao_hiem_thue_tncn
async function getBaoHiemThueTncnData(month, year) {
    try {
        const response = await fetch(`http://localhost/doanqlns/index.php/api/baohiem`);
        if (!response.ok) {
            throw new Error(`Lỗi khi tải dữ liệu bảo hiểm: ${response.status}`);
        }
        const data = await response.json();
        
        // Lọc dữ liệu theo tháng/năm
        const filterDate = `${year}-${month.toString().padStart(2, '0')}`;
        return data.filter(record => record.thang === filterDate);
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu bảo hiểm:', error);
        return [];
    }
}

// Hàm tính thuế TNCN theo biểu thuế lũy tiến từng phần (công thức mới)
function calculateThueTNCN(tongThuNhap, tongKhauTruBH, soNguoiPhuThuoc = 0, phuCapKhac = 0) {
    // Giảm trừ gia cảnh = 11,000,000 (bản thân) + 4,400,000 × (số người phụ thuộc)
    const giamTruGiaCanh = 11000000 + (soNguoiPhuThuoc * 4400000);
    
    // Phụ cấp cơm được miễn thuế (tối đa 730,000 VNĐ/tháng theo quy định)
    const phuCapComMienThue = Math.min(phuCapKhac, 730000);
    const phuCapKhacChiuThue = Math.max(0, phuCapKhac - phuCapComMienThue);
    
    // Thu nhập chịu thuế = Tổng thu nhập – Tổng khấu trừ BH – Giảm trừ – Phụ cấp cơm miễn thuế
    // (Nếu < 0 thì = 0)
    const thuNhapChiuThue = Math.max(0, tongThuNhap - tongKhauTruBH - giamTruGiaCanh - phuCapComMienThue);
    
    if (thuNhapChiuThue <= 0) return 0;
    
    // Biểu thuế lũy tiến từng phần
    if (thuNhapChiuThue <= 5000000) {
        return Math.round(thuNhapChiuThue * 0.05); // 5%
    } else if (thuNhapChiuThue <= 10000000) {
        return Math.round(250000 + (thuNhapChiuThue - 5000000) * 0.1); // 10%
    } else if (thuNhapChiuThue <= 18000000) {
        return Math.round(750000 + (thuNhapChiuThue - 10000000) * 0.15); // 15%
    } else if (thuNhapChiuThue <= 32000000) {
        return Math.round(1950000 + (thuNhapChiuThue - 18000000) * 0.2); // 20%
    } else if (thuNhapChiuThue <= 52000000) {
        return Math.round(4750000 + (thuNhapChiuThue - 32000000) * 0.25); // 25%
    } else if (thuNhapChiuThue <= 80000000) {
        return Math.round(9750000 + (thuNhapChiuThue - 52000000) * 0.3); // 30%
    } else {
        return Math.round(18150000 + (thuNhapChiuThue - 80000000) * 0.35); // 35%
    }
}

// Hàm tính lương Net (Lương thực nhận) - công thức mới
function calculateNetSalary(tongThuNhap, tongKhauTruBH, thueTNCN, cacKhoanTruKhac = 0) {
    // NET = Tổng thu nhập – Tổng khấu trừ BH – Thuế TNCN – Các khoản trừ khác
    return tongThuNhap - tongKhauTruBH - thueTNCN - cacKhoanTruKhac;
}

// Hàm tải dữ liệu nhân viên
async function loadUsersData() {
    try {
        console.log('👥 Đang tải dữ liệu nhân viên...');
        const response = await fetch('http://localhost/doanqlns/index.php/api/users');
        console.log('👥 Response status:', response.status);
        
        if (!response.ok) throw new Error(`Lỗi khi tải dữ liệu nhân viên: ${response.status}`);
        
        const data = await response.json();
        console.log('👥 Dữ liệu nhân viên thô:', data);
        
        if (!Array.isArray(data)) {
            console.error('❌ Dữ liệu nhân viên không phải array:', typeof data);
            throw new Error('Dữ liệu nhân viên không hợp lệ');
        }
        
        usersData = data;
        console.log('👥 Số nhân viên đã tải:', usersData.length);
        console.log('👥 Dữ liệu nhân viên:', usersData);
        return usersData; // Return dữ liệu để có thể dùng .then()
    } catch (error) {
        console.error('❌ Lỗi khi tải dữ liệu nhân viên:', error);
        usersData = [];
        return usersData; // Return array rỗng
    }
}

// Hàm tải và hiển thị bảng lương
async function loadPayrollData() {
    const month = parseInt(document.getElementById('selectMonth').value);
    const yearInput = document.getElementById('selectYear');
    const year = parseInt(yearInput.value) || new Date().getFullYear();

    if (!yearInput.value) {
        yearInput.value = new Date().getFullYear();
    }

    console.log('🔍 Bắt đầu tải dữ liệu lương cho tháng:', month, 'năm:', year);

    showLoading();
    try {
        // Tải dữ liệu lương với tham số tháng/năm
        console.log('📊 Đang tải dữ liệu lương...');
        const luongResponse = await fetch(`http://localhost/doanqlns/index.php/api/luong?month=${month}&year=${year}`);
        console.log('📊 Response status:', luongResponse.status);
        
        if (!luongResponse.ok) throw new Error(`Lỗi khi tải dữ liệu lương: ${luongResponse.status}`);
        
        let luongDataTemp = await luongResponse.json();
        console.log('📊 Dữ liệu lương thô:', luongDataTemp);
        
        if (!Array.isArray(luongDataTemp)) {
            console.error('❌ Dữ liệu lương không phải array:', typeof luongDataTemp);
            throw new Error('Dữ liệu lương không hợp lệ');
        }

        console.log('📊 Tổng số bản ghi lương:', luongDataTemp.length);

        // Cập nhật biến toàn cục - Dữ liệu lương đã được lọc theo tháng/năm từ API
        console.log('📊 Dữ liệu lương đã được lọc theo tháng/năm từ API');
        luongData = luongDataTemp;
        
        // Nếu không có dữ liệu lương, tạo dữ liệu mẫu từ users
        if (luongData.length === 0) {
            console.log('📊 Không có dữ liệu lương, tạo dữ liệu mẫu từ danh sách nhân viên');
            console.log('📊 Users data:', usersData);
            
            if (usersData && usersData.length > 0) {
                luongData = usersData.map(user => {
                    const luongCoBan = user.luong_co_ban || 10000000; // Mặc định 10 triệu nếu không có
                    const sampleData = {
                        id_luong: 'L' + user.id_nhan_vien + '_' + month + '_' + year,
                        id_nhan_vien: user.id_nhan_vien,
                        ho_ten: user.ho_ten,
                        ten_chuc_vu: user.ten_chuc_vu || 'Chưa xác định',
                        ten_phong_ban: user.ten_phong_ban || 'Chưa phân loại',
                        thang: `${year}-${month.toString().padStart(2, '0')}`,
                        so_ngay_cong: 0,
                        luong_co_ban: luongCoBan,
                        phu_cap_chuc_vu: 0,
                        phu_cap_bang_cap: 0,
                        phu_cap_khac: 0,
                        hoa_hong: 0,
                        so_nguoi_phu_thuoc: 0,
                        cac_khoan_tru_khac: 0,
                        luong_thuc_nhan: luongCoBan
                    };
                    console.log('📊 Tạo dữ liệu mẫu cho nhân viên:', user.ho_ten, 'Lương cơ bản:', luongCoBan, sampleData);
                    return sampleData;
                });
                console.log('📊 Đã tạo dữ liệu mẫu cho', luongData.length, 'nhân viên');
                console.log('📊 Dữ liệu mẫu:', luongData);
            } else {
                console.log('❌ Không có dữ liệu users để tạo dữ liệu mẫu');
            }
        }
        
        // Sắp xếp dữ liệu theo ID nhân viên (tăng dần)
        console.log('📊 Sắp xếp dữ liệu theo ID nhân viên...');
        luongData.sort((a, b) => {
            const idA = parseInt(a.id_nhan_vien) || 0;
            const idB = parseInt(b.id_nhan_vien) || 0;
            console.log(`🔍 So sánh: ${idA} vs ${idB}`);
            return idA - idB; // Tăng dần: 1, 2, 3, ...
        });
        console.log('📊 Dữ liệu sau khi sắp xếp:', luongData);

        console.log('📊 Số bản ghi lương cho tháng', month, 'năm', year, ':', luongData.length);
        console.log('📊 Dữ liệu lương:', luongData);

        // Tải dữ liệu chấm công và thưởng
        console.log('📊 Đang tải dữ liệu chấm công...');
        attendanceByEmployee = await loadAttendanceData(month, year);
        console.log('📊 Dữ liệu chấm công:', attendanceByEmployee);
        console.log('📊 Kiểm tra từng nhân viên:');
        Object.keys(attendanceByEmployee).forEach(id => {
            console.log(`  - Nhân viên ${id}: ${attendanceByEmployee[id]} ngày công`);
        });

        console.log('📊 Đang tải dữ liệu thưởng...');
        bonusData = await loadBonusData(month, year);
        console.log('📊 Dữ liệu thưởng:', bonusData);

        // Tải dữ liệu hoa hồng KPI
        console.log('📊 Đang tải dữ liệu hoa hồng...');
        console.log('📊 Dữ liệu hoa hồng: Sử dụng trực tiếp từ database (hoa_hong)');

        // Tải dữ liệu bảo hiểm và thuế TNCN
        console.log('📊 Đang tải dữ liệu bảo hiểm...');
        baoHiemThueTncnData = await getBaoHiemThueTncnData(month, year);
        console.log('📊 Dữ liệu bảo hiểm:', baoHiemThueTncnData);

        // Gọi hàm filter để hiển thị dữ liệu
        console.log('📊 Gọi filterAndDisplayData...');
        await filterAndDisplayData();
    } catch (error) {
        console.error('❌ Lỗi khi tải dữ liệu:', error);
        document.getElementById('luongTableBody').innerHTML = '<tr><td colspan="26">Lỗi khi tải dữ liệu: ' + error.message + '</td></tr>';
    } finally {
        hideLoading();
    }
}

// Hàm hiển thị chi tiết bảng lương
async function showDetailLuong(userId, luongId) {
    showLoading();
    try {
        // Tìm bản ghi lương
        const luongRecord = luongData.find(record => record.id_luong == luongId);
        if (!luongRecord) throw new Error('Không tìm thấy bản ghi lương');

        // Tìm thông tin nhân viên
        let user = usersData.find(u => u.id_nhan_vien == userId);
        if (!user) {
            const response = await fetch(`http://localhost/doanqlns/index.php/api/users?id=${userId}`);
            if (!response.ok) throw new Error(`Lỗi khi tải thông tin nhân viên: ${response.status}`);
            const data = await response.json();
            user = Array.isArray(data) ? data[0] : data;
            if (!user) throw new Error('Không tìm thấy thông tin nhân viên');
        }

        // Tính toán thông tin lương
        const month = parseInt(document.getElementById('selectMonth').value);
        const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();
        const bonusData = await loadBonusData(month, year);
        const soNgayCong = attendanceByEmployee[luongRecord.id_nhan_vien] || 0;
        const luongCoBan = parseFloat(luongRecord.luong_co_ban) || 0; // Lấy trực tiếp từ DB
        const phuCapChucVu = parseFloat(luongRecord.phu_cap_chuc_vu) || 0;
        const tienThuong = calculateTotalBonus(bonusData, luongRecord.id_nhan_vien, month, year);
        const cacKhoanTru = parseFloat(luongRecord.cac_khoan_tru) || 0;
        const luongThucNhan = parseFloat(luongRecord.luong_thuc_nhan) || 0;

        // Điền thông tin nhân viên
        document.getElementById('detailHoTen').textContent = user.ho_ten || 'Không có dữ liệu';
        document.getElementById('detailGioiTinh').textContent = user.gioi_tinh || 'Không có dữ liệu';
        document.getElementById('detailNgaySinh').textContent = user.ngay_sinh || 'Không có dữ liệu';
        document.getElementById('detailEmail').textContent = user.email || 'Không có dữ liệu';
        document.getElementById('detailSoDienThoai').textContent = user.so_dien_thoai || 'Không có dữ liệu';
        document.getElementById('detailDiaChi').textContent = user.dia_chi || 'Không có dữ liệu';
        document.getElementById('detailPhongBan').textContent = user.ten_phong_ban || 'Không có dữ liệu';
        document.getElementById('detailChucVu').textContent = user.ten_chuc_vu || 'Không có dữ liệu';
        document.getElementById('detailLuongCoBanNhanVien').textContent = formatCurrency(user.luong_co_ban) || 'Không có dữ liệu';

        // Điền thông tin lương
        document.getElementById('detailIdLuong').textContent = luongRecord.id_luong || 'Không có dữ liệu';
        document.getElementById('detailThangNam').textContent = `${month}/${year}`;
        document.getElementById('detailSoNgayCong').textContent = formatNumber(soNgayCong);
        document.getElementById('detailLuongCoBan').textContent = formatCurrency(luongCoBan);
        document.getElementById('detailPhuCap').textContent = formatCurrency(phuCapChucVu);
        document.getElementById('detailTienThuong').textContent = formatCurrency(tienThuong);
        document.getElementById('detailCacKhoanTru').textContent = formatCurrency(cacKhoanTru);
        document.getElementById('detailLuongThucNhan').textContent = formatCurrency(luongThucNhan);

        // Hiển thị modal
        document.getElementById('detailLuongModal').style.display = 'flex';
    } catch (error) {
        console.error('Lỗi khi hiển thị chi tiết bảng lương:', error);
        alert('Lỗi khi hiển thị chi tiết bảng lương: ' + error.message);
    } finally {
        hideLoading();
    }
}

// Hàm đóng modal
function closeDetailModal() {
    document.getElementById('detailLuongModal').style.display = 'none';
}

// Hàm xuất Excel (CSV)
async function exportToExcel() {
    const month = parseInt(document.getElementById('selectMonth').value);
    const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();

    showLoading();
    try {
        const attendanceByEmployee = await loadAttendanceData(month, year);
        const bonusData = await loadBonusData(month, year);
        console.log('💰 Export - Dữ liệu thưởng:', bonusData);

        if (luongData.length === 0) {
            throw new Error(`Không có dữ liệu lương cho tháng ${month}/${year}`);
        }

        // Chuẩn bị dữ liệu CSV
        const headers = [
            'STT',
            'Họ và Tên',
            'Chức Vụ',
            'Tháng',
            'Ngày Công',
            'Lương Cơ Bản',
            'Lương Theo Ngày',
            'Trách nhiệm',
            'Bằng Cấp',
            'Khác',
            'Thưởng',
            'Hoa Hồng',
            'Số Người Phụ Thuộc',
            'Tổng Giảm Trừ',
            'BHXH (17.5%)',
            'BHYT (3%)',
            'BHTN (1%)',
            'BHXH (8%)',
            'BHYT (1.5%)',
            'BHTN (1%)',
            'Thu Nhập Trước Thuế',
            'Thuế TNCN',
            'Các Khoản Trừ Khác',
            'Tổng',
            'Lương Thực Nhận'
        ];

        const csvRows = [headers.map(header => `"${header}"`).join(',')];

        let sttCounter = 1;
        luongData.forEach(record => {
            const adjustedBasicSalary = parseFloat(record.luong_co_ban) || 0;
            const totalBonus = calculateTotalBonus(bonusData, record.id_nhan_vien, month, year);
            const phuCapChucVu = parseFloat(record.phu_cap_chuc_vu) || 0;
            const phuCapBangCap = parseFloat(record.phu_cap_bang_cap) || 0;
            const phuCapKhac = parseFloat(record.phu_cap_khac) || 0;
            const hoaHong = parseFloat(record.hoa_hong) || 0;
            console.log(`💰 Export - Nhân viên ${record.id_nhan_vien}: Hoa hồng = ${hoaHong}`);
            const soNgayCong = attendanceByEmployee[record.id_nhan_vien] || 0;
            const soNguoiPhuThuoc = parseInt(record.so_nguoi_phu_thuoc) || 0;
            
            // Tính lương theo ngày
            const luongTheoNgay = calculateSalaryByDay(adjustedBasicSalary, soNgayCong, 26);
            
            // Sử dụng trực tiếp dữ liệu đã prorate từ database
            const phuCapChucVuTheoNgay = phuCapChucVu;
            const phuCapComTheoNgay = phuCapKhac;
            
            // Tính tổng thu nhập theo công thức mới (dựa trên lương theo ngày)
            const tongThuNhap = luongTheoNgay + phuCapChucVuTheoNgay + phuCapBangCap + phuCapComTheoNgay + totalBonus + hoaHong;
            
            // Tính bảo hiểm nhân viên theo lương theo ngày (đã prorate)
            const baoHiemData = calculateBaoHiem(luongTheoNgay);
            
            // Tính thuế TNCN theo công thức mới (bao gồm xử lý phụ cấp cơm)
            const thueTNCN = calculateThueTNCN(tongThuNhap, baoHiemData.tongBaoHiem, soNguoiPhuThuoc, phuCapComTheoNgay);
            
            // Lấy các khoản trừ khác từ database
            const cacKhoanTruKhac = parseFloat(record.cac_khoan_tru_khac) || 0;
            
            // Sử dụng lương thực nhận từ database thay vì tính toán lại
            const luongNet = parseFloat(record.luong_thuc_nhan) || 0;
            
            // Tổng các khoản trừ = BH + Thuế TNCN + Các khoản trừ khác
            const tongCacKhoanTru = baoHiemData.tongBaoHiem + thueTNCN + cacKhoanTruKhac;

            const row = [
                sttCounter,
                record.ho_ten || '',
                record.ten_chuc_vu || 'Chưa xác định',
                `${month}/${year}`,
                formatNumber(soNgayCong),
                adjustedBasicSalary.toLocaleString('vi-VN'),
                luongTheoNgay.toLocaleString('vi-VN'),
                phuCapChucVuTheoNgay.toLocaleString('vi-VN'),
                phuCapBangCap.toLocaleString('vi-VN'),
                phuCapComTheoNgay.toLocaleString('vi-VN'),
                totalBonus.toLocaleString('vi-VN'),
                hoaHong.toLocaleString('vi-VN'),
                record.so_nguoi_phu_thuoc || 0,
                (11000000 + (soNguoiPhuThuoc * 4400000)).toLocaleString('vi-VN'),
                (record.bhxh_cty || 0).toLocaleString('vi-VN'),
                (record.bhyt_cty || 0).toLocaleString('vi-VN'),
                (record.bhtn_cty || 0).toLocaleString('vi-VN'),
                baoHiemData.bhxh.toLocaleString('vi-VN'),
                baoHiemData.bhyt.toLocaleString('vi-VN'),
                baoHiemData.bhtn.toLocaleString('vi-VN'),
                tongThuNhap.toLocaleString('vi-VN'),
                thueTNCN.toLocaleString('vi-VN'),
                cacKhoanTruKhac.toLocaleString('vi-VN'),
                tongCacKhoanTru.toLocaleString('vi-VN'),
                luongNet.toLocaleString('vi-VN')
            ].map(value => `"${value.toString().replace(/"/g, '""')}"`);

            csvRows.push(row.join(','));
            sttCounter++;
        });

        // Tạo nội dung CSV với BOM để hỗ trợ tiếng Việt
        const csvContent = '\uFEFF' + csvRows.join('\n');

        // Tạo Blob và tải xuống
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', `BangLuong_Thang${month}_${year}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Lỗi khi xuất CSV:', error);
        alert('Lỗi khi xuất file CSV: ' + error.message);
    } finally {
        hideLoading();
    }
}

// Hàm lọc và hiển thị dữ liệu theo phòng ban
async function filterAndDisplayData() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const month = parseInt(document.getElementById('selectMonth').value);
    const year = parseInt(document.getElementById('selectYear').value) || new Date().getFullYear();
    const tableBody = document.getElementById('luongTableBody');

    try {
        // Đảm bảo có dữ liệu chấm công và thưởng
        attendanceByEmployee = await loadAttendanceData(month, year);
        bonusData = await loadBonusData(month, year);
        console.log('💰 Filter - Dữ liệu thưởng:', bonusData);

        // Lọc dữ liệu - Hiển thị tất cả nhân viên nếu không có search term
        console.log('🔍 Bắt đầu lọc dữ liệu...');
        let filteredData;
        
        if (searchTerm.trim() === '') {
            // Không có search term, hiển thị tất cả (đã sắp xếp theo ID)
            console.log('🔍 Không có search term, hiển thị tất cả nhân viên theo thứ tự ID');
            filteredData = luongData;
        } else {
            // Có search term, lọc theo tên nhưng vẫn giữ thứ tự ID
            console.log('🔍 Có search term, lọc theo tên:', searchTerm);
            filteredData = luongData.filter(record => {
                console.log('🔍 Kiểm tra record:', record);
                if (!record.ho_ten) {
                    console.log('❌ Record không có ho_ten:', record);
                    return false;
                }
                
                const matchesSearch = record.ho_ten.toLowerCase().includes(searchTerm);
                console.log('🔍 Tên:', record.ho_ten, 'Search term:', searchTerm, 'Matches:', matchesSearch);
                return matchesSearch;
            });
            
            // Sắp xếp lại kết quả tìm kiếm theo ID nhân viên
            console.log('🔍 Sắp xếp lại kết quả tìm kiếm theo ID nhân viên...');
            filteredData.sort((a, b) => {
                const idA = parseInt(a.id_nhan_vien) || 0;
                const idB = parseInt(b.id_nhan_vien) || 0;
                return idA - idB; // Tăng dần: 1, 2, 3, ...
            });
        }

        // Cập nhật bảng
        if (filteredData.length > 0) {
            tableBody.innerHTML = '';
            
            // Nhóm dữ liệu theo phòng ban
            const groupedData = groupDataByDepartment(filteredData);
            
            // Hiển thị dữ liệu theo phòng ban
            let globalStt = 1;
            Object.keys(groupedData).forEach(departmentName => {
                const departmentData = groupedData[departmentName];
                
                // Thêm header phòng ban
                const departmentHeader = document.createElement('tr');
                departmentHeader.className = 'department-header';
                departmentHeader.innerHTML = `
                    <td colspan="26">
                        <i class="fas fa-building"></i> Phòng ban: ${departmentName} (${departmentData.length} nhân viên)
                    </td>
                `;
                tableBody.appendChild(departmentHeader);
                
                // Hiển thị dữ liệu nhân viên trong phòng ban
                departmentData.forEach(async (record, index) => {
                // Lấy số ngày công trực tiếp từ bảng luong database
                const soNgayCong = parseFloat(record.so_ngay_cong) || 0;
                console.log(`🔍 Nhân viên ${record.id_nhan_vien} (${record.ho_ten}): Số ngày công từ DB = ${soNgayCong}`);
                const luongCoBanRaw = record.luong_co_ban;
                const luongCoBan = parseFloat(luongCoBanRaw) || 0;
                const phuCapChucVu = parseFloat(record.phu_cap_chuc_vu) || 0;
                const phuCapBangCap = parseFloat(record.phu_cap_bang_cap) || 0;
                const phuCapKhac = parseFloat(record.phu_cap_khac) || 0;
                const tienThuong = calculateTotalBonus(bonusData, record.id_nhan_vien, month, year);
                    const hoaHong = parseFloat(record.hoa_hong) || 0;
                console.log(`💰 Display - Nhân viên ${record.id_nhan_vien}: Hoa hồng = ${hoaHong}`);
                const soNguoiPhuThuoc = parseInt(record.so_nguoi_phu_thuoc) || 0;
                
                console.log(`🔍 Record ${record.id_nhan_vien}: Lương cơ bản raw="${luongCoBanRaw}", parsed=${luongCoBan}, Số ngày công từ DB=${soNgayCong}, Phụ cấp chức vụ=${phuCapChucVu}, Phụ cấp bằng cấp=${phuCapBangCap}, Phụ cấp khác=${phuCapKhac}, Thưởng=${tienThuong}, Hoa hồng=${hoaHong}, Số người phụ thuộc=${soNguoiPhuThuoc}`);
                
                // Sử dụng trực tiếp dữ liệu từ database, không cần cập nhật
                
                // Tính lương theo ngày
                const luongTheoNgay = calculateSalaryByDay(luongCoBan, soNgayCong, 26);
                
                // Sử dụng trực tiếp dữ liệu đã prorate từ database
                const phuCapChucVuTheoNgay = phuCapChucVu;
                const phuCapComTheoNgay = phuCapKhac;
                
                // Tính tổng thu nhập theo công thức mới (dựa trên lương theo ngày)
                    // Tổng thu nhập = Lương theo ngày + Các phụ cấp + Tiền thưởng + Hoa hồng
                    const tongThuNhap = luongTheoNgay + phuCapChucVuTheoNgay + phuCapBangCap + phuCapComTheoNgay + tienThuong + hoaHong;
                
                // Tính bảo hiểm nhân viên theo lương theo ngày (đã prorate)
                const baoHiemData = calculateBaoHiem(luongTheoNgay);
                
                // Tính thuế TNCN theo công thức mới (bao gồm xử lý phụ cấp cơm)
                const thueTNCN = calculateThueTNCN(tongThuNhap, baoHiemData.tongBaoHiem, soNguoiPhuThuoc, phuCapComTheoNgay);
                
                // Lấy các khoản trừ khác từ database
                const cacKhoanTruKhac = parseFloat(record.cac_khoan_tru_khac) || 0;
                
                // Sử dụng lương thực nhận từ database thay vì tính toán lại
                const luongNet = parseFloat(record.luong_thuc_nhan) || 0;
                
                // Tổng các khoản trừ = BH + Thuế TNCN + Các khoản trừ khác
                const tongCacKhoanTru = baoHiemData.tongBaoHiem + thueTNCN + cacKhoanTruKhac;

                const row = document.createElement('tr');
                row.innerHTML = `
                        <td><strong>${globalStt}</strong></td>
                    <td><a href="#" class="name-link" data-id="${record.id_nhan_vien}" data-luong-id="${record.id_luong}">${record.ho_ten}</a></td>
                        <td>${record.ten_chuc_vu || 'Chưa xác định'}</td>
                    <td>${month}/${year}</td>
                    <td>${formatNumber(soNgayCong)}</td>
                    <td>${formatCurrency(luongCoBan)}</td>
                    <td>${formatCurrency(luongTheoNgay)}</td>
                    <td>${formatCurrency(phuCapChucVuTheoNgay)}</td>
                    <td>${formatCurrency(phuCapBangCap)}</td>
                    <td>${formatCurrency(phuCapComTheoNgay)}</td>
                    <td>${formatCurrency(tienThuong)}</td>
                        <td style="color: #28a745; font-weight: bold;">${formatCurrency(hoaHong)}</td>
                    <td>${record.so_nguoi_phu_thuoc || 0}</td>
                        <td>${formatCurrency(11000000 + (soNguoiPhuThuoc * 4400000))}</td>
                    <td>${formatCurrency(record.bhxh_cty || 0)}</td>
                    <td>${formatCurrency(record.bhyt_cty || 0)}</td>
                    <td>${formatCurrency(record.bhtn_cty || 0)}</td>
                        <td>${formatCurrency(baoHiemData.bhxh)}</td>
                        <td>${formatCurrency(baoHiemData.bhyt)}</td>
                        <td>${formatCurrency(baoHiemData.bhtn)}</td>
                        <td>${formatCurrency(tongThuNhap)}</td>
                    <td>${formatCurrency(thueTNCN)}</td>
                    <td>${formatCurrency(cacKhoanTruKhac)}</td>
                    <td>${formatCurrency(tongCacKhoanTru)}</td>
                    <td><strong style="color: #007bff;">${formatCurrency(luongNet)}</strong></td>
                `;
                tableBody.appendChild(row);
                    globalStt++; // Tăng STT cho nhân viên tiếp theo
                });
            });

            // Thêm lại event listeners cho các liên kết tên
            document.querySelectorAll('.name-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const luongId = this.getAttribute('data-luong-id');
                    showDetailLuong(userId, luongId);
                });
            });
        } else {
            console.log('❌ Không có dữ liệu để hiển thị');
            console.log('🔍 Nguyên nhân có thể:');
            console.log('  - Không có dữ liệu lương cho tháng/năm này');
            console.log('  - Dữ liệu lương không có trường ho_ten');
            console.log('  - Dữ liệu lương không có trường thang đúng format');
            console.log('  - Search term quá hạn chế');
            
            tableBody.innerHTML = '<tr><td colspan="26">Không tìm thấy dữ liệu phù hợp</td></tr>';
        }
    } catch (error) {
        console.error('❌ Lỗi khi lọc và hiển thị dữ liệu:', error);
        tableBody.innerHTML = '<tr><td colspan="26">Lỗi khi tải dữ liệu: ' + error.message + '</td></tr>';
    }
}

// Hàm nhóm dữ liệu theo phòng ban
function groupDataByDepartment(data) {
    const grouped = {};
    
    data.forEach(record => {
        const departmentName = record.ten_phong_ban || 'Chưa phân loại';
        
        if (!grouped[departmentName]) {
            grouped[departmentName] = [];
        }
        
        grouped[departmentName].push(record);
    });
    
    // Sắp xếp các phòng ban theo tên
    const sortedGroups = {};
    Object.keys(grouped).sort().forEach(key => {
        sortedGroups[key] = grouped[key];
    });
    
    return sortedGroups;
}

// Khởi tạo khi trang tải
document.addEventListener('DOMContentLoaded', () => {
    const currentDate = new Date();
    document.getElementById('selectMonth').value = currentDate.getMonth() + 1;
    document.getElementById('selectYear').value = currentDate.getFullYear();
    
    // Tải dữ liệu ban đầu - Users trước, sau đó mới tải Payroll
    console.log('🚀 Khởi tạo trang - Tải dữ liệu ban đầu');
    loadUsersData().then(() => {
        console.log('✅ Đã tải xong dữ liệu users, bắt đầu tải payroll');
        loadPayrollData();
    }).catch(error => {
        console.error('❌ Lỗi khi tải dữ liệu users:', error);
        // Vẫn tải payroll ngay cả khi users lỗi
        loadPayrollData();
    });

    // Thêm sự kiện cho tìm kiếm
    document.getElementById('searchInput').addEventListener('input', filterAndDisplayData);

    // Các sự kiện khác
    document.getElementById('selectMonth').addEventListener('change', loadPayrollData);
    document.getElementById('selectYear').addEventListener('change', loadPayrollData);
    document.getElementById('exportExcelBtn').addEventListener('click', exportToExcel);

    // Sự kiện đóng modal
    document.getElementById('detailLuongModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('detailLuongModal')) {
            closeDetailModal();
        }
    });

    // Sự kiện mở modal công thức
    document.getElementById('formulaBtn').addEventListener('click', () => {
        document.getElementById('formulaModal').style.display = 'flex';
    });

    // Sự kiện đóng modal công thức
    document.getElementById('closeFormulaModal').addEventListener('click', () => {
        document.getElementById('formulaModal').style.display = 'none';
    });

    // Đóng modal khi click bên ngoài
    document.getElementById('formulaModal').addEventListener('click', (e) => {
        if (e.target === document.getElementById('formulaModal')) {
            document.getElementById('formulaModal').style.display = 'none';
        }
    });
});
</script>
<?php include(__DIR__ . '/../includes/footer.php'); ?>
        </div>
    </div>
</div>
</body>
</html>