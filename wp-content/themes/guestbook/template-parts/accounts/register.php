<?php
  if ( ! defined('ABSPATH')) {
    exit;
  }
  $_user = new \GB\GB_User();
?>
<?php if ( ! $_user->loggedIn()) : ?>
  <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3 panel shadow">
    <section class="panel-body">
      <?= \GB\GB_Account::getRegisterForm() ?>
    </section>
  </div>
<?php else : ?>
  <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3 panel shadow">
    <section class="panel-body">
      <div class="alert alert-success">
        <?= __('You have logged in successfully.'); ?>
      </div>
    </section>
  </div>
<?php endif; ?>