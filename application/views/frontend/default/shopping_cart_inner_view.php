<?php
// === LOGIC XỬ LÝ DỮ LIỆU CƠ BẢN ===
$cart_items = $this->session->userdata('cart_items');
$cart_total_selling_price  = 0; 

if (count($cart_items) > 0) {
    foreach ($cart_items as $cart_item) {
        $course_details = $this->crud_model->get_course_by_id($cart_item)->row_array();
        if ($course_details['discount_flag'] == 1) {
            $cart_total_selling_price += $course_details['discounted_price'];
        } else {
            $cart_total_selling_price += $course_details['price'];
        }
    }
}

// Xử lý mã giảm giá
if (!isset($coupon_code) || empty($coupon_code)) {
    $coupon_code = $this->session->userdata('applied_coupon') ? $this->session->userdata('applied_coupon') : "";
}

$coupon_details = null;
$price_after_coupon = $cart_total_selling_price;
$coupon_discount_amount = 0;
$coupon_error_message = "";
$applied_course_badges = ""; // Biến chứa tên khóa học khi giảm giá thành công

// === LOGIC KIỂM TRA MÃ GIẢM GIÁ CHUYÊN SÂU TỪ DATABASE ===
if (!empty($coupon_code)) {
    $coupon_query = $this->db->get_where('coupons', array('code' => $coupon_code));
    
    if ($coupon_query->num_rows() == 0) {
        $coupon_error_message = "Mã giảm giá không tồn tại.";
        $this->session->set_userdata('applied_coupon', null);
    } else {
        $coupon_details_raw = $coupon_query->row_array();
        $current_time = time(); // Lấy thời gian Unix hiện tại
        
        // 1. Kiểm tra hết hạn
        if ($coupon_details_raw['expiry_date'] < $current_time) {
            $coupon_error_message = "Mã giảm giá đã hết hạn sử dụng.";
            $this->session->set_userdata('applied_coupon', null);
        } 
        // 2. Kiểm tra hết lượt (cột quantity = 0)
        elseif ($coupon_details_raw['quantity'] <= 0) {
            $coupon_error_message = "Mã giảm giá đã hết lượt sử dụng.";
            $this->session->set_userdata('applied_coupon', null);
        } 
        // 3. Kiểm tra mã dành riêng cho khóa học
        else {
            $allowed_courses = json_decode($coupon_details_raw['course_ids'], true);
            $is_valid_for_cart = true;

            // Nếu mảng không rỗng (nghĩa là có giới hạn khóa học)
            if (is_array($allowed_courses) && count($allowed_courses) > 0) {
                // Kiểm tra xem giỏ hàng có chứa khóa học nào được phép không
                $intersect = array_intersect($cart_items, $allowed_courses);
                
                if (count($intersect) == 0) {
                    $is_valid_for_cart = false;
                    $course_badges = "";
                    
                    // Lặp để lấy tên các khóa học được phép -> BÁO LỖI
                    foreach ($allowed_courses as $cid) {
                        $c_title = $this->crud_model->get_course_by_id($cid)->row('title');
                        if ($c_title) {
                            $course_badges .= '<span class="course-badge-coupon"><i class="fas fa-book-open mr-1"></i> '.$c_title.'</span>';
                        }
                    }
                    $coupon_error_message = "Mã giảm giá chỉ sử dụng cho khóa học: <br>" . $course_badges;
                } else {
                    // Lặp để lấy tên các khóa học được phép CÓ TRONG GIỎ HÀNG -> BÁO THÀNH CÔNG
                    foreach ($intersect as $cid) {
                        $c_title = $this->crud_model->get_course_by_id($cid)->row('title');
                        if ($c_title) {
                            $applied_course_badges .= '<span class="course-badge-success"><i class="fas fa-check-circle mr-1"></i> '.$c_title.'</span>';
                        }
                    }
                }
            }

            // Nếu qua hết các vòng gửi xe, thực hiện việc giảm giá
            if ($is_valid_for_cart) {
                if ($this->crud_model->check_coupon_validity($coupon_code)) {
                    $coupon_details = $coupon_details_raw;
                    $price_after_coupon = $this->crud_model->get_discounted_price_after_applying_coupon($coupon_code);
                    $coupon_discount_amount = $cart_total_selling_price - $price_after_coupon;
                    $this->session->set_userdata('applied_coupon', $coupon_code);
                } else {
                    $coupon_error_message = "Mã giảm giá không hợp lệ.";
                    $this->session->set_userdata('applied_coupon', null);
                }
            } else {
                $this->session->set_userdata('applied_coupon', null);
            }
        }
    }
} else {
    $this->session->set_userdata('applied_coupon', null);
    $coupon_code = ""; 
}

// Tính Thuế (nếu có)
$tax_percentage = get_settings('course_selling_tax');
$total_tax = 0;
if ($tax_percentage > 0) {
    $total_tax = round(($price_after_coupon / 100) * $tax_percentage, 2);
}

// Thành tiền (Grand Total)
$grand_total = round($price_after_coupon + $total_tax, 2);
$this->session->set_userdata('total_price_of_checking_out', $grand_total);
?>

<!-- === GIAO DIỆN 1 CỘT Ở GIỮA === -->
<div class="row justify-content-center w-100 m-0">
    <div class="col-lg-8 px-2">
        <div class="cart-box-minimal p-4 p-md-5">
            
            <!-- Tiêu đề -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0">Thông tin đơn hàng</h5>
                <span class="badge badge-primary px-3 py-2" style="border-radius: 6px; font-size: 14px;"><?php echo count($cart_items); ?> khóa học</span>
            </div>

            <!-- Danh sách khóa học -->
            <div class="course-list-minimal mb-4">
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $cart_item): 
                        $course_details = $this->crud_model->get_course_by_id($cart_item)->row_array();
                    ?>
                        <div class="course-item d-flex align-items-start align-items-md-center">
                            <!-- HÌNH ẢNH: Đã fix lỗi dính vào chữ -->
                            <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_details['title'])) . '/' . $course_details['id']); ?>" style="flex-shrink: 0; margin-right: 18px;">
                                <img src="<?php echo $this->crud_model->get_course_thumbnail_url($cart_item); ?>" alt="Course" class="course-item-img border">
                            </a>
                            
                            <div class="pr-4 flex-grow-1">
                                <a href="<?php echo site_url('home/course/' . rawurlencode(slugify($course_details['title'])) . '/' . $course_details['id']); ?>" class="course-title-cart text-decoration-none">
                                    <?php echo $course_details['title']; ?>
                                </a>
                                <div class="mt-2 text-primary font-weight-bold" style="font-size: 15px;">
                                    <?php if ($course_details['discount_flag'] == 1): ?>
                                        <?php echo currency($course_details['discounted_price']); ?>
                                        <small class="text-muted ml-1" style="font-weight: 500;"><del><?php echo currency($course_details['price']); ?></del></small>
                                    <?php else: ?>
                                        <?php echo currency($course_details['price']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Nút Xóa -->
                            <a href="javascript:void(0)" onclick="removeFromCartList(this)" id="<?php echo $course_details['id']; ?>" class="btn-remove-item">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #e0e0e0;"></i>
                        <p class="m-0">Giỏ hàng của bạn đang trống.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Khu vực Mã giảm giá -->
            <?php if (count($cart_items) > 0): ?>
            <div class="coupon-section mb-4">
                <label class="font-weight-bold mb-2 text-dark" style="font-size: 14px;">Mã giảm giá (nếu có)</label>
                <div class="input-group">
                    <input type="text" class="form-control coupon-input-minimal" placeholder="Nhập mã giảm giá..." id="coupon-code" value="<?php echo htmlspecialchars($coupon_code); ?>">
                    <div class="input-group-append">
                        <button class="btn coupon-btn-minimal px-4" type="button" onclick="applyCoupon()">
                            <i class="fas fa-spinner fa-pulse hidden" id="spinner"></i> Áp dụng
                        </button>
                    </div>
                </div>

                <!-- Phản hồi trạng thái mã -->
                <?php if(!empty($coupon_details)): ?>
                    <div class="text-success small font-weight-bold mt-2" style="line-height: 1.6;">
                        <i class="fas fa-check"></i> Áp dụng thành công! Giảm <?php echo $coupon_details['discount_percentage']; ?>%
                        <?php if (!empty($applied_course_badges)): ?>
                            cho khóa học:<br><?php echo $applied_course_badges; ?>
                        <?php endif; ?>
                    </div>
                <?php elseif(!empty($coupon_error_message)): ?>
                    <div class="text-danger small font-weight-bold mt-2" style="line-height: 1.6;">
                        <i class="fas fa-times"></i> <?php echo $coupon_error_message; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Bảng tính tiền -->
            <div class="summary-text text-muted">
                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng cộng</span>
                    <span class="text-dark font-weight-bold"><?php echo currency(round($cart_total_selling_price, 2)); ?></span>
                </div>
                
                <?php if ($coupon_discount_amount > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Giảm giá (<?php echo $coupon_details['discount_percentage']; ?>%)</span>
                    <span class="font-weight-bold">-<?php echo currency(round($coupon_discount_amount, 2)); ?></span>
                </div>
                <?php endif; ?>

                <?php if($tax_percentage > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo get_phrase('TAX_INCLUDED'); ?> (<?php echo $tax_percentage; ?>%)</span>
                    <span><?php echo currency($total_tax); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Thành tiền -->
            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 mb-4" style="border-top: 1px solid #eaeaea;">
                <span class="font-weight-bold text-dark" style="font-size: 18px;">Thành tiền</span>
                <span class="font-weight-bold" style="font-size: 26px; color: #0088cc;"><?php echo currency($grand_total); ?></span>
            </div>

            <!-- Nút Thanh toán -->
            <?php if (count($cart_items) > 0): ?>
                <span id="total_price_of_checking_out" hidden><?php echo $grand_total; ?></span>
                
                <?php if ($grand_total <= 0 && $coupon_code != ""): ?>
                    <a href="<?php echo site_url('home/free_checkout'); ?>" class="btn btn-block btn-checkout-minimal" style="background-color: #28a745; border-color: #28a745;">
                        Đăng ký ngay (Miễn phí)
                    </a>
                <?php else: ?>
                    <button type="button" class="btn btn-block btn-checkout-minimal" onclick="handleCheckOut()">
                        Tiến hành thanh toán
                    </button>
                <?php endif; ?>
            <?php else: ?>
                 <button type="button" class="btn btn-secondary btn-block" style="border-radius: 8px; padding: 12px; font-weight: 600;" disabled>
                    Giỏ hàng trống
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>