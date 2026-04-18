<section class="category-course-list-area" style="background-color: #f7f8fa; min-height: 80vh; display: flex; align-items: center;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8 col-sm-12">
        
        <div class="verification-card text-center">
            <div class="icon-wrapper">
                <i class="fas fa-shield-alt"></i>
            </div>

            <h2 class="fw-700 mb-2" style="color: #333; font-size: 26px;"><?php echo site_phrase('login_confirmation'); ?></h2>
            <p class="text-15px text-muted mb-4">
                <?php echo site_phrase('let_us_know_that_this_email_address_belongs_to_you'); ?> 
                <br>
                <?php echo site_phrase('Enter_the_code_from_the_email_sent_to'); ?> <b style="color: #333;"><?php echo $this->session->userdata('new_device_user_email'); ?></b>
            </p>

            <div class="spam-notice shadow-sm">
                <i class="fas fa-exclamation-circle mt-1 me-2" style="color: #ffb822; font-size: 18px;"></i> 
                <div>
                    <strong>Lưu ý:</strong> Nếu không thấy mail ở Hộp thư đến, vui lòng kiểm tra trong thư mục <strong>Spam (Thư rác)</strong> nhé!
                </div>
            </div>

            <form action="<?php echo site_url('login/new_login_confirmation/submit'); ?>" method="post" id="email_verification">
                
                <div class="form-group mb-3">
                    <input type="text" class="form-control otp-input" placeholder="<?php echo site_phrase('enter_the_verification_code'); ?>" name="new_device_verification_code" id="new_device_verification_code" required autocomplete="off">
                </div>

                <div class="d-flex justify-content-end align-items-center mb-4">
                    <a href="javascript:;" class="text-14px fw-600" id="resend_mail_button" onclick="resend_new_device_verification_code()" style="color: #ec5252; text-decoration: none;">
                        <i class="fas fa-redo-alt me-1"></i> <?= site_phrase('resend_verification_code') ?>
                    </a>
                    <div id="resend_mail_loader" class="ms-2"></div>
                </div>

                <div class="form-group mb-4">
                    <button type="submit" class="btn btn-verify w-100 shadow-sm">
                        <?php echo site_phrase('continue'); ?> <i class="fas fa-sign-in-alt ms-2"></i>
                    </button>
                </div>

                <div class="form-group mt-4 mb-0 text-center text-muted">
                    <?php echo site_phrase('want_to_go_back'); ?>? 
                    <a class="text-15px fw-700" href="<?php echo site_url('login') ?>" style="color: #ec5252; text-decoration: none;">
                        <?php echo site_phrase('login'); ?>
                    </a>
                </div>

            </form>
        </div>

      </div>
    </div>
  </div>
</section>

<script type="text/javascript">
  function resend_new_device_verification_code() {
    $("#resend_mail_loader").html('<img src="<?= base_url('assets/global/gif/page-loader-3.gif'); ?>" style="width: 25px;">');
    $.ajax({
      type: 'post',
      url: '<?php echo site_url('login/new_login_confirmation/resend'); ?>',
      success: function(response){
        toastr.success('<?php echo site_phrase('mail_successfully_sent_to_your_inbox');?>');
        $("#resend_mail_loader").html('');
      }
    });
  }
</script>