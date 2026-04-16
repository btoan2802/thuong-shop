<?php
    // Fetch products
    $sql_new = "SELECT p.*, c.category_name FROM `product` p LEFT JOIN category c ON p.cat_id = c.id ORDER BY p.`created_at` DESC LIMIT 0,4;";
    $new_products = $f->getAll($sql_new);

    $sql_sale = "SELECT p.*, c.category_name FROM `product` p LEFT JOIN category c ON p.cat_id = c.id WHERE p.`is_on_sale` = 1 LIMIT 0,4";
    $sale_products = $f->getAll($sql_sale);

    $sql_view = "SELECT p.*, c.category_name FROM product p LEFT JOIN category c ON p.cat_id = c.id ORDER BY p.views DESC LIMIT 0,4";
    $most_viewed = $f->getAll($sql_view);
?>

<!-- Hero Slider -->
<section class="hero-slider" id="heroSlider">
    <!-- Slide 1 (Original) -->
    <div class="slide active">
        <img src="asset/images/banner.jpg?v=2" alt="Fashion Banner 1" />
    </div>
    
    <!-- Slide 2 -->
    <div class="slide">
        <img src="asset/images/banner2.jpg?v=2" alt="Fashion Banner 2" />
    </div>

    <!-- Slide 3 -->
    <div class="slide">
        <img src="asset/images/banner3.jpg?v=2" alt="Fashion Banner 3" />
    </div>

    <!-- Slide 4 -->
    <div class="slide">
        <img src="asset/images/banner4.jpg?v=2" alt="Fashion Banner 4" />
    </div>

    <!-- Controls -->
    <div class="slider-nav">
        <div class="nav-btn" id="prevSlide"><i class="fa fa-chevron-left"></i></div>
        <div class="nav-btn" id="nextSlide"><i class="fa fa-chevron-right"></i></div>
    </div>
    <div class="slider-dots" id="sliderDots">
        <div class="dot active" data-index="0"></div>
        <div class="dot" data-index="1"></div>
        <div class="dot" data-index="2"></div>
        <div class="dot" data-index="3"></div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');
        const prevBtn = document.getElementById('prevSlide');
        const nextBtn = document.getElementById('nextSlide');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('active');
            
            currentSlide = (index + slides.length) % slides.length;
            
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function prevSlide() {
            showSlide(currentSlide - 1);
        }

        function startAutoSlide() {
            slideInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoSlide() {
            clearInterval(slideInterval);
        }

        nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoSlide();
            startAutoSlide();
        });

        prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoSlide();
            startAutoSlide();
        });

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
                stopAutoSlide();
                startAutoSlide();
            });
        });

        const sliderContainer = document.getElementById('heroSlider');
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);

        startAutoSlide();
    });
</script>

<div class="container">
    
    <!-- New Arrivals -->
    <section class="mb-5">
        <div class="section-title">
            <p class="elegant-font" style="color: var(--accent); text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">Mới nhất</p>
            <h2>Sản Phẩm Mới</h2>
        </div>
        
        <div class="product-grid">
            <?php foreach($new_products as $item): ?>
                <div class="product-card" onclick="window.location.href='<?= PATH ?>page=detail&id=<?= $item['id'] ?>'" style="cursor: pointer;">
                    <div class="product-image">
                        <img src="asset/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" />
                        <div class="product-badge">Mới</div>
                        <div class="product-actions">
                            <span class="btn-elegant" style="padding: 8px 20px; font-size: 0.8rem;">Xem chi tiết</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Xu hướng</div>
                        <h3 class="product-name"><?= $item['name'] ?></h3>
                        <div class="product-price">
                            <?php if($item['is_on_sale'] == 1): ?>
                                <span class="price-old"><?= number_format($item['price']) ?>đ</span>
                                <span class="price-new"><?= number_format($item['sale_price']) ?>đ</span>
                            <?php else: ?>
                                <span class="price-new"><?= number_format($item['price']) ?>đ</span>
                            <?php endif; ?>
                        </div>
                        <?php 
                            $catName = mb_strtolower($item['category_name'] ?? '', 'UTF-8');
                            $isFootwear = (strpos($catName, 'giày') !== false || strpos($catName, 'dép') !== false);
                            
                            // Footwear always has multiple sizes (35-43) even if not in DB yet
                            $hasMultipleSizes = $isFootwear || (!empty($item['sizes']) && strpos($item['sizes'], 'One Size') === false);
                            
                            $pPrice = $item['is_on_sale'] == 1 ? $item['sale_price'] : $item['price'];
                            
                            // Calculate total stock
                            $totalStock = 0;
                            if(!empty($item['sizes'])) {
                                $szArr = explode(',', $item['sizes']);
                                foreach($szArr as $sz) {
                                    $parts = explode(':', $sz);
                                    $totalStock += (int)($parts[1] ?? 0);
                                }
                            } else {
                                $totalStock = $isFootwear ? 0 : 10;
                            }

                            if($totalStock > 0):
                                if($hasMultipleSizes): 
                        ?>
                            <button onclick="event.stopPropagation(); openQuickAddModal(<?= $item['id'] ?>, '<?= $item['image'] ?>', '<?= addslashes($item['name']) ?>', <?= $pPrice ?>, '<?= $item['sizes'] ?>', '<?= addslashes($item['category_name'] ?? '') ?>')" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php else: ?>
                            <button onclick="event.stopPropagation(); addToCartAJAX(<?= $item['id'] ?>)" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php endif; ?>
                        <?php else: ?>
                            <button class="btn-elegant mt-3 disabled" style="border: none; cursor: not-allowed; width: 100%; background: #ccc; opacity: 0.6;">Hết hàng</button>
                        <?php endif; ?>                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Sale Section -->
    <section class="mb-5">
        <div class="section-title">
            <p class="elegant-font" style="color: var(--accent); text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">Ưu đãi</p>
            <h2>Đang Giảm Giá</h2>
        </div>
        
        <div class="product-grid">
            <?php foreach($sale_products as $item): ?>
                <div class="product-card" onclick="window.location.href='<?= PATH ?>page=detail&id=<?= $item['id'] ?>'" style="cursor: pointer;">
                    <div class="product-image">
                        <img src="asset/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" />
                        <div class="product-badge">Sale</div>
                        <div class="product-actions">
                            <span class="btn-elegant" style="padding: 8px 20px; font-size: 0.8rem;">Xem chi tiết</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Khuyến mãi</div>
                        <h3 class="product-name"><?= $item['name'] ?></h3>
                        <div class="product-price">
                            <span class="price-old"><?= number_format($item['price']) ?>đ</span>
                            <span class="price-new"><?= number_format($item['sale_price']) ?>đ</span>
                        </div>
                        <?php 
                            $catName = mb_strtolower($item['category_name'] ?? '', 'UTF-8');
                            $isFootwear = (strpos($catName, 'giày') !== false || strpos($catName, 'dép') !== false);
                            
                            // Footwear always has multiple sizes (35-43) even if not in DB yet
                            $hasMultipleSizes = $isFootwear || (!empty($item['sizes']) && strpos($item['sizes'], 'One Size') === false);
                            
                            $pPrice = $item['is_on_sale'] == 1 ? $item['sale_price'] : $item['price'];
                            
                            // Calculate total stock
                            $totalStock = 0;
                            if(!empty($item['sizes'])) {
                                $szArr = explode(',', $item['sizes']);
                                foreach($szArr as $sz) {
                                    $parts = explode(':', $sz);
                                    $totalStock += (int)($parts[1] ?? 0);
                                }
                            } else {
                                $totalStock = $isFootwear ? 0 : 10;
                            }

                            if($totalStock > 0):
                                if($hasMultipleSizes): 
                        ?>
                            <button onclick="event.stopPropagation(); openQuickAddModal(<?= $item['id'] ?>, '<?= $item['image'] ?>', '<?= addslashes($item['name']) ?>', <?= $pPrice ?>, '<?= $item['sizes'] ?>', '<?= addslashes($item['category_name'] ?? '') ?>')" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php else: ?>
                            <button onclick="event.stopPropagation(); addToCartAJAX(<?= $item['id'] ?>)" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php endif; ?>
                        <?php else: ?>
                            <button class="btn-elegant mt-3 disabled" style="border: none; cursor: not-allowed; width: 100%; background: #ccc; opacity: 0.6;">Hết hàng</button>
                        <?php endif; ?>                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Most Viewed -->
    <section class="mb-5">
        <div class="section-title">
            <p class="elegant-font" style="color: var(--accent); text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">Quan tâm nhiều</p>
            <h2>Được Xem Nhiều Nhất</h2>
        </div>
        
        <div class="product-grid">
            <?php foreach($most_viewed as $item): ?>
                <div class="product-card" onclick="window.location.href='<?= PATH ?>page=detail&id=<?= $item['id'] ?>'" style="cursor: pointer;">
                    <div class="product-image">
                        <img src="asset/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" />
                        <div class="product-actions">
                            <span class="btn-elegant" style="padding: 8px 20px; font-size: 0.8rem;">Xem chi tiết</span>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Phổ biến</div>
                        <h3 class="product-name"><?= $item['name'] ?></h3>
                        <div class="product-price">
                            <?php if($item['is_on_sale'] == 1): ?>
                                <span class="price-old"><?= number_format($item['price']) ?>đ</span>
                                <span class="price-new"><?= number_format($item['sale_price']) ?>đ</span>
                            <?php else: ?>
                                <span class="price-new"><?= number_format($item['price']) ?>đ</span>
                            <?php endif; ?>
                        </div>
                        <?php 
                            $catName = mb_strtolower($item['category_name'] ?? '', 'UTF-8');
                            $isFootwear = (strpos($catName, 'giày') !== false || strpos($catName, 'dép') !== false);
                            
                            // Footwear always has multiple sizes (35-43) even if not in DB yet
                            $hasMultipleSizes = $isFootwear || (!empty($item['sizes']) && strpos($item['sizes'], 'One Size') === false);
                            
                            $pPrice = $item['is_on_sale'] == 1 ? $item['sale_price'] : $item['price'];
                            
                            // Calculate total stock
                            $totalStock = 0;
                            if(!empty($item['sizes'])) {
                                $szArr = explode(',', $item['sizes']);
                                foreach($szArr as $sz) {
                                    $parts = explode(':', $sz);
                                    $totalStock += (int)($parts[1] ?? 0);
                                }
                            } else {
                                $totalStock = $isFootwear ? 0 : 10;
                            }

                            if($totalStock > 0):
                                if($hasMultipleSizes): 
                        ?>
                            <button onclick="event.stopPropagation(); openQuickAddModal(<?= $item['id'] ?>, '<?= $item['image'] ?>', '<?= addslashes($item['name']) ?>', <?= $pPrice ?>, '<?= $item['sizes'] ?>', '<?= addslashes($item['category_name'] ?? '') ?>')" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php else: ?>
                            <button onclick="event.stopPropagation(); addToCartAJAX(<?= $item['id'] ?>)" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                        <?php endif; ?>
                        <?php else: ?>
                            <button class="btn-elegant mt-3 disabled" style="border: none; cursor: not-allowed; width: 100%; background: #ccc; opacity: 0.6;">Hết hàng</button>
                        <?php endif; ?>                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div> <!-- .container closing -->

<!-- Newsletter / CTA -->
<section class="newsletter-section">
    <div class="container">
        <h2 class="elegant-font">Đăng ký nhận bản tin</h2>
        <p>Nhận ngay thông tin về các bộ sưu tập mới nhất và ưu đãi đặc quyền dành riêng cho bạn.</p>
        <div class="newsletter-form">
            <input type="email" placeholder="Địa chỉ email của bạn">
            <button class="btn-elegant">Đăng ký</button>
        </div>
    </div>
</section>