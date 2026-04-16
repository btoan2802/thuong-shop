<?php
    if (!isset($_GET['productId'])) {
        header("Location: " . PATH_ADMIN . "page=productList");
        exit();
    }

    $productId = $_GET['productId'];
    $listProduct = $f->getOne("SELECT * FROM product WHERE id = $productId");

    if (!$listProduct) {
        echo "<div class='luxury-card'>Không tìm thấy sản phẩm.</div>";
        return;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $imageName = $listProduct['image'];
        if(isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = $_FILES['image'];
            $imageName = time() . "_" . basename($image['name']);
            $targetDir = "../asset/images/";
            move_uploaded_file($image['tmp_name'], $targetDir . $imageName);
        }

        $thumbnailsStr = $listProduct['thumbnails'];
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
            if (!empty($thumbArr)) {
                $thumbnailsStr = implode(",", $thumbArr);
            }
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
            'created_at'=> $listProduct['created_at'],
            'views'=> $listProduct['views']
        ];

        $f->editRecord("product", $productId, $product);
        header("Location: ".PATH_ADMIN. "page=productList"); 
        exit();
    }
?>

<div class="row flex-between mb-40">
    <div>
        <h1 class="page-title">Chỉnh sửa sản phẩm</h1>
        <p style="color: #999; font-size: 0.85rem;">Cập nhật lại thông tin cho sản phẩm #<?= $productId ?>.</p>
    </div>
    <a href="<?= PATH_ADMIN ?>page=productList" class="btn-luxury btn-luxury-outline">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</div>

<form method="post" action="" enctype="multipart/form-data" class="form-luxury">
    <div class="product-form-grid">
        <!-- Main Info -->
        <div class="luxury-card">
            <div class="form-group">
                <label for="productName">Tên sản phẩm <span style="color: #f64e60;">*</span></label>
                <input type="text" class="form-control" name="productName" value="<?= htmlspecialchars($listProduct['name']) ?>" required />
            </div>
            
            <div class="form-group">
                <label for="description">Mô tả chi tiết</label>
                <textarea class="form-control" name="description"><?= htmlspecialchars($listProduct['description']) ?></textarea>
            </div>

            <div class="form-grid-2col">
                <div class="form-group">
                    <label for="price">Giá niêm yết (đ) <span style="color: #f64e60;">*</span></label>
                    <input type="number" class="form-control" name="price" value="<?= $listProduct['price'] ?>" required />
                </div>
                <div class="form-group">
                    <label for="salePrice">Giá khuyến mãi (đ)</label>
                    <input type="number" class="form-control" name="salePrice" id="salePrice" value="<?= $listProduct['sale_price'] ?>" />
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="isOnSale" name="isOnSale" <?= $listProduct['is_on_sale'] ? 'checked' : '' ?> style="width: 18px; height: 18px; cursor: pointer;"/>
                <label for="isOnSale" style="margin-bottom: 0; cursor: pointer;">Đang chạy chương trình giảm giá</label>
            </div>

            <div id="size-config-section" class="form-group" style="margin-top: 20px; transition: opacity 0.3s ease;">
                <label>Phân loại Kích thước & Tồn kho</label>
                <div style="margin-bottom: 15px; display: flex; gap: 10px;">
                    <button type="button" id="template-clothes" class="btn-luxury btn-luxury-outline" style="font-size: 0.75rem; padding: 5px 10px;" onclick="applySizeTemplate('clothes')">Gợi ý: Quần áo (S-XL)</button>
                    <button type="button" id="template-shoes" class="btn-luxury btn-luxury-outline" style="font-size: 0.75rem; padding: 5px 10px;" onclick="applySizeTemplate('shoes')">Gợi ý: Giày dép (35-43)</button>
                </div>
                <div id="size-container">
                    <?php 
                        $savedSizes = explode(",", $listProduct['sizes']);
                        foreach($savedSizes as $s): 
                            $s = trim($s);
                            if ($s != ""):
                                $parts = explode(":", $s);
                                $sName = trim($parts[0]);
                                $sQty = isset($parts[1]) ? intval($parts[1]) : 10;
                                $isOneSize = ($sName == 'One Size');
                    ?>
                    <div class="size-row" style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="size_name[]" class="form-control" value="<?= htmlspecialchars($sName) ?>" placeholder="Tên Size" required style="flex: 2; display: <?= $isOneSize ? 'none' : 'block' ?>">
                        <input type="number" name="size_qty[]" class="form-control" value="<?= $sQty ?>" placeholder="Số lượng" required min="0" style="flex: 1;">
                        <button type="button" class="btn-luxury btn-luxury-outline btn-remove-size" style="padding: 0 15px; display: <?= $isOneSize ? 'none' : 'block' ?>" onclick="this.parentElement.remove()">X</button>
                    </div>
                    <?php endif; endforeach; ?>
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
                        <?php
                            $sqlCategory = "SELECT category_name,id FROM category"; 
                            $category = $f->getAll($sqlCategory); 
                            foreach ($category as $cat):
                        ?>
                        <option value="<?=$cat['id']?>" <?= $cat['id'] == $listProduct['cat_id'] ? 'selected' : '' ?>><?=$cat['category_name']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="luxury-card">
                <div class="form-group">
                    <label for="image">Hình ảnh sản phẩm (Ảnh chính)</label>
                    <div id="image-preview" style="margin-bottom: 20px; text-align: center;">
                        <img src="../asset/images/<?= $listProduct['image'] ?>" style="max-width: 100%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    </div>
                    <div style="border: 2px dashed #eee; border-radius: 12px; padding: 20px; text-align: center; position: relative; transition: var(--transition);">
                        <i class="fa fa-cloud-upload-alt" style="font-size: 1.5rem; color: var(--accent); opacity: 0.5; margin-bottom: 5px;"></i>
                        <div style="font-size: 0.75rem; color: #999;">Nhấp để thay đổi ảnh chính</div>
                        <input type="file" class="form-control" name="image" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;" onchange="previewImage(this)" />
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label for="thumbnails">Ảnh phụ chân thực (Thumbnails)</label>
                    <?php if(!empty($listProduct['thumbnails'])): ?>
                    <div style="display: flex; gap: 10px; margin-bottom: 10px; flex-wrap: wrap;">
                        <?php 
                            $thumbs = explode(",", $listProduct['thumbnails']);
                            foreach($thumbs as $th): 
                                if(trim($th) != ""):
                        ?>
                        <img src="../asset/images/<?= trim($th) ?>" style="width: 50px; height: 50px; border-radius: 5px; object-fit: cover; border: 1px solid #ddd;">
                        <?php endif; endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <input type="file" class="form-control" name="thumbnails[]" multiple accept="image/*" />
                    <p style="font-size: 0.8rem; color: #999; margin-top: 5px;">Tải lên files mới sẽ ghi đè các ảnh phụ cũ.</p>
                </div>
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn-luxury" style="width: 100%; justify-content: center; height: 50px; font-size: 1rem;">
                    <i class="fa fa-save"></i> Cập nhật sản phẩm
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
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function addSizeRow(name = '', qty = 0, isOneSize = false) {
        let select = document.querySelector('select[name="catId"]');
        if(!select) return;
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
        if(!confirm('Thao tác này sẽ xóa các size hiện tại của sản phẩm này. Bạn có chắc chắn?')) return;
        document.getElementById('size-container').innerHTML = '';
        let sizes = [];
        if(type === 'clothes') sizes = ['S', 'M', 'L', 'XL'];
        if(type === 'shoes') sizes = ['35', '36', '37', '38', '39', '40', '41', '42', '43'];
        
        sizes.forEach(s => addSizeRow(s, 0));
    }

    function checkCategoryForSizes() {
        let select = document.querySelector('select[name="catId"]');
        if(!select) return;
        let selectedText = select.options[select.selectedIndex].text.toLowerCase();
        let sizeSection = document.getElementById('size-config-section');
        if(!sizeSection) return;

        let btnClothes = document.getElementById('template-clothes');
        let btnShoes = document.getElementById('template-shoes');
        let btnAddManual = document.getElementById('btn-add-size-manual');
        
        // Mặc định ẩn gợi ý
        btnClothes.style.display = 'none';
        btnShoes.style.display = 'none';
        btnAddManual.style.display = 'block';
        
        if (selectedText.includes('phụ kiện') || selectedText.includes('túi xách')) {
            // Đối với hàng không size: Chỉ giữ 1 dòng duy nhất và ẩn tên size
            let container = document.getElementById('size-container');
            let rows = container.querySelectorAll('.size-row');
            
            // Nếu chưa có dòng nào hoặc đang có nhiều dòng hoặc dòng đầu không phải One Size
            let firstRowNameInput = rows.length > 0 ? rows[0].querySelector('input[name="size_name[]"]') : null;
            if(rows.length !== 1 || (firstRowNameInput && firstRowNameInput.value !== 'One Size')) {
                // Giữ lại số lượng của dòng đầu tiên nếu có để đỡ công admin nhập lại
                let currentQty = rows.length > 0 ? rows[0].querySelector('input[name="size_qty[]"]').value : 0;
                container.innerHTML = '';
                addSizeRow('One Size', currentQty, true);
            } else {
                // Đã có 1 dòng "One Size", đảm bảo nó ẩn tên và không bắt buộc nhập
                if(rows[0]) {
                    let nameInput = rows[0].querySelector('input[name="size_name[]"]');
                    nameInput.style.display = 'none';
                    nameInput.required = false; 
                    rows[0].querySelector('.btn-remove-size').style.display = 'none';
                }
            }
            btnAddManual.style.display = 'none';
            sizeSection.style.opacity = '1';
            sizeSection.style.pointerEvents = 'auto';
        } else {
            sizeSection.style.opacity = '1';
            sizeSection.style.pointerEvents = 'auto';

            // Hiện lại các thành phần nếu trước đó bị ẩn (cho hàng có size)
            document.querySelectorAll('input[name="size_name[]"]').forEach(input => {
                input.style.display = 'block';
                input.required = true;
                if(input.value === 'One Size') input.value = ''; 
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
        $('select[name="catId"]').on('change', checkCategoryForSizes);
        checkCategoryForSizes();
    });
</script>