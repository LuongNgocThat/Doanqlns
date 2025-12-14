<?php
require_once __DIR__ . '/../includes/check_login.php';
require_once __DIR__ . '/../includes/base_url.php';

$id_nhan_vien = $_SESSION['id_nhan_vien'];
$ten_nhan_vien = $_SESSION['ho_ten'] ?? 'N/A'; // Lấy tên nhân viên từ session
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM Pro - Đăng Ký Người Phụ Thuộc</title>

    <!-- Font Awesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- CSS chính -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/stylenghiphep.css">

    <!-- CSS riêng -->
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, rgb(245, 246, 249) 0%, rgb(146, 201, 235) 100%);
            min-height: 100vh;
        }

        .profile-container {
            max-width: 1200px;
            margin: 20px 20px 20px 370px;
            padding: 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin: -20px -20px 30px -20px;
            border-radius: 20px 20px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
        }

        .table-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-add {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            color: white;
            background: #28a745;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background: #218838;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .data-table tr:hover {
            background: #f0f4ff;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-style: italic;
            font-size: 1.1rem;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
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
            border-radius: 15px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: fadeInUp 0.3s ease-out;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }

        .close {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.25);
        }

        .modal-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6cd8;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5c636a;
        }

        .error {
            color: #e74c3c;
            background: #fdf2f2;
            border-color: #f5c6cb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .profile-container {
                margin: 10px;
                padding: 15px;
                border-radius: 15px;
            }

            .table-header {
                margin: -15px -15px 20px -15px;
                padding: 15px;
                border-radius: 15px 15px 0 0;
                flex-direction: column;
                gap: 10px;
            }

            .table-header h2 {
                font-size: 1.5rem;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .modal-content {
                width: 95%;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .profile-container {
                margin: 15px 15px 15px 300px;
            }

            .data-table th,
            .data-table td {
                padding: 12px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="table-header">
            <h2><i class="fas fa-user-plus"></i> Đăng Ký Người Phụ Thuộc</h2>
            <button class="btn-add" onclick="showAddNguoiPhuThuocModal()">
                <i class="fas fa-plus"></i> Thêm người phụ thuộc
            </button>
        </div>
        <div class="table-container">
            <table class="data-table" id="nguoiPhuThuocTable">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Họ tên</th>
                        <th>Mối quan hệ</th>
                        <th>Ngày sinh</th>
                        <th>CCCD/CMND</th>
                        <th>Minh chứng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="no-data">
                            <div class="loading-spinner"></div> Đang tải...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Thêm Người Phụ Thuộc -->
    <div class="modal" id="nguoiPhuThuocModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="nguoiPhuThuocModalTitle">Thêm người phụ thuộc</h2>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="nguoiPhuThuocForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_nhan_vien" value="<?php echo $id_nhan_vien; ?>">
                    <input type="hidden" id="trangThaiHidden" name="trang_thai" value="Chờ duyệt">
                    <input type="hidden" id="ngayBatDauHidden" name="ngay_bat_dau" value="">
                    <div class="form-group">
                        <label>Nhân viên</label>
                        <input type="text" value="<?php echo htmlspecialchars($ten_nhan_vien); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="hoTen">Họ tên người phụ thuộc * <span class="text-danger">*</span></label>
                        <input type="text" id="hoTen" name="ho_ten" required>
                    </div>
                    <div class="form-group">
                        <label for="moiQuanHe">Mối quan hệ * <span class="text-danger">*</span></label>
                        <select id="moiQuanHe" name="quan_he" required>
                            <option value="">Chọn mối quan hệ</option>
                            <option value="Vợ/Chồng">Vợ/Chồng</option>
                            <option value="Con">Con</option>
                            <option value="Cha">Cha</option>
                            <option value="Mẹ">Mẹ</option>
                            <option value="Anh">Anh</option>
                            <option value="Chị">Chị</option>
                            <option value="Em">Em</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ngaySinh">Ngày sinh * <span class="text-danger">*</span></label>
                        <input type="date" id="ngaySinh" name="ngay_sinh" required>
                    </div>
                    <div class="form-group">
                        <label for="canCuocCongDan">CCCD/CMND * <span class="text-danger">*</span></label>
                        <input type="text" id="canCuocCongDan" name="can_cuoc_cong_dan" required>
                    </div>
                    <div class="form-group">
                        <label for="minhChung">Minh chứng (đường link)</label>
                        <input type="url" id="minhChung" name="minh_chung" placeholder="https://...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="button" class="btn btn-primary" onclick="saveNguoiPhuThuoc()">Gửi Yêu Cầu</button>
            </div>
        </div>
    </div>

    <script>
    let nguoiPhuThuocData = [];
    const API_BASE_CANDIDATES = Array.from(new Set([
        '<?= rtrim($base_url, '/') ?>/index.php/api',
        `${window.location.origin}/doanqlns/index.php/api`,
        `${window.location.origin}/index.php/api`
    ])).filter(Boolean);

    function buildFetchOptions(options = {}) {
        const finalOptions = {
            method: options.method || 'GET',
            credentials: 'include'
        };

        const isFormData = options.body instanceof FormData;
        const defaultHeaders = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (!isFormData) {
            defaultHeaders['Content-Type'] = 'application/json';
        }

        finalOptions.headers = {
            ...defaultHeaders,
            ...(options.headers || {})
        };

        if (options.body) {
            finalOptions.body = options.body;
        }

        return finalOptions;
    }

    async function callApi(endpoint, options = {}) {
        const normalizedEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`;
        let lastError = null;

        for (const base of API_BASE_CANDIDATES) {
            const normalizedBase = base.replace(/\/+$/, '');
            const url = `${normalizedBase}${normalizedEndpoint}`;
            try {
                const response = await fetch(url, buildFetchOptions(options));
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                const text = await response.text();
                const cleaned = text.trim();
                return cleaned ? JSON.parse(cleaned) : {};
            } catch (error) {
                console.warn('[nguoiPhuThuoc] API call failed:', url, error);
                lastError = error;
            }
        }

        throw lastError || new Error('Không thể kết nối API');
    }

    // Format ngày tháng
    function formatDate(dateString) {
        if (!dateString || dateString === '0000-00-00') return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Set ngày_bat_dau mặc định = hôm nay
    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date().toISOString().split('T')[0];
        const hiddenStart = document.getElementById('ngayBatDauHidden');
        if (hiddenStart) hiddenStart.value = today;
    });

    // Load dữ liệu người phụ thuộc (ưu tiên lấy theo nhân viên hiện tại)
    async function loadNguoiPhuThuoc() {
        const tbody = document.querySelector('#nguoiPhuThuocTable tbody');
        const nhanVienId = <?php echo (int)$id_nhan_vien; ?>;

        const extractList = (payload) => {
            if (!payload) return [];
            if (Array.isArray(payload)) return payload;
            if (Array.isArray(payload.data)) return payload.data;
            if (Array.isArray(payload.items)) return payload.items;
            if (payload.success === true && Array.isArray(payload.data)) return payload.data;
            return [];
        };

        try {
            let list = [];

            try {
                const payload = await callApi(`/nguoiphuthuoc?nhanvien=${nhanVienId}`);
                list = extractList(payload);
            } catch (primaryError) {
                console.warn('[nguoiPhuThuoc] Primary fetch failed, trying fallback list...', primaryError);
            }

            if (!list.length) {
                const allPayload = await callApi('/nguoiphuthuoc');
                const allList = extractList(allPayload);
                nguoiPhuThuocData = allList.filter(item => String(item.id_nhan_vien) === String(nhanVienId));
            } else {
                nguoiPhuThuocData = list.filter(item => String(item.id_nhan_vien) === String(nhanVienId));
            }

            // Hiển thị
            displayNguoiPhuThuocData();
        } catch (error) {
            console.error('Error loading dependents:', error);
            tbody.innerHTML = `<tr><td colspan="7" class="no-data error">Lỗi tải dữ liệu: ${error.message || ''}</td></tr>`;
        }
    }

    // Hiển thị dữ liệu người phụ thuộc
    function displayNguoiPhuThuocData() {
        const tbody = document.querySelector('#nguoiPhuThuocTable tbody');
        tbody.innerHTML = '';

        if (nguoiPhuThuocData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="no-data">Bạn chưa đăng ký người phụ thuộc nào.</td></tr>';
            return;
        }

        nguoiPhuThuocData.forEach(item => {
            const row = document.createElement('tr');
            const statusClass = item.trang_thai === 'Đã duyệt' ? 'status-approved' :
                               item.trang_thai === 'Từ chối' ? 'status-rejected' : 'status-pending';
            const minhChung = item.minh_chung_url || item.minh_chung || '';
            row.innerHTML = `
                <td><?php echo htmlspecialchars($ten_nhan_vien); ?></td>
                <td>${item.ho_ten || 'N/A'}</td>
                <td>${(item.quan_he || item.moi_quan_he) || 'N/A'}</td>
                <td>${formatDate(item.ngay_sinh)}</td>
                <td>${item.can_cuoc_cong_dan || 'N/A'}</td>
                <td>${minhChung ? `<a href="${minhChung}" target="_blank">Xem</a>` : '—'}</td>
                <td><span class="status-badge ${statusClass}">${item.trang_thai || 'Chờ duyệt'}</span></td>
            `;
            tbody.appendChild(row);
        });
    }

    // Hiển thị modal thêm người phụ thuộc
    function showAddNguoiPhuThuocModal() {
        document.getElementById('nguoiPhuThuocModalTitle').textContent = 'Thêm người phụ thuộc';
        const form = document.getElementById('nguoiPhuThuocForm');
        form.reset();
        // Set mặc định giống màn quản trị: ngày bắt đầu = hôm nay, trạng thái chờ duyệt
        const today = new Date().toISOString().split('T')[0];
        const startHidden = document.getElementById('ngayBatDauHidden');
        if (startHidden) startHidden.value = today;
        const trangThaiHidden = document.getElementById('trangThaiHidden');
        if (trangThaiHidden) trangThaiHidden.value = 'Chờ duyệt';
        // ho_ten đã là hidden preset từ session
        document.getElementById('nguoiPhuThuocModal').style.display = 'flex';
    }

    // Đóng modal
    function closeModal() {
        document.getElementById('nguoiPhuThuocModal').style.display = 'none';
    }

    // Lưu người phụ thuộc
    async function saveNguoiPhuThuoc() {
        const form = document.getElementById('nguoiPhuThuocForm');
        const formData = new FormData(form);
        // Đặt trạng thái mặc định chờ duyệt
        if (!formData.get('trang_thai')) {
            formData.append('trang_thai', 'Chờ duyệt');
        }
        // Bảo đảm gửi họ_tên của nhân viên hiện tại
        if (!formData.get('ho_ten')) {
            formData.append('ho_ten', '<?php echo htmlspecialchars($ten_nhan_vien); ?>');
        }
        // Map trường moi_quan_he -> quan_he nếu cần
        if (!formData.get('quan_he') && formData.get('moi_quan_he')) {
            formData.append('quan_he', formData.get('moi_quan_he'));
        }
        // Đặt ngày_bat_dau nếu backend yêu cầu (theo DB)
        if (!formData.get('ngay_bat_dau')) {
            const today = new Date().toISOString().split('T')[0];
            formData.append('ngay_bat_dau', today);
        }

        try {
            const result = await callApi('/nguoiphuthuoc', {
                method: 'POST',
                body: formData,
                headers: {}
            });

            if (result.success) {
                alert('Đã gửi yêu cầu đăng ký người phụ thuộc thành công. Vui lòng chờ phòng nhân sự duyệt.');
                closeModal();
                loadNguoiPhuThuoc(); // reload danh sách để hiện ngay giao diện
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Error saving dependent:', error);
            alert('Không thể gửi yêu cầu đăng ký');
        }
    }

    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
        const modal = document.getElementById('nguoiPhuThuocModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Load dữ liệu khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        loadNguoiPhuThuoc();
    });
    </script>
</body>
</html>