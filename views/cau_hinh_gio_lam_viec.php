<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Cấu hình giờ làm việc
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" onclick="themCauHinhMoi()">
                            <i class="fas fa-plus"></i> Thêm cấu hình mới
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Cấu hình hiện tại -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5>Cấu hình hiện tại</h5>
                            <div id="cau-hinh-hien-tai" class="alert alert-info">
                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                            </div>
                        </div>
                    </div>

                    <!-- Danh sách cấu hình -->
                    <div class="row">
                        <div class="col-12">
                            <h5>Lịch sử cấu hình</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="bang-cau-hinh">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên cấu hình</th>
                                            <th>Giờ sáng</th>
                                            <th>Giờ trưa</th>
                                            <th>Giờ chiều</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày tạo</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="danh-sach-cau-hinh">
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <i class="fas fa-spinner fa-spin"></i> Đang tải...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal thêm/sửa cấu hình -->
<div class="modal fade" id="modal-cau-hinh" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tieu-de-modal">Thêm cấu hình mới</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-cau-hinh">
                    <input type="hidden" id="id-cau-hinh" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ten-cau-hinh">Tên cấu hình *</label>
                                <input type="text" class="form-control" id="ten-cau-hinh" name="ten_cau_hinh" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ghi-chu">Ghi chú</label>
                                <input type="text" class="form-control" id="ghi-chu" name="ghi_chu">
                            </div>
                        </div>
                    </div>

                    <!-- Giờ sáng -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Giờ sáng</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-sang-bat-dau">Bắt đầu điểm danh sáng</label>
                                        <input type="time" class="form-control" id="gio-sang-bat-dau" name="gio_sang_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-sang-ket-thuc">Kết thúc điểm danh sáng đúng giờ</label>
                                        <input type="time" class="form-control" id="gio-sang-ket-thuc" name="gio_sang_ket_thuc" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-sang-tre-bat-dau">Bắt đầu điểm danh sáng trễ</label>
                                        <input type="time" class="form-control" id="gio-sang-tre-bat-dau" name="gio_sang_tre_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-sang-tre-ket-thuc">Kết thúc điểm danh sáng trễ</label>
                                        <input type="time" class="form-control" id="gio-sang-tre-ket-thuc" name="gio_sang_tre_ket_thuc" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Giờ trưa -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Giờ trưa</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-trua-bat-dau">Bắt đầu điểm danh trưa</label>
                                        <input type="time" class="form-control" id="gio-trua-bat-dau" name="gio_trua_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-trua-ket-thuc">Kết thúc điểm danh trưa đúng giờ</label>
                                        <input type="time" class="form-control" id="gio-trua-ket-thuc" name="gio_trua_ket_thuc" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-trua-tre-bat-dau">Bắt đầu điểm danh trưa trễ</label>
                                        <input type="time" class="form-control" id="gio-trua-tre-bat-dau" name="gio_trua_tre_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-trua-tre-ket-thuc">Kết thúc điểm danh trưa trễ</label>
                                        <input type="time" class="form-control" id="gio-trua-tre-ket-thuc" name="gio_trua_tre_ket_thuc" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Giờ chiều -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Giờ chiều</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-chieu-ra-som-bat-dau">Bắt đầu điểm danh chiều ra sớm</label>
                                        <input type="time" class="form-control" id="gio-chieu-ra-som-bat-dau" name="gio_chieu_ra_som_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-chieu-ra-som-ket-thuc">Kết thúc điểm danh chiều ra sớm</label>
                                        <input type="time" class="form-control" id="gio-chieu-ra-som-ket-thuc" name="gio_chieu_ra_som_ket_thuc" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-chieu-bat-dau">Bắt đầu điểm danh chiều đúng giờ</label>
                                        <input type="time" class="form-control" id="gio-chieu-bat-dau" name="gio_chieu_bat_dau" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gio-chieu-ket-thuc">Kết thúc điểm danh chiều</label>
                                        <input type="time" class="form-control" id="gio-chieu-ket-thuc" name="gio_chieu_ket_thuc" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="luuCauHinh()">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cauHinhHienTai = null;

// Tải cấu hình hiện tại
async function taiCauHinhHienTai() {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec/hien-tai');
        const result = await response.json();
        
        if (result.success) {
            cauHinhHienTai = result.data;
            hienThiCauHinhHienTai(result.data);
        } else {
            document.getElementById('cau-hinh-hien-tai').innerHTML = 
                '<div class="alert alert-warning">Không có cấu hình nào</div>';
        }
    } catch (error) {
        console.error('Lỗi tải cấu hình:', error);
        document.getElementById('cau-hinh-hien-tai').innerHTML = 
            '<div class="alert alert-danger">Lỗi tải cấu hình</div>';
    }
}

// Hiển thị cấu hình hiện tại
function hienThiCauHinhHienTai(cauHinh) {
    const html = `
        <div class="row">
            <div class="col-md-3">
                <strong>Giờ sáng:</strong><br>
                Đúng giờ: ${cauHinh.gio_sang_bat_dau} - ${cauHinh.gio_sang_ket_thuc}<br>
                Đi trễ: ${cauHinh.gio_sang_tre_bat_dau} - ${cauHinh.gio_sang_tre_ket_thuc}
            </div>
            <div class="col-md-3">
                <strong>Giờ trưa:</strong><br>
                Đúng giờ: ${cauHinh.gio_trua_bat_dau} - ${cauHinh.gio_trua_ket_thuc}<br>
                Đi trễ: ${cauHinh.gio_trua_tre_bat_dau} - ${cauHinh.gio_trua_tre_ket_thuc}
            </div>
            <div class="col-md-3">
                <strong>Giờ chiều:</strong><br>
                Ra sớm: ${cauHinh.gio_chieu_ra_som_bat_dau} - ${cauHinh.gio_chieu_ra_som_ket_thuc}<br>
                Đúng giờ: ${cauHinh.gio_chieu_bat_dau} - ${cauHinh.gio_chieu_ket_thuc}
            </div>
            <div class="col-md-3">
                <strong>Ghi chú:</strong><br>
                ${cauHinh.ghi_chu || 'Không có'}
            </div>
        </div>
    `;
    document.getElementById('cau-hinh-hien-tai').innerHTML = html;
}

// Tải danh sách cấu hình
async function taiDanhSachCauHinh() {
    try {
        const response = await fetch('http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec');
        const result = await response.json();
        
        if (result.success) {
            hienThiDanhSachCauHinh(result.data);
        } else {
            document.getElementById('danh-sach-cau-hinh').innerHTML = 
                '<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>';
        }
    } catch (error) {
        console.error('Lỗi tải danh sách:', error);
        document.getElementById('danh-sach-cau-hinh').innerHTML = 
            '<tr><td colspan="8" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>';
    }
}

// Hiển thị danh sách cấu hình
function hienThiDanhSachCauHinh(danhSach) {
    const html = danhSach.map(cauHinh => `
        <tr>
            <td>${cauHinh.id}</td>
            <td>${cauHinh.ten_cau_hinh}</td>
            <td>${cauHinh.gio_sang_bat_dau} - ${cauHinh.gio_sang_ket_thuc}</td>
            <td>${cauHinh.gio_trua_bat_dau} - ${cauHinh.gio_trua_ket_thuc}</td>
            <td>${cauHinh.gio_chieu_bat_dau} - ${cauHinh.gio_chieu_ket_thuc}</td>
            <td>
                <span class="badge ${cauHinh.trang_thai === 'active' ? 'badge-success' : 'badge-secondary'}">
                    ${cauHinh.trang_thai === 'active' ? 'Hoạt động' : 'Không hoạt động'}
                </span>
            </td>
            <td>${new Date(cauHinh.ngay_tao).toLocaleDateString('vi-VN')}</td>
            <td>
                <button class="btn btn-sm btn-info" onclick="xemCauHinh(${cauHinh.id})">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning" onclick="suaCauHinh(${cauHinh.id})">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="xoaCauHinh(${cauHinh.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
    
    document.getElementById('danh-sach-cau-hinh').innerHTML = html;
}

// Thêm cấu hình mới
function themCauHinhMoi() {
    document.getElementById('tieu-de-modal').textContent = 'Thêm cấu hình mới';
    document.getElementById('form-cau-hinh').reset();
    document.getElementById('id-cau-hinh').value = '';
    $('#modal-cau-hinh').modal('show');
}

// Sửa cấu hình
async function suaCauHinh(id) {
    try {
        const response = await fetch(`http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const cauHinh = result.data;
            document.getElementById('tieu-de-modal').textContent = 'Sửa cấu hình';
            document.getElementById('id-cau-hinh').value = cauHinh.id;
            document.getElementById('ten-cau-hinh').value = cauHinh.ten_cau_hinh;
            document.getElementById('ghi-chu').value = cauHinh.ghi_chu || '';
            
            // Điền các giờ
            document.getElementById('gio-sang-bat-dau').value = cauHinh.gio_sang_bat_dau;
            document.getElementById('gio-sang-ket-thuc').value = cauHinh.gio_sang_ket_thuc;
            document.getElementById('gio-sang-tre-bat-dau').value = cauHinh.gio_sang_tre_bat_dau;
            document.getElementById('gio-sang-tre-ket-thuc').value = cauHinh.gio_sang_tre_ket_thuc;
            
            document.getElementById('gio-trua-bat-dau').value = cauHinh.gio_trua_bat_dau;
            document.getElementById('gio-trua-ket-thuc').value = cauHinh.gio_trua_ket_thuc;
            document.getElementById('gio-trua-tre-bat-dau').value = cauHinh.gio_trua_tre_bat_dau;
            document.getElementById('gio-trua-tre-ket-thuc').value = cauHinh.gio_trua_tre_ket_thuc;
            
            document.getElementById('gio-chieu-ra-som-bat-dau').value = cauHinh.gio_chieu_ra_som_bat_dau;
            document.getElementById('gio-chieu-ra-som-ket-thuc').value = cauHinh.gio_chieu_ra_som_ket_thuc;
            document.getElementById('gio-chieu-bat-dau').value = cauHinh.gio_chieu_bat_dau;
            document.getElementById('gio-chieu-ket-thuc').value = cauHinh.gio_chieu_ket_thuc;
            
            $('#modal-cau-hinh').modal('show');
        }
    } catch (error) {
        console.error('Lỗi tải cấu hình:', error);
        alert('Lỗi tải cấu hình');
    }
}

// Lưu cấu hình
async function luuCauHinh() {
    const formData = new FormData(document.getElementById('form-cau-hinh'));
    const data = Object.fromEntries(formData.entries());
    
    try {
        const id = document.getElementById('id-cau-hinh').value;
        const url = id ? 
            `http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec/${id}` : 
            'http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec';
        
        const response = await fetch(url, {
            method: id ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Lưu cấu hình thành công!');
            $('#modal-cau-hinh').modal('hide');
            taiCauHinhHienTai();
            taiDanhSachCauHinh();
        } else {
            alert('Lỗi: ' + result.message);
        }
    } catch (error) {
        console.error('Lỗi lưu cấu hình:', error);
        alert('Lỗi lưu cấu hình');
    }
}

// Xóa cấu hình
async function xoaCauHinh(id) {
    if (confirm('Bạn có chắc chắn muốn xóa cấu hình này?')) {
        try {
            const response = await fetch(`http://localhost/doanqlns/index.php/api/cau-hinh-gio-lam-viec/${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Xóa cấu hình thành công!');
                taiCauHinhHienTai();
                taiDanhSachCauHinh();
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Lỗi xóa cấu hình:', error);
            alert('Lỗi xóa cấu hình');
        }
    }
}

// Khởi tạo
document.addEventListener('DOMContentLoaded', function() {
    taiCauHinhHienTai();
    taiDanhSachCauHinh();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


