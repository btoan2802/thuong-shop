<?php 
    $categoryId = $_GET['id']; 
    
    $limit = 8;
    $currentPage = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    if ($currentPage < 1) $currentPage = 1;
    $offset = ($currentPage - 1) * $limit;

    $sql_cat = "SELECT category_name FROM category WHERE id = $categoryId";
    $resultCat = $f->getOne($sql_cat);

    $totalProducts = $f->getOne("SELECT COUNT(*) as total FROM product WHERE cat_id = $categoryId")['total'];
    $totalPages = ceil($totalProducts / $limit);

    $sql = "SELECT * FROM product WHERE cat_id = $categoryId LIMIT $limit OFFSET $offset";
    $result = $f->getAll($sql);
?>

<div class="container mt-5 pb-5">
    <div class="section-title">
        <p class="elegant-font" style="color: var(--accent); text-transform: uppercase; letter-spacing: 3px; font-size: 0.9rem;">Danh mục</p>
        <h2><?= $resultCat['category_name']?></h2>
    </div>

    <div class="product-grid">
        <?php foreach($result as $item): ?>
            <div class="product-card" onclick="window.location.href='<?= PATH ?>page=detail&id=<?= $item['id'] ?>'" style="cursor: pointer;">
                <div class="product-image">
                    <img src="asset/images/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" />
                    <?php if($item['is_on_sale'] == 1): ?>
                        <div class="product-badge">Sale</div>
                    <?php endif; ?>
                    <div class="product-actions">
                        <span class="btn-elegant" style="padding: 8px 20px; font-size: 0.8rem;">Xem chi tiết</span>
                    </div>
                </div>
                <div class="product-info">
                    <div class="product-category"><?= $resultCat['category_name']?></div>
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
                        $catName = mb_strtolower($resultCat['category_name'] ?? '', 'UTF-8');
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
                        <button onclick="event.stopPropagation(); openQuickAddModal(<?= $item['id'] ?>, '<?= $item['image'] ?>', '<?= addslashes($item['name']) ?>', <?= $pPrice ?>, '<?= $item['sizes'] ?>', '<?= addslashes($resultCat['category_name']) ?>')" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                    <?php else: ?>
                        <button onclick="event.stopPropagation(); addToCartAJAX(<?= $item['id'] ?>)" class="btn-elegant mt-3" style="border: none; cursor: pointer; width: 100%;">Thêm vào giỏ</button>
                    <?php endif; ?>
                    <?php else: ?>
                        <button class="btn-elegant mt-3 disabled" style="border: none; cursor: not-allowed; width: 100%; background: #ccc; opacity: 0.6;">Hết hàng</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <?php if ($currentPage > 1): ?>
                <a href="<?= PATH ?>page=catProduct&id=<?= $categoryId ?>&p=<?= $currentPage - 1 ?>" class="pagination-item"><i class="fa fa-chevron-left"></i></a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= PATH ?>page=catProduct&id=<?= $categoryId ?>&p=<?= $i ?>" class="pagination-item <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= PATH ?>page=catProduct&id=<?= $categoryId ?>&p=<?= $currentPage + 1 ?>" class="pagination-item"><i class="fa fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>