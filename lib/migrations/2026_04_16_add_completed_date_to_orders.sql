-- ============================================================
-- Migration: Thêm cột completed_date vào bảng orders
-- Ngày tạo: 2026-04-16
-- Mô tả: Lưu lại ngày/giờ admin bấm "Hoàn thành" đơn hàng,
--         dùng để tính doanh thu theo ngày hoàn thành thực tế
--         thay vì ngày đặt hàng (order_date).
-- ============================================================

ALTER TABLE `orders`
ADD COLUMN `completed_date` DATETIME NULL DEFAULT NULL AFTER `order_date`;

-- Backfill: Các đơn đã Completed trước khi có cột này
-- → Dùng order_date làm ngày hoàn thành (giá trị xấp xỉ)
UPDATE `orders`
SET `completed_date` = `order_date`
WHERE `status` = 'Completed'
  AND `completed_date` IS NULL;
