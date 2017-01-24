<?php
  if ( ! defined('ABSPATH')) {
    exit;
  }
  $_user = new \GB\GB_User();
  $_options      = get_option('_guestbook-options_options');
  $_account_page = $_options['account'] ?? \GB\GB_Setup::getCorePage('account');
  $_logout_page  = $_options['after-logout'] ?? null;
  $_account_url  = get_permalink($_account_page);
  $_logout_url   = get_permalink($_logout_page);
?>
<?php if ( ! $_user->loggedIn()) : ?>
  <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3 panel shadow">
    <section class="panel-body">
      <?= \GB\GB_Account::getLoginForm() ?>
    </section>
  </div>
<?php else: ?>
  <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3 panel shadow">
    <section class="panel-body">
      <div class="profile-sidebar center-block">
        <div class="profile-userpic half-bottom-margin">
          <a href="<?= esc_url($_account_url) ?>">
            <?= $_user->getAvatar() ?>
          </a>
        </div>
        <div class="profile-usertitle text-center half-bottom-margin">
          <h3>
            <?= $_user->getDisplayName() ?>
            <br>
            <small>
              <?= $_user->getEmail() ?>
            </small>
          </h3>
        </div>
        <div class="profile-userbuttons text-center half-bottom-margin">
          <a class="btn btn-success" href="<?= esc_url($_account_url) ?>">
            <?= esc_html(get_the_title($_account_page)) ?>
          </a>
          <a class="btn btn-danger" href="<?= esc_url(wp_logout_url($_logout_url)) ?>">
            <?= __('Log out') ?>
          </a>
        </div>
      </div>
    </section>
  </div>
<?php endif; ?>