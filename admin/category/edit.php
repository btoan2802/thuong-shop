<?php
    if (!isset($_GET['catId'])) {
        header("Location: " . PATH_ADMIN . "page=catList");
        exit();
    }

    $catId = $_GET['catId'];
    $cat = $f->getOne("SELECT * FROM category WHERE id = $catId");

    if (!$cat) {
        echo "<div class='luxury-card'>Không tìm thấy danh mục.</div>";
        return;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $categoryName = $_POST['catname'];
        $data = [
            'category_name' => $categoryName
        ];
        
        $f->editRecord("category", $catId, $data);
        header("Location: ".PATH_ADMIN. "page=catList&success=".urlencode("Cập nhật danh mục thành công!")); 
        exit();
    }
?>

<div class="row flex-between mb-40">
    <div>
        <h1 class="page-title">Chỉnh sửa danh mục</h1>
        <p style="color: #999; font-size: 0.85rem;">Cập nhật lại thông tin cho danh mục #<?= $catId ?>.</p>
    </div>
    <a href="<?= PATH_ADMIN ?>page=catList" class="btn-luxury btn-luxury-outline">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="luxury-card" style="max-width: 500px;">
    <form method="post" action="" class="form-luxury">
        <div class="form-group">
            <label for="catname">Tên danh mục <span style="color: #f64e60;">*</span></label>
            <input type="text" class="form-control" name="catname" value="<?= htmlspecialchars($cat['category_name']) ?>" required />
        </div>
        <div style="margin-top: 30px;">
            <button type="submit" class="btn-luxury" style="width: 100%; justify-content: center;">
                <i class="fa fa-sync-alt"></i> Cập nhật danh mục
            </button>
        </div>
    </form>
</div>