<!DOCTYPE html>
<html lang="vi">

<head>
    <title>Junia Fashion | Elegant & Luxury</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="asset/images/favicon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Spectral:wght@400;500;600;700;800&family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom Style -->
    <link href="asset/css/style.css?v=1.6" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
</head>

<body>

    <header>
        <!-- Top Header: Logo, Search, User Icons -->
        <div class="top-header">
            <div class="logo">
                <a href="<?= PATH ?>" class="logo-link">
                    <img src="asset/images/junia_logo_gold.png" alt="Junia Fashion Logo" />
                    <span class="logo-text">JUNIA FASHION</span>
                </a>
            </div>

            <div class="search-container">
                <form action="<?= PATH ?>page=search" method="get">
                    <input type="hidden" name="page" value="search">
                    <?php
                    $searchTerm = isset($_GET['product']) ? $_GET['product'] : '';
                    ?>
                    <input type="text" placeholder="Tìm kiếm sản phẩm..." name="product" value="<?= $searchTerm ?>"
                        id="txtProduct" autocomplete="off">
                </form>
                <div id="history"
                    style="display:none; position:absolute; top:100%; left:0; right:0; background:white; z-index:1001; box-shadow:var(--shadow); border-radius:10px; margin-top:5px; padding:10px;">
                    <div id="lstHistory"></div>
                </div>
            </div>

            <div class="header-icons">
                <?php if (!isset($_SESSION['user'])): ?>
                    <a href="<?= PATH ?>page=login" title="Đăng nhập"><i class="fa-regular fa-user"></i></a>
                <?php else:
                    $currUser = $f->getOne("SELECT * FROM user WHERE id = " . $_SESSION['userId']);
                    ?>
                    <a href="<?= PATH ?>page=account" title="<?= $_SESSION['user'] ?>"
                        style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                        <div style="text-align: right; line-height: 1.2;">
                            <span
                                style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 2px; color: #999; display: block; margin-bottom: 2px;">Chào
                                bạn</span>
                            <span class="user-name-text"
                                style="font-size: 0.85rem; font-weight: 700; letter-spacing: 1px;"><?= $_SESSION['user'] ?></span>
                        </div>
                        <?php if (!empty($currUser['avatar']) && file_exists('asset/images/' . $currUser['avatar'])): ?>
                            <img src="asset/images/<?= $currUser['avatar'] ?>"
                                style="width: 35px; height: 35px; border-radius: 50%; border: 1.5px solid var(--accent); padding: 2px; object-fit: cover; background: #fff;">
                        <?php else: ?>
                            <div
                                style="width: 35px; height: 35px; border-radius: 50%; border: 1.5px solid var(--accent); display: flex; align-items: center; justify-content: center; background: #fdfaf7; color: var(--accent);">
                                <i class="fa fa-user" style="font-size: 0.9rem;"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="#" title="Yêu thích"><i class="fa-regular fa-heart"></i><span class="badge">0</span></a>

                <a href="<?= PATH ?>page=cart" title="Giỏ hàng">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="badge" id="cart-count"><?= isset($_SESSION['cart']) ? $_SESSION['number_of_item'] : 0 ?></span>
                </a>

                <div class="mobile-toggle" id="mobile-menu-btn" style="order: 10; margin-left: 10px;">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
<script>
    function addToCartAJAX(id, size = 'One Size', qty = 1) {
        fetch(`ajax/addToCart.php?id=${id}&size=${size}&qty=${qty}`)
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Cập nhật số lượng trên icon giỏ hàng
                    let badge = document.getElementById('cart-count');
                    if(badge) {
                        badge.innerText = data.count;
                        badge.classList.add('pulse-animation');
                        setTimeout(() => badge.classList.remove('pulse-animation'), 1000);
                    }
                    
                    // Thông báo thành công kiểu sang trọng
                    showNotify("Đã thêm sản phẩm vào túi đồ của bạn!", "success");
                } else {
                    if(data.message.includes('login')) {
                        window.location.href = "<?= PATH ?>page=login";
                    } else {
                        showNotify(data.message, "error");
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotify("Có lỗi xảy ra, vui lòng thử lại.", "error");
            });
    }

    function showNotify(msg, type) {
        // Tạo thông báo bay lên kiểu luxury
        let toast = document.createElement('div');
        toast.style = `
            position: fixed; top: 100px; right: 20px; 
            background: ${type === 'success' ? 'rgba(201, 169, 110, 0.95)' : 'rgba(220, 53, 69, 0.95)'}; 
            color: #fff; padding: 15px 30px; border-radius: 50px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2); 
            z-index: 9999; font-weight: 600; font-size: 0.9rem;
            transform: translateX(120%); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        `;
        toast.innerHTML = (type === 'success' ? '<i class="fa fa-check-circle me-2"></i> ' : '<i class="fa fa-exclamation-circle me-2"></i> ') + msg;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.style.transform = 'translateX(0)', 100);
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
</script>
        <nav class="main-nav" id="main-nav">
            <div class="mobile-nav-header">
                <span class="logo-text">JUNIA FASHION</span>
                <div class="close-menu" id="close-menu-btn">
                    <i class="fa-solid fa-times"></i>
                </div>
            </div>
            <ul>
                <li><a href="<?= PATH ?>">Trang chủ</a></li>
                <li class="has-children">
                    <a href="<?= PATH ?>page=product">Cửa hàng <i class="fa fa-chevron-down"
                            style="font-size: 10px; margin-left: 5px;"></i></a>
                    <ul class="submenu">
                        <li><a href="<?= PATH ?>page=product" style="font-weight: 600;">Tất cả sản phẩm</a></li>
                        <?php
                        $sql_cats = "SELECT * FROM category";
                        $categories = $f->getAll($sql_cats);
                        if(!empty($categories)) {
                            foreach ($categories as $cat):
                        ?>
                            <li><a href="<?= PATH ?>page=catProduct&id=<?= $cat['id'] ?>"><?= $cat['category_name'] ?></a></li>
                        <?php 
                            endforeach; 
                        }
                        ?>
                    </ul>
                </li>
                <li><a href="<?= PATH ?>page=blog">Blog</a></li>
                <li><a href="<?= PATH ?>page=contact">Liên hệ</a></li>
            </ul>
        </nav>

        <script>
            $(document).ready(function() {
                $('#mobile-menu-btn').click(function() {
                    $('#main-nav').addClass('active');
                    $('body').css('overflow', 'hidden');
                });

                $('#close-menu-btn').click(function() {
                    $('#main-nav').removeClass('active');
                    $('body').css('overflow', 'auto');
                });

                // Xử lý click submenu trên mobile
                $('.has-children > a').click(function(e) {
                    if ($(window).width() <= 992) {
                        e.preventDefault();
                        var $submenu = $(this).siblings('.submenu');
                        $submenu.toggleClass('mobile-open');
                        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
                    }
                });

                // Auto show notifications from URL
                const urlParams = new URLSearchParams(window.location.search);
                const successMsg = urlParams.get('success');
                const errorMsg = urlParams.get('error');
                
                if (successMsg) {
                    showNotify(successMsg, 'success');
                    window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/&?success=[^&]*/, ''));
                }
                if (errorMsg) {
                    showNotify(errorMsg, 'error');
                    window.history.replaceState({}, document.title, window.location.pathname + window.location.search.replace(/&?error=[^&]*/, ''));
                }
            });
        </script>
    </header>

    <main>