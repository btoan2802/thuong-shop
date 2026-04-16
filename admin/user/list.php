<?php
    $sql = "SELECT id,username,name,email,phone,role, avatar FROM user ORDER BY id DESC"; 
    $result = $f->getAll($sql); 
?>

<div class="row align-items-end mb-5">
    <div class="col-md-7">
        <h1 class="page-title mb-2">Quản lý người dùng</h1>
        <p style="color: #999; font-size: 0.9rem; font-weight: 500;">Danh sách tài khoản khách hàng và nhân viên trên hệ thống.</p>
    </div>
    <div class="col-md-5">
        <form action="" method="GET" class="d-flex gap-2 justify-content-md-end mb-3">
            <input type="hidden" name="page" value="userList">
            <div style="position: relative; width: 100%; max-width: 320px;">
                <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Tìm tên hoặc tài khoản..." 
                       class="form-control form-control-pill" 
                       style="padding-right: 55px; border: 1.5px solid #eee; background: #fff;">
                <button type="submit" class="btn-luxury btn-circle" 
                        style="position: absolute; right: 4px; top: 4px; width: 38px; height: 38px; box-shadow: none;">
                    <i class="fa fa-search" style="font-size: 0.8rem;"></i>
                </button>
            </div>
        </form>
        <div class="text-md-end">
            <a href="<?= PATH_ADMIN ?>page=userAdd" class="btn-luxury">
                <i class="fa fa-user-plus me-1"></i> THÊM MỚI
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
                    <th>Người dùng</th>
                    <th>Liên hệ</th>
                    <th>Vai trò</th>
                    <th style="width: 180px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $value): ?>
                <tr>
                    <td data-label="ID">
                        <span style="color: #bbb; font-weight: 600;">#<?= $value['id'] ?></span>
                    </td>
                    <td data-label="Ảnh">
                        <img src="../asset/images/<?= $value['avatar'] ?: 'default_avatar.png' ?>" class="product-img-admin" style="border-radius: 50%; width: 50px; height: 50px; border: 2px solid #f8f9fa;">
                    </td>
                    <td data-label="Người dùng">
                        <div style="font-weight: 700; color: #111;"><?= $value['name'] ?></div>
                        <div style="font-size: 0.8rem; color: #999;">@<?= $value['username'] ?></div>
                    </td>
                    <td data-label="Liên hệ">
                        <div style="font-size: 0.9rem; font-weight: 500;"><?= $value['email'] ?></div>
                        <div style="font-size: 0.8rem; color: #999;"><?= $value['phone'] ?></div>
                    </td>
                    <td data-label="Vai trò">
                        <span class="status-badge <?= $value['role'] == 'admin' ? 'status-cancelled' : 'status-processing' ?>" style="min-width: 80px; text-align: center;">
                            <?= ucfirst($value['role']) ?>
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <button class="btn-luxury btn-circle" title="Xem" onclick="fUserDetail(<?= $value['id']?>)">
                                <i class="fa fa-eye" style="font-size: 0.8rem;"></i>
                            </button>
                            <a href="<?= PATH_ADMIN ?>page=userEdit&id=<?= $value['id'] ?>" class="btn-luxury btn-circle btn-luxury-outline" title="Sửa">
                                <i class="fa fa-pencil-alt" style="font-size: 0.8rem;"></i>
                            </a>
                            <button class="btn-luxury btn-circle btn-luxury-outline" style="border-color: #eee; color: #bbb !important;" title="Xóa" onclick="if(confirm('Bạn có chắc chắn muốn xóa người dùng này?')) window.location.href='<?= PATH_ADMIN ?>page=userDelete&id=<?= $value['id'] ?>'">
                                <i class="fa fa-trash-alt" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Overlay backdrop -->
<div id="userModalBackdrop" style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    z-index: 9998;
    backdrop-filter: blur(3px);
" onclick="closeUserModal()"></div>

<!-- Luxury User Modal -->
<div id="userModal" style="
    display: none;
    position: fixed;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    z-index: 9999;
    width: 680px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 20px;
    box-shadow: 0 25px 80px rgba(0,0,0,0.25);
    background: #fff;
    animation-fill-mode: forwards;
    transition: transform 0.3s ease, opacity 0.3s ease;
    opacity: 0;
">
    <!-- Modal Header -->
    <div style="background: var(--text); color: #fff; border: none; padding: 25px 30px; display: flex; align-items: center; justify-content: space-between; border-radius: 20px 20px 0 0;">
        <h5 style="font-family: 'Playfair Display', serif; letter-spacing: 1px; margin: 0; font-size: 1.1rem;">CHI TIẾT NGƯỜI DÙNG</h5>
        <button type="button" onclick="closeUserModal()" style="background: none; border: none; color: #fff; opacity: 0.8; cursor: pointer; font-size: 1.5rem; line-height: 1; padding: 0;">&times;</button>
    </div>
    <!-- Modal Body -->
    <div style="padding: 40px;">
        <div style="display: flex; gap: 40px; flex-wrap: wrap;">
            <!-- Avatar + Name -->
            <div style="text-align: center; flex-shrink: 0;">
                <div style="position: relative; display: inline-block;">
                    <img id="u_avatar" src="" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #f8f9fa; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
                    <span id="u_role_badge" class="badge-luxury badge-danger" style="position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); padding: 5px 15px; white-space: nowrap;">Admin</span>
                </div>
                <h3 id="u_name" style="margin-top: 20px; font-family: 'Playfair Display', serif; font-size: 1.4rem;">...</h3>
                <p id="u_username" style="color: #999; font-size: 0.9rem;">@username</p>
            </div>
            <!-- Info Grid -->
            <div style="flex: 1; min-width: 220px;">
                <div class="form-grid-2col" style="gap: 25px;">
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #bbb; display: block; margin-bottom: 5px;">Email</label>
                        <div id="u_email" style="font-weight: 500; word-break: break-all;">...</div>
                    </div>
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #bbb; display: block; margin-bottom: 5px;">Số điện thoại</label>
                        <div id="u_phone" style="font-weight: 500;">...</div>
                    </div>
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #bbb; display: block; margin-bottom: 5px;">Giới tính</label>
                        <div id="u_gender" style="font-weight: 500;">...</div>
                    </div>
                    <div>
                        <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #bbb; display: block; margin-bottom: 5px;">Ngày tham gia</label>
                        <div id="u_created_at" style="font-weight: 500;">...</div>
                    </div>
                </div>
                <div style="margin-top: 25px;">
                    <label style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #bbb; display: block; margin-bottom: 5px;">Địa chỉ</label>
                    <div id="u_address" style="font-weight: 500; font-size: 0.95rem; line-height: 1.5;">...</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Footer -->
    <div style="padding: 20px 30px; background: #f8f9fa; border: none; border-radius: 0 0 20px 20px; display: flex; justify-content: flex-end;">
        <button type="button" onclick="closeUserModal()" class="btn-luxury btn-luxury-outline">Đóng</button>
    </div>
</div>

<script>
    function fUserDetail(userId){
        $.ajax({
            url: "../ajax/userDetail.php", 
            type: "get",
            dataType: 'json',
            data: {userId: userId},
            success: function(result){
                if (!result) return;
                $("#u_name").text(result["name"] || "N/A");
                $("#u_username").text("@" + (result["username"] || ""));
                $("#u_email").text(result["email"] || "Chưa cập nhật");
                $("#u_phone").text(result["phone"] || "Chưa cập nhật");
                $("#u_gender").text(result["gender"] == 0 ? "Nữ" : "Nam");
                $("#u_avatar").attr("src", "../asset/images/" + (result["avatar"] || "default_avatar.png"));
                $("#u_address").text(result["address"] || "Chưa cập nhật");
                var role = result["role"] || "user";
                $("#u_role_badge")
                    .text(role.charAt(0).toUpperCase() + role.slice(1))
                    .removeClass("badge-danger badge-info")
                    .addClass(role === "admin" ? "badge-danger" : "badge-info");
                $("#u_created_at").text(result["created_at"] || "N/A");
                openUserModal();
            },
            error: function(){
                showNotify("Không thể tải thông tin người dùng.", "error");
            }
        });
    }

    function openUserModal(){
        $("#userModalBackdrop").fadeIn(200);
        var $modal = $("#userModal");
        $modal.css({ display: "block", opacity: 0, transform: "translate(-50%, -50%) scale(0.9)" });
        setTimeout(function(){
            $modal.css({ opacity: 1, transform: "translate(-50%, -50%) scale(1)" });
        }, 10);
        $("body").css("overflow", "hidden");
    }

    function closeUserModal(){
        var $modal = $("#userModal");
        $modal.css({ opacity: 0, transform: "translate(-50%, -50%) scale(0.9)" });
        $("#userModalBackdrop").fadeOut(200);
        setTimeout(function(){
            $modal.hide();
            $("body").css("overflow", "");
        }, 300);
    }

    // Đóng modal khi nhấn ESC
    $(document).on("keydown", function(e){
        if (e.key === "Escape") closeUserModal();
    });
</script>