<?php
  namespace GB;

  /**
   * Class GB_Filter
   *
   * @package GB
   */
  class GB_Filter
  {

    /**
     * GB_Filter constructor.
     */
    public function __construct()
    {
      add_action('display_nav_menu', [$this, 'display_nav_menu']);
      add_filter('query_vars', [$this, 'add_query_vars']);
      add_filter('comments_open', [$this, 'comments_open'], 10, 2);
      add_action('wp_ajax_nopriv_delete_comments_form', [$this, 'delete_comments_form']);
      add_action('wp_ajax_delete_comments_form', [$this, 'delete_comments_form']);
      add_action('wp_ajax_nopriv_edit_comments_form', [$this, 'edit_comments_form']);
      add_action('wp_ajax_edit_comments_form', [$this, 'edit_comments_form']);
      add_action('wp_ajax_nopriv_add_comments_form', [$this, 'add_comments_form']);
      add_action('wp_ajax_add_comments_form', [$this, 'add_comments_form']);
      add_action('wp_ajax_nopriv_add_comments', [$this, 'add_comments']);
      add_action('wp_ajax_add_comments', [$this, 'add_comments']);
      add_action('wp_ajax_nopriv_update_comment', [$this, 'update_comment']);
      add_action('wp_ajax_update_comment', [$this, 'update_comment']);
    }

    public function delete_comments_form()
    {
      $cid      = $_POST['cid'];
      $response = ['success' => false];
      if (current_user_can('moderate_comments')) {
        $comments = wp_delete_comment($cid, true);
        $response = $comments ? ['success' => true] : ['success' => false];
      }
      wp_send_json_success($response);
      wp_die();
    }

    public function update_comment()
    {
      if (empty($_POST)) {
        return;
      }
      $attr = $_POST['data'];
      if ($attr['request'] !== '' || ! wp_verify_nonce($attr['gb_form'], 'gb_form')) {
        wp_send_json_error(GB_Helper::notice('spam', 'Привіт спам :))'));
      }
      $comment_data = [
        'comment_ID'           => $attr['comment_ID'],
        'comment_post_ID'      => $attr['comment_post_ID'],
        'comment_content'      => sanitize_textarea_field($attr['comment']),
        'comment_author'       => $attr['newcomment_author'],
        'comment_author_email' => sanitize_email($attr['newcomment_author_email']),
        'comment_author_url'   => esc_url($attr['newcomment_author_url']),
      ];
      $comment      = wp_update_comment($comment_data);
      if ($comment === 0) {
        ob_start();
        ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <?= GB_Helper::notice('update_comment_errore', 'Ваш коментар не оновлено', 'warning'); ?>
        </div>
        <div class="modal-footer">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __('Ok') ?></button>
          </div>
        </div>
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        wp_send_json_error($result);
      } else {
        ob_start();
        ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <?= GB_Helper::notice('update_comment_success', 'Ваш коментар оновлено', 'success'); ?>
        </div>
        <div class="modal-footer">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __('Ok') ?></button>
          </div>
        </div>
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        wp_send_json_success($result);
      }
    }

    public function edit_comments_form()
    {
      ob_start();
      ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title"><?= __('Edit comment') ?> </h4>
      </div>
      <div class="modal-body">
        <?php GB_Account::getCommentEditForm($_POST); ?>
      </div>
      <?php
      $result = ob_get_contents();
      ob_end_clean();
      wp_send_json_success($result);
      wp_die();
    }

    public function add_comments_form()
    {
      ob_start();
      ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title"><?= __('Post Comment') ?></h4>
      </div>
      <div class="modal-body">
        <?php GB_Account::getCommentAddForm(); ?>
      </div>
      <?php
      $result = ob_get_contents();
      ob_end_clean();
      wp_send_json_success($result);
      wp_die();
    }

    public function add_comments()
    {
      if (empty($_POST)) {
        return;
      }
      $attr = $_POST['data'];
      if ($attr['request'] !== '' || ! wp_verify_nonce($attr['gb_form'], 'gb_form')) {
        wp_send_json_error(GB_Helper::notice('spam', 'Привіт спам :))'));
      }
      $comment_data = [
        'comment_post_ID' => $attr['comment_post_ID'],
        'comment'         => sanitize_textarea_field($attr['comment']),
        'author'          => $attr['newcomment_author'],
        'email'           => sanitize_email($attr['newcomment_author_email']),
        'url'             => esc_url($attr['newcomment_author_url']),
      ];
      $comment = wp_handle_comment_submission($comment_data);
      if (is_wp_error($comment)) {
        ob_start();
        ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <?= GB_Helper::notice($comment->get_error_code(), $comment->get_error_message()); ?>
        </div>
        <div class="modal-footer">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __('Ok') ?></button>
          </div>
        </div>
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        wp_send_json_error($result);
      } else {
        ob_start();
        ?>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">
          <?= GB_Helper::notice('update_comment_success', 'Ваш коментар додано', 'success'); ?>
        </div>
        <div class="modal-footer">
          <div class="btn-group">
            <button type="button" class="btn btn-primary" data-dismiss="modal"><?= __('Ok') ?></button>
          </div>
        </div>
        <?php
        $result = ob_get_contents();
        ob_end_clean();
        wp_send_json_success($result);
      }
      wp_send_json_success($_POST);
      wp_die();
    }

    /**
     * @param array $menus
     */
    public function display_nav_menu(array $menus = [])
    {
      $defaults = [];
      if ( ! empty($menus)) {
        $defaults[] = $menus;
      }
      if ( ! empty($defaults) && is_array($defaults)) {
        foreach ($defaults as $menu) {
          wp_nav_menu($menu);
        }
      }
    }

    /**
     * @param $vars
     *
     * @return array
     */
    public function add_query_vars($vars)
    {
      $vars[] = 'profiletab';
      $vars[] = 'action';
      $vars[] = 'c';

      return $vars;
    }

    /**
     * @param $open
     * @param $post_id
     *
     * @return bool
     */
    public function comments_open($open, $post_id)
    {
      return true;
    }

  }