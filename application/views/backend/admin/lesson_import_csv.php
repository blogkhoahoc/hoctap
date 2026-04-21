<form action="<?php echo site_url('admin/lesson_import_csv/'.$param2); ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Chọn file CSV của bạn</label>
        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
        <small class="text-muted">Cấu trúc cột: ID Section, Tên video, Link Url, Thời lượng. Lưu ý: File chỉ đọc tạm thời và tự động xóa sau khi nhập.</small>
    </div>
    
    <div class="text-right">
        <button type="submit" class="btn btn-primary" onclick="this.innerHTML='Đang xử lý...';">Bắt đầu nhập</button>
    </div>
</form>