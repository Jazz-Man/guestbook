<?php
  //  var_dump($args);
  $tabs  = $args ?? [];
  $_user = new \GB\GB_User();
?>
<div class="profile-userpic half-bottom-margin">
  <?= $_user->getAvatar() ?>
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
  <button id="add_comments" class="btn btn-success btn-sm">
    <?= __('Post Comment') ?>
  </button>
</div>
<?php if ( ! empty($tabs)): ?>
  <div class="profile-usermenu">
    <ul class="nav">
      <?php foreach ((array)$tabs as $id => $tab): ?>
        <?php
        $nav_link                  = add_query_arg('profiletab', $id, get_permalink());
        $active_tab                = get_query_var('profiletab');
        $comments_count            = get_comments(
          [
            'hierarchical' => 'flat',
            'count'        => 1,
            'user_id'      => get_current_user_id(),
          ]
        );
        $um_profile_nav_item_class = [
          "item-{$id}",
          $id === $active_tab ? 'active' : ''
        ];
        ?>
        <li class="<?= implode(' ', array_filter($um_profile_nav_item_class)); ?>">
          <a href="<?= $nav_link ?>" title="<?= $tab['name'] ?>">
            <i class="<?= $tab['icon'] ?>"></i>
            <?= $tab['name'] ?>
            <span class="badge pull-right"><?= $comments_count ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
