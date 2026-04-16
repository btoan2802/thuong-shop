<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $productName = $_POST['catname'];
        $cat = [
            'category_name' => $productName
        ];
        $messenge=$f->checkExist("category","category_name",$productName);
        if($messenge != 1){
            $f->message($messenge);
        }
        else{
            $f->addRecord("category", $cat);
            header("Location: ".PATH_ADMIN. "page=catList"); 
            exit();
        }
        
    }
?>

<div class="row flex-between mb-40">
    <div>
        <h1 class="page-title">Thêm danh mục</h1>
        <p style="color: #999; font-size: 0.85rem;">Tạo một phân loại mới cho sản phẩm của bạn.</p>
    </div>
    <a href="<?= PATH_ADMIN ?>page=catList" class="btn-luxury btn-luxury-outline">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="luxury-card" style="max-width: 500px;">
    <form method="post" action="<?= PATH_ADMIN ?>page=catAdd" class="form-luxury">
        <div class="form-group">
            <label for="catname">Tên danh mục <span style="color: #f64e60;">*</span></label>
            <input type="text" class="form-control" name="catname" placeholder="VD: Thời trang mùa hè" required />
        </div>
        <div style="margin-top: 30px;">
            <button type="submit" class="btn-luxury" style="width: 100%; justify-content: center;">
                <i class="fa fa-save"></i> Lưu danh mục
            </button>
        </div>
    </form>
</div>