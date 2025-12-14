<?php
session_start();
require_once __DIR__ . '/../controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['google_id_token'])) {
        $result = $auth->googleLogin($_POST['google_id_token']);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit();
    } else {
        $ten_dang_nhap = $_POST['ten_dang_nhap'] ?? '';
        $mat_khau = $_POST['mat_khau'] ?? '';
        
        $result = $auth->login($ten_dang_nhap, $mat_khau);
        
        if ($result['success']) {
            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/doanqlns/giaodien.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect_url");
            exit();
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập hệ thống</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
        }

        .split-layout {
            display: flex;
            height: 100vh;
        }

        .login-section {
            flex: 1;
            padding: 2rem 4rem;
            background: white;
            overflow-y: auto;
        }

        .brand-section {
            flex: 1;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 2rem;
        }

        .back-link {
            color: #6c757d;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            margin-bottom: 3rem;
        }

        .back-link i {
            margin-right: 0.5rem;
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .login-header {
            margin-bottom: 1.5rem;
        }

        .login-header h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #718096;
            margin-bottom: 2rem;
        }

        .google-btn {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            background: white;
            color: #4a5568;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 2rem;
        }

        .google-btn img {
            margin-right: 0.75rem;
            width: 20px;
        }

        .divider {
            text-align: center;
            position: relative;
            margin: 2rem 0;
        }

        .divider::before,
        .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #718096;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
        }

        .form-text {
            font-size: 0.875rem;
            color: #718096;
        }

        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1.5rem 0;
        }

        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background: #4e54c8;
            border: none;
            border-radius: 0.5rem;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #3f44a0;
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: #718096;
        }

        .register-link a {
            color: #4e54c8;
            text-decoration: none;
            font-weight: 500;
        }

        .brand-logo {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo i {
            font-size: 60px;
            color: #4e54c8;
        }

        .brand-text {
            margin-top: 2rem;
        }

        .brand-text h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .brand-text p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            .split-layout {
                flex-direction: column;
            }
            
            .brand-section {
                display: none;
            }
            
            .login-section {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="split-layout">
        <section class="login-section">
            <a href="/doanqlns/giaodien.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Quay lại trang chủ
            </a>
            
            <div class="login-container">
                <div class="login-header">
                    <h1>Đăng nhập</h1>
                    <p>Nhập thông tin đăng nhập của bạn để tiếp tục</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="google-signin mb-4">
                    <div id="g_id_onload"
                         data-client_id="725719363319-vtnuuo1goitapagommctgbf91g8f5741.apps.googleusercontent.com"
                         data-callback="handleCredentialResponse"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin" data-type="standard" data-size="large"></div>
                </div>

                <div class="divider">
                    <span>hoặc</span>
                </div>

                <form method="POST" id="login-form">
                    <div class="form-group">
                        <label for="ten_dang_nhap">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" required>
                    </div>

                    <div class="form-group">
                        <label for="mat_khau">Mật khẩu</label>
                        <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                        <div class="form-text">Tối thiểu 8 ký tự</div>
                    </div>

                    <div class="form-check">
                        <div>
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        <a href="#" class="text-decoration-none">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Đăng nhập
                    </button>
                </form>

                
            </div>
        </section>

        <section class="brand-section">
            <div class="brand-logo">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="brand-text">
                <h2>Hệ thống Quản lý Nhân sự</h2>
                <p>Quản lý hiệu quả - Phát triển bền vững</p>
            </div>
        </section>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function handleCredentialResponse(response) {
            const idToken = response.credential;
            fetch('/doanqlns/views/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'google_id_token=' + encodeURIComponent(idToken)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '<?php echo isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '/doanqlns/giaodien.php'; ?>';
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra khi đăng nhập bằng Google');
            });
        }
    </script>
</body>
</html>