<?php
    $offset = 0;
    $limit = 10;
    if(isset($_GET["p"]) && $_GET["p"] > 0){
        $offset = ($_GET["p"] - 1) * $limit;
    }

    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $where = "";
    if($search) {
        $where = "WHERE p.name LIKE '%$search%'";
    }

    $sql = "SELECT p.*, c.category_name 
            FROM product p 
            LEFT JOIN category c ON p.cat_id = c.id 
            $where 
            ORDER BY p.id DESC 
            LIMIT $offset, $limit"; 
    $result = $f->getAll($sql); 
?>

<div class="row align-items-end mb-5">
    <div class="col-md-7">
        <h1 class="page-title mb-2">Quản lý sản phẩm</h1>
        <p style="color: #999; font-size: 0.9rem; font-weight: 500;">Danh sách toàn bộ sản phẩm trong kho hàng của bạn.</p>
    </div>
    <div class="col-md-5">
        <form action="" method="GET" class="d-flex gap-2 justify-content-md-end mb-3">
            <input type="hidden" name="page" value="productList">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên sản phẩm..." 
                       class="form-control form-control-pill" 
                       style="padding-right: 55px; border: 1.5px solid #eee; background: #fff;">
                <button type="submit" class="btn-luxury btn-circle" 
                        style="position: absolute; right: 4px; top: 4px; width: 38px; height: 38px; box-shadow: none;">
                    <i class="fa fa-search" style="font-size: 0.8rem;"></i>
                </button>
            </div>
        </form>
        <div class="text-md-end">
            <a href="<?= PATH_ADMIN ?>page=productAdd" class="btn-luxury">
                <i class="fa fa-plus-circle me-1"></i> THÊM MỚI
            </a>
        </div>
    </div>
</div>

<div class="luxury-card p-0" style="overflow: hidden;">
    <div class="table-responsive">
        <table class="table-luxury">
            <thead>
                <tr>
                    <th style="width: 80px;">ID</th>
                    <th style="width: 100px;">Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Tồn kho</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Khuyến mãi</th>
                    <th style="width: 150px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($result) > 0): ?>
                    <?php foreach ($result as $value): ?>
                    <tr>
                        <td data-label="ID">
                            <span style="color: #bbb; font-weight: 600;">#<?= $value['id'] ?></span>
                        </td>
                        <td data-label="Ảnh">
                            <img src="../asset/images/<?= $value['image'] ?>" class="product-img-admin" alt="product" style="border-radius: 10px;">
                        </td>
                        <td data-label="Sản phẩm">
                            <div style="font-weight: 700; color: #111;"><?= $value['name'] ?></div>
                            <div style="font-size: 0.75rem; color: #999; margin-top: 3px;"><i class="fa fa-eye"></i> <?= number_format($value['views']) ?> views</div>
                        </td>
                        <td data-label="Tồn kho">
                            <?php 
                                $sizesStr = $value['sizes'];
                                $sizesArr = explode(",", $sizesStr);
                                $totalStock = 0;
                                foreach($sizesArr as $s) {
                                    $parts = explode(":", trim($s));
                                    if(count($parts) == 2) $totalStock += intval($parts[1]);
                                }
                            ?>
                            <span class="status-badge <?= $totalStock > 0 ? 'status-completed' : 'status-cancelled' ?>" style="padding: 4px 12px; font-size: 0.6rem;">
                                <?= $totalStock ?> sp
                            </span>
                        </td>
                        <td data-label="Danh mục">
                            <span style="font-weight: 600; color: #666; font-size: 0.85rem;">
                                <?= $value['category_name'] ?: '---' ?>
                            </span>
                        </td>
                        <td data-label="Giá bán">
                            <span style="font-weight: 700; color: #111;"><?= number_format($value['price']) ?>₫</span>
                        </td>
                        <td data-label="Khuyến mãi">
                            <?php if($value['is_on_sale'] && $value['sale_price'] > 0): ?>
                                <span style="font-weight: 700; color: var(--gold);"><?= number_format($value['sale_price']) ?>₫</span>
                            <?php else: ?>
                                <span style="color: #ddd;">---</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <a href="<?= PATH_ADMIN ?>page=productEdit&productId=<?=$value['id']?>" class="btn-luxury btn-circle btn-luxury-outline" title="Sửa">
                                    <i class="fa fa-pencil-alt" style="font-size: 0.8rem;"></i>
                                </a>
                                <a href="<?= PATH_ADMIN ?>page=productDelete&productId=<?=$value['id']?>" class="btn-luxury btn-circle btn-luxury-outline" 
                                   style="border-color: #eee; color: #bbb !important;"
                                   title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    <i class="fa fa-trash-alt" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 60px;">
                            <i class="fa fa-search mb-3" style="font-size: 2.5rem; color: #eee; display: block;"></i>
                            <span style="color: #bbb; font-weight: 500;">Không tìm thấy sản phẩm nào.</span>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row" style="margin-top: 40px; display: flex; justify-content: center;">
    <?php
        $sqlCount = "SELECT Count(1) as total FROM product p $where"; 
        $resCount = $f->getOne($sqlCount);
        $totalPages = ceil($resCount["total"] / $limit);
    ?>
    <?php if($totalPages > 1): ?>
    <div class="pagination-luxury">
        <?php for ($i=1; $i <= $totalPages; $i++): ?>
            <?php $active = (isset($_GET['p']) && $_GET['p'] == $i) || (!isset($_GET['p']) && $i == 1); ?>
            <a href="<?= PATH_ADMIN ?>page=productList&p=<?=$i?>&search=<?= urlencode($search) ?>" 
               class="pagination-item <?= $active ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>