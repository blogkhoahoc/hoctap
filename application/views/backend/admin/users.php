<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo $page_title; ?>
                    <a href="<?php echo site_url('admin/user_form/add_user_form'); ?>" class="btn btn-outline-primary btn-rounded alignToTitle"><i class="mdi mdi-plus"></i><?php echo get_phrase('add_student'); ?></a>
                    <button type="button" onclick="confirmDeleteUnverified()" class="btn btn-outline-danger btn-rounded alignToTitle mr-1"><i class="mdi mdi-delete-sweep"></i> Xóa HV chưa xác thực</button>
                </h4>
            </div> 
        </div> 
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
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
                            <tr><th>#</th><th>Ảnh</th><th>Tên</th><th>Email</th><th>Khóa học</th><th>Hành động</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div> 
        </div> 
    </div>
</div>

<div id="dynamicDeleteModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center" style="border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: none;">
            <div class="modal-body p-4">
                
                <div id="modalIconContainer" class="mb-3"></div>
                
                <h4 class="mb-2" id="modalTitle" style="font-weight: 600; color: #313a46;"></h4>
                <p class="text-muted mb-4" id="modalMessage" style="font-size: 15px; line-height: 1.5;"></p>
                
                <div class="mt-3" id="modalButtonContainer"></div>
                
            </div>
        </div>
    </div>
</div>

<script>
var userTable;

$(document).ready(function() {
    userTable = $('#server_side_users_data').DataTable({
        "processing": true,
        "serverSide": true,
        "bDestroy": true,
        "searchDelay": 1200, 
        "ajax": {
            "url": "<?php echo site_url('admin/get_users_ajax'); ?>",
            "type": "POST",
            "data": function (d) {
                d.status_filter = $('#status_filter').val(); 
            }
        },
        "columnDefs": [
            { "targets": [1, 4, 5], "orderable": false }
        ]
    });

    $('#status_filter').on('change', function() {
        userTable.ajax.reload(); 
    });
});

// HÀM 1: Đếm số lượng và cấu hình UI cho Popup
function confirmDeleteUnverified() {
    $.ajax({
        url: "<?php echo site_url('admin/check_unverified_users_count'); ?>",
        type: "GET",
        success: function(response) {
            var res = JSON.parse(response);
            
            if (res.status === 'success') {
                if (res.count > 0) {
                    // GIAO DIỆN CẢNH BÁO (KHI CÓ DATA)
                    $('#modalIconContainer').html('<i class="mdi mdi-alert-circle-outline text-warning" style="font-size: 65px; line-height: 1;"></i>');
                    $('#modalTitle').text('Xác nhận dọn dẹp');
                    $('#modalMessage').html('Hiện tại đang có <strong>' + res.count + '</strong> tài khoản chưa kích hoạt.<br><br>Bạn có chắc chắn xóa tất cả chứ?');
                    
                    $('#modalButtonContainer').html(
                        '<button type="button" class="btn btn-light mx-1" data-dismiss="modal" style="min-width: 90px; border-radius: 6px;">Hủy bỏ</button>' +
                        '<button type="button" class="btn btn-danger mx-1" onclick="executeDeleteUnverified()" style="min-width: 90px; border-radius: 6px;">Xóa tất cả</button>'
                    );
                } else {
                    // GIAO DIỆN THÔNG BÁO TỐT (KHI BẰNG 0)
                    $('#modalIconContainer').html('<i class="mdi mdi-check-circle-outline text-success" style="font-size: 65px; line-height: 1;"></i>');
                    $('#modalTitle').text('Trống trải!');
                    $('#modalMessage').html('Tuyệt vời! Hiện tại không có tài khoản chưa kích hoạt nào cần dọn dẹp cả.');
                    
                    $('#modalButtonContainer').html(
                        '<button type="button" class="btn btn-light" data-dismiss="modal" style="min-width: 120px; border-radius: 6px;">Đóng</button>'
                    );
                }
                
                // Hiển thị Popup
                $('#dynamicDeleteModal').modal('show');
            }
        }
    });
}

// HÀM 2: Thực thi lệnh xóa Database
function executeDeleteUnverified() {
    $.ajax({
        url: "<?php echo site_url('admin/execute_delete_unverified'); ?>",
        type: "POST",
        success: function() { 
            // Xóa xong reload lại trang để hiện Flash Message của hệ thống
            location.reload(); 
        }
    });
}
</script>