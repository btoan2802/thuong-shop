<?php
    // Add Category Logic
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_add_cat'])){
        $catName = $_POST['catname'];
        $catData = ['category_name' => $catName];
        $messenge = $f->checkExist("category", "category_name", $catName);
        if($messenge != 1){
            header("Location: ".PATH_ADMIN. "page=catList&error=".urlencode($messenge));
            exit();
        } else {
            $f->addRecord("category", $catData);
            header("Location: ".PATH_ADMIN. "page=catList&success=".urlencode("Thêm danh mục thành công!")); 
            exit();
        }
    }

    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $sql = "SELECT * FROM category WHERE category_name LIKE '%$search%' ORDER BY id DESC";
    } else {
        $sql = "SELECT * FROM category ORDER BY id DESC"; 
    }
    $result = $f->getAll($sql); 
?>

<div class="row align-items-end mb-5">
    <div class="col-md-7">
        <h1 class="page-title mb-2">Quản lý danh mục</h1>
        <p style="color: #999; font-size: 0.9rem; font-weight: 500;">Phân loại sản phẩm để khách hàng dễ dàng tìm kiếm.</p>
    </div>
    <div class="col-md-5">
        <form action="" method="GET" class="d-flex gap-2 justify-content-md-end">
            <input type="hidden" name="page" value="catList">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm tên danh mục..." 
                       class="form-control form-control-pill" 
                       style="padding-right: 55px; border: 1.5px solid #eee; background: #fff;">
                <button type="submit" class="btn-luxury btn-circle" 
                        style="position: absolute; right: 4px; top: 4px; width: 38px; height: 38px; box-shadow: none;">
                    <i class="fa fa-search" style="font-size: 0.8rem;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Category List -->
    <div class="col-lg-8">
        <div class="luxury-card p-0" style="overflow: hidden;">
            <div class="table-responsive">
                <table class="table-luxury">
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th>Tên danh mục</th>
                            <th style="width: 150px; text-align: center;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($result) > 0): ?>
                            <?php foreach ($result as $value): ?>
                            <tr>
                                <td data-label="ID">
                                    <span style="color: #bbb; font-weight: 600; font-family: 'Poppins';">#<?= $value['id'] ?></span>
                                </td>
                                <td data-label="Tên danh mục">
                                    <div style="font-weight: 700; color: #111; font-size: 1.05rem;"><?= $value['category_name'] ?></div>
                                </td>
                                <td data-label="Thao tác">
                                    <div style="display: flex; gap: 12px; justify-content: center;">
                                        <a href="<?= PATH_ADMIN ?>page=catEdit&catId=<?=$value['id']?>" class="btn-luxury btn-circle btn-luxury-outline" title="Sửa">
                                            <i class="fa fa-pencil-alt" style="font-size: 0.8rem;"></i>
                                        </a>
                                        <a href="<?= PATH_ADMIN ?>page=catDelete&catId=<?=$value['id']?>" class="btn-luxury btn-circle btn-luxury-outline" 
                                           style="border-color: #eee; color: #bbb !important;"
                                           title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                            <i class="fa fa-trash-alt" style="font-size: 0.8rem;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 60px;">
                                    <i class="fa fa-folder-open mb-3" style="font-size: 2.5rem; color: #eee; display: block;"></i>
                                    <span style="color: #bbb; font-weight: 500;">Chưa có danh mục nào được tìm thấy.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Add Category Form -->
    <div class="col-lg-4">
        <div class="luxury-card shadow-sm h-100">
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; margin-bottom: 30px; position: relative; display: inline-block;">
                Thêm danh mục mới
                <div style="position: absolute; bottom: -8px; left: 0; width: 40px; height: 3px; background: var(--gold); border-radius: 10px;"></div>
            </h3>
            
            <form method="post" action="<?= PATH_ADMIN ?>page=catList" class="form-luxury">
                <div class="form-group">
                    <label>Tên danh mục <span style="color: var(--gold);">*</span></label>
                    <input type="text" class="form-control" name="catname" placeholder="VD: Dress" required />
                </div>
                
                <div class="mt-4">
                    <button type="submit" name="btn_add_cat" class="btn-luxury" style="width: 100%; justify-content: center; padding: 15px; border-radius: 15px;">
                        TẠO DANH MỤC <i class="fa fa-plus-circle ms-2"></i>
                    </button>
                </div>
            </form>

            <div style="margin-top: 40px; padding: 25px; background: #fffcf5; border-radius: 20px; border: 1px solid #f6f0df;">
                <p style="font-size: 0.82rem; color: #8a6d3b; line-height: 1.8; margin-bottom: 0;">
                    <i class="fa fa-lightbulb" style="color: var(--gold); margin-right: 8px; font-size: 1rem;"></i> 
                    <strong>Lưu ý:</strong> Hãy chọn tên danh mục súc tích và dễ hiểu để khách hàng có trải nghiệm mua sắm tốt nhất.
                </p>
            </div>
        </div>
    </div>
</div>