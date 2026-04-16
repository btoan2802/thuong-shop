<?php
session_start();
ob_start();
define("PATH_ADMIN", "index.php?");
require("../lib/coreFunction.php");
$f = new CoreFunction();

if (!isset($_SESSION['admin']) || $_SESSION['admin'] == false) {
    header("Location: login.php");
    exit();
}

$adminUser = $f->getOne("SELECT * FROM user WHERE username = '" . $_SESSION['admin'] . "'");
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Junia Fashion | Quản lý hệ thống</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../asset/images/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Style CSS -->
    <link href="../asset/css/style.css" rel="stylesheet" />
    <link href="asset/css/admin-luxury.css?v=3.1" rel="stylesheet" />

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="../index.php" target="_blank">
                    <img src="../asset/images/junia_logo_gold.png" alt="Logo" style="max-height: 100px;">
                </a>
                <div class="brand-name">JUNIA FASHION</div>
            </div>

            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : '';
            ?>

            <ul class="sidebar-menu">
                <li class="<?= $page == '' ? 'active' : '' ?>">
                    <a href="index.php"><i class="fa fa-th-large"></i> <span>Dashboard</span></a>
                </li>
                <li class="<?= $page == 'catList' ? 'active' : '' ?>">
                    <a href="<?= PATH_ADMIN ?>page=catList"><i class="fa fa-folder-open"></i> <span>Danh mục</span></a>
                </li>
                <li class="<?= (strpos($page, 'product') !== false) ? 'active' : '' ?>">
                    <a href="<?= PATH_ADMIN ?>page=productList"><i class="fa fa-tshirt"></i> <span>Sản phẩm</span></a>
                </li>
                <li class="<?= (strpos($page, 'order') !== false) ? 'active' : '' ?>">
                    <a href="<?= PATH_ADMIN ?>page=orderList"><i class="fa fa-shopping-bag"></i> <span>Đơn
                            hàng</span></a>
                </li>
                <li class="<?= $page == 'userList' ? 'active' : '' ?>">
                    <a href="<?= PATH_ADMIN ?>page=userList"><i class="fa fa-users"></i> <span>Người dùng</span></a>
                </li>
                <li style="margin-top: auto; padding-bottom: 20px;">
                    <a href="../index.php" target="_blank"><i class="fa fa-external-link-alt"></i> <span>Xem
                            Website</span></a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main">
            <!-- Top Navbar -->
            <header class="admin-top-nav">
                <div class="top-nav-left" style="display: flex; align-items: center; gap: 20px;">
                    <div id="sidebar-toggle" class="sidebar-toggle">
                        <i class="fa fa-bars"></i>
                    </div>
                    <div class="search-box">
                        <span
                            style="color: #bbb; font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; font-weight: 600;">Hệ
                            thống quản trị Junia Fashion</span>
                    </div>
                </div>
                <div class="user-profile" style="display: flex; align-items: center; gap: 15px;">
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--text);"><?= $adminUser['name'] ?>
                        </div>
                        <div style="font-size: 0.75rem; color: #999;"><?= ucfirst($adminUser['role']) ?></div>
                    </div>
                    <img src="../asset/images/<?= $adminUser['avatar'] ?: 'default_avatar.png' ?>"
                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #eee;">
                    <a href="logout.php" style="color: #999; margin-left: 5px;" title="Đăng xuất"><i
                            class="fa fa-sign-out-alt"></i></a>
                </div>
            </header>

            <!-- Dynamic Content -->
            <div class="admin-content-inner">
                <?php
                $route = [
                    '' => 'home.php',
                    'catList' => 'category/list.php',
                    'catAdd' => 'category/add.php',
                    'catEdit' => 'category/edit.php',
                    'catDelete' => 'category/delete.php',
                    'productList' => 'product/list.php',
                    'productAdd' => 'product/add.php',
                    'productEdit' => 'product/edit.php',
                    'productDelete' => 'product/delete.php',
                    'userList' => 'user/list.php',
                    'userAdd' => 'user/add.php',
                    'userEdit' => 'user/edit.php',
                    'userDelete' => 'user/delete.php',
                    'orderList' => 'order/list.php',
                    'orderDetail' => 'order/detail.php',
                ];

                $found = false;
                foreach ($route as $r => $val) {
                    if ($r === $page) {
                        if (file_exists($val)) {
                            require($val);
                        } else {
                            echo "<div class='luxury-card'>Tập tin không tồn tại: $val</div>";
                        }
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    echo "<div class='luxury-card'>Trang không tìm thấy.</div>";
                }
                ?>
            </div>
        </main>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#sidebar-toggle').click(function () {
                $('.admin-sidebar').toggleClass('active-mobile');
            });

            // Close sidebar when clicking outside on mobile
            $(document).on('click', function (e) {
                if ($(window).width() <= 992) {
                    if (!$(e.target).closest('.admin-sidebar').length && !$(e.target).closest('#sidebar-toggle').length) {
                        $('.admin-sidebar').removeClass('active-mobile');
                    }
                }
            });

            // Auto add table-responsive class to tables
            $('table').wrap('<div class="table-responsive"></div>');
        });

        function showNotify(msg, type = 'success') {
            let toast = document.createElement('div');
            toast.style = `
                position: fixed; top: 30px; right: 30px; 
                background: ${type === 'success' ? 'rgba(201, 169, 110, 0.95)' : 'rgba(220, 53, 69, 0.95)'}; 
                color: #fff; padding: 15px 35px; border-radius: 50px; 
                box-shadow: 0 10px 40px rgba(0,0,0,0.2); 
                z-index: 10000; font-weight: 600; font-size: 0.9rem;
                transform: translateX(120%); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                backdrop-filter: blur(10px); display: flex; align-items: center; gap: 10px;
            `;
            toast.innerHTML = (type === 'success' ? '<i class="fa fa-check-circle"></i> ' : '<i class="fa fa-exclamation-circle"></i> ') + msg;
            document.body.appendChild(toast);

            setTimeout(() => toast.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                toast.style.transform = 'translateX(150%)';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        }

        // Auto show notifications from URL
        $(document).ready(function () {
            const urlParams = new URLSearchParams(window.location.search);
            const successMsg = urlParams.get('success');
            const errorMsg = urlParams.get('error');

            if (successMsg) {
                showNotify(successMsg, 'success');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/&?success=[^&]*/, ''));
            }
            if (errorMsg) {
                showNotify(errorMsg, 'error');
                // Clean URL
                window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/&?error=[^&]*/, ''));
            }
        });
    </script>
</body>

</html>