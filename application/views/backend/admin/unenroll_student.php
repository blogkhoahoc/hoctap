<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-account-minus title_icon"></i> <?php echo $page_title; ?> </h4>
            </div> </div> </div></div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo site_url('admin/unenroll_student/search'); ?>" method="post">
                    <div class="form-group row mb-3">
                        <label class="col-md-3 col-form-label" for="email">Nhập Email sinh viên cần tìm</label>
                        <div class="col-md-6">
                            <input type="email" class="form-control" id="email" name="email" placeholder="vd: student@example.com" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary btn-block">Tìm kiếm sinh viên</button>
                        </div>
                    </div>
                </form>

                <?php if (isset($user_id)): ?>
                    <hr>
                    <h4 class="header-title mb-3 mt-4">Sinh viên: <span class="text-primary"><?php echo $user_name; ?></span></h4>
                    
                    <form action="<?php echo site_url('admin/unenroll_student/unenroll'); ?>" method="post">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        
                        <?php if (empty($enrolled_courses)): ?>
                            <div class="alert alert-info">Sinh viên này hiện chưa tham gia khóa học nào.</div>
                        <?php else: ?>
                            <div class="table-responsive-sm mt-4">
                                <table class="table table-bordered table-centered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">Chọn</th>
                                            <th>Tên khóa học</th>
                                            <th>Ngày đăng ký</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrolled_courses as $enrol): 
                                            // Lấy thông tin khóa học dựa trên course_id
                                            $course_details = $this->crud_model->get_course_by_id($enrol['course_id'])->row_array();
                                            if (!$course_details) continue;
                                        ?>
                                            <tr>
                                                <td class="text-center">
                                                    <input type="checkbox" name="course_ids[]" value="<?php echo $enrol['course_id']; ?>" style="transform: scale(1.5);">
                                                </td>
                                                <td>
                                                    <strong><a href="<?php echo site_url('admin/course_form/course_edit/'.$course_details['id']); ?>" target="_blank"><?php echo $course_details['title']; ?></a></strong>
                                                </td>
                                                <td>
                                                    <?php echo date('d M Y', $enrol['date_added']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Bạn có chắc chắn muốn XÓA sinh viên khỏi các khóa học đã đánh dấu không?');">
                                    <i class="mdi mdi-delete"></i> Thực hiện Xóa (Unenroll)
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>