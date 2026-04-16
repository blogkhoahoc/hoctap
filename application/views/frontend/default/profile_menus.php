<?php
$user_id = $this->session->userdata('user_id');

// Gom nhóm điều kiện: Tìm các luồng chat có bạn tham gia (Dù là người bắt đầu hay người nhận)
$this->db->group_start();
$this->db->where('message_thread.receiver', $user_id);
$this->db->or_where('message_thread.sender', $user_id);
$this->db->group_end();

// Lọc ra các tin nhắn do NGƯỜI KHÁC gửi và trạng thái là CHƯA ĐỌC (0)
$this->db->where('message.sender !=', $user_id);
$this->db->where('message.read_status', 0);
$this->db->from('message_thread');
$this->db->join('message', 'message_thread.message_thread_code = message.message_thread_code');

$unreaded_message = $this->db->get()->num_rows();
?>

<section class="profile-nav-section">
    <div class="profile-header-box text-center">
        <div class="container">
            <h1 class="print-hidden"><?php echo $page_title; ?></h1>
        </div>
    </div>

    <div class="container">
        <ul class="modern-grid-nav print-hidden">
            <li class="<?php if ($page_name == 'my_courses') echo 'active'; ?>">
                <a href="<?php echo site_url('home/my_courses'); ?>"> 
                    <i class="fas fa-play-circle"></i> 
                    <span><?php echo site_phrase('courses'); ?></span>
                </a>
            </li>

            <?php if (addon_status('ebook')) : ?>
                <li class="<?php if ($page_name == 'my_ebooks' || $page_name == 'ebook_invoice') echo 'active'; ?>">
                    <a href="<?php echo site_url('home/my_ebooks'); ?>"> 
                        <i class="fas fa-book-open"></i>
                        <span><?php echo site_phrase('ebooks'); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (addon_status('course_bundle')) : ?>
                <li class="<?php if ($page_name == 'my_bundles' || $page_name == 'bundle_invoice') echo 'active'; ?>">
                    <a href="<?php echo site_url('home/my_bundles'); ?>"> 
                        <i class="fas fa-layer-group"></i> 
                        <span><?php echo site_phrase('bundles'); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="<?php if ($page_name == 'my_wishlist') echo 'active'; ?>">
                <a href="<?php echo site_url('home/my_wishlist'); ?>"> 
                    <i class="fas fa-heart"></i> 
                    <span><?php echo site_phrase('wishlists'); ?></span>
                </a>
            </li>

            <li class="msg-item <?php if ($page_name == 'my_messages') echo 'active'; ?>">
                <a href="<?php echo site_url('home/my_messages'); ?>">
                    <i class="fas fa-comment-dots"></i> 
                    <span><?php echo site_phrase('messages'); ?></span>
                    <?php if ($unreaded_message > 0) : ?>
                        <span class="msg-count-badge"><?php echo $unreaded_message; ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="<?php if ($page_name == 'purchase_history' || $page_name == 'invoice') echo 'active'; ?>">
                <a href="<?php echo site_url('home/purchase_history'); ?>"> 
                    <i class="fas fa-file-invoice-dollar"></i> 
                    <span><?php echo site_phrase('purchase_history'); ?></span>
                </a>
            </li>

            <li class="<?php if ($page_name == 'user_profile' || $page_name == 'user_credentials' || $page_name == 'update_user_photo') echo 'active'; ?>">
                <a href="<?php echo site_url('home/profile/user_profile'); ?>"> 
                    <i class="fas fa-user-cog"></i> 
                    <span><?php echo site_phrase('profile'); ?></span>
                </a>
            </li>

            <?php if (addon_status('tutor_booking')) : ?>
                <li class="<?php if( $page_name=='booked_schedule_student' ) echo 'active'; ?>">
                    <a href="<?php echo site_url('my_bookings'); ?>"> 
                        <i class="fas fa-calendar-check"></i> 
                        <span><?php echo site_phrase('Booked Tutions'); ?></span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if (addon_status('affiliate_course')) :
                $CI = &get_instance();
                $CI->load->model('addons/affiliate_course_model');
                if ($CI->affiliate_course_model->is_affilator($this->session->userdata('user_id')) == 1) : ?>
                    <li class="<?php if ($page_name == 'affiliate_course_history') echo 'active'; ?>">
                        <a href="<?php echo site_url('addons/affiliate_course/affiliate_course_history'); ?>"> 
                            <i class="fas fa-chart-line"></i> 
                            <span><?php echo site_phrase('Affiliate History'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
</section>