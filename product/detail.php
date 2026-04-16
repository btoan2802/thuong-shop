<?php 
    $id=$_GET['id'];
    $sql= "SELECT p.*, c.category_name 
           FROM product p 
           LEFT JOIN category c ON p.cat_id = c.id 
           WHERE p.id=$id";
    $result=$f->getOne($sql);
    $sql1="UPDATE product SET views=views+1 WHERE id=$id";
    $f->setQuery($sql1);
?>

<style>
    .lux-flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
        align-items: flex-start;
        width: 100%;
    }

    @media (min-width: 992px) {
        .lux-flex-container { flex-wrap: nowrap !important; }
        .lux-gallery-col { 
            flex: 0 0 37% !important; 
            max-width: 37% !important; 
        }
        .lux-info-col { 
            flex: 0 0 60% !important; 
            max-width: 60% !important; 
            margin-left: 3%;
        }
    }

    @media (max-width: 991px) {
        .lux-gallery-col, .lux-info-col { 
            flex: 0 0 100% !important; 
            max-width: 100% !important; 
        }
        .main-img-container {
            height: 300px !important;
        }
        .lux-detail-wrapper {
            padding: 20px 0 !important;
        }
        .lux-left-meta-centered h1 {
            font-size: 1.6rem !important;
        }
        .lux-price-badge-centered {
            padding: 10px 20px !important;
        }
        .lux-price-badge-centered span:last-child {
            font-size: 1.8rem !important;
        }
    }

    .main-img-container {
        background: #fdfaf7;
        border-radius: 20px;
        overflow: hidden;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        border: 1px solid #f0f0f0;
    }
    .main-img-container img {
        max-height: 95%;
        max-width: 95%;
        object-fit: contain;
    }
    
    .lux-thumb-item {
        width: 70px;
        height: 70px;
        border-radius: 0;
        border: 3px solid transparent;
        cursor: pointer;
        overflow: hidden;
        transition: 0.3s;
    }
    .lux-thumb-item.active { border-color: #d30000; }
    
    .thumb-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.8);
        color: #c9a96e;
        border: 1px solid #eee;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        cursor: pointer;
        z-index: 10;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .thumb-nav-btn:hover { 
        background: #c9a96e; 
        color: #fff; 
        border-color: #c9a96e;
        transform: translateY(-50%) scale(1.1); 
    }
    .thumb-nav-btn.left-btn { left: 0; }
    .thumb-nav-btn.right-btn { right: 0; }

    .lux-price-badge-centered {
        background: #fafafa;
        padding: 20px 30px;
        border-radius: 15px;
        border: 1px dashed #ddd;
        display: inline-block;
        margin-top: 15px;
    }

    .size-option-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border: 1px solid #eee;
        border-radius: 10px;
        margin-right: 12px;
        cursor: pointer;
        font-weight: 700;
        transition: 0.3s;
    }
    .size-option-box:hover:not(.disabled), .size-option-box.active {
        background: var(--accent-gradient);
        border-color: transparent;
        color: #000;
        box-shadow: 0 5px 15px rgba(201, 169, 110, 0.3);
    }
    .size-option-box.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9f9f9;
        text-decoration: line-through;
        border-color: #eee;
        color: #bbb;
    }

    .btn-buy-lux {
        flex: 1;
        background: var(--accent-gradient);
        color: #000;
        text-decoration: none !important;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 1rem;
        letter-spacing: 2px;
        box-shadow: 0 10px 25px rgba(201, 169, 110, 0.2);
        transition: 0.3s;
    }
    .btn-buy-lux:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(201, 169, 110, 0.4); }
</style>

<div class="lux-detail-wrapper" style="background: #fff; padding: 60px 0; font-family: 'Be Vietnam Pro', sans-serif;">
    <div class="container">
        <div class="lux-flex-container">
            
            <!-- LEFT COLUMN: IMAGE & CENTERED INFO -->
            <div class="lux-gallery-col text-center">
                <div class="main-img-container">
                    <img src="asset/images/<?= $result['image'] ?>" id="mainImg" alt="<?= $result['name'] ?>">
                </div>
                
                <div style="position: relative; margin-bottom: 35px; padding: 0 20px;">
                    <button onclick="navigateGallery(-1)" class="thumb-nav-btn left-btn"><i class="fas fa-chevron-left"></i></button>
                    
                    <div id="thumbSlider" style="display: flex; gap: 10px; overflow-x: auto; scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; padding: 5px 0;">
                        <style>#thumbSlider::-webkit-scrollbar { display: none; }</style>
                        <div class="lux-thumb-item active" style="flex: 0 0 70px;" onclick="changePImg(this, '<?= $result['image'] ?>')">
                            <img src="asset/images/<?= $result['image'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php 
                            if(!empty($result['thumbnails'])):
                                $thumbs = explode(",", $result['thumbnails']);
                                foreach($thumbs as $th):
                                    if(trim($th) != ""):
                        ?>
                        <div class="lux-thumb-item" style="flex: 0 0 70px;" onclick="changePImg(this, '<?= trim($th) ?>')">
                            <img src="asset/images/<?= trim($th) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php endif; endforeach; endif; ?>
                    </div>

                    <button onclick="navigateGallery(1)" class="thumb-nav-btn right-btn"><i class="fas fa-chevron-right"></i></button>
                </div>

                <div class="lux-left-meta-centered mt-4">
                    <?php 
                        // Compute inventory for later use
                        $sizesStr = !empty($result['sizes']) ? $result['sizes'] : '';
                        $sizesArr = ($sizesStr != "") ? explode(",", $sizesStr) : [];
                        $totalStock = 0;
                        $parsedSizes = [];
                        
                        // Parse existing sizes into a key-value pair for easy lookup
                        $dbSizes = [];
                        foreach($sizesArr as $s) {
                            $s = trim($s);
                            if($s != "") {
                                $parts = explode(":", $s);
                                $sName = trim($parts[0]);
                                $sQty = isset($parts[1]) ? intval($parts[1]) : 0;
                                $dbSizes[$sName] = $sQty;
                                $totalStock += $sQty;
                            }
                        }

                        $catName = mb_strtolower($result['category_name'] ?? '', 'UTF-8');
                        $isFootwear = (strpos($catName, 'giày') !== false || strpos($catName, 'dép') !== false);
                        
                        if($isFootwear) {
                            // Enforce 35-43 range for footwear
                            $footwearRange = ['35', '36', '37', '38', '39', '40', '41', '42', '43'];
                            foreach($footwearRange as $fs) {
                                $parsedSizes[] = ['name' => $fs, 'qty' => $dbSizes[$fs] ?? 0];
                            }
                        } else if(empty($sizesArr)) {
                            // If no specific sizes defined and not footwear, default to One Size
                            $totalStock = 10; 
                            $parsedSizes[] = ['name' => 'One Size', 'qty' => 10];
                        } else {
                            // For other regular products, use database sizes
                            foreach($dbSizes as $name => $qty) {
                                $parsedSizes[] = ['name' => $name, 'qty' => $qty];
                            }
                        }
                        
                        $hasMultipleSizes = ($isFootwear || ($sizesStr != ""));
                        // Nếu thuộc danh mục Phụ kiện hoặc Túi xách -> KHÔNG có size (ghi đè nếu cần)
                        if(strpos($catName, 'phụ kiện') !== false || strpos($catName, 'túi xách') !== false) {
                            $hasMultipleSizes = false;
                        }
                    ?>
                    <h1 style="font-family: 'Spectral', serif; font-size: 2.2rem; font-weight: 800; color: #111; margin-bottom: 12px; line-height: 1.1;"><?= $result['name'] ?></h1>
                    
                    <div style="display: flex; align-items: center; gap: 15px; justify-content: center; margin-bottom: 25px;">
                        <div style="color: #ffb400; font-size: 0.9rem;">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <span style="color: #bbb; font-size: 0.8rem; border-left: 1px solid #eee; padding-left: 15px;">(<?= $result['views'] ?> Views)</span>
                    </div>

                    <div class="lux-price-badge-centered">
                        <?php if($result['is_on_sale'] == 1): ?>
                            <span style="color: #bbb; text-decoration: line-through; font-size: 1.1rem; margin-right: 20px;"><?= number_format($result['price']) ?>đ</span>
                            <span style="color: #c9a96e; font-size: 2.5rem; font-weight: 800;"><?= number_format($result['sale_price']) ?>đ</span>
                        <?php else: ?>
                            <span style="color: #c9a96e; font-size: 2.5rem; font-weight: 800;"><?= number_format($result['price']) ?>đ</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: DETAILS & ACTIONS -->
            <div class="col-lg-7 lux-info-col" style="padding-top: 10px;">
                <div style="margin-bottom: 50px;">
                    <h4 style="text-transform: uppercase; font-size: 1.7rem; font-weight: 800; color: #111; letter-spacing: 3px; margin-bottom: 25px; border-left: 5px solid #c9a96e; padding-left: 20px;">Chi tiết sản phẩm</h4>
                    <p style="color: #666; line-height: 1.9; font-size: 1.1rem; text-align: justify;"><?= nl2br($result['description']) ?></p>
                </div>

                <div style="margin-bottom: 50px; display: <?= $hasMultipleSizes ? 'block' : 'none' ?>;">
                    <h4 style="text-transform: uppercase; font-size: 0.85rem; font-weight: 800; color: #111; letter-spacing: 3px; margin-bottom: 20px; border-left: 3px solid #c9a96e; padding-left: 15px;">Kích thước / SIZE:</h4>
                    <div style="display: flex; flex-wrap: wrap; margin-bottom: 12px;">
                        <?php 
                            $firstActiveSet = false;
                            foreach($parsedSizes as $idx => $ps):
                                $sName = $ps['name'];
                                $sQty = $ps['qty'];
                                $isDisabled = ($sQty <= 0);
                                
                                $activeClass = '';
                                if (!$isDisabled && !$firstActiveSet) {
                                    $activeClass = 'active';
                                    $firstActiveSet = true;
                                }
                                $boxClass = $isDisabled ? 'size-option-box disabled' : "size-option-box $activeClass";
                        ?>
                        <div class="<?= $boxClass ?>" data-stock="<?= $sQty ?>" <?= !$isDisabled ? 'onclick="selectSize(this)"' : '' ?> title="<?= $isDisabled ? 'Hết hàng' : "Còn $sQty sản phẩm" ?>"><?= $sName ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div id="stockLabel" style="font-size: 0.95rem; color: #555; font-weight: 600; background: #fdfaf7; padding: 10px 15px; border-radius: 8px; display: inline-block; margin-top: 5px; border: 1px solid #f0f0f0;">
                        Vui lòng chọn kích thước để xem tồn kho
                    </div>
                </div>

                <?php if(!$hasMultipleSizes): ?>
                    <!-- Hidden input or data for One Size products -->
                    <div class="size-option-box active" data-stock="<?= $totalStock ?>" style="display: none;"><?= $parsedSizes[0]['name'] ?></div>
                <?php endif; ?>

                <div style="margin-bottom: 60px;">
                    <div style="display: flex; gap: 20px; align-items: stretch; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; border: 1px solid #eee; border-radius: 12px; padding: 5px; background: #fafafa; min-width: 150px; opacity: <?= $totalStock <= 0 ? '0.5' : '1' ?>;">
                            <button onclick="changeQty(-1)" style="flex: 1; border: none; background: none; font-size: 1.4rem; cursor: pointer; color: #111;" <?= $totalStock <= 0 ? 'disabled' : '' ?>>-</button>
                            <input type="number" value="<?= $totalStock <= 0 ? '0' : '1' ?>" id="qtyBox" min="1" max="100" style="width: 50px; border: none; text-align: center; font-weight: 800; font-size: 1.3rem; background: transparent; color: #111;" readonly>
                            <button onclick="changeQty(1)" style="flex: 1; border: none; background: none; font-size: 1.4rem; cursor: pointer; color: #111;" <?= $totalStock <= 0 ? 'disabled' : '' ?>>+</button>
                        </div>
                        
                        <?php if($totalStock > 0): ?>
                        <button onclick="addToCart(<?= $result['id'] ?>)" class="btn-buy-lux" style="border: none; cursor: pointer;">
                            <i class="fa fa-shopping-bag" style="margin-right: 15px;"></i>
                            Thêm vào giỏ hàng
                        </button>
                        <?php else: ?>
                        <button class="btn-buy-lux disabled" style="background: #ccc; border: none; cursor: not-allowed; opacity: 0.6;">
                            Hết hàng
                        </button>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 0.85rem; color: #999; font-weight: 600;">Số lượng tồn kho: <span id="stockInSelector" style="color: var(--accent);"><?= $totalStock ?></span> sản phẩm</div>
                </div>

                <div style="border-top: 1px solid #f0f0f0; padding-top: 40px; display: flex; align-items: center; gap: 25px;">
                    <span style="font-size: 0.9rem; color: #999; font-weight: 600;">Share on:</span>
                    <a href="#" style="color: #444; font-size: 1.2rem;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" style="color: #444; font-size: 1.2rem;"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="color: #444; font-size: 1.2rem;"><i class="fab fa-pinterest-p"></i></a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function changeQty(amt) {
        let q = document.getElementById('qtyBox');
        let val = parseInt(q.value);
        let max = parseInt(q.getAttribute('max') || 100);
        if(val + amt >= 1 && val + amt <= max) q.value = val + amt;
    }

    function changePImg(el, src) {
        document.getElementById('mainImg').src = 'asset/images/' + src;
        document.querySelectorAll('.lux-thumb-item').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    function scrollThumbs(dir) {
        let slider = document.getElementById('thumbSlider');
        slider.scrollBy({ left: dir * 80, behavior: 'smooth' }); // 70px width + 10px gap
    }

    function selectSize(el) {
        if(el.classList.contains('disabled')) return;
        document.querySelectorAll('.size-option-box').forEach(b => b.classList.remove('active'));
        el.classList.add('active');

        let stockValue = parseInt(el.getAttribute('data-stock'));
        let qtyBox = document.getElementById('qtyBox');
        if(qtyBox) {
            qtyBox.setAttribute('max', stockValue);
            if(parseInt(qtyBox.value) > stockValue) qtyBox.value = stockValue;
        }

        // Update labels
        let stockLbl = document.getElementById('stockLabel');
        let stockInSelector = document.getElementById('stockInSelector');
        if (stockLbl) stockLbl.innerHTML = `Còn <span style="color: var(--accent); font-weight: 700;">${stockValue}</span> sản phẩm (Size ${el.innerText})`;
        if (stockInSelector) stockInSelector.innerHTML = stockValue;
    }

    function changeQty(dir) {
        let qtyBox = document.getElementById('qtyBox');
        let current = parseInt(qtyBox.value);
        let max = parseInt(qtyBox.getAttribute('max')) || 100;
        let next = current + dir;
        if(next >= 1 && next <= max) {
            qtyBox.value = next;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        let activeSize = document.querySelector('.size-option-box.active');
        if(activeSize) {
            let stock = parseInt(activeSize.getAttribute('data-stock'));
            let qtyBox = document.getElementById('qtyBox');
            if(qtyBox) qtyBox.setAttribute('max', stock);

            let stockLabel = document.getElementById('stockLabel');
            if(stockLabel) {
                stockLabel.innerHTML = 'Còn ' + stock + ' sản phẩm trong kho';
            }
        }
    });

    function navigateGallery(dir) {
        let items = document.querySelectorAll('.lux-thumb-item');
        let activeIdx = -1;
        items.forEach((item, idx) => {
            if(item.classList.contains('active')) activeIdx = idx;
        });
        
        let nextIdx = activeIdx + dir;
        
        // Vòng lặp vô tận
        if(nextIdx < 0) nextIdx = items.length - 1;
        if(nextIdx >= items.length) nextIdx = 0;
        
        if(items[nextIdx]) {
            items[nextIdx].click();
            items[nextIdx].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }
    }

    function changePImg(el, src) {
        document.getElementById('mainImg').src = 'asset/images/' + src;
        document.querySelectorAll('.lux-thumb-item').forEach(item => item.classList.remove('active'));
        el.classList.add('active');
    }

    function addToCart(productId) {
        let activeSizeEl = document.querySelector('.size-option-box.active');
        if(!activeSizeEl) {
            showNotify('Vui lòng chọn size!', 'error');
            return;
        }
        let size = activeSizeEl.innerText.trim();
        let qty = document.getElementById('qtyBox').value;
        
        // Gọi hàm AJAX toàn cục
        addToCartAJAX(productId, size, qty);
    }
</script>