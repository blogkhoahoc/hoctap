<input type="hidden" name="lesson_type" value="other-iframe">

<div class="form-group">
    <label><?php echo get_phrase('iframe_source'); ?>( <?php echo get_phrase('provide_the_source_only'); ?> )</label>
    <input type="text" id = "iframe_source" name = "iframe_source" class="form-control" placeholder="<?php echo get_phrase('provide_the_source_only'); ?>">
</div>

<div class="form-group">
    <label><?php echo get_phrase('duration'); ?></label>
    <input type="text" class="form-control" name="duration" id="duration" placeholder="00:00:00" value="00:00:00">
    <small class="text-muted">Ví dụ: 00:10:00 (10 phút)</small>
</div>