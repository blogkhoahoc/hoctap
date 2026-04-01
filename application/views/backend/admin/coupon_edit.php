<!-- start page title -->
<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                    <a href="<?php echo site_url('admin/coupons'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><?php echo get_phrase('back_to_coupons'); ?></a>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>

<div class="row justify-content-center">
    <div class="col-xl-7">
        <div class="card">
            <div class="card-body">
                <div class="col-lg-12">
                    <h4 class="mb-3 header-title"><?php echo get_phrase('coupon_edit_form'); ?></h4>

                    <form class="required-form" action="<?php echo site_url('admin/coupons/edit/' . $coupon['id']); ?>" method="post" enctype="multipart/form-data">

                        <div class="form-group">
                            <label for="code"><?php echo get_phrase('coupon_code'); ?><span class="required">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="<?php echo $coupon['code']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="discount_percentage"><?php echo get_phrase('discount_percentage'); ?></label>
                            <div class="input-group">
                                <input type="number" name="discount_percentage" id="discount_percentage" class="form-control" value="<?php echo $coupon['discount_percentage']; ?>" min="1" max="100">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-percent"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="course_ids"><?php echo get_phrase('courses'); ?></label>
                            <select class="form-control select2" data-toggle="select2" name="course_ids[]" multiple="multiple">
                                <?php 
                                $courses = $this->crud_model->get_status_wise_courses('active')->result_array();
                                $selected_courses = json_decode($coupon['course_ids']) ?? []; 
                                foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['id']; ?>" <?php if(in_array($course['id'], $selected_courses)) echo 'selected'; ?>>
                                        <?php echo $course['title']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Bỏ trống nếu muốn áp dụng mã giảm giá này cho toàn bộ hóa đơn.</small>
                        </div>
                        <div class="form-group">
                            <label for="quantity">Số lượng mã</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="<?php echo $coupon['quantity']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="expiry_date"><?php echo get_phrase('expiry_date'); ?><span class="required">*</span></label>
                            <input type="text" name="expiry_date" class="form-control date" id="expiry_date" data-toggle="date-picker" data-single-date-picker="true" value="<?php echo date('m/d/Y', $coupon['expiry_date']); ?>">
                        </div>

                        <button type="submit" class="btn btn-primary"><?php echo get_phrase("submit"); ?></button>
                    </form>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>