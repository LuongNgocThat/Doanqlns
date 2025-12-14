<?php
$current_page = basename($_SERVER['PHP_SELF']);
require_once __DIR__ . '/network_check.php';
// Determine if current employee is 'Trưởng phòng' (id_chuc_vu = 4)
$can_show_danh_gia = false;
try {
    if (isset($_SESSION['user_id'])) {
        require_once __DIR__ . '/../config/Database.php';
        $db = (new Database())->getConnection();
        $stmt = $db->prepare("SELECT cv.id_chuc_vu, cv.ten_chuc_vu
                              FROM nhan_vien nv
                              LEFT JOIN chuc_vu cv ON nv.id_chuc_vu = cv.id_chuc_vu
                              WHERE nv.id_nhan_vien = :id LIMIT 1");
        $stmt->bindValue(':id', (int)($_SESSION['id_nhan_vien'] ?? $_SESSION['user_id']), PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $can_show_danh_gia = ((int)$row['id_chuc_vu'] === 4) || ($row['ten_chuc_vu'] === 'Trưởng phòng');
        }
    }
} catch (Exception $e) {
    // Fallback: do not show if any error occurs
}
?>
<style>
/* Modern sidebar styling like the image */
.layout-menu {
    background: #ffffff !important;
    border-right: 1px solid #f0f0f0;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
}

/* Force clear text rendering */
#layout-menu * {
    text-rendering: optimizeLegibility !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Special styling for clicked/active menu items */
#layout-menu .menu-item.active .menu-link,
#layout-menu .menu-item.active .menu-link span,
#layout-menu .menu-item.active .menu-link .menu-text {
    color:rgb(101, 134, 231) !important;
    font-weight: 700 !important;
    text-shadow: none !important;
    filter: none !important;
    opacity: 1 !important;
    transform: none !important;
    -webkit-text-stroke: 0 !important;
    -webkit-text-fill-color:rgb(31, 92, 225) !important;
}

.menu-inner {
    padding: 20px 0 !important;
}

.menu-item {
    margin: 2px 15px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
}

.menu-item:hover {
    background: #f8f9fa !important;
}

.menu-item.active {
    background: #e3f2fd !important;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15) !important;
}

#layout-menu .menu-item.active .menu-link {
    color:rgb(78, 159, 239) !important;
    font-weight: 700 !important;
    text-shadow: none !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    filter: none !important;
    opacity: 1 !important;
    transform: none !important;
    background-color: #e3f2fd !important;
}

#layout-menu .menu-item.active .menu-icon {
    color: #000000 !important;
    font-weight: bold !important;
    text-shadow: none !important;
    filter: none !important;
    opacity: 1 !important;
}

#layout-menu .menu-link {
    padding: 12px 16px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    text-decoration: none !important;
    color: #495057 !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    line-height: 1.4 !important;
    transition: all 0.2s ease !important;
    text-shadow: none !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    filter: none !important;
    opacity: 1 !important;
    transform: none !important;
}

#layout-menu .menu-link:hover {
    color: #1976d2 !important;
    text-decoration: none !important;
    text-shadow: none !important;
    filter: none !important;
    opacity: 1 !important;
}

.menu-icon {
    font-size: 16px !important;
    margin-right: 12px !important;
    width: 20px !important;
    text-align: center !important;
    color: #6c757d !important;
}

.menu-header {
    margin: 30px 15px 8px 15px !important;
    padding: 0 !important;
}

.menu-header-text {
    color: #9e9e9e !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    letter-spacing: 1px !important;
    text-transform: uppercase !important;
    position: relative !important;
}

/* Home item special styling */
.menu-item:first-child {
    background: #e3f2fd !important;
    margin-bottom: 15px !important;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15) !important;
}

.menu-item:first-child .menu-link {
    color: #1976d2 !important;
    font-weight: 600 !important;
    font-size: 15px !important;
}

.menu-item:first-child .menu-icon {
    color: #1976d2 !important;
}

/* Account section styling */
.menu-item:last-child {
    margin-top: 20px !important;
    border-top: 1px solid #e9ecef !important;
    padding-top: 15px !important;
}

.menu-item:last-child .menu-link {
    color: #dc3545 !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    text-shadow: 0 1px 2px rgba(220, 53, 69, 0.2) !important;
}

.menu-item:last-child .menu-icon {
    color: #dc3545 !important;
}

.menu-item:last-child:hover {
    background: #f8d7da !important;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .layout-menu {
        width: 280px !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        height: 100vh !important;
        z-index: 1000 !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
        background: #f8f9fa !important;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1) !important;
    }
    
    .layout-menu.menu-open {
        transform: translateX(0) !important;
    }
    
    .menu-item {
        margin: 3px 10px !important;
    }
    
    .menu-link {
        padding: 10px 15px !important;
    }
    
    .menu-icon {
        font-size: 16px !important;
        margin-right: 10px !important;
    }
    
    /* Mobile overlay */
    .mobile-menu-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }
    
        .mobile-menu-overlay.show {
            display: block;
        }
        
        /* Mobile Attendance Modal Styles (from frontend/index.html) */
        .mobile-modal-content {
            background: linear-gradient(135deg, #8ce3ef 0%, #edebef 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
            margin: 0 auto;
        }

        .mobile-modal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            text-align: center;
            border-radius: 20px 20px 0 0;
            padding: 20px;
            position: relative;
        }

        .mobile-modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .mobile-modal-header .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: white;
            opacity: 0.8;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .mobile-modal-header .modal-close:hover {
            opacity: 1;
        }

        .mobile-modal-body {
            padding: 20px;
            text-align: center;
        }

        .mobile-video-container {
            position: relative;
            display: inline-block;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        #mobileVideo {
            border-radius: 10px;
            object-fit: cover;
            width: 400px;
            height: 400px;
            display: none;
        }

        #mobileCanvas {
            border-radius: 10px;
            object-fit: cover;
            width: 400px;
            height: 400px;
            max-width: 100%;
            background: #f8f9fa;
        }

        .mobile-face-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .mobile-face-square {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            border: 3px solid rgba(255,255,255,0.8);
            box-shadow: 0 0 20px rgba(255,255,255,0.5);
        }

        .mobile-center-point {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            box-shadow: 0 0 10px rgba(255,255,255,0.7);
        }

        .mobile-success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(40, 167, 69, 0.9);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: mobileFadeIn 0.5s ease-in-out;
        }

        .mobile-success-circle {
            width: 120px;
            height: 120px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(40, 167, 69, 0.4);
            animation: mobilePulse 1s ease-in-out;
        }

        .mobile-success-circle i {
            font-size: 48px;
            color: white;
        }

        @keyframes mobileFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes mobilePulse {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .mobile-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .mobile-loading-spinner {
            text-align: center;
            color: white;
        }

        .mobile-loading-spinner i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .mobile-loading-spinner p {
            font-size: 16px;
            margin: 0;
            font-weight: 500;
        }

        .mobile-btn-lg {
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 30px;
            font-size: 16px;
        }

        .mobile-result-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 8px 32px rgba(40, 167, 69, 0.3);
        }
        
        .mobile-result-success h4 {
            margin: 0 0 15px 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .mobile-result-success p {
            margin: 8px 0;
            font-size: 0.95rem;
        }
        
        .mobile-result-success strong {
            font-weight: 600;
            color: #fff;
        }
        
        .mobile-result-error {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 8px 32px rgba(220, 53, 69, 0.3);
        }
        
        .mobile-result-error h4 {
            margin: 0 0 15px 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .mobile-result-error p {
            margin: 8px 0;
            font-size: 0.95rem;
        }
        
        .mobile-result-info {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 8px 32px rgba(23, 162, 184, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .mobile-modal-content {
                width: 95%;
                margin: 10px auto;
            }
            
            #mobileVideo, #mobileCanvas {
                width: 300px;
                height: 300px;
            }
            
            .mobile-face-square {
                width: 150px;
                height: 150px;
            }
        }
    
    /* Ensure menu toggle is visible */
    .layout-menu-toggle {
        display: block !important;
        position: fixed !important;
        top: 15px !important;
        left: 15px !important;
        z-index: 1001 !important;
        background: #667eea !important;
        color: white !important;
        padding: 10px !important;
        border-radius: 8px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;
        cursor: pointer !important;
    }
    
    /* Navbar menu toggle styling */
    .navbar .layout-menu-toggle {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        background: transparent !important;
        color: #667eea !important;
        padding: 8px !important;
        border-radius: 6px !important;
        box-shadow: none !important;
        border: 1px solid #e9ecef !important;
    }
    
    .navbar .layout-menu-toggle:hover {
        background: #f8f9fa !important;
        color: #495057 !important;
    }
    
    .navbar .layout-menu-toggle i {
        font-size: 18px !important;
    }
}

/* Simple menu styling - no animations */
</style>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="height: 100vh; overflow-y: auto;">
    <div class="app-brand demo">
        <a href="/doanqlns/giaodien.php" class="app-brand-link">
            <span class="app-brand-logo demo">
                <i class="fas fa-users-cog text-primary fa-2x"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">HRM Pro</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="fas fa-chevron-left align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
            // Kiểm tra quyền
            $is_admin = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] && 
                       isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'] && 
                       isset($_SESSION['quyen_xoa']) && $_SESSION['quyen_xoa'];
            
            $is_manager = isset($_SESSION['quyen_them']) && $_SESSION['quyen_them'] && 
                         isset($_SESSION['quyen_sua']) && $_SESSION['quyen_sua'];
            
            if ($is_admin || $is_manager): 
            ?>
                <!-- Dashboard -->
                <li class="menu-item <?= $current_page == 'giaodien.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/giaodien.php" class="menu-link">
                        <i class="menu-icon fas fa-home"></i>
                        <div>Dashboard</div>
                    </a>
                </li>

                <!-- Quản lý nhân sự -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Quản lý nhân sự</span>
                </li>

                <li class="menu-item <?= $current_page == 'users.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/users.php" class="menu-link">
                        <i class="menu-icon fas fa-users"></i>
                        <div>Nhân viên</div>
                    </a>
                </li>

                <li class="menu-item has-sub <?= in_array($current_page, ['phucap.php', 'nguoiphuthuoc.php', 'kpi.php', 'phuc_tra.php']) ? 'active open' : '' ?>">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon fas fa-cogs"></i>
                        <div>Quản lý</div>
                        <i class="menu-arrow fas fa-chevron-right"></i>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item <?= $current_page == 'phucap.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/phucap.php" class="menu-link">
                                <i class="menu-icon fas fa-money-bill-wave"></i>
                                <div>Phụ Cấp</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'kpi.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/kpi.php" class="menu-link">
                                <i class="menu-icon fas fa-chart-line"></i>
                                <div>Hoa Hồng</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'nguoiphuthuoc.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/nguoiphuthuoc.php" class="menu-link">
                                <i class="menu-icon fas fa-user-plus"></i>
                                <div>Người Phụ Thuộc</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'quan_ly_thuong.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/quan_ly_thuong.php" class="menu-link">
                                <i class="menu-icon fas fa-sliders-h"></i>
                                <div>Quản lý thưởng</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'hopdong.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/hopdong.php" class="menu-link">
                                <i class="menu-icon fas fa-file-contract"></i>
                                <div>Hợp đồng</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'phuc_tra.php' ? 'active' : '' ?>">
                            <a href="/doanqlns/views/phuc_tra.php" class="menu-link">
                                <i class="menu-icon fas fa-undo"></i>
                                <div>Bổ sung điểm danh</div>
                            </a>
                        </li>
                        <li class="menu-item <?= $current_page == 'phuc_tra.php' ? 'active' : '' ?>">
                            <!-- <a href="/doanqlns/views/phuc_tra.php" class="menu-link">
                                <i class="menu-icon fas fa-undo"></i>
                                <div>Bổ sung điểm danh</div>
                            </a> -->
                        </li>
                    </ul>
                </li>

                <li class="menu-item <?= $current_page == 'nghiphep.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/nghiphep.php" class="menu-link">
                        <i class="menu-icon fas fa-calendar-minus"></i>
                        <div>Nghỉ phép</div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'chamcong.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/chamcong.php" class="menu-link">
                        <i class="menu-icon fas fa-calendar-check"></i>
                        <div>Chấm công</div>
                    </a>
                </li>


                <!-- Quản lý tài chính -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Quản lý tài chính</span>
                </li>

                <!-- <li class="menu-item <?= $current_page == 'baohiem.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/baohiem.php" class="menu-link">
                        <i class="menu-icon fas fa-file-invoice-dollar"></i>
                        <div>Bảo hiểm & Thuế</div>
                    </a>
                </li> -->
                <li class="menu-item <?= $current_page == 'luong.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/luong.php" class="menu-link">
                        <i class="menu-icon fas fa-coins"></i>
                        <div>Lương</div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'thuong.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/thuong.php" class="menu-link">
                        <i class="menu-icon fas fa-gift"></i>
                        <div>Thưởng & Khấu trừ</div>
                    </a>
                </li>

                

                <li class="menu-item <?= $current_page == 'thuong_tet.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/thuong_tet.php" class="menu-link">
                        <i class="menu-icon fas fa-gift"></i>
                        <div>Thưởng Tết</div>
                    </a>
                </li>

                <!-- Đánh giá & báo cáo-->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Đánh giá & Thống kê</span>
                </li>

                <li class="menu-item <?= $current_page == 'danhgia.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/danhgia.php" class="menu-link">
                        <i class="menu-icon fas fa-star"></i>
                        <div>Đánh giá nhân viên</div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'baocao.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/baocao.php" class="menu-link">
                        <i class="menu-icon fas fa-chart-line"></i>
                        <div>Báo cáo & Thống kê</div>
                    </a>
                </li>

            <?php else: ?>
                <!-- Menu cho nhân viên -->
                <li class="menu-item <?= $current_page == 'giaodien_nhanvien.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/giaodien_nhanvien.php" class="menu-link">
                        <i class="menu-icon fas fa-home"></i>
                        <div>Trang chủ</div>
                    </a>
                </li>

                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Thông tin cá nhân</span>
                </li>

                <li class="menu-item <?= $current_page == 'hoso_responsive.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/hoso_responsive.php" class="menu-link">
                        <i class="menu-icon fas fa-user"></i>
                        <div>Hồ sơ </div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'luongmy_responsive.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/luongmy_responsive.php" class="menu-link">
                        <i class="menu-icon fas fa-money-bill-wave"></i>
                        <div>Lương </div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'nghiphepmy_responsive.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/nghiphepmy_responsive.php" class="menu-link">
                        <i class="menu-icon fas fa-calendar-alt"></i>
                        <div>Nghỉ phép</div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'phuc_tra_my.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/phuc_tra_my.php" class="menu-link">
                        <i class="menu-icon fas fa-undo"></i>
                        <div>Bổ sung điểm danh</div>
                    </a>
                </li>
                 <li class="menu-item <?= $current_page == 'dangkyphuthuoc.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/dangkyphuthuoc.php" class="menu-link">
                        <i class="menu-icon fas fa-user-plus"></i>
                        <div>Đăng ký phụ thuộc</div>
                    </a>
                </li>

                <?php if ($can_show_danh_gia): ?>
                <li class="menu-item <?= $current_page == 'giaodien_truongphong.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/giaodien_truongphong.php" class="menu-link">
                        <i class="menu-icon fas fa-star"></i>
                        <div>Đánh giá nhân viên</div>
                    </a>
                </li>
                <?php endif; ?>

                <!-- <li class="menu-item <?= $current_page == 'chatnoibo.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/chatnoibo.php" class="menu-link">
                        <i class="menu-icon fas fa-comments"></i>
                        <div>Chat Nội Bộ</div>
                    </a>
                </li> -->

                <li class="menu-item <?= $current_page == 'lich_su_diem_danh_my.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/lich_su_diem_danh_my.php" class="menu-link">
                        <i class="menu-icon fas fa-history"></i>
                        <div>Lịch sử điểm danh</div>
                    </a>
                </li>

                                   <!-- Menu diem danh - only show on desktop when in company network AND connected to company WiFi AND correct time -->
                                   <li class="menu-item" id="attendance-menu-item" style="display: none;"> 
                                    <a href="#" class="menu-link" onclick="showFaceModalFromSidebar(); return false;"> 
                                     <i class="menu-icon fas fa-calendar-alt"></i> <div>Điểm danh</div> </a> </li>
                                   
                                   <!-- Menu diem danh mobile - only show on mobile devices when in company network -->
                                   <li class="menu-item" id="attendance-mobile-menu-item" style="display: none;"> 
                                    <a href="#" class="menu-link" onclick="showMobileAttendanceModal(); return false;"> 
                                     <i class="menu-icon fas fa-mobile-alt"></i> <div>Điểm danh mobile</div> </a> </li>
            <?php endif; ?>

            <?php if ($is_admin): ?>
                <!-- Cài đặt hệ thống -->
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">Cài đặt hệ thống</span>
                </li>

                <li class="menu-item <?= $current_page == 'setting.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/setting.php" class="menu-link">
                        <i class="menu-icon fas fa-cog"></i>
                        <div>Cài đặt</div>
                    </a>
                </li>

                <li class="menu-item <?= $current_page == 'lich_su_diem_danh.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/lich_su_diem_danh.php" class="menu-link">
                        <i class="menu-icon fas fa-history"></i>
                        <div>Lịch sử điểm danh</div>
                    </a>
                </li>
                
                <!-- <li class="menu-item <?= $current_page == 'face_recognition_stats.php' ? 'active' : '' ?>">
                    <a href="/doanqlns/views/face_recognition_stats.php" class="menu-link">
                        <i class="menu-icon fas fa-brain"></i>
                        <div>Thống kê Nhận diện</div>
                    </a>
                </li> -->

            <?php endif; ?>

            <!-- Đăng xuất -->
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Tài khoản</span>
            </li>

            <li class="menu-item">
                <a href="/doanqlns/views/logout.php" class="menu-link" onclick="return confirmLogout()">
                    <i class="menu-icon fas fa-sign-out-alt"></i>
                    <div>Đăng xuất</div>
                </a>
            </li>

        <?php else: ?>
            <li class="menu-item">
                <a href="/doanqlns/views/login.php" class="menu-link">
                    <i class="menu-icon fas fa-sign-in-alt"></i>
                    <div>Đăng nhập</div>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>

<!-- Modal Điểm Danh Bằng Gương Mặt cho Sidebar -->
<div id="sidebarFaceModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Điểm Danh Bằng Gương Mặt</h2>
            <button class="modal-close" onclick="closeSidebarFaceModal()" aria-label="Đóng modal">×</button>
        </div>
        <div class="modal-body" style="padding:0; height:80vh;">
            <iframe id="sidebarFaceIframe" src="http://localhost:5001/" style="width:100%; height:100%; border:0;" allow="camera; microphone"></iframe>
        </div>
        <div class="modal-footer">
            <button class="btn-close" onclick="closeSidebarFaceModal()">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal Điểm Danh Mobile -->
<div id="mobileAttendanceModal" class="modal">
    <div class="modal-content mobile-modal-content">
        <div class="modal-header mobile-modal-header">
            <h2 class="modal-title"><i class="fas fa-camera"></i> Điểm danh bằng khuôn mặt</h2>
            <button class="modal-close" onclick="closeMobileAttendanceModal()" aria-label="Đóng modal">×</button>
        </div>
        <div class="modal-body mobile-modal-body">
            <div class="mobile-video-container position-relative">
                <video id="mobileVideo" width="400" height="400" autoplay muted style="display:none;"></video>
                <canvas id="mobileCanvas" width="400" height="400" style="max-width: 100%; border-radius: 10px; background: #f8f9fa;"></canvas>
                <div id="mobileLoadingOverlay" class="mobile-loading-overlay">
                    <div class="mobile-loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Đang khởi tạo...</p>
                    </div>
                </div>
                <div id="mobileFaceOverlay" class="mobile-face-overlay">
                    <div class="mobile-face-square"></div>
                    <div class="mobile-center-point"></div>
                </div>
                <div id="mobileSuccessOverlay" class="mobile-success-overlay" style="display:none;">
                    <div class="mobile-success-circle">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>
            <button id="mobileCapture" class="btn btn-primary mobile-btn-lg">
                <i class="fas fa-camera"></i> Chụp ảnh và điểm danh
            </button>
            <div id="mobileResult" class="mt-3"></div>
        </div>
    </div>
</div>

<style>
.layout-menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    background: #fff;
    border-right: 1px solid #eee;
    z-index: 999;
    transition: all 0.3s ease;
}

.app-brand {
    padding: 20px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 10px;
}

.app-brand-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
}

.app-brand-logo {
    color: #1976d2;
    font-size: 24px;
    margin-right: 12px;
}

.app-brand-text {
    color: #1976d2;
    font-size: 18px;
    font-weight: 600;
}

.menu-inner {
    list-style: none;
    margin: 0;
    padding: 0;
}

.menu-header {
    padding: 0.625rem 1.25rem;
    margin-top: 0.75rem;
    color: #a1acb8;
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
}

.menu-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 1.25rem;
    right: 1.25rem;
    height: 1px;
    background: linear-gradient(90deg, transparent, #a1acb8, transparent);
    opacity: 0.3;
}

.menu-item {
    margin: 0.125rem 0;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    color: #697a8d;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 0.375rem;
    margin: 0.125rem 0.5rem;
    position: relative;
    overflow: hidden;
}

.menu-link:hover,
.menu-item.active .menu-link {
    background: #f8f9fa;
    color: #007bff;
}

.menu-item.active .menu-link {
    background: #007bff;
    color: white;
    font-weight: 500;
}

.menu-icon {
    width: 1.375rem;
    height: 1.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.875rem;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.menu-link:hover .menu-icon {
    color: #007bff;
}

.menu-item.active .menu-icon {
    color: white;
}

.layout-menu-toggle {
    display: none;
}

@media (max-width: 1199.98px) {
    .layout-menu {
        transform: translateX(-100%);
    }
    
    .layout-menu.show {
        transform: translateX(0);
    }
    
    .layout-menu-toggle {
        display: block;
    }
}

/* Modal Container */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border: none;
    border-radius: 8px;
    width: 90%;
    max-width: 1300px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

/* Modal Header */
.modal-header {
    background: linear-gradient(90deg, #007bff, #0056b3);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.modal-close {
    color: white;
    font-size: 1.8rem;
    font-weight: bold;
    background: none;
    border: none;
    cursor: pointer;
    transition: transform 0.2s;
}

.modal-close:hover {
    transform: scale(1.2);
    color: #f1f1f1;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    background-color: #f8f9fa;
    padding: 15px 20px;
    text-align: right;
    border-top: 1px solid #dee2e6;
}

.btn-close {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.2s;
}

.btn-close:hover {
    background-color: #5a6268;
}
</style>

<script>
// ID nhân viên hiện đang đăng nhập (dùng để kiểm tra chủ tài khoản khi điểm danh gương mặt)
const CURRENT_EMPLOYEE_ID = <?php echo isset($_SESSION['id_nhan_vien']) ? (int)$_SESSION['id_nhan_vien'] : 0; ?>;
function confirmLogout() {
    return confirm("Bạn có chắc chắn muốn đăng xuất?");
}

// Toggle menu on mobile
document.addEventListener('DOMContentLoaded', function() {
    const menuToggles = document.querySelectorAll('.layout-menu-toggle');
    const layoutMenu = document.querySelector('.layout-menu');
    
    // Create mobile overlay
    const overlay = document.createElement('div');
    overlay.className = 'mobile-menu-overlay';
    document.body.appendChild(overlay);
    
    function toggleMenu() {
        const isOpen = layoutMenu.classList.contains('menu-open');
        
        console.log('toggleMenu called, current state:', isOpen);
        console.log('layoutMenu element:', layoutMenu);
        console.log('overlay element:', overlay);
        
        if (isOpen) {
            // Close menu
            layoutMenu.classList.remove('menu-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            console.log('Menu closed');
        } else {
            // Open menu
            layoutMenu.classList.add('menu-open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            console.log('Menu opened');
        }
        
        console.log('Menu toggle clicked, isOpen:', !isOpen);
    }
    
    if (layoutMenu) {
        // Add event listeners to all menu toggles
        menuToggles.forEach(function(menuToggle) {
            menuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMenu();
            });
        });
        
        // Close menu when clicking overlay
        overlay.addEventListener('click', function() {
            layoutMenu.classList.remove('menu-open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        });
        
        // Close menu when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth > 768) return;
            
            let isMenuToggle = false;
            menuToggles.forEach(function(toggle) {
                if (toggle.contains(e.target)) {
                    isMenuToggle = true;
                }
            });
            
            if (layoutMenu && !layoutMenu.contains(e.target) && !isMenuToggle) {
                layoutMenu.classList.remove('menu-open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
        
        // Close menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                layoutMenu.classList.remove('menu-open');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    }
});

// Kiểm tra và hiển thị nút điểm danh dựa trên thời gian
async function checkAndShowAttendanceButton() {
    // Chỉ chạy trên desktop, không chạy trên mobile
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        console.log('Skipping desktop attendance button check on mobile device');
        return;
    }
    
    try {
        // Lấy thời gian hiện tại từ máy tính người dùng
        const now = new Date();
        const clientTime = now.toTimeString().slice(0, 8); // HH:MM:SS
        
        console.log('Checking attendance button with client time:', clientTime);
        
        // Gọi API để kiểm tra
        const response = await fetch('/doanqlns/api_check_attendance_button.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                client_time: clientTime
            })
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                const attendanceMenuItem = document.getElementById('attendance-menu-item');
                if (attendanceMenuItem) {
                    if (result.can_show) {
                        attendanceMenuItem.style.display = 'block';
                        console.log('Attendance button shown - time is allowed');
                    } else {
                        attendanceMenuItem.style.display = 'none';
                        console.log('Attendance button hidden - time not allowed');
                    }
                }
            }
        }
    } catch (error) {
        console.error('Error checking attendance button:', error);
        // Nếu có lỗi, ẩn nút để an toàn
        const attendanceMenuItem = document.getElementById('attendance-menu-item');
        if (attendanceMenuItem) {
            attendanceMenuItem.style.display = 'none';
        }
    }
}

let sidebarAttendanceConfig = null;

async function loadSidebarAttendanceConfig() {
    try {
        if (sidebarAttendanceConfig) {
            return sidebarAttendanceConfig;
        }
        const response = await fetch('/doanqlns/index.php/api/cau-hinh-gio-lam-viec/hien-tai');
        if (!response.ok) {
            console.warn('Không thể tải cấu hình giờ làm việc (sidebar). Sử dụng mặc định.');
            return null;
        }
        const result = await response.json();
        if (result.success && result.data) {
            sidebarAttendanceConfig = result.data;
        } else {
            console.warn('API cấu hình giờ làm việc trả về rỗng. Sử dụng mặc định.');
        }
    } catch (error) {
        console.warn('Lỗi khi tải cấu hình giờ làm việc (sidebar):', error);
    }
    return sidebarAttendanceConfig;
}

function timeToMinutes(timeStr) {
    if (!timeStr) return null;
    const parts = timeStr.split(':');
    if (parts.length < 2) return null;
    const hours = parseInt(parts[0], 10);
    const minutes = parseInt(parts[1], 10);
    return hours * 60 + minutes;
}

function determineAttendanceStatus(currentTimeInMinutes) {
    if (!sidebarAttendanceConfig) {
        return determineAttendanceStatusDefault(currentTimeInMinutes);
    }

    const cfg = sidebarAttendanceConfig;
    const gioSangBatDau = timeToMinutes(cfg.gio_sang_bat_dau);
    const gioSangKetThuc = timeToMinutes(cfg.gio_sang_ket_thuc);
    const gioSangTreBatDau = timeToMinutes(cfg.gio_sang_tre_bat_dau);
    const gioSangTreKetThuc = timeToMinutes(cfg.gio_sang_tre_ket_thuc);

    const gioTruaBatDau = timeToMinutes(cfg.gio_trua_bat_dau);
    const gioTruaKetThuc = timeToMinutes(cfg.gio_trua_ket_thuc);
    const gioTruaTreBatDau = timeToMinutes(cfg.gio_trua_tre_bat_dau);
    const gioTruaTreKetThuc = timeToMinutes(cfg.gio_trua_tre_ket_thuc);

    const gioChieuRaSomBatDau = timeToMinutes(cfg.gio_chieu_ra_som_bat_dau);
    const gioChieuRaSomKetThuc = timeToMinutes(cfg.gio_chieu_ra_som_ket_thuc);
    const gioChieuBatDau = timeToMinutes(cfg.gio_chieu_bat_dau);
    const gioChieuKetThuc = timeToMinutes(cfg.gio_chieu_ket_thuc);

    if (gioSangBatDau !== null && gioSangKetThuc !== null &&
        currentTimeInMinutes >= gioSangBatDau && currentTimeInMinutes <= gioSangKetThuc) {
        return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
    }

    if (gioSangTreBatDau !== null && gioSangTreKetThuc !== null &&
        currentTimeInMinutes >= gioSangTreBatDau && currentTimeInMinutes <= gioSangTreKetThuc) {
        return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
    }

    if (gioTruaBatDau !== null && gioTruaKetThuc !== null &&
        currentTimeInMinutes >= gioTruaBatDau && currentTimeInMinutes <= gioTruaKetThuc) {
        return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
    }

    if (gioTruaTreBatDau !== null && gioTruaTreKetThuc !== null &&
        currentTimeInMinutes >= gioTruaTreBatDau && currentTimeInMinutes <= gioTruaTreKetThuc) {
        return { type: 'trua', status: 'Đi trễ', gio: 'gio_trua' };
    }

    if (gioChieuRaSomBatDau !== null && gioChieuRaSomKetThuc !== null &&
        currentTimeInMinutes >= gioChieuRaSomBatDau && currentTimeInMinutes <= gioChieuRaSomKetThuc) {
        return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
    }

    if (gioChieuBatDau !== null && gioChieuKetThuc !== null &&
        currentTimeInMinutes >= gioChieuBatDau && currentTimeInMinutes <= gioChieuKetThuc) {
        return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
    }

    return determineAttendanceStatusDefault(currentTimeInMinutes);
}

function determineAttendanceStatusDefault(currentTimeInMinutes) {
    if (currentTimeInMinutes >= 450 && currentTimeInMinutes <= 495) {
        return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
    }

    if (currentTimeInMinutes >= 496 && currentTimeInMinutes <= 689) {
        return { type: 'sang', status: 'Đi trễ', gio: 'gio_vao' };
    }

    if (currentTimeInMinutes >= 690 && currentTimeInMinutes <= 780) {
        return { type: 'trua', status: 'Đúng giờ', gio: 'gio_trua' };
    }

    if (currentTimeInMinutes >= 781 && currentTimeInMinutes <= 959) {
        return { type: 'chieu', status: 'Đi trễ', gio: 'gio_ra' };
    }

    if (currentTimeInMinutes >= 960 && currentTimeInMinutes <= 1049) {
        return { type: 'chieu', status: 'Ra sớm', gio: 'gio_ra' };
    }

    if (currentTimeInMinutes >= 1050 && currentTimeInMinutes <= 1260) {
        return { type: 'chieu', status: 'Đúng giờ', gio: 'gio_ra' };
    }

    return { type: 'sang', status: 'Đúng giờ', gio: 'gio_vao' };
}

function calculateOverallStatus(trangThaiSang, trangThaiTrua, trangThaiChieu) {
    if (!trangThaiSang && !trangThaiTrua && !trangThaiChieu) {
        return 'Chưa điểm danh';
    }

    if (!trangThaiSang && trangThaiTrua && trangThaiChieu) {
        return 'Nghỉ nữa buổi';
    }

    if (trangThaiSang && trangThaiTrua && !trangThaiChieu) {
        return 'Nghỉ nữa buổi';
    }

    if (trangThaiSang && !trangThaiTrua && !trangThaiChieu) {
        return trangThaiSang;
    }
    if (!trangThaiSang && trangThaiTrua && !trangThaiChieu) {
        return trangThaiTrua;
    }
    if (!trangThaiSang && !trangThaiTrua && trangThaiChieu) {
        return trangThaiChieu;
    }

    const statusPriority = {
        'Nghỉ nữa buổi': 1,
        'Đi trễ': 2,
        'Ra sớm': 3,
        'Có phép': 4,
        'Phép Năm': 5,
        'Nghỉ Lễ': 6,
        'Đúng giờ': 7
    };

    const priorities = [
        { status: trangThaiSang, priority: statusPriority[trangThaiSang] || 8 },
        { status: trangThaiTrua, priority: statusPriority[trangThaiTrua] || 8 },
        { status: trangThaiChieu, priority: statusPriority[trangThaiChieu] || 8 }
    ].filter(item => item.status);

    if (priorities.length === 0) return 'Chưa điểm danh';

    const worstStatus = priorities.reduce((min, current) =>
        current.priority < min.priority ? current : min
    );

    return worstStatus.status;
}

// Kiểm tra ngay khi trang load
document.addEventListener('DOMContentLoaded', async function() {
    await loadSidebarAttendanceConfig();
    // Kiểm tra thiết bị
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    if (isMobile) {
        // Trên mobile: ẩn nút thông thường, chỉ kiểm tra nút mobile
        const attendanceMenuItem = document.getElementById('attendance-menu-item');
        if (attendanceMenuItem) {
            attendanceMenuItem.style.display = 'none';
        }
        
        await checkAndShowMobileAttendanceButton();
        setInterval(async () => {
            await checkAndShowMobileAttendanceButton();
        }, 60000);
    } else {
        // Trên desktop: ẩn nút mobile, chỉ kiểm tra nút thông thường
        const attendanceMobileMenuItem = document.getElementById('attendance-mobile-menu-item');
        if (attendanceMobileMenuItem) {
            attendanceMobileMenuItem.style.display = 'none';
        }
        
        checkAndShowAttendanceButton();
        setInterval(checkAndShowAttendanceButton, 60000);
    }
});

// Hiển thị modal điểm danh gương mặt từ sidebar
async function showFaceModalFromSidebar() {
    try {
        // Lấy thời gian hiện tại từ máy tính người dùng
        const now = new Date();
        const clientTime = now.toTimeString().slice(0, 8); // HH:MM:SS
        
        console.log('Checking attendance time with client time:', clientTime);
        
        // Kiểm tra cả mạng và thời gian
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
            const result = await response.json();
            if (result.success) {
                const data = result.data;
                
                // Kiểm tra xem có được phép điểm danh không
                if (!data.allowed) {
                    let message = data.message || 'Không thể điểm danh';
                    
                    // Nếu vấn đề là thời gian, hiển thị thông tin chi tiết
                    if (!data.time_ok) {
                        const settingsResponse = await fetch('/doanqlns/index.php/api/settings/attendance-time');
                        if (settingsResponse.ok) {
                            const settingsResult = await settingsResponse.json();
                            if (settingsResult.success) {
                                const settings = settingsResult.data;
                                message = `Hiện tại không trong khung giờ điểm danh!\n\nĐiểm danh sáng: ${settings.morning_start_time} - ${settings.morning_end_time}\nĐiểm danh trưa: ${settings.lunch_start_time} - ${settings.lunch_end_time}\nĐiểm danh chiều: ${settings.afternoon_start_time} - ${settings.afternoon_end_time}\n\nThời gian máy tính bạn: ${clientTime}`;
                            }
                        }
                    }
                    
                    alert(message);
                    return;
                }
            }
        }
        
        // Nếu kiểm tra thành công, mở modal
        document.getElementById('sidebarFaceModal').style.display = 'flex';
    } catch (error) {
        console.error('Lỗi khi kiểm tra điều kiện điểm danh:', error);
        // Nếu có lỗi, vẫn cho phép mở modal
        document.getElementById('sidebarFaceModal').style.display = 'flex';
    }
}

function closeSidebarFaceModal() {
    const modal = document.getElementById('sidebarFaceModal');
    modal.style.display = 'none';
}

// Mobile Attendance Functions
let mobileStream = null;
let mobileCapturedImage = null;

// Kiểm tra và hiển thị nút điểm danh mobile
async function checkAndShowMobileAttendanceButton() {
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const attendanceMobileMenuItem = document.getElementById('attendance-mobile-menu-item');
    const attendanceMenuItem = document.getElementById('attendance-menu-item');
    
    if (attendanceMobileMenuItem) {
        if (isMobile) {
            // Trên mobile: ẩn nút điểm danh thông thường
            if (attendanceMenuItem) {
                attendanceMenuItem.style.display = 'none';
            }
            // Kiểm tra mạng công ty trước khi hiển thị nút
            try {
                // Lấy thời gian hiện tại từ client
                const now = new Date();
                const clientTime = now.getHours().toString().padStart(2, '0') + ':' + 
                                 now.getMinutes().toString().padStart(2, '0') + ':' + 
                                 now.getSeconds().toString().padStart(2, '0');
                
                const response = await fetch(`/doanqlns/api/check_network.php?client_time=${encodeURIComponent(clientTime)}&t=${Date.now()}`, {
                    method: 'GET',
                    credentials: 'include'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    // Debug logging (chỉ khi cần thiết)
                    console.log('Mobile attendance check - API Response:', data);
                    
                    if (data.success && data.can_show_mobile_attendance) {
                        // Hiển thị nút nếu đúng mạng công ty
                        attendanceMobileMenuItem.style.display = 'block';
                        console.log('Mobile attendance button: SHOWN (company network)');
                    } else {
                        // Ẩn nút nếu không đúng mạng công ty
                        attendanceMobileMenuItem.style.display = 'none';
                        console.log('Mobile attendance button: HIDDEN (not company network)');
                        console.log('Network check result:', data);
                        
                        // Hiển thị thông báo cho người dùng
                        if (data && data.message) {
                            console.warn('Mobile attendance restricted:', data.message);
                        }
                    }
                } else {
                    // Nếu API lỗi, ẩn nút để an toàn
                    attendanceMobileMenuItem.style.display = 'none';
                    console.log('Mobile attendance button: HIDDEN (API error)');
                }
            } catch (error) {
                // Nếu có lỗi, ẩn nút để an toàn
                attendanceMobileMenuItem.style.display = 'none';
                console.error('Network check error:', error);
                console.log('Mobile attendance button: HIDDEN (network error)');
            }
        } else {
            // Trên desktop: ẩn nút mobile, hiển thị nút thông thường
            attendanceMobileMenuItem.style.display = 'none';
            if (attendanceMenuItem) {
                // Nút thông thường sẽ được hiển thị bởi checkAndShowAttendanceButton()
            }
        }
    }
}

// Hiển thị modal điểm danh mobile
async function showMobileAttendanceModal() {
    // Kiểm tra mạng trước khi hiển thị modal
    try {
        // Lấy thời gian hiện tại từ client
        const now = new Date();
        const clientTime = now.getHours().toString().padStart(2, '0') + ':' + 
                         now.getMinutes().toString().padStart(2, '0') + ':' + 
                         now.getSeconds().toString().padStart(2, '0');
        
        const response = await fetch(`/doanqlns/api/check_network.php?client_time=${encodeURIComponent(clientTime)}&t=${Date.now()}`, {
            method: 'GET',
            credentials: 'include'
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (!data.success || !data.can_show_mobile_attendance) {
                // Hiển thị thông báo lỗi nếu không đúng mạng
                alert('Bạn cần kết nối đúng mạng công ty để sử dụng tính năng điểm danh mobile.\n\n' + 
                      (data && data.message ? data.message : 'Vui lòng kiểm tra kết nối WiFi của bạn.'));
                return;
            }
        } else {
            // Nếu API lỗi, hiển thị thông báo
            alert('Không thể kiểm tra mạng. Vui lòng thử lại sau.');
            return;
        }
    } catch (error) {
        // Nếu có lỗi, hiển thị thông báo
        console.error('Network check error:', error);
        alert('Không thể kiểm tra mạng. Vui lòng thử lại sau.');
        return;
    }
    
    const modal = document.getElementById('mobileAttendanceModal');
    modal.style.display = 'flex';
    
    // Reset UI
    document.getElementById('mobileVideo').style.display = 'none';
    document.getElementById('mobileCanvas').style.display = 'block';
    document.getElementById('mobileLoadingOverlay').style.display = 'flex';
    document.getElementById('mobileFaceOverlay').style.display = 'none';
    document.getElementById('mobileSuccessOverlay').style.display = 'none';
    document.getElementById('mobileResult').innerHTML = '';
    
    // Start camera automatically
    startMobileCamera();
}

// Đóng modal điểm danh mobile
function closeMobileAttendanceModal() {
    const modal = document.getElementById('mobileAttendanceModal');
    modal.style.display = 'none';
    
    // Stop camera
    if (mobileStream) {
        mobileStream.getTracks().forEach(track => track.stop());
        mobileStream = null;
    }
    
    // Reset variables
    mobileCapturedImage = null;
}

// Bật camera mobile
async function startMobileCamera() {
    try {
        const video = document.getElementById('mobileVideo');
        const canvas = document.getElementById('mobileCanvas');
        const loadingOverlay = document.getElementById('mobileLoadingOverlay');
        const faceOverlay = document.getElementById('mobileFaceOverlay');
        
        // Request camera access with optimal settings for mobile
        mobileStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user', // Front camera
                width: { ideal: 800, max: 1280 },
                height: { ideal: 600, max: 720 },
                frameRate: { ideal: 30, max: 60 },
                // Tối ưu cho điện thoại
                focusMode: 'continuous',
                whiteBalanceMode: 'continuous',
                exposureMode: 'continuous'
            }
        });
        
        video.srcObject = mobileStream;
        video.style.display = 'block';
        canvas.style.display = 'none';
        
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            loadingOverlay.style.display = 'none';
            faceOverlay.style.display = 'block';
            
            console.log('Mobile camera resolution:', video.videoWidth, 'x', video.videoHeight);
        };
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        document.getElementById('mobileLoadingOverlay').style.display = 'none';
        showMobileResult('Không thể truy cập camera. Vui lòng cho phép quyền camera.', 'error');
    }
}

// Chụp ảnh và điểm danh
async function captureMobileImage() {
    if (!mobileStream) {
        showMobileResult('Camera chưa hoạt động. Vui lòng thử lại.', 'error');
        return;
    }
    
    try {
        const video = document.getElementById('mobileVideo');
        const canvas = document.getElementById('mobileCanvas');
        const ctx = canvas.getContext('2d');
        const loadingOverlay = document.getElementById('mobileLoadingOverlay');
        const successOverlay = document.getElementById('mobileSuccessOverlay');
        
        // Show loading
        loadingOverlay.style.display = 'flex';
        
        // Set canvas size to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob with high quality for mobile
        canvas.toBlob(async function(blob) {
            mobileCapturedImage = blob;
            
            console.log('Captured image size:', blob.size, 'bytes');
            console.log('Canvas resolution:', canvas.width, 'x', canvas.height);
            
        // Hide loading overlay
        loadingOverlay.style.display = 'none';
        
        // Perform attendance (không hiển thị success overlay ở đây)
        await markMobileAttendance();
        }, 'image/jpeg', 0.9); // Tăng quality lên 90% cho mobile
        
    } catch (error) {
        console.error('Error capturing photo:', error);
        document.getElementById('mobileLoadingOverlay').style.display = 'none';
        showMobileResult('Lỗi khi chụp ảnh. Vui lòng thử lại.', 'error');
    }
}

        // Thực hiện điểm danh mobile với face recognition
        async function markMobileAttendance() {
            if (!mobileCapturedImage) {
                showMobileResult('Không có ảnh để điểm danh.', 'error');
                speak('Không có ảnh để điểm danh');
                return;
            }
            
            try {
                // Create FormData
                const formData = new FormData();
                formData.append('image', mobileCapturedImage, 'mobile_attendance.jpg');
                
                // Send to mobile face attendance API
                const response = await fetch('/doanqlns/api/mobile_face_attendance.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const result = await response.json();
                    if (result.success) {
                        // Hiển thị success overlay
                        showMobileSuccessOverlay();
                        
                        // Nói thông báo thành công
                        speak('Điểm danh thành công');
                        
                        // Hiển thị thông tin chi tiết
                        const successMessage = `
                            <h4><i class="fas fa-check-circle"></i> Điểm danh thành công!</h4>
                            <p><strong>Nhân viên:</strong> ${result.employee_name}</p>
                            <p><strong>Loại:</strong> ${result.attendance_type}</p>
                            <p><strong>Thời gian:</strong> ${result.time}</p>
                            <p><strong>Độ tin cậy:</strong> ${result.confidence}%</p>
                            <p><strong>Anti-spoofing:</strong> ${result.anti_spoofing_passed ? 'Đã vượt qua' : 'Không vượt qua'}</p>
                        `;
                        showMobileResult(successMessage, 'success');
                        
                        // Close modal after 5 seconds
                        setTimeout(() => {
                            closeMobileAttendanceModal();
                        }, 5000);
                    } else {
                        // Ẩn success overlay nếu có
                        hideMobileSuccessOverlay();
                        
                        // Nói thông báo lỗi
                        speak('Xin vui lòng thử lại');
                        
                        showMobileResult('Lỗi điểm danh: ' + (result.message || 'Không xác định'), 'error');
                        console.error('Mobile Face Attendance API Error:', result);
                    }
                } else {
                    // Ẩn success overlay nếu có
                    hideMobileSuccessOverlay();
                    
                    // Nói thông báo lỗi
                    speak('Xin vui lòng thử lại');
                    
                    showMobileResult('Lỗi kết nối server. Vui lòng thử lại.', 'error');
                }
                
            } catch (error) {
                console.error('Error taking attendance:', error);
                
                // Ẩn success overlay nếu có
                hideMobileSuccessOverlay();
                
                // Nói thông báo lỗi
                speak('Xin vui lòng thử lại');
                
                showMobileResult('Lỗi hệ thống. Vui lòng thử lại sau.', 'error');
            }
        }

// Hiển thị kết quả mobile
function showMobileResult(message, type) {
    const resultDiv = document.getElementById('mobileResult');
    const className = `mobile-result-${type}`;
    resultDiv.innerHTML = `<div class="${className}">${message}</div>`;
}

// Hàm nói thông báo bằng loa (giống như hệ thống gốc)
function speak(text) {
    try {
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'vi-VN';
        utter.rate = 1.0;
        utter.pitch = 1.0;
        utter.volume = 1.0;
        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(utter);
    } catch (e) {
        console.warn('Speech synthesis error:', e);
    }
}

// Hiển thị success overlay (giống như hệ thống gốc)
function showMobileSuccessOverlay() {
    const successOverlay = document.getElementById('mobileSuccessOverlay');
    if (successOverlay) {
        successOverlay.style.display = 'flex';
        setTimeout(() => {
            successOverlay.style.display = 'none';
        }, 3000);
    }
}

// Ẩn success overlay
function hideMobileSuccessOverlay() {
    const successOverlay = document.getElementById('mobileSuccessOverlay');
    if (successOverlay) {
        successOverlay.style.display = 'none';
    }
}

// Add event listener for capture button
document.addEventListener('DOMContentLoaded', function() {
    const mobileCaptureBtn = document.getElementById('mobileCapture');
    if (mobileCaptureBtn) {
        mobileCaptureBtn.addEventListener('click', captureMobileImage);
    }
});

// Nhận thông điệp từ iframe để cập nhật sau khi điểm danh thành công
window.addEventListener('message', async function(event) {
    try {
        const data = event.data || {};
        if (data.type !== 'faceAttendanceSuccess') return;

        const numericId = parseInt(data.student_id, 10);
        if (isNaN(numericId)) return;

        // Chỉ cho phép điểm danh cho chính chủ tài khoản
        if (CURRENT_EMPLOYEE_ID && numericId !== CURRENT_EMPLOYEE_ID) {
            alert('Bạn không phải là chủ tài khoản. Điểm danh thất bại.');
            return;
        }

        await loadSidebarAttendanceConfig();

        const now = new Date();
        const yyyy = now.getFullYear();
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const dd = String(now.getDate()).padStart(2, '0');
        const hh = String(now.getHours()).padStart(2, '0');
        const mi = String(now.getMinutes()).padStart(2, '0');
        const ss = String(now.getSeconds()).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;
        const timeStr = `${hh}:${mi}:${ss}`;

        const currentTime = now.getHours() * 60 + now.getMinutes();
        const attendanceInfo = determineAttendanceStatus(currentTime);

        let gioVao = null;
        let gioTrua = null;
        let gioRa = null;
        let trangThaiSang = null;
        let trangThaiTrua = null;
        let trangThaiChieu = null;

        if (attendanceInfo.type === 'sang') {
            gioVao = timeStr;
            trangThaiSang = attendanceInfo.status;
        } else if (attendanceInfo.type === 'trua') {
            gioTrua = timeStr;
            trangThaiTrua = attendanceInfo.status;
        } else if (attendanceInfo.type === 'chieu') {
            gioRa = timeStr;
            trangThaiChieu = attendanceInfo.status;
        }

        let finalTrangThai = 'Chưa điểm danh';
        const statuses = [trangThaiSang, trangThaiTrua, trangThaiChieu].filter(Boolean);
        if (statuses.length > 0) {
            if (statuses.includes('Đi trễ')) finalTrangThai = 'Đi trễ';
            else if (statuses.includes('Ra sớm')) finalTrangThai = 'Ra sớm';
            else if (statuses.includes('Đúng giờ')) finalTrangThai = 'Đúng giờ';
        }

        const shiftKey = `${numericId}-${dateStr}-${attendanceInfo.type}`;
        window.sidebarShiftAttendance = window.sidebarShiftAttendance || new Set();
        if (window.sidebarShiftAttendance.has(shiftKey)) {
            alert('Bạn đã điểm danh ca này rồi!');
            return;
        }

        const payload = {
            id_nhan_vien: numericId,
            ngay_lam_viec: dateStr,
            gio_vao: gioVao,
            gio_trua: gioTrua,
            gio_ra: gioRa,
            trang_thai_sang: trangThaiSang,
            trang_thai_trua: trangThaiTrua,
            trang_thai_chieu: trangThaiChieu,
            trang_thai: finalTrangThai,
            month: now.getMonth() + 1,
            year: now.getFullYear()
        };

        const checkResp = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${numericId}&ngay_cham_cong=${dateStr}`);
        const existingRaw = await checkResp.json();
        const existing = Array.isArray(existingRaw)
            ? existingRaw
            : (existingRaw && Array.isArray(existingRaw.data) ? existingRaw.data : []);
        let resp;

        if (existing && existing.length > 0) {
            const existingRecord = existing[0];
            const shiftRecorded = (
                (attendanceInfo.type === 'sang' && existingRecord.gio_vao) ||
                (attendanceInfo.type === 'trua' && existingRecord.gio_trua) ||
                (attendanceInfo.type === 'chieu' && existingRecord.gio_ra)
            );
            if (shiftRecorded) {
                alert('Bạn đã điểm danh ca này rồi!');
                return;
            }
        }

        if (existing && existing.length > 0) {
            const existingRecord = existing[0];
            const finalTrangThaiSang = trangThaiSang !== null ? trangThaiSang : existingRecord.trang_thai_sang;
            const finalTrangThaiTrua = trangThaiTrua !== null ? trangThaiTrua : existingRecord.trang_thai_trua;
            const finalTrangThaiChieu = trangThaiChieu !== null ? trangThaiChieu : existingRecord.trang_thai_chieu;
            let mergedTrangThai = 'Chưa điểm danh';
            const mergedStatuses = [finalTrangThaiSang, finalTrangThaiTrua, finalTrangThaiChieu].filter(Boolean);
            if (mergedStatuses.length > 0) {
                if (mergedStatuses.includes('Đi trễ')) mergedTrangThai = 'Đi trễ';
                else if (mergedStatuses.includes('Ra sớm')) mergedTrangThai = 'Ra sớm';
                else if (mergedStatuses.includes('Đúng giờ')) mergedTrangThai = 'Đúng giờ';
            }

            const updatePayload = {
                ...payload,
                gio_vao: gioVao !== null ? gioVao : existingRecord.gio_vao,
                gio_trua: gioTrua !== null ? gioTrua : existingRecord.gio_trua,
                gio_ra: gioRa !== null ? gioRa : existingRecord.gio_ra,
                trang_thai_sang: finalTrangThaiSang,
                trang_thai_trua: finalTrangThaiTrua,
                trang_thai_chieu: finalTrangThaiChieu,
                trang_thai: mergedTrangThai
            };

            resp = await fetch(`http://localhost/doanqlns/index.php/api/chamcong?id_nhan_vien=${numericId}&ngay_cham_cong=${dateStr}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(updatePayload)
            });
        } else {
            const newPayload = {
                ...payload,
                trang_thai: (trangThaiSang || trangThaiTrua || trangThaiChieu) ? 'Đúng giờ' : 'Chưa điểm danh'
            };
            resp = await fetch('http://localhost/doanqlns/index.php/api/chamcong', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(newPayload)
            });
        }

        const resJson = await resp.json();
        if (!resJson.success) {
            console.warn('Ghi nhận điểm danh từ nhận diện khuôn mặt thất bại:', resJson.message);
            alert('Điểm danh thất bại: ' + resJson.message);
            return;
        }

        window.sidebarShiftAttendance.add(shiftKey);

        let timeType = '';
        if (gioVao) timeType = 'Giờ Vào';
        else if (gioTrua) timeType = 'Giờ Trưa';
        else if (gioRa) timeType = 'Giờ Ra';

        alert(`Điểm danh thành công! (${timeType || 'Giờ Vào'})`);
        closeSidebarFaceModal();
    } catch (err) {
        console.warn('Không thể xử lý message từ iframe:', err);
    }
});
</script>

<style>
/* Sidebar scroll styles */
#layout-menu {
    height: 100vh !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 1000 !important;
}

/* Custom scrollbar for sidebar */
#layout-menu::-webkit-scrollbar {
    width: 6px;
}

#layout-menu::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

#layout-menu::-webkit-scrollbar-thumb {
    background:rgb(250, 249, 249);
    border-radius: 3px;
}

#layout-menu::-webkit-scrollbar-thumb:hover {
    background:rgb(116, 249, 242);
}

/* Main content margin for fixed sidebar */
body {
    margin-left: 0 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #layout-menu {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    body {
        margin-left: 0 !important;
    }
    
    #layout-menu.menu-open {
        transform: translateX(0);
    }
}

/* Modern dropdown menu styles */
.menu-item.has-sub > .menu-link {
    position: relative;
    padding-right: 35px;
    background: transparent !important;
    border: none !important;
    color: #495057 !important;
    transition: all 0.2s ease !important;
}

.menu-item.has-sub > .menu-link .menu-icon {
    color: #6c757d !important;
}

.menu-arrow {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 10px;
    color: #9e9e9e;
    transition: transform 0.2s ease;
}

.menu-item.open > .menu-link {
    background: #f8f9fa !important;
    color: #1976d2 !important;
    font-weight: 600 !important;
}

.menu-item.open > .menu-link .menu-icon {
    color: #1976d2 !important;
    font-weight: bold !important;
}

.menu-item.open > .menu-link .menu-arrow {
    transform: translateY(-50%) rotate(90deg);
    color: #1976d2;
}

.menu-item.has-sub > .menu-link:hover {
    background: #f8f9fa !important;
    color: #1976d2 !important;
}

.menu-item.has-sub > .menu-link:hover .menu-icon {
    color: #1976d2 !important;
}

.menu-sub {
    display: none !important;
    padding: 4px 0;
    background-color: transparent;
    list-style: none;
    margin: 0 0 0 20px;
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.menu-item.open > .menu-sub {
    display: block !important;
}

/* Force show submenu when open */
#layout-menu .menu-item.open .menu-sub {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.menu-sub .menu-item {
    margin: 0;
}

#layout-menu .menu-sub .menu-link {
    padding: 10px 16px !important;
    font-size: 13px !important;
    font-weight: 400 !important;
    color: #6c757d !important;
    line-height: 1.4 !important;
    border-radius: 6px !important;
    margin: 1px 0 !important;
    background-color: transparent !important;
    border: none !important;
    transition: all 0.2s ease !important;
    text-shadow: none !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

#layout-menu .menu-sub .menu-link:hover {
    background-color: #f8f9fa !important;
    color:rgb(130, 183, 236) !important;
}

#layout-menu .menu-sub .menu-item.active > .menu-link {
    background-color: #e3f2fd !important;
    color: #000000 !important;
    font-weight: 700 !important;
    text-shadow: none !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    filter: none !important;
    opacity: 1 !important;
}
</style>

<script>
// Enhanced dropdown menu functionality like CRM example
document.addEventListener('DOMContentLoaded', function() {
    const menuToggles = document.querySelectorAll('.menu-toggle');
    
    menuToggles.forEach((toggle) => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const menuItem = this.closest('.menu-item');
            const submenu = menuItem.querySelector('.menu-sub');
            const arrow = menuItem.querySelector('.menu-arrow');
            
            if (submenu) {
                // Close other open menus with smooth animation
                document.querySelectorAll('.menu-item.open').forEach(openItem => {
                    if (openItem !== menuItem) {
                        const otherSubmenu = openItem.querySelector('.menu-sub');
                        const otherArrow = openItem.querySelector('.menu-arrow');
                        
                        // Smooth close animation
                        if (otherSubmenu) {
                            otherSubmenu.style.opacity = '0';
                            otherSubmenu.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                openItem.classList.remove('open');
                                otherSubmenu.style.display = 'none';
                            }, 150);
                        }
                        
                        if (otherArrow) {
                            otherArrow.style.transform = 'translateY(-50%) rotate(0deg)';
                        }
                    }
                });
                
                // Toggle current menu with smooth animation
                const isOpen = menuItem.classList.contains('open');
                
                if (isOpen) {
                    // Close with animation
                    submenu.style.opacity = '0';
                    submenu.style.transform = 'translateY(-10px)';
                    if (arrow) {
                        arrow.style.transform = 'translateY(-50%) rotate(0deg)';
                    }
                    setTimeout(() => {
                        menuItem.classList.remove('open');
                        submenu.style.display = 'none';
                    }, 150);
                } else {
                    // Open with animation
                    menuItem.classList.add('open');
                    submenu.style.display = 'block';
                    submenu.style.opacity = '0';
                    submenu.style.transform = 'translateY(-10px)';
                    
                    if (arrow) {
                        arrow.style.transform = 'translateY(-50%) rotate(90deg)';
                    }
                    
                    // Trigger animation
                    setTimeout(() => {
                        submenu.style.opacity = '1';
                        submenu.style.transform = 'translateY(0)';
                    }, 10);
                }
            }
        });
    });
});
</script>