<div class="col-md-3 col-sm-3 profile-sidebar">
  <?php do_action('account_nav_tabs', $args); ?>
</div>
<div class="col-md-9 col-sm-9">
  <div class="panel shadow">
    <div class="panel-body">
      <?php do_action('account_content_tabs', $args); ?>
    </div>
  </div>
</div>