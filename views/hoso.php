<?php
require_once __DIR__ . '/../includes/check_login.php';
include(__DIR__ . '/../includes/header.php');
include(__DIR__ . '/../includes/sidebar.php');

// Include base URL helper
require_once __DIR__ . '/../includes/base_url.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Hồ Sơ Của Tôi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .profile-container {
            padding: 30px;
            background: #cadae9ff; /* Alice Blue */
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        .profile-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #bde0fe; /* Light blue border */
        }

        .profile-header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
        }

        .profile-header i {
            margin-right: 15px;
            color: #34badbff;
        }

        .profile-content {
            display: flex;
            gap: 40px;
        }

        .profile-left {
            flex: 0 0 250px;
        }

        .profile-right {
            flex: 1;
        }

        .avatar-section {
            margin-bottom: 30px;
        }

        .avatar-image {
            width: 250px;
            height: 250px;
            border-radius: 12px;
            object-fit: cover;
            border: 4px solid #34dbc8ff;
            box-shadow: 0 4px 8px rgba(248, 30, 30, 0.1);
        }

        .profile-section {
            background: #e6f3ff; /* Lighter blue background */
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #bde0fe;
        }

        .profile-section h2 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
            display: flex;
            align-items: center;
        }

        .profile-section h2 i {
            margin-right: 10px;
            color: #3498db;
        }

        .info-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-group.three-columns {
            grid-template-columns: repeat(3, 1fr);
        }

        .info-group.dependents {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        }

        .info-value small {
            display: block;
            margin-top: 5px;
            color: #6c757d;
        }

        .info-value strong {
            color: #2c3e50;
            font-size: 16px;
        }

        .info-item {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e3f2fd; /* Very light blue border */
        }

        .info-label {
            font-weight: 500;
            color: #7f8c8d;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }

        .info-value {
            color: #2c3e50;
            font-weight: 500;
            display: block;
            padding: 8px;
            background: #f8fbff; /* Very light blue background */
            border-radius: 6px;
            border: 1px solid #e3f2fd;
        }

        @media (max-width: 1024px) {
            .profile-content {
                flex-direction: column;
            }

            .profile-left {
                flex: none;
                display: flex;
                justify-content: center;
            }

            .avatar-section {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 768px) {
            .info-group {
                grid-template-columns: 1fr;
            }

            .info-group.three-columns {
                grid-template-columns: 1fr;
            }

            .info-group.dependents {
                grid-template-columns: 1fr;
            }

            .avatar-image {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="profile-container">
            <div class="profile-header">
                <h1><i class="fas fa-user-circle"></i> Hồ Sơ Của Tôi</h1>
            </div>

            <div class="profile-content">
                <div class="profile-left">
                    <div class="avatar-section">
                        <img id="avatarImage" src="../img/gdhoso.jpg" alt="Avatar" class="avatar-image">
                    </div>
                </div>

                <div class="profile-right">
                    <div class="profile-section">
                        <h2><i class="fas fa-info-circle"></i> Thông Tin Cá Nhân</h2>
                        <div class="info-group three-columns">
                            <div class="info-item">
                                <span class="info-label">Họ và Tên</span>
                                <span id="hoTen" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Giới Tính</span>
                                <span id="gioiTinh" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày Sinh</span>
                                <span id="ngaySinh" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">CCCD</span>
                                <span id="cccd" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày Cấp CCCD</span>
                                <span id="ngayCap" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nơi Cấp CCCD</span>
                                <span id="noiCap" class="info-value">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h2><i class="fas fa-address-card"></i> Thông Tin Liên Hệ</h2>
                        <div class="info-group">
                            <div class="info-item">
                                <span class="info-label">Email</span>
                                <span id="email" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Số Điện Thoại</span>
                                <span id="soDienThoai" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Địa Chỉ</span>
                                <span id="diaChi" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Nơi Thường Trú</span>
                                <span id="noiThuongTru" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Chỗ Ở Hiện Tại</span>
                                <span id="choOHienTai" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Dân Tộc</span>
                                <span id="danToc" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Quê Quán</span>
                                <span id="queQuan" class="info-value">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h2><i class="fas fa-briefcase"></i> Thông Tin Công Việc</h2>
                        <div class="info-group">
                            <div class="info-item">
                                <span class="info-label">Phòng Ban</span>
                                <span id="phongBan" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Chức Vụ</span>
                                <span id="chucVu" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày Vào Làm</span>
                                <span id="ngayVaoLam" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Loại Hợp Đồng</span>
                                <span id="loaiHopDong" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Lương Cơ Bản</span>
                                <span id="luongCoBan" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phụ Cấp Chức Vụ</span>
                                <span id="phuCapChucVu" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phụ Cấp Bằng Cấp</span>
                                <span id="phuCapBangCap" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phụ Cấp Khác</span>
                                <span id="phuCapKhac" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Trạng Thái</span>
                                <span id="trangThai" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Ngày Nghỉ Việc</span>
                                <span id="ngayNghiViec" class="info-value">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h2><i class="fas fa-university"></i> Thông Tin Ngân Hàng</h2>
                        <div class="info-group">
                            <div class="info-item">
                                <span class="info-label">Số Tài Khoản</span>
                                <span id="soTaiKhoan" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Tên Ngân Hàng</span>
                                <span id="tenNganHang" class="info-value">Loading...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Chi Nhánh Ngân Hàng</span>
                                <span id="chiNhanhNganHang" class="info-value">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h2><i class="fas fa-users"></i> Thông Tin Người Phụ Thuộc</h2>
                        <div id="nguoiPhuThuocList" class="info-group dependents">
                            <div class="info-item" style="grid-column: 1 / -1; text-align: center;">
                                <span class="info-label">Đang tải thông tin người phụ thuộc...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadEmployeeProfile();
        });

        // Hàm format ngày tháng
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
        }

        // Hàm format tiền tệ
        function formatCurrency(amount) {
            if (!amount) return '0';
            return new Intl.NumberFormat('vi-VN').format(amount);
        }

        // Hàm load thông tin người phụ thuộc
        async function loadNguoiPhuThuoc(nhanVienId) {
            try {
                console.log('Loading dependents for nhanVienId:', nhanVienId);
                const response = await fetch(`<?= BASE_URL ?>/index.php/api/nguoiphuthuoc?nhanvien=${nhanVienId}`);
                console.log('Dependents response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Failed to fetch dependents data`);
                }
                const data = await response.json();
                console.log('Dependents data:', data);
                
                const container = document.getElementById('nguoiPhuThuocList');
                container.innerHTML = '';
                
                if (data && data.length > 0) {
                    data.forEach((dependent, index) => {
                        const dependentItem = document.createElement('div');
                        dependentItem.className = 'info-item';
                        dependentItem.innerHTML = `
                            <span class="info-label">${dependent.quan_he}</span>
                            <span class="info-value">
                                <strong>${dependent.ho_ten}</strong><br>
                                <small>Ngày sinh: ${dependent.ngay_sinh ? formatDate(dependent.ngay_sinh) : 'N/A'}</small><br>
                                <small>CCCD: ${dependent.can_cuoc_cong_dan || 'N/A'}</small><br>
                                <small>Trạng thái: <span style="color: ${dependent.trang_thai === 'Đang phụ thuộc' ? '#28a745' : '#6c757d'}">${dependent.trang_thai}</span></small>
                            </span>
                        `;
                        container.appendChild(dependentItem);
                    });
                } else {
                    container.innerHTML = `
                        <div class="info-item" style="grid-column: 1 / -1; text-align: center;">
                            <span class="info-label">Không có người phụ thuộc</span>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading dependents:', error);
                const container = document.getElementById('nguoiPhuThuocList');
                container.innerHTML = `
                    <div class="info-item" style="grid-column: 1 / -1; text-align: center;">
                        <span class="info-label" style="color: #dc3545;">Lỗi khi tải thông tin người phụ thuộc</span>
                    </div>
                `;
            }
        }

        async function loadEmployeeProfile() {
            try {
                // Lấy thông tin nhân viên từ API dựa trên user_id trong session
                console.log('Loading profile from:', `<?= BASE_URL ?>/index.php/api/employee/profile`);
                const response = await fetch(`<?= BASE_URL ?>/index.php/api/employee/profile`);
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Failed to fetch profile data`);
                }
                const data = await response.json();
                console.log('Profile data:', data);
                
                if (data.success && data.data) {
                    const profile = data.data;
                    
                    // Cập nhật avatar nếu có
                    if (profile.hinh_anh) {
                        // Đảm bảo đường dẫn đúng
                        let imagePath = profile.hinh_anh;
                        if (!imagePath.startsWith('http') && !imagePath.startsWith('/')) {
                            imagePath = '../' + imagePath;
                        }
                        document.getElementById('avatarImage').src = imagePath;
                        console.log('Updated avatar to:', imagePath);
                    }
                    
                    // Cập nhật các trường thông tin
                    document.getElementById('hoTen').textContent = profile.ho_ten || 'N/A';
                    document.getElementById('gioiTinh').textContent = profile.gioi_tinh || 'N/A';
                    document.getElementById('ngaySinh').textContent = profile.ngay_sinh ? formatDate(profile.ngay_sinh) : 'N/A';
                    document.getElementById('cccd').textContent = profile.can_cuoc_cong_dan || 'N/A';
                    document.getElementById('ngayCap').textContent = profile.ngay_cap ? formatDate(profile.ngay_cap) : 'N/A';
                    document.getElementById('noiCap').textContent = profile.noi_cap || 'N/A';
                    document.getElementById('email').textContent = profile.email || 'N/A';
                    document.getElementById('soDienThoai').textContent = profile.so_dien_thoai || 'N/A';
                    document.getElementById('diaChi').textContent = profile.dia_chi || 'N/A';
                    document.getElementById('noiThuongTru').textContent = profile.noi_thuong_tru || 'N/A';
                    document.getElementById('choOHienTai').textContent = profile.cho_o_hien_tai || 'N/A';
                    document.getElementById('danToc').textContent = profile.dan_toc || 'N/A';
                    document.getElementById('queQuan').textContent = profile.que_quan || 'N/A';
                    document.getElementById('phongBan').textContent = profile.ten_phong_ban || 'N/A';
                    document.getElementById('chucVu').textContent = profile.ten_chuc_vu || 'N/A';
                    document.getElementById('ngayVaoLam').textContent = profile.ngay_vao_lam ? formatDate(profile.ngay_vao_lam) : 'N/A';
                    document.getElementById('loaiHopDong').textContent = profile.loai_hop_dong || 'N/A';
                    document.getElementById('luongCoBan').textContent = profile.luong_co_ban ? formatCurrency(profile.luong_co_ban) + ' VNĐ' : 'N/A';
                    document.getElementById('phuCapChucVu').textContent = profile.phu_cap_chuc_vu ? formatCurrency(profile.phu_cap_chuc_vu) + ' VNĐ' : 'N/A';
                    document.getElementById('phuCapBangCap').textContent = profile.phu_cap_bang_cap ? formatCurrency(profile.phu_cap_bang_cap) + ' VNĐ' : 'N/A';
                    document.getElementById('phuCapKhac').textContent = profile.phu_cap_khac ? formatCurrency(profile.phu_cap_khac) + ' VNĐ' : 'N/A';
                    document.getElementById('trangThai').textContent = profile.trang_thai || 'N/A';
                    document.getElementById('ngayNghiViec').textContent = profile.ngay_nghi_viec ? formatDate(profile.ngay_nghi_viec) : 'Chưa nghỉ việc';
                    
                    // Cập nhật thông tin ngân hàng
                    document.getElementById('soTaiKhoan').textContent = profile.so_tai_khoan || 'N/A';
                    document.getElementById('tenNganHang').textContent = profile.ten_ngan_hang || 'N/A';
                    document.getElementById('chiNhanhNganHang').textContent = profile.chi_nhanh_ngan_hang || 'N/A';
                    
                    // Load thông tin người phụ thuộc
                    loadNguoiPhuThuoc(profile.id_nhan_vien);
                }
            } catch (error) {
                console.error('Error loading profile:', error);
                alert('Có lỗi xảy ra khi tải thông tin hồ sơ');
            }
        }
    </script>

    <?php include(__DIR__ . '/../includes/footer.php'); ?>
</body>
</html> 