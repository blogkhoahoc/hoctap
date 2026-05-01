<div class="row ">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title"> <i class="mdi mdi-apple-keyboard-command title_icon"></i> <?php echo get_phrase('data_center'); ?>
                </h4>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>


<div class="row ">
    <div class="col-md-5 col-xl-6">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title">
                    <?php echo get_phrase('import_your_data'); ?>
                </h5>
                <p>You can import your demo and your backup data from here.</p>
                <form action="<?php echo site_url('data_center/demo_importer'); ?>" method="post" enctype="multipart/form-data" id="import_backup_data_form">
                    <div class="input-group mb-3">
                    	<div class="input-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" name = "data_file" id="data_file" onchange="changeTitleOfImageUploader(this)" accept=".zip" required>
								<label class="custom-file-label ellipsis" for="data_file"><?php echo get_phrase('choose_your_demo_file'); ?></label>
							</div>
						</div>
                        <span class="badge badge-light">Ex: uploads_v<?php echo get_settings('version'); ?>.zip</span>
                    </div>

                    <div class="form-group">
                        <button onclick="jQuery('#data-import-alert-modal').modal('show', {backdrop: 'static'});" type="button" class="btn btn-primary w-100"> <i class="mdi mdi-database-export"></i> <?php echo get_phrase('import'); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="header-title">
                    <?php echo get_phrase('import_your_language_file'); ?>
                </h5>
                <p>You can import your language files from here.</p>
                <form action="<?php echo site_url('data_center/language_import'); ?>" method="post" enctype="multipart/form-data">
                    <div class="input-group mb-3">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name = "language_files[]" id="language_files" onchange="changeTitleOfImageUploader(this)" accept=".json" multiple required>
                                <label class="custom-file-label ellipsis" for="language_files"><?php echo get_phrase('choose_your_json_file'); ?></label>
                            </div>
                        </div>
                        <span class="badge badge-light">Ex: english.json</span>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary w-100"> <i class="mdi mdi-database-export"></i> <?php echo get_phrase('import'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7 col-xl-6">
        
        <!-- Khu vực 1: Backup Source Code -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex mb-3">
                    <h5><?php echo get_phrase('backup_source_code'); ?></h5>
                    <a class="ml-auto btn btn-primary px-2 py-1" data-toggle="tooltip" title="<?php echo get_phrase('backup_your_current_data'); ?>" href="<?php echo site_url('data_center/create_backup_file'); ?>"><i class="dripicons-cloud-upload"></i> <?php echo get_phrase('keep_a_backup'); ?></a>
                </div>

                <?php if($this->session->flashdata('imported_message')): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="dripicons-checkmark mr-2"></i> 
                        <?php echo $this->session->flashdata('imported_message'); ?>
                    </div>
                <?php endif; ?>

                <?php
                    $all_backups = glob('backups/*.zip'); 
                    if(count($all_backups) == 0): ?>
                        
                    <div class="alert alert-light" role="alert">
                        <i class="mdi mdi-folder-multiple-outline mr-2"></i> 
                        <?php echo get_phrase('No backup'); ?>
                    </div>
                <?php endif; ?>

                <?php
                    foreach($all_backups as $key => $file_path){
                        ++$key;
                        $file_arr = explode('/', $file_path);
                        $file_name = end($file_arr);
                        $file_details = explode('_', str_replace('.zip', '', $file_name)); 
                        $date_string = end($file_details); 
                        ?>

                    <div class="card mb-1 shadow-none border">
                        <div class="p-2">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded">
                                            <i class="mdi mdi-zip-box"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col pl-0">
                                    <a href="javascript:void(0);" class="text-muted text-capitalize font-weight-bold">
                                        <?php echo $file_name; ?>
                                    </a>
                                    <br>
                                    <?php
                                        $created_date_arr = explode('-', $date_string);
                                        if (count($created_date_arr) == 6) {
                                            $formatted_date = date('d M Y, H:i:s', strtotime($created_date_arr[0].' '.$created_date_arr[1].' '.$created_date_arr[2].' '.$created_date_arr[3].':'.$created_date_arr[4].':'.$created_date_arr[5]));
                                        } else {
                                            $formatted_date = date('d M Y, H:i:s', filemtime($file_path));
                                        }
                                    ?>
                                    <small class="mb-0 text-muted w-100"><?php echo $formatted_date; ?></small>
                                </div>
                                <div class="col-auto">
                                    <!-- Button -->
                                    <a href="<?php echo site_url('data_center/download_zip_file/'.$file_name); ?>" data-toggle="tooltip" title="<?php echo get_phrase('export'); ?>" target="_blank" class="btn btn-link btn-lg text-muted">
                                        <i class="dripicons-download"></i>
                                    </a>
                                    <a href="javascript:;" data-toggle="tooltip" title="<?php echo get_phrase('Delete'); ?>" onclick="confirm_modal('<?php echo site_url('data_center/delete_dir/'.$file_name); ?>')" class="btn btn-link btn-lg text-muted">
                                        <i class="dripicons-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div> <!-- end card-->
        </div>
        
        <!-- Khu vực 2: Thêm mới Export Database -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex mb-1">
                    <h5><?php echo get_phrase('export_database'); ?></h5>
                    <a class="ml-auto btn btn-info px-2 py-1" data-toggle="tooltip" title="<?php echo get_phrase('download_database'); ?>" href="<?php echo site_url('data_center/export_database'); ?>">
                        <i class="mdi mdi-database-export"></i> <?php echo get_phrase('download_sql'); ?>
                    </a>
                </div>
                <small class="text-muted"><?php echo get_phrase('download_a_copy_of_your_current_database_in_zip_format'); ?></small>
            </div>
        </div>

    </div>
</div>

<div class="row ">
    <div class="col-md-5 col-xl-6">
        
    </div>
    <div class="col-md-7 col-xl-6"></div>
</div>