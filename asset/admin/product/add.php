<?php
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $imageName = "";
        if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . basename($image['name']);
            // We'll manualy handle upload here to be safer and match the environment
            $targetDir = "../asset/images/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            move_uploaded_file($image['tmp_name'], $targetDir . $imageName);
        }

        $thumbnailsStr = "";
        if(isset($_FILES['thumbnails']) && !empty($_FILES['thumbnails']['name'][0])){
            $thumbArr = [];
            $targetDir = "../asset/images/";
            foreach($_FILES['thumbnails']['name'] as $key => $name){
                if($_FILES['thumbnails']['error'][$key] == 0){
                    $tName = time() . "_" . $key . "_" . basename($name);
                    move_uploaded_file($_FILES['thumbnails']['tmp_name'][$key], $targetDir . $tName);
                    $thumbArr[] = $tName;
                }
            }
            $thumbnailsStr = implode(",", $thumbArr);
        }

        $sizeNames = $_POST['size_name'] ?? [];
        $sizeQtys = $_POST['size_qty'] ?? [];
        $sizeList = [];
        foreach($sizeNames as $index => $name) {
            if(trim($name) != '') {
                $qty = intval($sizeQtys[$index]);
                $sizeList[] = trim($name) . ":" . $qty;
            }
        }
        $sizesStr = implode(",", $sizeList);

        $product = [
            'name' => $_POST['productName'],
            'cat_id'=> $_POST['catId'],
            'image'=> $imageName,
            'price'=> $_POST['price'],
            'description'=> $_POST['description'],
            'is_on_sale'=> isset($_POST['isOnSale']) ? 1 : 0,
            'sale_price'=> $_POST['salePrice'] ?: 0,
            'sizes' => $sizesStr,
            'thumbnails' => $thumbnailsStr,
            'created_at'=> date('Y-m-d H:i:s'),
            'views'=> 0
        ];

        $f->addRecord("product", $product);
        header("Location: ".PATH_ADMIN. "page=productList"); 
        exit();
    }
?>

<div class="row flex-between mb-40">
    <div>
        <h1 class="page-title">Thêm sản phẩm mới</h1>
        <p style="color: #999; font-size: 0.85rem;">Điền các thông tin dưới đây để tạo sản phẩm mới trong hệ thống.</p>
    </div>
    <a href="<?= PATH_ADMIN ?>page=productList" class="btn-luxury btn-luxury-outline">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</div>

<form method="post" action="<?= PATH_ADMIN ?>page=productAdd" enctype="multipart/form-data" class="form-luxury">
    <div class="product-form-grid">
        <!-- Main Info -->
        <div class="luxury-card">
            <div class="form-group">
                <label for="productName">Tên sản phẩm <span style="color: #f64e60;">*</span></label>
                <input type="text" class="form-control" name="productName" placeholder="VD: Dress with CD Buttons" required />
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả chi tiết</label>
                <textarea class="form-control" name="description" placeholder="Nhập mô tả sản phẩm ở đây..."></textarea>
            </div>

            <div class="form-grid-2col">
                <div class="form-group">
                    <label for="price">Giá niêm yết (đ) <span style="color: #f64e60;">*</span></label>
                    <input type="number" class="form-control" name="price" placeholder="2,000,000" required />
                </div>
                <div class="form-group">
                    <label for="salePrice">Giá khuyến mãi (đ)</label>
                    <input type="number" class="form-control" name="salePrice" id="salePrice" placeholder="1,800,000" />
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="isOnSale" name="isOnSale" style="width: 18px; height: 18px; cursor: pointer;"/>
                <label for="isOnSale" style="margin-bottom: 0; cursor: pointer;">Đang chạy chương trình giảm giá</label>
            </div>

            <div id="size-config-section" class="form-group" style="margin-top: 20px; transition: opacity 0.3s ease;">
                <label>Phân loại Kích thước & Tồn kho</label>
                <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                    <button type="button" id="template-clothes" class="btn-luxury btn-luxury-outline" style="font-size: 0.75rem; padding: 5px 10px;" onclick="applySizeTemplate('clothes')">Gợi ý: Quần áo (S-XL)</button>
                    <button type="button" id="template-shoes" class="btn-luxury btn-luxury-outline" style="font-size: 0.75rem; padding: 5px 10px;" onclick="applySizeTemplate('shoes')">Gợi ý: Giày dép (35-43)</button>
                </div>
                <div id="size-container">
                    <!-- Rows will be added here -->
                </div>
                <button type="button" id="btn-add-size-manual" class="btn-luxury btn-luxury-outline" style="margin-top: 10px; padding: 5px 15px; font-size: 0.85rem;" onclick="addSizeRow()">+ Thêm Size thủ công</button>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div>
            <div class="luxury-card" style="margin-bottom: 30px;">
                <div class="form-group">
                    <label for="catId">Danh mục sản phẩm <span style="color: #f64e60;">*</span></label>
                    <select class="form-control" name="catId" required style="cursor: pointer;">
                        <option value="">-- Chọn danh mục --</option>
                        <?php
                            $sqlCategory = "SELECT category_name,id FROM category"; 
                            $category = $f->getAll($sqlCategory); 
                            foreach ($category as $cat):
                        ?>
                        <option value="<?=$cat['id']?>"><?=$cat['category_name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="luxury-card">
                <div class="form-group">
                    <label for="image">Hình ảnh sản phẩm (Ảnh chính) <span style="color: #f64e60;">*</span></label>
                    <div style="border: 2px dashed #eee; border-radius: 12px; padding: 30px; text-align: center; position: relative; transition: var(--transition);">
                        <i class="fa fa-cloud-upload-alt" style="font-size: 2rem; color: var(--accent); opacity: 0.5; margin-bottom: 10px;"></i>
                        <div style="font-size: 0.8rem; color: #999;">Kéo thả hoặc nhấp để tải ảnh chính</div>
                        <input type="file" class="form-control" name="image" required style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="previewImage(this)" />
                    </div>
                    <div id="image-preview" style="margin-top: 20px; text-align: center; display: none;">
                        <img src="" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="thumbnails">Ảnh phụ chân thực (Thumbnails)</label>
                    <input type="file" class="form-control" name="thumbnails[]" multiple accept="image/*" />
                    <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">Có thể chọn nhiều file ảnh cùng lúc.</p>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-luxury" style="width: 100%; justify-content: center; height: 50px; font-size: 1rem;">
                    <i class="fa fa-save"></i> Lưu sản phẩm
                </button>
            </div>
        </div>
    </div>
</form>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview img').attr('src', e.target.result);
                $('#image-preview').fadeIn();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addSizeRow(name = '', qty = 0, isOneSize = false) {
        let select = document.querySelector('select[name="catId"]');
        let selectedText = select.options[select.selectedIndex].text.toLowerCase();
        let placeholder = (selectedText.includes('giày') || selectedText.includes('dép')) ? 'VD: 35, 36' : 'VD: M, L';

        let container = document.getElementById('size-container');
        let row = document.createElement('div');
        row.className = 'size-row';
        row.style = 'display: flex; gap: 10px; margin-bottom: 10px;';
        row.innerHTML = `
            <input type="text" name="size_name[]" class="form-control" value="${name}" placeholder="Tên Size (${placeholder})" ${isOneSize ? '' : 'required'} style="flex: 2; display: ${isOneSize ? 'none' : 'block'}">
            <input type="number" name="size_qty[]" class="form-control" value="${qty}" placeholder="Số lượng" required min="0" style="flex: 1;">
            <button type="button" class="btn-luxury btn-luxury-outline btn-remove-size" style="padding: 0 15px; display: ${isOneSize ? 'none' : 'block'}" onclick="this.parentElement.remove()">X</button>
        `;
        container.appendChild(row);
    }

    function applySizeTemplate(type) {
        if(!confirm('Thao tác này sẽ xóa các size hiện tại. Bạn có chắc chắn?')) return;
        document.getElementById('size-container').innerHTML = '';
        let sizes = [];
        if(type === 'clothes') sizes = ['S', 'M', 'L', 'XL'];
        if(type === 'shoes') sizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43'];
        
        sizes.forEach(s => addSizeRow(s, 0));
    }

    function checkCategoryForSizes() {
        let select = document.querySelector('select[name="catId"]');
        let selectedText = select.options[select.selectedIndex].text.toLowerCase();
        let sizeSection = document.getElementById('size-config-section');
        let btnClothes = document.getElementById('template-clothes');
        let btnShoes = document.getElementById('template-shoes');
        let btnAddManual = document.getElementById('btn-add-size-manual');
        
        // Mặc định hiện tất cả
        btnClothes.style.display = 'none';
        btnShoes.style.display = 'none';
        btnAddManual.style.display = 'block';

        if (selectedText.includes('phụ kiện') || selectedText.includes('túi xách')) {
            // Đối với hàng không size: Chỉ giữ 1 dòng duy nhất và ẩn tên size
            let container = document.getElementById('size-container');
            let rows = container.querySelectorAll('.size-row');
            
            // Nếu chưa có dòng nào hoặc đang có nhiều dòng, Reset về 1 dòng "One Size"
            if(rows.length !== 1 || rows[0].querySelector('input[name="size_name[]"]').value !== 'One Size') {
                container.innerHTML = '';
                addSizeRow('One Size', 0, true);
            } else {
                // Đã có 1 dòng, đảm bảo nó là ẩn tên và không bắt buộc nhập
                let nameInput = rows[0].querySelector('input[name="size_name[]"]');
                nameInput.style.display = 'none';
                nameInput.required = false; 
                rows[0].querySelector('.btn-remove-size').style.display = 'none';
            }
            btnAddManual.style.display = 'none';
            sizeSection.style.opacity = '1';
            sizeSection.style.pointerEvents = 'auto';
        } else {
            sizeSection.style.opacity = '1';
            sizeSection.style.pointerEvents = 'auto';
            
            // Hiện tên size nếu trước đó bị ẩn
            document.querySelectorAll('input[name="size_name[]"]').forEach(input => {
                input.style.display = 'block';
                input.required = true;
                if(input.value === 'One Size') input.value = ''; // Xóa chữ One Size để admin nhập size thật
            });
            document.querySelectorAll('.btn-remove-size').forEach(btn => btn.style.display = 'block');

            // Cập nhật placeholder cho các ô hiện tại
            let placeholder = (selectedText.includes('giày') || selectedText.includes('dép')) ? 'Tên Size (VD: 35, 36)' : 'Tên Size (VD: M, L)';
            document.querySelectorAll('input[name="size_name[]"]').forEach(input => {
                input.placeholder = placeholder;
            });

            // Hiện gợi ý tương ứng
            if(selectedText.includes('giày') || selectedText.includes('dép')) {
                btnShoes.style.display = 'block';
            } else if(selectedText.includes('trang phục') || selectedText.includes('áo') || selectedText.includes('quần') || selectedText.includes('váy')) {
                btnClothes.style.display = 'block';
            }
        }
    }

    $(document).ready(function() {
        if(document.getElementById('size-container').children.length === 0) {
            addSizeRow();
        }
        
        $('select[name="catId"]').on('change', checkCategoryForSizes);
        checkCategoryForSizes();
    });
</script>