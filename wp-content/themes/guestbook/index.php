<?php get_header(); ?>
  <div class="container">
    <div class="row" role="main">
      <div class="col-md-9 col-sm-9 panel">
        <section class="page-title">
          <h1><?php wp_title('') ?></h1>
        </section>
        <div class="panel-body">
          <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
              <?php get_template_part('template-parts/content', get_post_format()); ?>
            <?php endwhile; ?>
          <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-md-3 col-sm-3">
        <?php get_sidebar(); ?>
      </div>
    </div>
  </div>
<?php
  get_footer();