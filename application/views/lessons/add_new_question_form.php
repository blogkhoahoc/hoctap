<div class="row justify-content-center">
	<div class="col-md-7 pt-4 pb-3">
		<div class="row">
			<div class="col-md-6">
				<a class="btn-primary py-2 px-3 rounded-50" href="javascript:;" onclick="load_questions('<?= $course_id; ?>')"><i class="fas fa-arrow-left"></i> <?= site_phrase('all_questions'); ?></a>
			</div>
			<div class="col-md-6 text-right"><h5 class="mb-3"><?= site_phrase('ask_your_question'); ?></h5></div>
		</div>
		<form class="add-question-form" action="<?= site_url('addons/course_forum/add_new_question'); ?>" method="post">
			<label for="questionDescription"><?= site_phrase('title_or_summary'); ?></label>
    		<input type="text" id="questionTitle" class="form-control" name="title" required>
    		<br>
    		<label for="questionDescription"><?= site_phrase('details'); ?></label>
    		<textarea class="form-control" name="description" id="questionDescription" rows="4"></textarea>

    		<a href="javascript:;" class="btn btn-primary mt-4 px-5 float-end" onclick="publish_question('<?= $course_id; ?>')"><?= site_phrase('publish'); ?></a>
		</form>
	</div>
</div>