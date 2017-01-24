<?php get_header(); ?>
<div class="container">
  <section class="page-title center">
    <h1><?php wp_title('') ?></h1>
  </section>
  <section>
    <div class="row">
      <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
      <?php endwhile; ?>
    </div>
  </section>
</div>
<?php get_footer(); ?>
