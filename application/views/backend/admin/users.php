<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                    <a href="<?php echo site_url('admin/user_form/add_user_form'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-plus"></i><?php echo get_phrase('add_student'); ?></a>
                </h4>
            </div> 
        </div> 
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3 header-title"><?php echo get_phrase('student'); ?></h4>
                
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select id="status_filter" class="form-control select2" data-toggle="select2">
                            <option value="all">Tất cả sinh viên</option>
                            <option value="unverified">Chưa xác thực (Unverified)</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive-sm mt-4">
                    <table id="server_side_users_data" class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo get_phrase('photo'); ?></th>
                                <th><?php echo get_phrase('name'); ?></th>
                                <th><?php echo get_phrase('email'); ?></th>
                                <th><?php echo get_phrase('enrolled_courses'); ?></th>
                                <th><?php echo get_phrase('actions'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> 
        </div> 
    </div>
</div>

<script>
$(document).ready(function() {
    // Gán thư viện DataTable vào một biến để dễ dàng gọi lệnh reload sau này
    var userTable = $('#server_side_users_data').DataTable({
        "processing": true,
        "serverSide": true,
        "bDestroy": true,
        "searchDelay": 1500, // Thêm delay 800ms: Gõ phím xong chờ 1.5s mới gọi Ajax để giảm tải cho server
        "ajax": {
            "url": "<?php echo site_url('admin/get_users_ajax'); ?>",
            "type": "POST",
            "data": function (d) {
                // Lấy giá trị của ô Select Filter và gộp chung vào cục data gửi xuống Controller
                d.status_filter = $('#status_filter').val(); 
            }
        },
        "columnDefs": [
            // Tắt chức năng sắp xếp (sort) ở các cột không cần thiết: Ảnh, Khóa học, Hành động
            { "targets": [1, 4, 5], "orderable": false }
        ]
    });

    // Bắt sự kiện khi ô Select thay đổi giá trị (Admin chọn lọc Unverified)
    $('#status_filter').on('change', function() {
        userTable.ajax.reload(); // Yêu cầu bảng vẽ lại dữ liệu mới
    });
});
</script>