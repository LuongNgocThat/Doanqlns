

<footer class="footer">
    <div class="footer-content">
        <p>
            <i class="fas fa-copyright"></i> 
            <?php echo date('Y'); ?> Lương Ngọc Thật & Lê Thị Ngọc Thúy. 
        </p>
        <div class="footer-links">
            <a href="https://www.facebook.com" target="_blank">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="https://zalo.me" target="_blank">
                <i class="fab fa-zalo"></i> Zalo
            </a>
            <a href="mailto:support@qlns.com">
                <i class="fas fa-envelope"></i> Hỗ trợ
            </a>
            <!-- <a href="/doanqlns/views/about.php">
                <i class="fas fa-info-circle"></i> Giới thiệu
            </a> -->
        </div>
    </div>
</footer>

<style>
    .footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 10px 20px;
        text-align: center;
        position: fixed;
        bottom: 0;
        left: 280px; /* Phù hợp với chiều rộng sidebar */
        right: 0;
        font-size: 14px;
        color: #6c757d;
        z-index: 1000;
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .footer-content p {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .footer-links {
        display: flex;
        gap: 15px;
    }

    .footer-links a {
        color: #007bff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: color 0.2s;
    }

    .footer-links a:hover {
        color: #0056b3;
    }

    /* Tùy chỉnh icon Zalo (Font Awesome không có icon Zalo chính thức, sử dụng icon chung) */
    .fa-zalo::before {
        content: "\f075"; /* Sử dụng icon comment như biểu tượng tạm thời cho Zalo */
    }

    @media (max-width: 768px) {
        .footer {
            left: 0;
            padding: 10px;
        }

        .footer-content {
            flex-direction: column;
            text-align: center;
        }

        .footer-links {
            margin-top: 5px;
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>