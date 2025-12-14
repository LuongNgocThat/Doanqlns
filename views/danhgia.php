<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
?>

<!DOCTYPE html>
<html lang="vi" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Đánh giá nhân viên</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: var(--bs-body-bg);
            font-family: 'Roboto', sans-serif;
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

        .evaluation-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin-bottom: 24px;
        }

        .evaluation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #e9ecef;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn-import {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-import:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        /* CSS cho modal import */
        .import-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .import-tabs .tab {
            padding: 12px 24px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .import-tabs .tab.active {
            border-bottom-color: #667eea;
            background: #f8f9ff;
            color: #667eea;
        }

        .import-tabs .tab:hover {
            background: #f8f9ff;
        }

        .import-content {
            display: none;
        }

        .import-content.active {
            display: block;
        }

        .file-preview {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .file-preview table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .file-preview th,
        .file-preview td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        .file-preview th {
            background: #e9ecef;
            font-weight: 600;
        }

        .form-text {
            color: #6c757d;
            font-size: 12px;
            margin-top: 5px;
        }

        .evaluation-title {
            font-size: 28px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .btn-add-evaluation {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-add-evaluation:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .filter-container {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            align-items: center;
        }

        .filter-container select,
        .filter-container input {
            padding: 10px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .filter-container select:focus,
        .filter-container input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .evaluation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .evaluation-table th,
        .evaluation-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }

        .evaluation-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .evaluation-table tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            min-width: 80px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .status-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .status-badge:hover::before {
            left: 100%;
        }

        .status-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .status-nhap { 
            background: linear-gradient(135deg, #d1d5db, #9ca3af);
            color: #374151;
            border: 2px solid #a1a1aa;
        }
        .status-da-duyet { 
            background: linear-gradient(135deg, #a8e6a3, #7dd3fc);
            color: #166534;
            border: 2px solid #86efac;
        }

        .rating-badge {
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            text-align: center;
            min-width: 80px;
            display: inline-block;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .rating-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .rating-badge:hover::before {
            left: 100%;
        }

        .rating-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .rating-excellent { 
            background: linear-gradient(135deg, #a8e6a3, #7dd3fc);
            border: 2px solid #86efac;
            color: #166534;
        }
        .rating-good { 
            background: linear-gradient(135deg, #93c5fd, #c4b5fd);
            border: 2px solid #a5b4fc;
            color: #1e40af;
        }
        .rating-fair { 
            background: linear-gradient(135deg, #fde68a, #fed7aa);
            color: #92400e;
            border: 2px solid #fbbf24;
        }
        .rating-average { 
            background: linear-gradient(135deg, #fed7aa, #fecaca);
            border: 2px solid #fb923c;
            color: #c2410c;
        }
        .rating-poor { 
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            border: 2px solid #f87171;
            color: #dc2626;
        }
        .rating-chưa-xếp-loại { 
            background: linear-gradient(135deg, #d1d5db, #9ca3af);
            border: 2px solid #a1a1aa;
            color: #374151;
        }

        .btn-action {
            padding: 8px 12px;
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 4px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-view { 
            background: linear-gradient(135deg, #93c5fd, #c4b5fd);
            color: #1e40af;
            border: 2px solid #a5b4fc;
        }
        .btn-edit { 
            background: linear-gradient(135deg, #fde68a, #fed7aa);
            color: #92400e;
            border: 2px solid #fbbf24;
        }
        .btn-delete { 
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            color: #dc2626;
            border: 2px solid #f87171;
        }
        .btn-approve { 
            background: linear-gradient(135deg, #a8e6a3, #7dd3fc);
            color: #166534;
            border: 2px solid #86efac;
        }

        .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            opacity: 0.9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 24px;
            max-height: 70vh;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .criteria-group {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        
        .attendance-info {
            margin-bottom: 24px;
        }
        
        .attendance-info h4 {
            color: #495057;
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 600;
        }
        
        .attendance-table {
            margin-top: 20px;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-align: center;
        }
        
        .attendance-table td {
            text-align: center;
            vertical-align: middle;
        }
        
        .attendance-table .rating-badge {
            margin: 0 auto;
        }
        
        /* CSS cho bảng chuyên cần mới */
        .attendance-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .attendance-summary h4 {
            margin: 0 0 10px 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        .attendance-summary p {
            margin: 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .table-responsive {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .attendance-table th {
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            padding: 16px 12px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }
        
        .attendance-table th i {
            margin-right: 8px;
            font-size: 16px;
        }
        
        .attendance-table th small {
            display: block;
            font-size: 11px;
            opacity: 0.8;
            margin-top: 4px;
        }
        
        .attendance-row {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .attendance-row:hover {
            background: linear-gradient(135deg, #f8f9ff, #f0f2ff);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .attendance-row td {
            padding: 16px 12px;
            vertical-align: middle;
            border: none;
        }
        
        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .employee-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }
        
        .employee-details strong {
            display: block;
            color: #2c3e50;
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .employee-details small {
            color: #6c757d;
            font-size: 11px;
        }
        
        .department-info {
            text-align: center;
        }
        
        .department-badge {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1976d2;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid #90caf9;
        }
        
        .score-cell {
            text-align: center;
            min-width: 120px;
        }
        
        .score-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-bottom: 8px;
        }
        
        .score-number {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .score-max {
            font-size: 12px;
            color: #6c757d;
        }
        
        .score-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .score-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        
        .chuyen-can .score-fill {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .hieu-qua .score-fill {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }
        
        .thai-do .score-fill {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .total-score {
            text-align: center;
            min-width: 100px;
        }
        
        .total-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            margin-bottom: 8px;
        }
        
        .total-number {
            font-size: 20px;
            font-weight: 800;
            color: #2c3e50;
        }
        
        .total-max {
            font-size: 14px;
            color: #6c757d;
        }
        
        .total-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .total-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .rating-cell {
            text-align: center;
        }

        .status-cell {
            text-align: center;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-badge.clickable-status {
            cursor: pointer;
            user-select: none;
            transition: all 0.3s ease;
        }

        .status-badge.clickable-status:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .status-badge.draft {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-badge.approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .rating-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .rating-badge i {
            font-size: 14px;
        }
        
        .attendance-legend {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid #e9ecef;
        }
        
        .attendance-legend h5 {
            margin: 0 0 15px 0;
            color: #495057;
            font-size: 16px;
            font-weight: 600;
        }
        
        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .legend-color.chuyen-can {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .legend-color.hieu-qua {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }
        
        .legend-color.thai-do {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        @media (max-width: 768px) {
            .attendance-table th,
            .attendance-table td {
                padding: 12px 8px;
                font-size: 12px;
            }
            
            .employee-info {
                flex-direction: column;
                gap: 8px;
            }
            
            .legend-items {
                flex-direction: column;
                gap: 10px;
            }
        }

        .criteria-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 12px;
            font-size: 16px;
        }

        .criteria-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .criteria-item:last-child {
            border-bottom: none;
        }

        .criteria-name {
            flex: 1;
            font-weight: 500;
            color: #495057;
        }

        .criteria-weight {
            font-size: 12px;
            color: #6c757d;
            margin-right: 16px;
        }

        .rating-input {
            width: 80px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-save {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }

        .stats-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 16px;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .stats-container::-webkit-scrollbar { height: 6px; }
        .stats-container::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 3px; }

        .stat-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-width: 220px;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #a8e6a3, #93c5fd, #fde68a, #fed7aa, #fecaca);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }

        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px 40px;
            border-radius: 8px;
            z-index: 2000;
        }

        /* CSS cho giao diện mới */
        .main-content {
            padding: 20px 0;
        }

        .welcome-section {
            margin-bottom: 40px;
        }

        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            gap: 30px;
            color: white;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }

        .welcome-icon {
            font-size: 60px;
            opacity: 0.9;
        }

        .welcome-content h2 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 15px 0;
        }

        .welcome-content p {
            font-size: 18px;
            opacity: 0.9;
            margin: 0;
            line-height: 1.6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            border-color: #667eea;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 20px;
        }

        .feature-icon.chuyen-can {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .feature-icon.hieu-qua {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }

        .feature-icon.thai-do {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .feature-content h3 {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 10px 0;
        }

        .feature-content p {
            color: #6c757d;
            margin: 0 0 15px 0;
            line-height: 1.5;
        }

        .feature-badge {
            display: inline-block;
            background: #f8f9fa;
            color: #495057;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-section {
            margin-top: 40px;
        }

        .info-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .info-card h3 {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .formula-display {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .formula-text {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            font-family: 'Courier New', monospace;
        }

        .rating-scale {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .rating-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .rating-item:hover {
            transform: translateY(-2px);
        }

        .rating-range {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .rating-label {
            font-size: 14px;
            font-weight: 500;
        }

        .rating-item.excellent {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .rating-item.good {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
        }

        .rating-item.fair {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            color: #856404;
        }

        .rating-item.average {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .rating-item.weak {
            background: linear-gradient(135deg, #f5c6cb, #f1b0b7);
            color: #721c24;
        }

        /* CSS cho bảng đánh giá chuyên cần */
        .evaluation-section {
            margin-top: 40px;
        }

        .evaluation-header-bar {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 12px 12px 0 0;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .header-actions .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .header-actions .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }

        .header-tabs {
            display: flex;
            gap: 20px;
        }

        .tab {
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .tab:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .tab.active {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .evaluation-summary {
            text-align: right;
        }

        .summary-text {
            font-size: 18px;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .summary-stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
        }

        .evaluation-table-container {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }

        .header-cell {
            padding: 16px 20px;
            font-weight: 600;
            color: #495057;
            text-align: center;
            border-right: 1px solid #e9ecef;
        }

        .header-cell:last-child {
            border-right: none;
        }

        .employee-col {
            text-align: left;
        }

        .table-body {
            max-height: 600px;
            overflow-y: auto;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
        }

        .table-row:hover {
            background: #f8f9fa;
        }

        .table-cell {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            border-right: 1px solid #e9ecef;
        }

        .table-cell:last-child {
            border-right: none;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .employee-details h4 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .employee-details .employee-id {
            font-size: 12px;
            color: #6c757d;
        }

        .department-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .score-cell {
            text-align: center;
        }

        .score-display {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .score-number {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }

        .score-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .chuyen-can .score-fill {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .hieu-qua .score-fill {
            background: linear-gradient(135deg, #007bff, #6610f2);
        }

        .thai-do .score-fill {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        .total-score .score-fill {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .rating-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .rating-badge.poor {
            background: #f8d7da;
            color: #721c24;
        }

        .rating-badge.average {
            background: #fff3cd;
            color: #856404;
        }

        .rating-badge.fair {
            background: #d1ecf1;
            color: #0c5460;
        }

        .rating-badge.good {
            background: #d4edda;
            color: #155724;
        }

        .rating-badge.excellent {
            background: #cce5ff;
            color: #004085;
        }

        .loading-row {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        /* CSS cho phân chia phòng ban */
        .department-section {
            margin-bottom: 20px;
        }

        .department-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
            border-left: 4px solid #667eea;
        }

        .department-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #495057;
            font-size: 16px;
        }

        .department-title i {
            color: #667eea;
            font-size: 18px;
        }

        .department-count {
            background: #667eea;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .department-divider {
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 0;
        }

        .department-section .table-row {
            border-left: 2px solid #e9ecef;
            border-right: 2px solid #e9ecef;
        }

        .department-section .table-row:last-child {
            border-bottom: 2px solid #e9ecef;
            border-radius: 0 0 8px 8px;
        }

        @media (max-width: 768px) {
            .welcome-card {
                flex-direction: column;
                text-align: center;
                padding: 30px 20px;
            }

            .welcome-icon {
                font-size: 40px;
            }

            .welcome-content h2 {
                font-size: 24px;
            }

            .welcome-content p {
                font-size: 16px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .feature-card {
                padding: 20px;
            }

            .rating-scale {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="layout-wrapper">
        <?php include('../includes/sidebar.php'); ?>
        <div class="layout-page">
            <div class="content-wrapper">
                <div class="evaluation-container">
                    <div class="evaluation-header">
                        <h1 class="evaluation-title">
                            <i class="fas fa-star"></i> Đánh giá nhân viên
                        </h1>
                        <div class="header-actions">
                            <button class="btn-import" onclick="showImportModal()">
                                <i class="fas fa-file-import"></i> Import CSV
                            </button>
                        </div>
                    </div>

                    <!-- <div class="main-content">
                        <div class="welcome-section">
                            <div class="welcome-card">
                                <div class="welcome-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="welcome-content">
                                    <h2>Hệ thống đánh giá nhân viên</h2>
                                    <p>Quản lý và theo dõi hiệu suất làm việc của nhân viên một cách tự động và chính xác</p>
                                </div>
                            </div>
                        </div> -->

                        <!-- <div class="features-grid">
                            <div class="feature-card" onclick="showAttendanceModal()">
                                <div class="feature-icon chuyen-can">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Chuyên cần (40%)</h3>
                                    <p>Tự động từ hệ thống chấm công và nghỉ phép</p>
                                    <div class="feature-badge">Tự động</div>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon hieu-qua">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Hiệu quả (40%)</h3>
                                    <p>Dựa trên doanh số và hoa hồng KPI</p>
                                    <div class="feature-badge">KPI</div>
                                </div>
                            </div>

                            <div class="feature-card">
                                <div class="feature-icon thai-do">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <div class="feature-content">
                                    <h3>Thái độ (20%)</h3>
                                    <p>Đánh giá từ phòng ban qua file CSV</p>
                                    <div class="feature-badge">CSV</div>
                                </div>
                            </div>
                        </div> -->

                        <div class="evaluation-section">
                            <div class="evaluation-header-bar">
                                <div class="header-tabs">
                                    <div class="tab active" onclick="switchTab('nhan-vien')">
                                        <i class="fas fa-users"></i> Nhân viên
                                    </div>
                                    <div class="tab" onclick="switchTab('phong-ban')">
                                        <i class="fas fa-building"></i> Phòng ban
                                    </div>
                                </div>
                                <div class="evaluation-summary">
                                    <span class="summary-text">Tổng quan đánh giá chuyên cần</span>
                                    <div class="summary-stats" id="summaryStats">
                                        <span>Tổng số nhân viên: <strong>0</strong></span>
                                        <span>Điểm trung bình: <strong>0.00/100</strong></span>
                                    </div>
                                </div>
                                <div class="header-actions" style="display:flex; gap:10px; align-items:center;">
                                    <select id="quarterFilter" class="form-control" style="width: 160px;">
                                        <option value="1">Quý 1 (Tháng 1 – 3)</option>
                                        <option value="2">Quý 2 (Tháng 4 – 6)</option>
                                        <option value="3">Quý 3 (Tháng 7 – 9)</option>
                                        <option value="4">Quý 4 (Tháng 10 – 12)</option>
                                    </select>
                                    <button class="btn btn-outline-success btn-sm" onclick="refreshData()">
                                        <i class="fas fa-sync-alt"></i> Làm mới
                                    </button>
                                </div>
                            </div>

                            <div class="evaluation-table-container">
                                <div class="table-header">
                                    <div class="header-cell employee-col">Nhân viên</div>
                                    <div class="header-cell department-col">Phòng ban</div>
                                    <div class="header-cell score-col">Chuyên cần (40%)</div>
                                    <div class="header-cell score-col">Hiệu quả (40%)</div>
                                    <div class="header-cell score-col">Thái độ (20%)</div>
                                    <div class="header-cell total-col">Tổng điểm</div>
                                    <div class="header-cell rating-col">Xếp loại</div>
                                    <div class="header-cell status-col">Trạng thái</div>
                                </div>
                                <div class="table-body" id="evaluationTableBody">
                                    <div class="loading-row">
                                        <i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chuyên cần -->
    <div id="attendanceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Đánh giá chuyên cần nhân viên</h2>
                <button class="modal-close" onclick="closeAttendanceModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tháng/Năm</label>
                    <div style="display: flex; gap: 12px;">
                        <select id="attendanceMonth" class="form-control" style="flex: 1;">
                            <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == date('n') ? 'selected' : '' ?>>
                                    Tháng <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <input type="number" id="attendanceYear" class="form-control" style="flex: 1;"
                               value="<?= date('Y') ?>" min="2020" max="2030" onchange="loadAttendanceData()">
                    </div>
                </div>
                
                
                <div id="attendanceTableContainer">
                    <!-- Bảng chuyên cần sẽ được load ở đây -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeAttendanceModal()">Đóng</button>
                <button class="btn-save" onclick="generateAttendanceEvaluation()">Tạo đánh giá</button>
            </div>
        </div>
    </div>

    <!-- Modal xem chi tiết -->
    <div id="viewEvaluationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Chi tiết đánh giá</h2>
                <button class="modal-close" onclick="closeViewEvaluationModal()">&times;</button>
            </div>
            <div class="modal-body" id="viewEvaluationContent">
                <!-- Nội dung chi tiết sẽ được load ở đây -->
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeViewEvaluationModal()">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Modal Import CSV -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Import dữ liệu đánh giá từ CSV</h2>
                <button class="modal-close" onclick="closeImportModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="import-tabs">
                    <div class="tab active" onclick="switchImportTab('hieu-qua')">
                        <i class="fas fa-chart-line"></i> Hiệu quả công việc (40%)
                    </div>
                    <div class="tab" onclick="switchImportTab('thai-do')">
                        <i class="fas fa-handshake"></i> Thái độ & Hợp tác (20%)
                    </div>
                </div>
                
                <div class="form-group" style="margin: 10px 0; display:flex; gap:10px; align-items:center;">
                    <label class="form-label" style="min-width:100px;">Chọn quý:</label>
                    <select id="importQuarter" class="form-control" style="width: 180px;">
                        <option value="">-- Chọn quý --</option>
                        <option value="1">Quý 1 (Tháng 1 – 3)</option>
                        <option value="2">Quý 2 (Tháng 4 – 6)</option>
                        <option value="3">Quý 3 (Tháng 7 – 9)</option>
                        <option value="4">Quý 4 (Tháng 10 – 12)</option>
                    </select>
                    <label class="form-label" style="min-width:60px;">Năm:</label>
                    <input type="number" id="importYear" class="form-control" style="width:120px;" min="2000" max="2100" value="" placeholder="Năm" />
                </div>

                <div id="importHieuQua" class="import-content active">
                    <div class="form-group">
                        <label class="form-label">Chọn file CSV cho Hiệu quả công việc</label>
                        <input type="file" id="hieuQuaFile" accept=".csv" class="form-control">
                        <small class="form-text">File CSV phải có cấu trúc: Mã nhân viên, Tên nhân viên, Câu hỏi 1-5, Tổng điểm, Điểm cuối</small>
                    </div>
                    <div class="file-preview" id="hieuQuaPreview" style="display: none;">
                        <h4>Xem trước dữ liệu:</h4>
                        <div id="hieuQuaTable"></div>
                    </div>
                </div>
                
                <div id="importThaiDo" class="import-content">
                    <div class="form-group">
                        <label class="form-label">Chọn file CSV cho Thái độ & Hợp tác</label>
                        <input type="file" id="thaiDoFile" accept=".csv" class="form-control">
                        <small class="form-text">File CSV phải có cấu trúc: Mã nhân viên, Tên nhân viên, Câu hỏi 1-5, Tổng điểm, Điểm cuối</small>
                    </div>
                    <div class="file-preview" id="thaiDoPreview" style="display: none;">
                        <h4>Xem trước dữ liệu:</h4>
                        <div id="thaiDoTable"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeImportModal()">Đóng</button>
                <button class="btn-save" onclick="importCSVData()">Import dữ liệu</button>
            </div>
        </div>
    </div>

    <!-- Loading indicator -->
    <div class="loading" id="loadingIndicator">
        <i class="fas fa-spinner fa-spin"></i> Đang xử lý...
    </div>

    <script>
        let evaluationData = [];
        let tieuChiData = [];
        let nhanVienData = [];

        // Khởi tạo
        document.addEventListener('DOMContentLoaded', function() {
            // Set default quarter to the current month quarter
            const qSelInit = document.getElementById('quarterFilter');
            if (qSelInit) {
                const m = new Date().getMonth() + 1;
                const defaultQ = m <= 3 ? 1 : m <= 6 ? 2 : m <= 9 ? 3 : 4;
                qSelInit.value = String(defaultQ);
            }
            loadTieuChiData();
            loadNhanVienData();
            loadEvaluationData();
        });

        // Load danh sách tiêu chí
        async function loadTieuChiData() {
            try {
                const response = await fetch('/doanqlns/index.php/api/danhgia/tieu-chi');
                const data = await response.json();
                if (data.success) {
                    tieuChiData = data.data;
                    renderCriteriaContainer();
                }
            } catch (error) {
                console.error('Lỗi khi load tiêu chí:', error);
            }
        }

        // Load danh sách nhân viên
        async function loadNhanVienData() {
            try {
                const response = await fetch('/doanqlns/index.php/api/users');
                const data = await response.json();
                if (data && Array.isArray(data)) {
                    nhanVienData = data;
                    renderEmployeeSelect();
                } else {
                    console.error('Dữ liệu nhân viên không hợp lệ:', data);
                }
            } catch (error) {
                console.error('Lỗi khi load nhân viên:', error);
            }
        }

        // Load dữ liệu đánh giá
        async function loadEvaluationData() {
            // Lấy tháng/năm theo quý nếu người dùng chọn
            let month = new Date().getMonth() + 1;
            const year = new Date().getFullYear();
            // Lưu ý: Không lọc trước khi tải dữ liệu; việc lọc sẽ được thực hiện
            // sau khi dữ liệu đánh giá thực được tải về ở bên dưới.
            
            console.log('Loading evaluation data for month:', month, 'year:', year);
            showLoading();
            try {
                // Load dữ liệu từ các API bao gồm dữ liệu đánh giá thực
                const [nhanVienResponse, quanLyNghiPhepResponse, doanhSoResponse, danhGiaResponse] = await Promise.all([
                    fetch('/doanqlns/index.php/api/nhanvien'),
                    fetch(`/doanqlns/index.php/api/quan-ly-nghi-phep?nam=${year}`),
                    fetch('/doanqlns/api/doanh-so-thang.php'),
                    fetch('/doanqlns/index.php/api/danhgia/all') // Load dữ liệu đánh giá thực
                ]);
                
                const nhanVienData = await nhanVienResponse.json();
                const quanLyNghiPhepData = await quanLyNghiPhepResponse.json();
                const doanhSoData = await doanhSoResponse.json();
                const danhGiaData = await danhGiaResponse.json();
                
                console.log('Nhan vien response status:', nhanVienResponse.status);
                console.log('Quan ly nghi phep response status:', quanLyNghiPhepResponse.status);
                console.log('Doanh so response status:', doanhSoResponse.status);
                console.log('Danh gia response status:', danhGiaResponse.status);
                console.log('Danh gia data:', danhGiaData);
                
                console.log('Nhan vien data:', nhanVienData);
                console.log('Quan ly nghi phep data:', quanLyNghiPhepData);
                console.log('Quan ly nghi phep data success:', quanLyNghiPhepData.success);
                console.log('Quan ly nghi phep data count:', quanLyNghiPhepData.data ? quanLyNghiPhepData.data.length : 0);
                console.log('Doanh so data:', doanhSoData);
                
                // Kiểm tra response status
                if (!nhanVienResponse.ok) {
                    throw new Error(`API nhân viên trả về lỗi: ${nhanVienResponse.status}`);
                }
                if (!quanLyNghiPhepResponse.ok) {
                    throw new Error(`API quản lý nghỉ phép trả về lỗi: ${quanLyNghiPhepResponse.status}`);
                }
                if (!doanhSoResponse.ok) {
                    console.warn('API doanh số trả về lỗi:', doanhSoResponse.status);
                }
                
                // Xử lý dữ liệu nhân viên (API trả về array trực tiếp)
                let nhanVienArray = [];
                if (Array.isArray(nhanVienData)) {
                    nhanVienArray = nhanVienData;
                } else if (nhanVienData.success && nhanVienData.data) {
                    nhanVienArray = nhanVienData.data;
                } else {
                    throw new Error('Dữ liệu nhân viên không đúng format: ' + JSON.stringify(nhanVienData));
                }
                
                // Xử lý dữ liệu quản lý nghỉ phép
                let quanLyArray = [];
                if (quanLyNghiPhepData.success && quanLyNghiPhepData.data) {
                    quanLyArray = quanLyNghiPhepData.data;
                } else if (Array.isArray(quanLyNghiPhepData)) {
                    quanLyArray = quanLyNghiPhepData;
                } else {
                    console.warn('Dữ liệu quản lý nghỉ phép không đúng format:', quanLyNghiPhepData);
                }
                
                // Xử lý dữ liệu doanh số
                let doanhSoArray = [];
                if (doanhSoData.success && doanhSoData.data) {
                    doanhSoArray = doanhSoData.data;
                } else if (Array.isArray(doanhSoData)) {
                    doanhSoArray = doanhSoData;
                } else {
                    console.warn('Dữ liệu doanh số không đúng format:', doanhSoData);
                }
                
                console.log('Processed data:');
                console.log('Nhan vien array length:', nhanVienArray.length);
                console.log('Quan ly array length:', quanLyArray.length);
                console.log('Doanh so array length:', doanhSoArray.length);
                
                if (nhanVienArray.length > 0) {
                    console.log('Processing', nhanVienArray.length, 'employees');
                    
                    // Sử dụng dữ liệu đánh giá thực từ database nếu có
                    console.log('Checking danhGiaData:', danhGiaData);
                    console.log('danhGiaData.success:', danhGiaData.success);
                    console.log('danhGiaData.data:', danhGiaData.data);
                    console.log('Array.isArray(danhGiaData.data):', Array.isArray(danhGiaData.data));
                    
                    if (danhGiaData.success && danhGiaData.data && Array.isArray(danhGiaData.data) && danhGiaData.data.length > 0) {
                        console.log('Using real evaluation data from database');
                        // Lọc theo quý dựa trên thang_danh_gia/nam_danh_gia (ưu tiên), fallback ngay_tao
                        let sourceList = danhGiaData.data;
                        const qSel = document.getElementById('quarterFilter');
                        if (qSel && qSel.value) {
                            const q = parseInt(qSel.value, 10);
                            const quarterRange = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                            const [startM, endM] = quarterRange[q] || [1,12];
                            const currentYear = new Date().getFullYear();
                            sourceList = sourceList.filter(dg => {
                                // Ưu tiên dùng trường tháng/năm rõ ràng từ DB nếu có
                                const mField = parseInt(dg.thang_danh_gia, 10);
                                const yField = parseInt(dg.nam_danh_gia, 10);
                                if (!isNaN(mField) && !isNaN(yField)) {
                                    // Do đã lưu điểm quý vào THÁNG CUỐI QUÝ, chỉ lấy đúng tháng endM
                                    return yField === currentYear && mField === endM;
                                }
                                // Fallback: dùng ngay_tao nếu trường tháng/năm không có
                                const dateStr = dg.ngay_tao || dg.ngay || dg.ngay_cap_nhat || '';
                                if (!dateStr) return false; // khi lọc theo quý mà thiếu ngày thì loại bỏ
                                const match = dateStr.match(/\d{4}-(\d{2})-(\d{2})/);
                                if (match) {
                                    const m = parseInt(match[1], 10);
                                    const y = parseInt(dateStr.substring(0, 4), 10);
                                    return y === currentYear && m === endM;
                                }
                                const d = new Date(dateStr);
                                if (!isNaN(d.getTime())) {
                                const m = d.getMonth() + 1;
                                const y = d.getFullYear();
                                    return y === currentYear && m === endM;
                                }
                                return false;
                            });
                            // Khử trùng lặp theo nhân viên: ưu tiên bản ghi có diem_chuyen_can, nếu không có thì lấy bản ghi mới nhất
                            const pickByEmployee = new Map();
                            sourceList.forEach(dg => {
                                const key = dg.id_nhan_vien;
                                const curr = pickByEmployee.get(key);
                                const dateStr = (dg.ngay_tao || dg.ngay || dg.ngay_cap_nhat || '');
                                const ts = Date.parse(dateStr) || 0;
                                const hasCC = (dg.diem_chuyen_can !== undefined && dg.diem_chuyen_can !== null);
                                if (!curr) {
                                    pickByEmployee.set(key, { ...dg, __ts: ts, __hasCC: hasCC });
                                } else {
                                    const better = (hasCC && !curr.__hasCC) ||
                                                   (hasCC === curr.__hasCC && (ts > curr.__ts || (ts === curr.__ts && (parseInt(dg.id_danh_gia||0) > parseInt(curr.id_danh_gia||0)))));
                                    if (better) {
                                        pickByEmployee.set(key, { ...dg, __ts: ts, __hasCC: hasCC });
                                    }
                                }
                            });
                            sourceList = Array.from(pickByEmployee.values());
                        }
                        // Nếu sau khi lọc theo quý mà không còn bản ghi, fallback sang tính toán theo quý từ dữ liệu tháng
                        if (sourceList.length === 0 && qSel && qSel.value) {
                            const q = parseInt(qSel.value, 10);
                            const quarterRange = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                            const [startM, endM] = quarterRange[q] || [1,12];
                            const aggregate = new Map(); // id -> {sumChuyenCan,sumHieuQua,sumThaiDo,count,ten_phong_ban,ho_ten}
                            for (let m = startM; m <= endM; m++) {
                                const monthEval = calculateAttendanceScores(nhanVienArray, quanLyArray, doanhSoArray, m, currentYear);
                                monthEval.forEach(item => {
                                    const key = item.id_nhan_vien || item.idNhanVien || item.id;
                                    if (!aggregate.has(key)) {
                                        aggregate.set(key, { sumChuyenCan: 0, sumHieuQua: 0, sumThaiDo: 0, count: 0, ho_ten: item.ho_ten, ten_phong_ban: item.ten_phong_ban });
                                    }
                                    const a = aggregate.get(key);
                                    a.sumChuyenCan += Number(item.chuyenCanScore || 0);
                                    a.sumHieuQua += Number(item.hieuQuaScore || 0);
                                    a.sumThaiDo += Number(item.thaiDoScore || 0);
                                    a.count += 1;
                                });
                            }
                            const fallbackEval = Array.from(aggregate.entries())
                              .filter(([_, a]) => a.count > 0)
                              .map(([id, a]) => {
                                const chuyenCanScore = a.count ? a.sumChuyenCan / a.count : 0;
                                
                                // Xử lý điểm hiệu quả - tính tổng công điểm theo quý từ doanh_so_thang
                                let hieuQuaScore = a.count ? a.sumHieuQua / a.count : 0;
                                
                                // Nếu là phòng ban Kinh doanh, tính tổng công điểm theo quý từ doanh_so_thang
                                if (a.ten_phong_ban && a.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
                                    const doanhSoInQuarter = doanhSoArray.filter(ds => 
                                        ds.id_nhan_vien == id &&
                                        ds.nam == currentYear &&
                                        ds.thang >= startM &&
                                        ds.thang <= endM
                                    );
                                    
                                    if (doanhSoInQuarter.length > 0) {
                                        hieuQuaScore = doanhSoInQuarter.reduce((sum, ds) => sum + (parseFloat(ds.cong_diem_danh_gia) || 0), 0);
                                        console.log(`✅ Tính tổng công điểm theo quý ${q} cho ${a.ho_ten} (${a.ten_phong_ban}): ${hieuQuaScore}`);
                                    }
                                }
                                
                                const thaiDoScore = a.count ? a.sumThaiDo / a.count : 0;
                                const totalScore = (chuyenCanScore * 0.4) + (hieuQuaScore * 0.4) + (thaiDoScore * 0.2);
                                return {
                                    idDanhGia: null,
                                    id_nhan_vien: id,
                                    ho_ten: a.ho_ten || 'N/A',
                                    ten_phong_ban: a.ten_phong_ban || 'Chưa phân loại',
                                    chuyenCanScore,
                                    hieuQuaScore,
                                    thaiDoScore,
                                    totalScore,
                                    xepLoai: getXepLoai(totalScore),
                                    trangThai: 'Nháp'
                                };
                            });
                            renderEvaluationTable(fallbackEval);
                            updateSummaryStats(fallbackEval);
                            try { cachedData = cachedData || {}; cachedData.evaluationData = fallbackEval; } catch(e) {}
                            return;
                        }
                        // Nếu có chọn quý, chuẩn bị điểm chuyên cần theo quý từ bảng chấm công
                        let quarterAttendanceScoreByEmp = null;
                        if (qSel && qSel.value) {
                            try {
                                const q = parseInt(qSel.value, 10);
                                const qr = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                                const [qmStart, qmEnd] = qr[q] || [1,12];
                                // Tải chấm công theo năm rồi lọc theo quý để tính điểm chuyên cần
                                const chamCongResp = await fetch(`/doanqlns/index.php/api/chamcong?nam=${currentYear}`);
                                const chamCongData = chamCongResp.ok ? (await chamCongResp.json()) : [];
                                quarterAttendanceScoreByEmp = computeQuarterAttendanceScoreMap(chamCongData, qmStart, qmEnd, currentYear);
                            } catch (e) {
                                console.warn('Không thể tính điểm chuyên cần theo quý từ chấm công:', e);
                            }
                        }

                        const evaluationData = sourceList.map(dg => {
                            const nv = nhanVienArray.find(n => n.id_nhan_vien == dg.id_nhan_vien);
                            
                            // Điểm chuyên cần: ưu tiên diem_chuyen_can đã lưu trong DB (tháng cuối quý)
                            let chuyenCanScore = (dg.diem_chuyen_can !== undefined && dg.diem_chuyen_can !== null)
                                ? parseFloat(dg.diem_chuyen_can)
                                : (quarterAttendanceScoreByEmp && quarterAttendanceScoreByEmp.has(dg.id_nhan_vien))
                                    ? quarterAttendanceScoreByEmp.get(dg.id_nhan_vien)
                                    : getChuyenCanScoreFromQuanLy(dg.id_nhan_vien, quanLyArray);
                            
                            // Xử lý điểm hiệu quả - ưu tiên diem_hieu_qua đã lưu trong DB (tổng công điểm theo quý)
                            let hieuQuaScore = parseFloat(dg.diem_hieu_qua) || 0;
                            
                            // Nếu không có điểm hiệu quả trong DB, tính tổng công điểm theo quý từ doanh_so_thang
                            if (hieuQuaScore === 0 && nv && nv.ten_phong_ban && nv.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
                                // Tính tổng công điểm theo quý
                                const qSel = document.getElementById('quarterFilter');
                                if (qSel && qSel.value) {
                                    const q = parseInt(qSel.value, 10);
                                    const quarterRange = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                                    const [startM, endM] = quarterRange[q] || [1,12];
                                    const currentYear = new Date().getFullYear();
                                    
                                    const doanhSoInQuarter = doanhSoArray.filter(ds => 
                                        ds.id_nhan_vien == dg.id_nhan_vien &&
                                        ds.nam == currentYear &&
                                        ds.thang >= startM &&
                                        ds.thang <= endM
                                    );
                                    
                                    if (doanhSoInQuarter.length > 0) {
                                        hieuQuaScore = doanhSoInQuarter.reduce((sum, ds) => sum + (parseFloat(ds.cong_diem_danh_gia) || 0), 0);
                                        console.log(`✅ Tính tổng công điểm theo quý ${q} cho ${nv.ho_ten}: ${hieuQuaScore}`);
                                    }
                                }
                            }
                            
                            // Tính lại tổng điểm với điểm hiệu quả mới
                            const thaiDoScore = parseFloat(dg.diem_thai_do) || 0;
                            const totalScore = (chuyenCanScore * 0.4) + (hieuQuaScore * 0.4) + (thaiDoScore * 0.2);
                            
                            // Tự động xếp loại nếu có đủ 3 điểm
                            let xepLoai = dg.xep_loai || 'Chưa xếp loại';
                            if (chuyenCanScore > 0 && hieuQuaScore > 0 && thaiDoScore > 0) {
                                xepLoai = getXepLoai(totalScore);
                            }
                            
                            return {
                                idDanhGia: dg.id_danh_gia,
                                id_nhan_vien: dg.id_nhan_vien,
                                ho_ten: nv ? nv.ho_ten : 'N/A',
                                ten_phong_ban: nv ? nv.ten_phong_ban : 'Chưa phân loại',
                                chuyenCanScore: chuyenCanScore,
                                hieuQuaScore: hieuQuaScore,
                                thaiDoScore: thaiDoScore,
                                totalScore: totalScore,
                                xepLoai: xepLoai,
                                trangThai: dg.trang_thai || 'Nháp'
                            };
                        });
                        
                        console.log('Real evaluation data:', evaluationData);
                        renderEvaluationTable(evaluationData);
                        updateSummaryStats(evaluationData);
                        // Cache evaluation data for later use (e.g., sync to Thuong Tet)
                        try { cachedData = cachedData || {}; cachedData.evaluationData = evaluationData; } catch (e) { console.warn('Cannot set cachedData', e); }
                    } else {
                        console.log('No real evaluation data, using calculated data');
                        const qSel = document.getElementById('quarterFilter');
                        if (qSel && qSel.value) {
                            const q = parseInt(qSel.value, 10);
                            const qr = { 1: [1,3], 2: [4,6], 3: [7,9], 4: [10,12] };
                            const [qmStart, qmEnd] = qr[q] || [1,12];

                            // 1) Tổng hợp điểm theo quý bằng cách tính từng tháng
                            const aggregate = new Map(); // id -> {sumChuyenCan,sumHieuQua,sumThaiDo,count,ten_phong_ban,ho_ten}
                            for (let m = qmStart; m <= qmEnd; m++) {
                                const monthEval = await calculateAttendanceScores(nhanVienArray, quanLyArray, doanhSoArray, m, year);
                                monthEval.forEach(item => {
                                    const key = item.id_nhan_vien || item.idNhanVien || item.id;
                                    if (!aggregate.has(key)) {
                                        aggregate.set(key, { sumChuyenCan: 0, sumHieuQua: 0, sumThaiDo: 0, count: 0, ho_ten: item.ho_ten, ten_phong_ban: item.ten_phong_ban });
                                    }
                                    const a = aggregate.get(key);
                                    a.sumChuyenCan += Number(item.chuyenCanScore || item.chuyen_can || 0);
                                    a.sumHieuQua += Number(item.hieuQuaScore || item.hieu_qua || 0);
                                    a.sumThaiDo += Number(item.thaiDoScore || item.thai_do || 0);
                                    a.count += 1;
                                });
                            }

                            // 2) Ghi đè điểm chuyên cần theo quý bằng dữ liệu chấm công (chuẩn theo quý)
                            let quarterAttendanceScoreByEmp = null;
                            try {
                                const chamCongResp = await fetch(`/doanqlns/index.php/api/chamcong?nam=${year}`);
                                const chamCongData = chamCongResp.ok ? (await chamCongResp.json()) : [];
                                quarterAttendanceScoreByEmp = computeQuarterAttendanceScoreMap(chamCongData, qmStart, qmEnd, year);
                            } catch (e) { console.warn('Không thể tính điểm chuyên cần theo quý từ chấm công (fallback branch):', e); }

                            const evaluationData = Array.from(aggregate.entries())
                                .filter(([_, a]) => a.count > 0)
                                .map(([id, a]) => {
                                    let chuyenCanScore = a.sumChuyenCan / a.count;
                                    if (quarterAttendanceScoreByEmp && quarterAttendanceScoreByEmp.has(id)) {
                                        chuyenCanScore = quarterAttendanceScoreByEmp.get(id);
                                    }
                                    
                                    // Xử lý điểm hiệu quả - tính tổng công điểm theo quý từ doanh_so_thang
                                    let hieuQuaScore = a.sumHieuQua / a.count;
                                    
                                    // Nếu là phòng ban Kinh doanh, tính tổng công điểm theo quý từ doanh_so_thang
                                    if (a.ten_phong_ban && a.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
                                        const doanhSoInQuarter = doanhSoArray.filter(ds => 
                                            ds.id_nhan_vien == id &&
                                            ds.nam == year &&
                                            ds.thang >= qmStart &&
                                            ds.thang <= qmEnd
                                        );
                                        
                                        if (doanhSoInQuarter.length > 0) {
                                            hieuQuaScore = doanhSoInQuarter.reduce((sum, ds) => sum + (parseFloat(ds.cong_diem_danh_gia) || 0), 0);
                                            console.log(`✅ Tính tổng công điểm theo quý ${q} cho ${a.ho_ten} (${a.ten_phong_ban}): ${hieuQuaScore}`);
                                        }
                                    }
                                    
                                    const thaiDoScore = a.sumThaiDo / a.count;
                                    const totalScore = (chuyenCanScore * 0.4) + (hieuQuaScore * 0.4) + (thaiDoScore * 0.2);
                                    return {
                                        idDanhGia: null,
                                        id_nhan_vien: id,
                                        ho_ten: a.ho_ten || 'N/A',
                                        ten_phong_ban: a.ten_phong_ban || 'Chưa phân loại',
                                        chuyenCanScore,
                                        hieuQuaScore,
                                        thaiDoScore,
                                        totalScore,
                                        xepLoai: getXepLoai(totalScore),
                                        trangThai: 'Nháp'
                                    };
                                });
                            renderEvaluationTable(evaluationData);
                            updateSummaryStats(evaluationData);
                            try { cachedData = cachedData || {}; cachedData.evaluationData = evaluationData; } catch(e) {}
                        } else {
                            // Không chọn quý: tính theo tháng hiện tại (giữ nguyên hành vi cũ)
                            const evaluationData = await calculateAttendanceScores(
                            nhanVienArray, 
                            quanLyArray, 
                            doanhSoArray, 
                            month, 
                            year
                        );
                        if (evaluationData && evaluationData.length > 0) {
                            renderEvaluationTable(evaluationData);
                            updateSummaryStats(evaluationData);
                            try { cachedData = cachedData || {}; cachedData.evaluationData = evaluationData; } catch (e) { console.warn('Cannot set cachedData', e); }
                            } else {
                                loadSampleData();
                            }
                        }
                    }
                } else {
                    console.error('No employee data found, loading sample data');
                    loadSampleData();
                }
            } catch (error) {
                console.error('Error loading evaluation data:', error);
                console.log('Loading sample data as fallback');
                try {
                    await loadSampleData();
                } catch (sampleError) {
                    console.error('Error loading sample data:', sampleError);
                    // Hiển thị thông báo lỗi
                    const tbody = document.getElementById('evaluationTableBody');
                    if (tbody) {
                        tbody.innerHTML = '<div class="loading-row">Lỗi khi tải dữ liệu. Vui lòng thử lại sau.</div>';
                    }
                }
            } finally {
                hideLoading();
            }
        }

        // Render bảng đánh giá chuyên cần
        function renderEvaluationTable(data) {
            console.log('Rendering evaluation table with data:', data);
            const tbody = document.getElementById('evaluationTableBody');
            if (!tbody) {
                console.error('Element evaluationTableBody not found!');
                return;
            }
            
            if (!data || data.length === 0) {
                tbody.innerHTML = '<div class="loading-row">Không có dữ liệu đánh giá</div>';
                return;
            }
            
            // Nhóm dữ liệu theo phòng ban
            const groupedData = {};
            data.forEach(item => {
                const department = item.ten_phong_ban || 'Chưa phân loại';
                if (!groupedData[department]) {
                    groupedData[department] = [];
                }
                groupedData[department].push(item);
            });
            
            // Tạo HTML với đường ngang phân chia phòng ban
            let html = '';
            Object.keys(groupedData).forEach((department, deptIndex) => {
                // Thêm header phòng ban
                html += `
                    <div class="department-section">
                        <div class="department-header">
                            <div class="department-title">
                                <i class="fas fa-building"></i>
                                Phòng ban: ${department}
                            </div>
                            <div class="department-count">
                                ${groupedData[department].length} nhân viên
                            </div>
                        </div>
                        <div class="department-divider"></div>
                `;
                
                // Thêm các nhân viên trong phòng ban
                groupedData[department].forEach((item, index) => {
                    html += `
                        <div class="table-row">
                            <div class="table-cell">
                                <div class="employee-info">
                                    <div class="employee-avatar">
                                        ${item.ho_ten ? item.ho_ten.charAt(0).toUpperCase() : 'N'}
                                    </div>
                                    <div class="employee-details">
                                        <h4>${item.ho_ten || 'N/A'}</h4>
                                        <div class="employee-id">ID: ${item.id_nhan_vien}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-cell">
                                <div class="department-badge">${item.ten_phong_ban || 'Chưa phân loại'}</div>
                            </div>
                            <div class="table-cell score-cell">
                                <div class="score-display chuyen-can">
                                    <span class="score-number">${item.chuyenCanScore.toFixed(1)} /10</span>
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: ${(item.chuyenCanScore / 10) * 100}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-cell score-cell">
                                <div class="score-display hieu-qua">
                                    <span class="score-number">${item.hieuQuaScore.toFixed(1)} /10</span>
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: ${(item.hieuQuaScore / 10) * 100}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-cell score-cell">
                                <div class="score-display thai-do">
                                    <span class="score-number">${item.thaiDoScore.toFixed(1)} /10</span>
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: ${(item.thaiDoScore / 10) * 100}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-cell score-cell">
                                <div class="score-display total-score">
                                    <span class="score-number">${item.totalScore.toFixed(1)} /10</span>
                                    <div class="score-bar">
                                        <div class="score-fill" style="width: ${item.totalScore * 10}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-cell">
                                <span class="rating-badge ${getRatingClass(item.xepLoai)}">
                                    ${item.xepLoai}
                                </span>
                            </div>
                            <div class="table-cell status-cell">
                                <span class="status-badge ${getStatusClass(item.trangThai || 'Nháp')} clickable-status" 
                                      onclick="changeStatus(${item.idDanhGia}, '${item.trangThai || 'Nháp'}')"
                                      title="Nhấn để thay đổi trạng thái">
                                    ${getStatusText(item.trangThai || 'Nháp')}
                                </span>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>'; // Đóng department-section
            });
            
            tbody.innerHTML = html;
        }

        // Cập nhật thống kê tổng quan
        function updateSummaryStats(data) {
            const summaryStats = document.getElementById('summaryStats');
            if (!summaryStats) return;
            
            const totalEmployees = data.length;
            const averageScore = data.length > 0 ? 
                (data.reduce((sum, item) => sum + item.totalScore, 0) / data.length).toFixed(2) : 0;
            
            summaryStats.innerHTML = `
                <span>Tổng số nhân viên: <strong>${totalEmployees}</strong></span>
                <span>Điểm trung bình: <strong>${averageScore}/10</strong></span>
            `;
        }

        // Chuyển tab
        function switchTab(tabName) {
            // Cập nhật active tab
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.closest('.tab').classList.add('active');
            
            // Có thể thêm logic load dữ liệu theo phòng ban ở đây
            console.log('Switched to tab:', tabName);
        }

        // Cache dữ liệu để tránh load lại
        let cachedData = null;
        let lastLoadTime = 0;
        const CACHE_DURATION = 30000; // 30 giây

        // Hàm làm mới dữ liệu
        function refreshData() {
            console.log('Refreshing data...');
            cachedData = null; // Clear cache
            lastLoadTime = 0;
            loadSampleData();
        }

        // Load dữ liệu nhanh - tối ưu hóa
        async function loadSampleData() {
            console.log('Loading sample data for testing');
            console.log('Current URL:', window.location.href);
            
            // Kiểm tra cache
            const now = Date.now();
            if (cachedData && (now - lastLoadTime) < CACHE_DURATION) {
                console.log('Using cached data');
                renderEvaluationTable(cachedData);
                updateSummaryStats(cachedData);
                return;
            }
            
            // Hiển thị loading indicator
            showLoading();
            
            try {
                // Load tất cả dữ liệu cần thiết song song
                const [nhanVienResponse, quanLyResponse, doanhSoResponse, danhGiaResponse] = await Promise.all([
                    fetch('/doanqlns/index.php/api/nhanvien'),
                    fetch('/doanqlns/index.php/api/quan-ly-nghi-phep?nam=2025'),
                    fetch('/doanqlns/api/doanh-so-thang.php'),
                    fetch('/doanqlns/index.php/api/danhgia/all') // API mới để lấy tất cả điểm đánh giá
                ]);
                
                const nhanVienData = await nhanVienResponse.json();
                const quanLyData = await quanLyResponse.json();
                const doanhSoData = await doanhSoResponse.json();
                const danhGiaData = await danhGiaResponse.json();
                
                console.log('Sample data - Nhan vien:', nhanVienData);
                console.log('Sample data - Quan ly:', quanLyData);
                console.log('Sample data - Doanh so:', doanhSoData);
                console.log('Sample data - Danh gia:', danhGiaData);
                console.log('Nhan vien data length:', Array.isArray(nhanVienData) ? nhanVienData.length : 'Not an array');
                
                if (Array.isArray(nhanVienData) && nhanVienData.length > 0) {
                    // Tạo dữ liệu cho tất cả nhân viên - không cần async/await
                    const allEmployeeData = nhanVienData.map(nv => {
                        // Tìm điểm chuyên cần từ API quan ly nghi phep
                        let chuyenCanScore = 5.0; // Mặc định
                        if (quanLyData.success && quanLyData.data) {
                            const employeeData = quanLyData.data.find(item => item.id == nv.id_nhan_vien);
                            if (employeeData && employeeData.diem) {
                                chuyenCanScore = parseFloat(employeeData.diem) || 5.0;
                            }
                        }
                        
                        // Điểm hiệu quả - ưu tiên cong_diem_danh_gia cho phòng kinh doanh
                        let hieuQuaScore = 0.0;
                        
                        // Nếu là phòng ban Kinh doanh, ưu tiên dùng cong_diem_danh_gia từ doanh_so_thang
                        if (nv.ten_phong_ban && nv.ten_phong_ban.toLowerCase().includes('kinh doanh')) {
                            console.log(`=== DEBUG CHO ${nv.ho_ten} (ID: ${nv.id_nhan_vien}) ===`);
                            console.log('Phòng ban:', nv.ten_phong_ban);
                            console.log('doanhSoData:', doanhSoData);
                            
                            const doanhSoArray = doanhSoData.success ? doanhSoData.data : (Array.isArray(doanhSoData) ? doanhSoData : []);
                            console.log('doanhSoArray length:', doanhSoArray.length);
                            
                            const doanhSoEmployee = doanhSoArray.find(ds => ds.id_nhan_vien == nv.id_nhan_vien);
                            console.log('doanhSoEmployee:', doanhSoEmployee);
                            
                            if (doanhSoEmployee && doanhSoEmployee.cong_diem_danh_gia) {
                                hieuQuaScore = parseFloat(doanhSoEmployee.cong_diem_danh_gia) || 0.0;
                                console.log(`✅ Sử dụng cong_diem_danh_gia cho nhân viên ${nv.ho_ten} (${nv.ten_phong_ban}): ${hieuQuaScore}`);
                            } else {
                                console.log(`❌ Không tìm thấy cong_diem_danh_gia cho ${nv.ho_ten}`);
                            }
                        } else {
                            // Các phòng ban khác lấy từ database
                            if (danhGiaData.success && danhGiaData.data) {
                                const danhGiaEmployee = danhGiaData.data.find(dg => dg.id_nhan_vien == nv.id_nhan_vien);
                                if (danhGiaEmployee && danhGiaEmployee.diem_hieu_qua !== null) {
                                    hieuQuaScore = parseFloat(danhGiaEmployee.diem_hieu_qua) || 0.0;
                                }
                            }
                        }
                        
                        // Điểm thái độ - ưu tiên từ database
                        let thaiDoScore = 0.0;
                        if (danhGiaData.success && danhGiaData.data) {
                            const danhGiaEmployee = danhGiaData.data.find(dg => dg.id_nhan_vien == nv.id_nhan_vien);
                            if (danhGiaEmployee && danhGiaEmployee.diem_thai_do !== null) {
                                thaiDoScore = parseFloat(danhGiaEmployee.diem_thai_do) || 0.0;
                            }
                        }
                        
                        // Nếu không có điểm từ database, dùng random
                        if (thaiDoScore === 0.0) {
                            thaiDoScore = Math.random() * 4 + 6;
                        }
                        
                        // Tổng điểm (thang 10) - ưu tiên từ database
                        let totalScore = (chuyenCanScore * 0.4) + (hieuQuaScore * 0.4) + (thaiDoScore * 0.2);
                        let xepLoai = getXepLoai(totalScore);
                        
                        // Kiểm tra xem có đánh giá trong database không
                        if (danhGiaData.success && danhGiaData.data) {
                            const danhGiaEmployee = danhGiaData.data.find(dg => dg.id_nhan_vien == nv.id_nhan_vien);
                            if (danhGiaEmployee) {
                                // Nếu có tong_diem trong database, sử dụng nó
                                if (danhGiaEmployee.tong_diem !== null && danhGiaEmployee.tong_diem !== undefined) {
                                    totalScore = parseFloat(danhGiaEmployee.tong_diem) || totalScore;
                                    xepLoai = danhGiaEmployee.xep_loai || xepLoai;
                                }
                            }
                        }
                        
                        return {
                            idDanhGia: nv.id_nhan_vien, // Sử dụng id_nhan_vien làm idDanhGia tạm thời
                            id_nhan_vien: nv.id_nhan_vien,
                            ho_ten: nv.ho_ten,
                            ten_phong_ban: nv.ten_phong_ban || 'Chưa phân loại',
                            chuyenCanScore: chuyenCanScore,
                            hieuQuaScore: hieuQuaScore,
                            thaiDoScore: thaiDoScore,
                            totalScore: totalScore,
                            xepLoai: xepLoai,
                            trangThai: 'Nháp'
                        };
                    });
                    
                    console.log('Generated data for all employees:', allEmployeeData);
                    
                    // Lưu vào cache
                    cachedData = allEmployeeData;
                    lastLoadTime = Date.now();
                    
                    renderEvaluationTable(allEmployeeData);
                    updateSummaryStats(allEmployeeData);
                } else {
                    console.log('No employee data found, using fallback');
                    // Fallback về dữ liệu mẫu cũ
                    const sampleData = [
                        {
                            id_nhan_vien: 1,
                            ho_ten: 'Lương Ngọc Thật',
                            ten_phong_ban: 'Nhân sự',
                            chuyenCanScore: 9.5,
                            hieuQuaScore: 0.0,
                            thaiDoScore: 8.3,
                            totalScore: 5.5,
                            xepLoai: 'Cần cải thiện',
                            trangThai: 'Nháp'
                        },
                        {
                            id_nhan_vien: 6,
                            ho_ten: 'Nguyễn Thị Fleur',
                            ten_phong_ban: 'Nhân sự',
                            chuyenCanScore: 10.0,
                            hieuQuaScore: 0.0,
                            thaiDoScore: 8.6,
                            totalScore: 5.7,
                            xepLoai: 'Cần cải thiện',
                            trangThai: 'Nháp'
                        }
                    ];
                    
                    renderEvaluationTable(sampleData);
                    updateSummaryStats(sampleData);
                }
            } catch (error) {
                console.error('Error loading sample data:', error);
                // Fallback về dữ liệu mẫu cũ
                const sampleData = [
                    {
                        id_nhan_vien: 1,
                        ho_ten: 'Lương Ngọc Thật',
                        ten_phong_ban: 'Nhân sự',
                        chuyenCanScore: 9.5,
                        hieuQuaScore: 0.0,
                        thaiDoScore: 8.3,
                        totalScore: 5.5,
                        xepLoai: 'Cần cải thiện',
                        trangThai: 'Nháp'
                    }
                ];
                
                renderEvaluationTable(sampleData);
                updateSummaryStats(sampleData);
            } finally {
                // Ẩn loading indicator
                hideLoading();
            }
        }

        // Render select nhân viên
        function renderEmployeeSelect() {
            const select = document.getElementById('selectEmployee');
            select.innerHTML = '<option value="">Chọn nhân viên</option>';
            nhanVienData.forEach(nv => {
                const option = document.createElement('option');
                option.value = nv.id_nhan_vien;
                option.textContent = `${nv.ho_ten} - ${nv.ten_phong_ban || 'N/A'}`;
                select.appendChild(option);
            });
        }

        // Render container tiêu chí
        function renderCriteriaContainer() {
            const container = document.getElementById('criteriaContainer');
            container.innerHTML = '';

            // Nhóm tiêu chí theo loại
            const groupedCriteria = {};
            tieuChiData.forEach(criteria => {
                if (!groupedCriteria[criteria.loai_tieu_chi]) {
                    groupedCriteria[criteria.loai_tieu_chi] = [];
                }
                groupedCriteria[criteria.loai_tieu_chi].push(criteria);
            });

            // Render từng nhóm
            Object.keys(groupedCriteria).forEach(loai => {
                const groupDiv = document.createElement('div');
                groupDiv.className = 'criteria-group';
                groupDiv.innerHTML = `
                    <div class="criteria-title">${loai}</div>
                    ${groupedCriteria[loai].map(criteria => `
                        <div class="criteria-item">
                            <div>
                                <div class="criteria-name">${criteria.ten_tieu_chi}</div>
                                <div class="criteria-weight">Thang điểm: 0 - 10</div>
                            </div>
                            <input type="number" class="rating-input" 
                                   id="criteria_${criteria.id_tieu_chi}"
                                   min="0" max="10" 
                                   step="0.1" placeholder="0-10">
                        </div>
                    `).join('')}
                `;
                container.appendChild(groupDiv);
            });
        }

        // TÍNH ĐIỂM SỐ NGÀY ĐI LÀM (0-10) theo dữ liệu nghỉ phép/chấm công
        async function computeAttendanceScore(employeeId, year) {
            const [nghiPhepResp, chamCongResp] = await Promise.all([
                fetch('/doanqlns/index.php/api/nghiphep'),
                fetch(`/doanqlns/index.php/api/chamcong?nam=${year}`)
            ]);
            const nghiPhepData = await jsonSafeArray(nghiPhepResp);
            const chamCongData = await jsonSafeArray(chamCongResp);

            const soNgayNghi = calcLeaveDaysByYearForEmployee(nghiPhepData, employeeId, year);
            const leaveTypes = calcLeaveTypesFromChamCongForEmployee(chamCongData, employeeId, year);
            const { diTre, raSom } = calcLateEarlyForEmployee(chamCongData, employeeId, year);

            const truNghiQuaPhep = Math.max(0, (soNgayNghi - 12)) * 0.5;
            const truNghiNuaBuoi = Math.floor((leaveTypes.nghiNuaBuoi || 0) / 2) * 0.5;
            const truDiTre = Math.floor(diTre / 4) * 0.5;
            const truRaSom = Math.floor(raSom / 4) * 0.5;
            const truKhongPhep = (leaveTypes.khongPhep || 0) * 1.0;
            let diem = 10 - (truNghiQuaPhep + truNghiNuaBuoi + truDiTre + truRaSom + truKhongPhep);
            if (diem < 0) diem = 0; if (diem > 10) diem = 10;
            return diem;
        }

        async function jsonSafeArray(resp) {
            const t = await resp.text();
            try { const j = JSON.parse(t); return Array.isArray(j) ? j : []; } catch { return []; }
        }

        function calcLeaveDaysByYearForEmployee(records, employeeId, year) {
            let days = 0;
            records.forEach(r => {
                if (r.id_nhan_vien != employeeId) return;
                const start = new Date(r.ngay_bat_dau); const end = new Date(r.ngay_ket_thuc);
                const yearStart = new Date(year, 0, 1); const yearEnd = new Date(year, 11, 31);
                let d = new Date(Math.max(start, yearStart)); const last = new Date(Math.min(end, yearEnd));
                while (d <= last) { if (d.getDay() !== 0) days++; d.setDate(d.getDate() + 1); }
            });
            return days;
        }

        function calcLeaveTypesFromChamCongForEmployee(chamCongData, employeeId, year) {
            const res = { nghiNuaBuoi: 0, coPhep: 0, khongPhep: 0 };
            chamCongData.forEach(rec => {
                if (rec.id_nhan_vien != employeeId) return;
                const d = new Date(rec.ngay_lam_viec || rec.ngay_cham_cong);
                if (d.getFullYear() !== year || d.getDay() === 0) return;
                const st = rec.trang_thai || '';
                if (st === 'Nghỉ nữa buổi') res.nghiNuaBuoi++;
                else if (st === 'Có phép') res.coPhep++;
                else if (st === 'Không phép') res.khongPhep++;
            });
            return res;
        }

        function calcLateEarlyForEmployee(chamCongData, employeeId, year) {
            let diTre = 0, raSom = 0;
            chamCongData.forEach(rec => {
                if (rec.id_nhan_vien != employeeId) return;
                const d = new Date(rec.ngay_lam_viec || rec.ngay_cham_cong);
                if (d.getFullYear() !== year || d.getDay() === 0) return;
                const st = rec.trang_thai || '';
                if (st === 'Đi trễ') diTre++;
                if (st === 'Ra sớm') raSom++;
            });
            return { diTre, raSom };
        }

        // Load thống kê
        async function loadStats() {
            const month = document.getElementById('selectMonth').value;
            const year = document.getElementById('selectYear').value;
            
            console.log('Loading stats for month:', month, 'year:', year);
            try {
                // Tính toán thống kê từ dữ liệu đã có
                if (evaluationData && evaluationData.length > 0) {
                    const stats = calculateStats(evaluationData);
                    console.log('Calculated stats:', stats);
                    renderStats(stats);
                } else {
                    // Nếu không có dữ liệu, hiển thị 0
                    renderStats({
                        tong_so_danh_gia: 0,
                        diem_trung_binh: 0,
                        xuat_sac: 0,
                        tot: 0,
                        kha: 0,
                        trung_binh: 0,
                        yeu: 0
                    });
                }
            } catch (error) {
                console.error('Lỗi khi load thống kê:', error);
            }
        }

        // Tính toán thống kê từ dữ liệu
        function calculateStats(data) {
            const tongSo = data.length;
            let tongDiem = 0;
            const xepLoaiCount = {
                'Xuất sắc': 0,
                'Tốt': 0,
                'Khá': 0,
                'Trung bình': 0,
                'Yếu': 0
            };

            data.forEach(eval => {
                const diem = parseFloat(eval.tong_diem) || 0;
                tongDiem += diem;
                
                // Xác định xếp loại dựa trên điểm
                let xepLoai = 'Yếu';
                if (diem >= 90) xepLoai = 'Xuất sắc';
                else if (diem >= 75) xepLoai = 'Tốt';
                else if (diem >= 60) xepLoai = 'Khá';
                else if (diem >= 50) xepLoai = 'Trung bình';
                
                xepLoaiCount[xepLoai]++;
            });

            return {
                tong_so_danh_gia: tongSo,
                diem_trung_binh: tongSo > 0 ? tongDiem / tongSo : 0,
                xuat_sac: xepLoaiCount['Xuất sắc'],
                tot: xepLoaiCount['Tốt'],
                kha: xepLoaiCount['Khá'],
                trung_binh: xepLoaiCount['Trung bình'],
                yeu: xepLoaiCount['Yếu']
            };
        }

        // Render thống kê
        function renderStats(stats) {
            console.log('Rendering stats:', stats);
            const container = document.getElementById('statsContainer');
            if (!container) {
                console.error('Element statsContainer not found!');
                return;
            }
            container.innerHTML = `
                <div class="stat-card">
                    <div class="stat-number">${stats.tong_so_danh_gia || 0}</div>
                    <div class="stat-label">Tổng đánh giá</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${(parseFloat(stats.diem_trung_binh) || 0).toFixed(1)}</div>
                    <div class="stat-label">Điểm trung bình</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.xuat_sac || 0}</div>
                    <div class="stat-label">Xuất sắc</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.tot || 0}</div>
                    <div class="stat-label">Tốt</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.kha || 0}</div>
                    <div class="stat-label">Khá</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.trung_binh || 0}</div>
                    <div class="stat-label">Trung bình</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">${stats.yeu || 0}</div>
                    <div class="stat-label">Yếu</div>
                </div>
            `;
        }

        // Hiển thị modal chuyên cần
        function showAttendanceModal() {
            document.getElementById('attendanceModal').style.display = 'flex';
            loadAttendanceData();
        }
        
        // Đóng modal chuyên cần
        function closeAttendanceModal() {
            document.getElementById('attendanceModal').style.display = 'none';
        }
        
        // Load dữ liệu chuyên cần
        async function loadAttendanceData() {
            const month = document.getElementById('attendanceMonth').value;
            const year = document.getElementById('attendanceYear').value;
            
            showLoading();
            try {
                // Load dữ liệu nhân viên
                const nhanVienResponse = await fetch('/doanqlns/index.php/api/users');
                const nhanVienData = await nhanVienResponse.json();
                
                // Load dữ liệu điểm từ "Quản lý nghỉ phép"
                const quanLyNghiPhepResponse = await fetch(`/doanqlns/index.php/api/quan-ly-nghi-phep?nam=${year}`);
                let quanLyNghiPhepData = [];
                try {
                    const quanLyResult = await quanLyNghiPhepResponse.json();
                    
                    if (quanLyResult.success && Array.isArray(quanLyResult.data)) {
                        quanLyNghiPhepData = quanLyResult.data;
                    } else if (Array.isArray(quanLyResult)) {
                        quanLyNghiPhepData = quanLyResult;
                    }
                } catch (e) {
                    console.warn('Không thể load dữ liệu quản lý nghỉ phép:', e);
                }
                
                // Load dữ liệu doanh số tháng
                const doanhSoResponse = await fetch(`/doanqlns/index.php/api/doanh-so-thang?thang=${month}&nam=${year}`);
                let doanhSoData = [];
                try {
                    const doanhSoResult = await doanhSoResponse.json();
                    if (doanhSoResult.success && Array.isArray(doanhSoResult.data)) {
                        doanhSoData = doanhSoResult.data;
                    } else if (Array.isArray(doanhSoResult)) {
                        doanhSoData = doanhSoResult;
                    }
                } catch (e) {
                    console.warn('Không thể load dữ liệu doanh số:', e);
                }
                
                // Tính toán điểm chuyên cần cho từng nhân viên
                const attendanceData = await calculateAttendanceScores(nhanVienData, quanLyArray, doanhSoData, month, year);
                
                renderAttendanceTable(attendanceData);
            } catch (error) {
                console.error('Lỗi khi load dữ liệu chuyên cần:', error);
                alert('Lỗi khi load dữ liệu chuyên cần: ' + error.message);
            } finally {
                hideLoading();
            }
        }
        
        // Tính toán điểm chuyên cần
        async function calculateAttendanceScores(nhanVienData, quanLyArray, doanhSoData, month, year) {
            console.log('Starting calculateAttendanceScores');
            console.log('nhanVienData:', nhanVienData);
            console.log('quanLyArray:', quanLyArray);
            console.log('doanhSoData:', doanhSoData);
            
            const results = [];
            
            if (!Array.isArray(nhanVienData)) {
                console.error('nhanVienData is not an array:', nhanVienData);
                return results;
            }
            
            for (const nv of nhanVienData) {
                const employeeId = nv.id_nhan_vien;
                console.log('Processing employee:', employeeId, nv.ho_ten);
                
                // Lấy điểm chuyên cần (40%) từ "Quản lý nghỉ phép" - sử dụng cột "Điểm"
                console.log('Getting chuyen can score for employee:', employeeId, 'from quanLyArray:', quanLyArray);
                const chuyenCanScore = getChuyenCanScoreFromQuanLy(employeeId, quanLyArray);
                
                // Tính điểm hiệu quả công việc (40%) - chỉ cho phòng ban Kinh doanh
                const hieuQuaScore = calculateHieuQuaScore(employeeId, doanhSoData, nv.ten_phong_ban);
                
                // Tính điểm thái độ & hợp tác (20%) - tạm thời random, sẽ load từ CSV sau
                const thaiDoScore = calculateThaiDoScore(employeeId);
                
                // Tổng điểm
                const totalScore = (chuyenCanScore * 0.4) + (hieuQuaScore * 0.4) + (thaiDoScore * 0.2);
                
                console.log('Scores for employee', employeeId, ':', {
                    chuyenCanScore,
                    hieuQuaScore,
                    thaiDoScore,
                    totalScore
                });
                
                results.push({
                    id_nhan_vien: employeeId,
                    ho_ten: nv.ho_ten,
                    ten_phong_ban: nv.ten_phong_ban || 'N/A',
                    chuyenCanScore: chuyenCanScore,
                    hieuQuaScore: hieuQuaScore,
                    thaiDoScore: thaiDoScore,
                    totalScore: totalScore,
                    xepLoai: getXepLoai(totalScore)
                });
            }
            
            console.log('Final results:', results);
            return results;
        }
        
        // Lấy điểm chuyên cần từ "Quản lý nghỉ phép" - sử dụng cột "Điểm"
        function getChuyenCanScoreFromQuanLy(employeeId, quanLyArray) {
            console.log('Getting chuyen can score for employee:', employeeId);
            console.log('Quan ly array:', quanLyArray);
            console.log('Quan ly array type:', typeof quanLyArray);
            console.log('Quan ly array is array:', Array.isArray(quanLyArray));
            
            // Kiểm tra nếu quanLyArray không phải array
            if (!Array.isArray(quanLyArray)) {
                console.warn('quanLyArray không phải array:', quanLyArray);
                return 5; // Điểm trung bình nếu không có dữ liệu
            }
            
            // Tìm dữ liệu của nhân viên trong "Quản lý nghỉ phép"
            const employeeData = quanLyArray.find(item => item.id == employeeId);
            console.log('Found employee data:', employeeData);
            
            if (!employeeData || !employeeData.diem) {
                console.warn(`Không tìm thấy điểm cho nhân viên ID ${employeeId}`);
                return 5; // Điểm trung bình nếu không có dữ liệu
            }
            
            // Trả về điểm đã được tính sẵn từ "Quản lý nghỉ phép"
            const score = parseFloat(employeeData.diem) || 5;
            console.log('Chuyen can score for employee', employeeId, ':', score);
            return score;
        }

        // Tính điểm chuyên cần theo quý từ dữ liệu chấm công: 10 điểm gốc, trừ theo quy tắc tương tự quản lý nghỉ phép
        function computeQuarterAttendanceScoreMap(chamCongData, startMonth, endMonth, year) {
            const byEmp = new Map();
            if (!Array.isArray(chamCongData)) return byEmp;
            const rangeStart = new Date(year, startMonth - 1, 1);
            const rangeEnd = new Date(year, endMonth, 0);

            // Gom số lần theo trạng thái
            const counter = new Map(); // id -> {nghiNuaBuoi, coPhep, khongPhep, diTre, raSom}
            chamCongData.forEach(rec => {
                const d = new Date(rec.ngay_lam_viec);
                if (isNaN(d.getTime())) return;
                if (d < rangeStart || d > rangeEnd) return;
                if (d.getDay() === 0) return; // bỏ Chủ nhật
                const id = rec.id_nhan_vien;
                if (!counter.has(id)) counter.set(id, { nghiNuaBuoi: 0, coPhep: 0, khongPhep: 0, diTre: 0, raSom: 0 });
                const c = counter.get(id);
                const status = rec.trang_thai || '';
                if (status === 'Nghỉ nữa buổi') c.nghiNuaBuoi++;
                else if (status === 'Có phép' || status === 'Phép Năm' || status === 'Nghỉ Lễ') c.coPhep++;
                else if (status === 'Không phép') c.khongPhep++;
                else if (status === 'Đi trễ') c.diTre++;
                else if (status === 'Ra sớm') c.raSom++;
            });

            // Quy tắc trừ điểm giống phần quản lý nghỉ phép
            counter.forEach((c, id) => {
                const truNghiNuaBuoi = Math.floor((c.nghiNuaBuoi || 0) / 2) * 0.5;
                const truDiTre = Math.floor((c.diTre || 0) / 4) * 0.5;
                const truRaSom = Math.floor((c.raSom || 0) / 4) * 0.5;
                const truKhongPhep = (c.khongPhep || 0) * 1.0;
                let score = 10 - (truNghiNuaBuoi + truDiTre + truRaSom + truKhongPhep);
                if (score < 0) score = 0;
                if (score > 10) score = 10;
                byEmp.set(id, score);
            });

            return byEmp;
        }
        
        // Tính điểm hiệu quả công việc từ doanh số
        function calculateHieuQuaScore(employeeId, doanhSoData, phongBan) {
            // Kiểm tra nếu doanhSoData không phải array
            if (!Array.isArray(doanhSoData)) {
                console.warn('doanhSoData không phải array:', doanhSoData);
                return 0;
            }
            
            const employeeDoanhSo = doanhSoData.find(ds => ds.id_nhan_vien == employeeId);
            
            // Nếu là phòng ban Kinh doanh, ưu tiên dùng cong_diem_danh_gia
            if (phongBan && phongBan.toLowerCase().includes('kinh doanh')) {
                if (employeeDoanhSo && employeeDoanhSo.cong_diem_danh_gia) {
                    const score = parseFloat(employeeDoanhSo.cong_diem_danh_gia) || 0;
                    console.log(`Điểm hiệu quả từ cong_diem_danh_gia cho nhân viên ${employeeId} (${phongBan}): ${score}`);
                    return score;
                } else {
                    console.log(`Không tìm thấy cong_diem_danh_gia cho nhân viên ${employeeId} thuộc phòng ban ${phongBan}`);
                    return 0;
                }
            } else {
                // Các phòng ban khác không có điểm hiệu quả từ doanh số
                console.log(`Phòng ban ${phongBan} không có điểm hiệu quả từ doanh số`);
                return 0;
            }
        }
        
        // Tính điểm thái độ & hợp tác (tạm thời random)
        function calculateThaiDoScore(employeeId) {
            // Tạm thời random từ 6-10, sẽ load từ CSV sau
            return Math.random() * 4 + 6;
        }
        
        // Xác định xếp loại
        function getXepLoai(diem) {
            if (diem >= 9) return 'Xuất sắc';
            if (diem >= 8) return 'Tốt';
            if (diem >= 7) return 'Khá';
            if (diem >= 6) return 'Đạt';
            return 'Cần cải thiện';
        }
        
        // Render bảng chuyên cần
        function renderAttendanceTable(data) {
            const container = document.getElementById('attendanceTableContainer');
            container.innerHTML = `
                <div class="attendance-summary">
                    <h4><i class="fas fa-chart-bar"></i> Tổng quan đánh giá chuyên cần</h4>
                    <p>Tổng số nhân viên: <strong>${data.length}</strong> | 
                       Điểm trung bình: <strong>${(data.reduce((sum, item) => sum + item.tong_diem, 0) / data.length).toFixed(2)}/10</strong></p>
                </div>
                
                <div class="table-responsive">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-user"></i> Nhân viên</th>
                                <th><i class="fas fa-building"></i> Phòng ban</th>
                                <th><i class="fas fa-calendar-check"></i> Chuyên cần<br><small>(40%)</small></th>
                                <th><i class="fas fa-chart-line"></i> Hiệu quả<br><small>(40%)</small></th>
                                <th><i class="fas fa-handshake"></i> Thái độ<br><small>(20%)</small></th>
                                <th><i class="fas fa-star"></i> Tổng điểm</th>
                                <th><i class="fas fa-trophy"></i> Xếp loại</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map((item, index) => `
                                <tr class="attendance-row">
                                    <td class="employee-info">
                                        <div class="employee-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="employee-details">
                                            <strong>${item.ho_ten}</strong>
                                            <small>ID: ${item.id_nhan_vien}</small>
                                        </div>
                                    </td>
                                    <td class="department-info">
                                        <span class="department-badge">${item.ten_phong_ban}</span>
                                    </td>
                                    <td class="score-cell chuyen-can">
                                        <div class="score-display">
                                            <span class="score-number">${item.chuyen_can.toFixed(1)}</span>
                                            <span class="score-max">/10</span>
                                        </div>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: ${(item.chuyen_can / 10) * 100}%"></div>
                                        </div>
                                    </td>
                                    <td class="score-cell hieu-qua">
                                        <div class="score-display">
                                            <span class="score-number">${item.hieu_qua.toFixed(1)}</span>
                                            <span class="score-max">/10</span>
                                        </div>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: ${(item.hieu_qua / 10) * 100}%"></div>
                                        </div>
                                    </td>
                                    <td class="score-cell thai-do">
                                        <div class="score-display">
                                            <span class="score-number">${item.thai_do.toFixed(1)}</span>
                                            <span class="score-max">/10</span>
                                        </div>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: ${(item.thai_do / 10) * 100}%"></div>
                                        </div>
                                    </td>
                                    <td class="total-score">
                                        <div class="total-display">
                                            <span class="total-number">${item.tong_diem.toFixed(1)}</span>
                                            <span class="total-max">/100</span>
                                        </div>
                                        <div class="total-bar">
                                            <div class="total-fill" style="width: ${item.tong_diem}%"></div>
                                        </div>
                                    </td>
                                    <td class="rating-cell">
                                        <span class="rating-badge rating-${getRatingClass(item.xep_loai)}">
                                            ${item.xep_loai}
                                        </span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div class="attendance-legend">
                    <h5><i class="fas fa-info-circle"></i> Chú thích:</h5>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="legend-color chuyen-can"></span>
                            <span>Chuyên cần: Dựa trên dữ liệu nghỉ phép (Quản lý nghỉ phép)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color hieu-qua"></span>
                            <span>Hiệu quả: Dựa trên doanh số và hoa hồng (KPI)</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color thai-do"></span>
                            <span>Thái độ: Đánh giá từ phòng ban (CSV)</span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Tạo đánh giá từ dữ liệu chuyên cần
        async function generateAttendanceEvaluation() {
            if (!confirm('Bạn có chắc chắn muốn tạo đánh giá chuyên cần cho tất cả nhân viên?')) return;
            
            showLoading();
            try {
                const month = document.getElementById('attendanceMonth').value;
                const year = document.getElementById('attendanceYear').value;
                
                // Load lại dữ liệu và tạo đánh giá
                const nhanVienResponse = await fetch('/doanqlns/index.php/api/users');
                const nhanVienData = await nhanVienResponse.json();
                
                const quanLyNghiPhepResponse = await fetch(`/doanqlns/index.php/api/quan-ly-nghi-phep?nam=${year}`);
                const quanLyNghiPhepData = await quanLyNghiPhepResponse.json();
                
                const doanhSoResponse = await fetch(`/doanqlns/index.php/api/doanh-so-thang?thang=${month}&nam=${year}`);
                const doanhSoData = await doanhSoResponse.json();
                
                const attendanceData = await calculateAttendanceScores(nhanVienData, quanLyArray, doanhSoData, month, year);
                
                // Tạo đánh giá cho từng nhân viên
                for (const item of attendanceData) {
                    await createAttendanceEvaluation(item, month, year);
                }
                
                alert('Tạo đánh giá chuyên cần thành công!');
                closeAttendanceModal();
                loadEvaluationData();
            } catch (error) {
                console.error('Lỗi khi tạo đánh giá chuyên cần:', error);
                alert('Lỗi khi tạo đánh giá chuyên cần: ' + error.message);
            } finally {
                hideLoading();
            }
        }
        
        // Tạo đánh giá cho một nhân viên
        async function createAttendanceEvaluation(item, month, year) {
            const diemTieuChi = {
                'chuyen_can': item.chuyen_can,
                'hieu_qua': item.hieu_qua,
                'thai_do': item.thai_do
            };
            
            const response = await fetch('/doanqlns/index.php/api/danhgia', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_nhan_vien: item.id_nhan_vien,
                    thang: parseInt(month),
                    nam: parseInt(year),
                    diem_tieu_chi: diemTieuChi,
                    ghi_chu: 'Đánh giá chuyên cần tự động',
                    trang_thai: 'Nháp'
                })
            });
            
            return response.json();
        }

        // Tự động điền khi chọn nhân viên hoặc đổi năm
        async function autofillAttendanceScore() {
            const idNhanVien = document.getElementById('selectEmployee').value;
            const year = parseInt(document.getElementById('evalYear').value) || new Date().getFullYear();
            if (!idNhanVien) return;
            try {
                const diem = await computeAttendanceScore(parseInt(idNhanVien), year);
                const target = tieuChiData.find(c => (c.ten_tieu_chi || '').toLowerCase().includes('số ngày đi làm'));
                if (target) {
                    const input = document.getElementById(`criteria_${target.id_tieu_chi}`);
                    if (input) input.value = (Math.round(diem * 10) / 10).toString();
                }
            } catch (e) {
                console.warn('Không thể tự động điền điểm Số ngày đi làm:', e);
            }
        }

        // Đóng modal thêm đánh giá
        function closeAddEvaluationModal() {
            document.getElementById('addEvaluationModal').style.display = 'none';
            // Reset form
            document.getElementById('selectEmployee').value = '';
            document.getElementById('evalNote').value = '';
            // Reset tất cả input điểm
            tieuChiData.forEach(criteria => {
                document.getElementById(`criteria_${criteria.id_tieu_chi}`).value = '';
            });
        }

        // Lưu đánh giá
        async function saveEvaluation() {
            const idNhanVien = document.getElementById('selectEmployee').value;
            const thang = document.getElementById('evalMonth').value;
            const nam = document.getElementById('evalYear').value;
            const ghiChu = document.getElementById('evalNote').value;

            if (!idNhanVien) {
                alert('Vui lòng chọn nhân viên');
                return;
            }

            // Thu thập điểm từ các tiêu chí
            const diemTieuChi = {};
            let hasError = false;
            
            tieuChiData.forEach(criteria => {
                const input = document.getElementById(`criteria_${criteria.id_tieu_chi}`);
                const diem = parseFloat(input.value);
                if (isNaN(diem) || diem < 0 || diem > 10) {
                    alert(`Điểm cho "${criteria.ten_tieu_chi}" phải từ 0 đến 10`);
                    hasError = true;
                    return;
                }
                
                diemTieuChi[criteria.id_tieu_chi] = diem;
            });

            if (hasError) return;

            // Lấy điểm chuyên cần từ quản lý nghỉ phép
            const chuyenCanScore = getChuyenCanScoreFromQuanLy(parseInt(idNhanVien), quanLyArray);
            console.log('Saving evaluation with chuyen can score:', chuyenCanScore);

            showLoading();
            try {
                const response = await fetch('/doanqlns/index.php/api/danhgia', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_nhan_vien: parseInt(idNhanVien),
                        thang: parseInt(thang),
                        nam: parseInt(nam),
                        diem_tieu_chi: diemTieuChi,
                        diem_chuyen_can: chuyenCanScore,
                        ghi_chu: ghiChu,
                        trang_thai: 'Nháp'
                    })
                });

                const responseText = await response.text();
                console.log('Response text:', responseText);
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON Parse Error:', parseError);
                    console.error('Response text:', responseText);
                    throw new Error("Phản hồi không phải JSON hợp lệ: " + responseText);
                }
                
                if (data.success) {
                    alert('Thêm đánh giá thành công!');
                    closeAddEvaluationModal();
                    loadEvaluationData();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Lỗi khi lưu đánh giá:', error);
                alert('Lỗi khi lưu đánh giá: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Xem chi tiết đánh giá
        async function viewEvaluation(idDanhGia) {
            showLoading();
            try {
                const response = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}/chi-tiet`);
                const data = await response.json();
                if (data.success) {
                    renderViewEvaluationModal(data.data);
                    document.getElementById('viewEvaluationModal').style.display = 'flex';
                }
            } catch (error) {
                console.error('Lỗi khi xem chi tiết:', error);
                alert('Lỗi khi xem chi tiết: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Render modal xem chi tiết
        function renderViewEvaluationModal(chiTiet) {
            console.log('Rendering view evaluation modal with data:', chiTiet);
            const content = document.getElementById('viewEvaluationContent');
            content.innerHTML = `
                <div class="criteria-group">
                    <div class="criteria-title">Chi tiết điểm đánh giá</div>
                    ${chiTiet.map(item => {
                        const diem10 = parseFloat(item.diem_so) || 0;
                        return `
                        <div class="criteria-item">
                            <div>
                                <div class="criteria-name">${item.ten_tieu_chi}</div>
                                <div class="criteria-weight">Thang điểm: 0 - 10</div>
                            </div>
                            <div>
                                <strong>${diem10.toFixed(2)}/10</strong>
                                <div style="font-size: 12px; color: #6c757d;">
                                    Điểm tính trực tiếp
                                </div>
                            </div>
                        </div>
                    `;
                    }).join('')}
                </div>
            `;
        }

        // Đóng modal xem chi tiết
        function closeViewEvaluationModal() {
            document.getElementById('viewEvaluationModal').style.display = 'none';
        }

        // Sửa đánh giá
        function editEvaluation(idDanhGia) {
            console.log('Editing evaluation:', idDanhGia);
            // Tìm đánh giá cần sửa
            const evaluation = evaluationData.find(eval => eval.id_danh_gia == idDanhGia);
            if (!evaluation) {
                alert('Không tìm thấy đánh giá cần sửa');
                return;
            }

            // Điền dữ liệu vào form
            document.getElementById('selectEmployee').value = evaluation.id_nhan_vien;
            document.getElementById('evalMonth').value = evaluation.thang_danh_gia;
            document.getElementById('evalYear').value = evaluation.nam_danh_gia;
            document.getElementById('evalNote').value = evaluation.ghi_chu || '';

            // Load chi tiết điểm để điền vào form
            loadEvaluationDetails(idDanhGia);

            // Hiển thị modal
            document.getElementById('addEvaluationModal').style.display = 'flex';
        }

        // Load chi tiết điểm cho form sửa
        async function loadEvaluationDetails(idDanhGia) {
            try {
                const response = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}/chi-tiet`);
                const data = await response.json();
                if (data.success) {
                    // Điền điểm vào các input
                    data.data.forEach(item => {
                        const input = document.getElementById(`criteria_${item.id_tieu_chi}`);
                        if (input) {
                            input.value = item.diem_so;
                        }
                    });
                }
            } catch (error) {
                console.error('Lỗi khi load chi tiết đánh giá:', error);
            }
        }

        // Xóa đánh giá
        async function deleteEvaluation(idDanhGia) {
            if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) return;

            showLoading();
            try {
                const response = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                if (data.success) {
                    alert('Xóa đánh giá thành công!');
                    loadEvaluationData();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Lỗi khi xóa đánh giá:', error);
                alert('Lỗi khi xóa đánh giá: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Duyệt đánh giá
        async function approveEvaluation(idDanhGia) {
            if (!confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')) return;

            showLoading();
            try {
                const response = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}/trang-thai`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        trang_thai: 'Đã duyệt'
                    })
                });
                const data = await response.json();
                if (data.success) {
                    alert('Duyệt đánh giá thành công!');
                    loadEvaluationData();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (error) {
                console.error('Lỗi khi duyệt đánh giá:', error);
                alert('Lỗi khi duyệt đánh giá: ' + error.message);
            } finally {
                hideLoading();
            }
        }

        // Utility functions
        function getRatingClass(xepLoai) {
            const classes = {
                'Xuất sắc': 'excellent',
                'Tốt': 'good',
                'Khá': 'fair',
                'Đạt': 'average',
                'Cần cải thiện': 'poor',
                'Chưa xếp loại': 'poor'
            };
            return classes[xepLoai] || 'poor';
        }

        function getStatusText(trangThai) {
            const texts = {
                'Nháp': 'Nháp',
                'Đã duyệt': 'Đã duyệt'
            };
            return texts[trangThai] || trangThai;
        }

        function getStatusClass(trangThai) {
            const classMap = {
                'Nháp': 'draft',
                'Đã duyệt': 'approved',
                'Từ chối': 'rejected'
            };
            return classMap[trangThai] || 'draft';
        }

        // Function thay đổi trạng thái
        async function changeStatus(idDanhGia, currentStatus) {
            console.log('changeStatus called:', { idDanhGia, currentStatus });
            try {
                // Xác định trạng thái mới
                let newStatus;
                if (currentStatus === 'Nháp') {
                    newStatus = 'Đã duyệt';
                } else if (currentStatus === 'Đã duyệt') {
                    newStatus = 'Từ chối';
                } else {
                    newStatus = 'Nháp';
                }
                
                console.log('New status will be:', newStatus);

                // Cập nhật trạng thái
                const apiUrl = `/doanqlns/simple_danhgia_api.php/danhgia/${idDanhGia}/trang-thai`;
                console.log('Sending request to:', apiUrl);
                console.log('Request data:', { trang_thai: newStatus });
                const response = await fetch(apiUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        trang_thai: newStatus
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                const responseText = await response.text();
                console.log('Raw response text:', responseText);
                
                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('Parsed API response:', result);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.log('Response is not valid JSON');
                    return;
                }

                if (result.success) {
                    // Clear cache trước khi reload
                    cachedData = null;
                    lastLoadTime = 0;
                    
                    // Reload dữ liệu
                    await loadEvaluationData();
                    
                    // Nếu trạng thái mới là "Đã duyệt", sync sang Thưởng Tết và Thưởng thường, chỉ hiển thị 1 thông báo
                    if (newStatus === 'Đã duyệt') {
                        const syncedTet = await syncToThuongTet(idDanhGia);
                        const syncedThuong = await syncToThuong(idDanhGia);
                        if (syncedTet || syncedThuong) {
                            alert('Đã duyệt thành công và đã thêm vào Thưởng');
                        } else {
                            alert('Đã duyệt thành công');
                        }
                    }
                    // Không cần thông báo cho các trạng thái khác để tránh nhiều popup
                } else {
                    alert('Lỗi: ' + result.message);
                }
            } catch (error) {
                console.error('Error changing status:', error);
                alert('Lỗi kết nối server');
            }
        }

        // Function sync sang Thưởng Tết (trả về true/false, không hiện alert)
        async function syncToThuongTet(idDanhGia) {
            try {
                console.log('Syncing to Thuong Tet for idDanhGia:', idDanhGia);

                // 1) Thử lấy dữ liệu đánh giá từ API (trình duyệt có session nên không bị 401)
                let danhGia = null;
                try {
                    const apiResp = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}`);
                    if (apiResp.ok) {
                        const apiJson = await apiResp.json();
                        if (apiJson && apiJson.success && apiJson.data) {
                            danhGia = apiJson.data; // kỳ vọng có: id_nhan_vien, nam_danh_gia, tong_diem, xep_loai
                            console.log('Loaded danhGia from API:', danhGia);
                        }
                    } else {
                        console.warn('Fetch danh gia by id failed with status:', apiResp.status);
                    }
                } catch (apiErr) {
                    console.warn('Error fetching danh gia from API, will fallback to cache:', apiErr);
                }

                // 2) Fallback: tìm trong dữ liệu đã load sẵn
                if (!danhGia) {
                    if (cachedData && cachedData.evaluationData) {
                        const cached = cachedData.evaluationData.find(item => item.idDanhGia == idDanhGia);
                        if (cached) {
                            danhGia = {
                                id_nhan_vien: cached.id_nhan_vien,
                                nam_danh_gia: (new Date()).getFullYear(), // nếu không có năm trong cache, dùng năm hiện tại
                                tong_diem: cached.totalScore,
                                xep_loai: cached.xepLoai
                            };
                            console.log('Using cached danhGia data:', danhGia);
                        }
                    }
                }

                if (!danhGia) {
                    console.warn('Không tìm thấy dữ liệu đánh giá để sync sang Thưởng Tết.');
                    return false;
                }

                // 3) Gửi sync sang Thưởng Tết
                const payload = {
                    id_nhan_vien: danhGia.id_nhan_vien,
                    nam: danhGia.nam_danh_gia ? danhGia.nam_danh_gia : (new Date()).getFullYear(),
                    tong_diem: danhGia.tong_diem,
                    xep_loai: danhGia.xep_loai
                };
                console.log('Sync Thuong Tet payload:', payload);

                const syncResponse = await fetch('/doanqlns/simple_thuong_tet_api.php/sync-from-danh-gia', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const syncResult = await syncResponse.json();
                console.log('Sync Thuong Tet result:', syncResult);

                return !!(syncResult && syncResult.success);
            } catch (error) {
                console.error('Error syncing to Thuong Tet:', error);
                return false;
            }
        }

        // Function: sync sang Thuong (thuong.php) dựa trên xếp loại
        async function syncToThuong(idDanhGia) {
            try {
                // Lấy đánh giá (API trước, cache sau)
                let danhGia = null;
                try {
                    const apiResp = await fetch(`/doanqlns/index.php/api/danhgia/${idDanhGia}`);
                    if (apiResp.ok) {
                        const apiJson = await apiResp.json();
                        if (apiJson && apiJson.success && apiJson.data) {
                            danhGia = apiJson.data; // id_nhan_vien, xep_loai, tong_diem, nam_danh_gia, ...
                        }
                    }
                } catch (e) { /* ignore */ }
                if (!danhGia && cachedData && cachedData.evaluationData) {
                    const cached = cachedData.evaluationData.find(item => item.idDanhGia == idDanhGia);
                    if (cached) {
                        danhGia = {
                            id_nhan_vien: cached.id_nhan_vien,
                            xep_loai: cached.xepLoai,
                            tong_diem: cached.totalScore,
                            nam_danh_gia: (new Date()).getFullYear()
                        };
                    }
                }
                if (!danhGia) {
                    console.warn('Không tìm thấy dữ liệu đánh giá để thêm thưởng.');
                    return false;
                }

                // Map xếp loại -> loại trong quan_ly_thuong (để lấy số tiền mặc định từ bảng cấu hình)
                let loai;
                const xl = (danhGia.xep_loai || '').trim();
                if (xl === 'Xuất sắc') loai = 'thành tích cá nhân - xuất sắc';
                else if (xl === 'Tốt') loai = 'thành tích cá nhân - tốt';
                else if (xl === 'Khá') loai = 'thành tích cá nhân - khá';
                else if (xl === 'Cần cải thiện') loai = 'phạt kỷ luật';
                else loai = 'thành tích cá nhân';

                const noiDung = loai === 'phạt kỷ luật'
                    ? `Phạt kỷ luật - ${danhGia.xep_loai || ''}`
                    : `Thưởng thành tích - ${danhGia.xep_loai || ''}`;
                const ngay = new Date().toISOString().slice(0,10); // YYYY-MM-DD

                const payload = {
                    id_nhan_vien: danhGia.id_nhan_vien,
                    noi_dung_thuong: noiDung,
                    ngay: ngay,
                    loai: loai
                    // Không gửi tien_thuong để backend đọc mặc định theo quan_ly_thuong
                };

                const resp = await fetch('/doanqlns/index.php/api/thuong', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const json = await resp.text();
                try {
                    const result = JSON.parse(json);
                    return !!(result && result.success);
                } catch { return false; }
            } catch (err) {
                console.error('syncToThuong error:', err);
                return false;
            }
        }

        function showLoading() {
            document.getElementById('loadingIndicator').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loadingIndicator').style.display = 'none';
        }

        function showError(message) {
            console.error('Error:', message);
            alert('Lỗi: ' + message);
        }

        // Load dữ liệu khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            loadEvaluationData();
        });

        // Đóng modal khi click outside
        window.addEventListener('click', function(event) {
            const attendanceModal = document.getElementById('attendanceModal');
            const viewModal = document.getElementById('viewEvaluationModal');
            
            if (event.target === attendanceModal) {
                closeAttendanceModal();
            }
            if (event.target === viewModal) {
                closeViewEvaluationModal();
            }
        });

        // ========== FUNCTIONS FOR CSV IMPORT ==========
        
        // Hiển thị modal import
        function showImportModal() {
            document.getElementById('importModal').style.display = 'block';
        }

        // Đóng modal import
        function closeImportModal() {
            document.getElementById('importModal').style.display = 'none';
            // Reset form
            document.getElementById('hieuQuaFile').value = '';
            document.getElementById('thaiDoFile').value = '';
            document.getElementById('hieuQuaPreview').style.display = 'none';
            document.getElementById('thaiDoPreview').style.display = 'none';
        }

        // Chuyển tab import
        function switchImportTab(tabName) {
            // Ẩn tất cả content
            document.querySelectorAll('.import-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Ẩn tất cả tab
            document.querySelectorAll('.import-tabs .tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Hiển thị tab và content được chọn
            if (tabName === 'hieu-qua') {
                document.getElementById('importHieuQua').classList.add('active');
                document.querySelector('.import-tabs .tab:first-child').classList.add('active');
            } else {
                document.getElementById('importThaiDo').classList.add('active');
                document.querySelector('.import-tabs .tab:last-child').classList.add('active');
            }
        }

        // Xử lý file CSV
        function handleFileUpload(file, type) {
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const csv = e.target.result;
                const lines = csv.split('\n');
                const headers = lines[0].split(',');
                
                // Parse CSV data
                const data = [];
                for (let i = 1; i < lines.length; i++) {
                    if (lines[i].trim()) {
                        const values = lines[i].split(',');
                        if (values.length >= 9) { // Đảm bảo có đủ cột
                            data.push({
                                idNhanVien: values[0].trim(),
                                tenNhanVien: values[1].trim(),
                                cauHoi1: values[2].trim(),
                                cauHoi2: values[3].trim(),
                                cauHoi3: values[4].trim(),
                                cauHoi4: values[5].trim(),
                                cauHoi5: values[6].trim(),
                                tongDiem: values[7].trim(),
                                diemCuoi: parseFloat(values[8].trim()) || 0
                            });
                        }
                    }
                }
                
                // Hiển thị preview
                showPreview(data, type);
            };
            reader.readAsText(file);
        }

        // Hiển thị preview dữ liệu
        function showPreview(data, type) {
            const previewId = type === 'hieu-qua' ? 'hieuQuaPreview' : 'thaiDoPreview';
            const tableId = type === 'hieu-qua' ? 'hieuQuaTable' : 'thaiDoTable';
            
            let html = '<table><thead><tr>';
            html += '<th>Mã NV</th><th>Tên NV</th><th>Câu hỏi 1</th><th>Câu hỏi 2</th><th>Câu hỏi 3</th><th>Câu hỏi 4</th><th>Câu hỏi 5</th><th>Tổng điểm</th><th>Điểm cuối</th>';
            html += '</tr></thead><tbody>';
            
            data.forEach(row => {
                html += '<tr>';
                html += `<td>${row.idNhanVien}</td>`;
                html += `<td>${row.tenNhanVien}</td>`;
                html += `<td>${row.cauHoi1}</td>`;
                html += `<td>${row.cauHoi2}</td>`;
                html += `<td>${row.cauHoi3}</td>`;
                html += `<td>${row.cauHoi4}</td>`;
                html += `<td>${row.cauHoi5}</td>`;
                html += `<td>${row.tongDiem}</td>`;
                html += `<td><strong>${row.diemCuoi}</strong></td>`;
                html += '</tr>';
            });
            
            html += '</tbody></table>';
            
            document.getElementById(tableId).innerHTML = html;
            document.getElementById(previewId).style.display = 'block';
            
            // Lưu dữ liệu để import
            window[type + 'Data'] = data;
        }

        // Import dữ liệu CSV
        async function importCSVData() {
            const activeTab = document.querySelector('.import-tabs .tab.active');
            const type = activeTab.textContent.includes('Hiệu quả') ? 'hieu-qua' : 'thai-do';
            const data = window[type + 'Data'];
            const quySelect = document.getElementById('importQuarter');
            const namInput = document.getElementById('importYear');
            const quy = quySelect && quySelect.value ? parseInt(quySelect.value, 10) : null;
            const nam = namInput && (parseInt(namInput.value, 10) || null);
            
            if (!data || data.length === 0) {
                alert('Vui lòng chọn file CSV trước!');
                return;
            }
            if (!quy || !nam) {
                alert('Vui lòng chọn Quý và Năm để import đúng kỳ.');
                return;
            }
            
            showLoading();
            
            try {
                const response = await fetch('/doanqlns/index.php/api/import-evaluation-csv', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: type,
                        data: data,
                        quy: quy,
                        nam: nam
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(`Import thành công ${result.count} bản ghi!`);
                    // Sau khi import, đồng bộ điểm chuyên cần theo quý nếu đang import hiệu quả/thái độ không ảnh hưởng.
                    try {
                        // Không biết data import cho chuyên cần hay không; nếu sau này có file chuyên cần, sẽ post sync ở đây.
                    } catch(e) {}
                    closeImportModal();
                    loadEvaluationData(); // Reload dữ liệu
                } else {
                    alert('Lỗi khi import: ' + result.message);
                }
            } catch (error) {
                console.error('Lỗi import:', error);
                alert('Lỗi khi import dữ liệu!');
            } finally {
                hideLoading();
            }
        }

        // Event listeners cho file upload
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('hieuQuaFile').addEventListener('change', function(e) {
                handleFileUpload(e.target.files[0], 'hieu-qua');
            });
            
            document.getElementById('thaiDoFile').addEventListener('change', function(e) {
                handleFileUpload(e.target.files[0], 'thai-do');
            });
        });

        const qSel = document.getElementById('quarterFilter');
        if (qSel) {
            qSel.addEventListener('change', () => {
                // Xóa cache để bắt buộc load lại theo quý vừa chọn
                try { cachedData = null; lastLoadTime = 0; } catch(e) {}
                try { loadEvaluationData(); } catch(e) { /* ignore if not on that section */ }
            });
        }
    </script>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html>

