<!-- Footer -->
<footer class="footer-modern">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="<?= PATH ?>" class="logo-footer"
                    style="margin-bottom: 20px; text-decoration: none; display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <img src="asset/images/junia_logo_gold.png" alt="Logo" style="max-height: 80px; width: auto;">
                    <div
                        style="font-family: 'Spectral', serif; font-size: 1.2rem; font-weight: 700; letter-spacing: 3px; background: var(--accent-gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; margin-top: -10px;">
                        JUNIA FASHION</div>
                </a>
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">Cửa hàng thời trang cao cấp Junia Fashion.
                    Nơi tôn vinh vẻ đẹp và sự tự tin của phụ nữ hiện đại.</p>
            </div>
            <div class="footer-col">
                <h4>Liên kết</h4>
                <ul>
                    <li><a href="<?= PATH ?>">Trang chủ</a></li>
                    <li><a href="<?= PATH ?>page=product">Cửa hàng</a></li>
                    <li><a href="<?= PATH ?>page=blog">Blog</a></li>
                    <li><a href="<?= PATH ?>page=contact">Liên hệ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Hỗ trợ</h4>
                <ul>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Hướng dẫn chọn Size</a></li>
                    <li><a href="#">Thanh toán và vận chuyển</a></li>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Thông tin</h4>
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem;"><i class="fa fa-map-marker-alt"
                        style="margin-right: 10px; color: var(--accent);"></i> 123 Đường Fashion, TP. Hồ Chí Minh</p>
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-top: 10px;"><i class="fa fa-phone"
                        style="margin-right: 10px; color: var(--accent);"></i> (028) 123 4567</p>
                <p style="color: rgba(255,255,255,0.6); font-size: 0.9rem; margin-top: 10px;"><i class="fa fa-envelope"
                        style="margin-right: 10px; color: var(--accent);"></i> contact@juniafashion.vn</p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2024 Junia Fashion. All Rights Reserved.
        </div>
    </div>
</footer>

<!-- External Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Quick Add to Cart Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; padding: 20px;">
            <div class="modal-header" style="border: none; padding-bottom: 0;">
                <h5 class="modal-title elegant-font" style="font-weight: 700;">Chọn kích thước</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4">
                    <img id="qa-img" src="" alt="" style="width: 80px; height: 100px; object-fit: cover; border-radius: 10px; margin-right: 15px;">
                    <div>
                        <div id="qa-name" style="font-weight: 600; font-size: 1.1rem; margin-bottom: 5px;"></div>
                        <div id="qa-price" style="color: var(--accent); font-weight: 700; font-size: 1.2rem;"></div>
                    </div>
                </div>
                <div id="qa-sizes" class="d-flex flex-wrap gap-2 mb-4">
                    <!-- Sizes injected here -->
                </div>
                <div class="d-flex justify-content-end">
                    <button id="qa-add-btn" class="btn-elegant w-100" style="padding: 12px; font-size: 1rem; border-radius: 50px;">
                        <i class="fa fa-cart-plus me-2"></i>Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let qaCurrentId = 0;
    let qaSelectedSize = '';

    function openQuickAddModal(id, img, name, price, sizesStr, catName = '') {
        qaCurrentId = id;
        document.getElementById('qa-img').src = 'asset/images/' + img;
        document.getElementById('qa-name').innerText = name;
        document.getElementById('qa-price').innerText = new Intl.NumberFormat().format(price) + 'đ';
        
        let sizeContainer = document.getElementById('qa-sizes');
        sizeContainer.innerHTML = '';
        qaSelectedSize = '';

        // Normalize category name
        let lowerCat = catName.toLowerCase();
        let isFootwear = lowerCat.includes('giày') || lowerCat.includes('dép');
        
        let sizesMap = {};
        if (sizesStr) {
            sizesStr.split(',').forEach(s => {
                let parts = s.split(':');
                if (parts.length >= 1) {
                    let sName = parts[0].trim();
                    let sQty = parseInt(parts[1] || 0);
                    sizesMap[sName] = sQty;
                }
            });
        }

        let sizesToDisplay = [];
        if (isFootwear) {
            // Enforce 35-43 for footwear
            ['35', '36', '37', '38', '39', '40', '41', '42', '43'].forEach(fs => {
                sizesToDisplay.push({ name: fs, qty: sizesMap[fs] || 0 });
            });
        } else {
            // Regular sizes from DB
            Object.keys(sizesMap).forEach(sName => {
                sizesToDisplay.push({ name: sName, qty: sizesMap[sName] });
            });
            // If empty, maybe it's One Size (though usually handled outside)
            if (sizesToDisplay.length === 0) {
                sizesToDisplay.push({ name: 'One Size', qty: 10 });
            }
        }

        // Render buttons
        sizesToDisplay.forEach(function(sObj) {
            let sName = sObj.name;
            let sQty = sObj.qty;
            let isAvailable = sQty > 0;

            let btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn ' + (isAvailable ? 'btn-outline-dark' : 'btn-outline-secondary');
            btn.style.borderRadius = '50px';
            btn.style.padding = '8px 20px';
            btn.style.fontWeight = '600';
            btn.innerText = sName;
            btn.title = isAvailable ? `Còn ${sQty} sản phẩm` : 'Hết hàng';
            
            if (!isAvailable) {
                btn.disabled = true;
                btn.style.opacity = '0.4';
                btn.style.cursor = 'not-allowed';
                btn.style.textDecoration = 'line-through';
                btn.style.background = '#f9f9f9';
                btn.style.color = '#bbb';
            } else {
                btn.onclick = function() {
                    Array.from(sizeContainer.children).forEach(b => {
                        b.classList.remove('btn-dark', 'text-white');
                        b.classList.add('btn-outline-dark');
                    });
                    btn.classList.remove('btn-outline-dark');
                    btn.classList.add('btn-dark', 'text-white');
                    qaSelectedSize = sName;
                };
            }
            sizeContainer.appendChild(btn);
        });

        // Show modal
        var myModal = new bootstrap.Modal(document.getElementById('quickAddModal'));
        myModal.show();
    }

    document.getElementById('qa-add-btn').addEventListener('click', function() {
        if (!qaSelectedSize) {
            alert('Vui lòng chọn kích thước!');
            return;
        }
        // Gọi AJAX add to cart
        addToCartAJAX(qaCurrentId, qaSelectedSize, 1);
        
        // Ẩn modal
        var myModalEl = document.getElementById('quickAddModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        modal.hide();
    });
</script>

<!-- Search autocomplete logic -->
<script>
    var timeout;
    $('#txtProduct').on('keyup', function () {
        if (timeout) { clearTimeout(timeout); }
        let keyword = $(this).val();
        if (keyword == "") {
            $("#history").hide();
            return;
        }
        $("#lstHistory").empty();
        timeout = setTimeout(function () {
            $.ajax({
                url: "ajax/searchProduct.php",
                type: "post",
                dataType: 'json',
                data: { keyword: keyword },
                success: function (result) {
                    if (result.length > 0) {
                        for (var item of result) {
                            $("#lstHistory").append("<div style='padding:10px; border-bottom:1px solid #eee;'><a href='<?= PATH ?>page=search&product=" + item["name"] + "' style='display:block; color:var(--text); hover:color:var(--accent);'>" + item["name"] + "</a></div>")
                        }
                        $("#history").show();
                    } else {
                        $("#history").hide();
                    }
                }
            });
        }, 500);
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-container').length) {
            $('#history').hide();
        }
    });
</script>
</body>

</html>