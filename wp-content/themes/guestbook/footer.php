</div><!-- #content -->
<footer id="colophon" class="site-footer" role="contentinfo">
  <div class="site-info">
    <a href="<?php echo esc_url(__('https://wordpress.org/', 'guestbook')); ?>"><?php printf(
        esc_html__('Proudly powered by %s', 'guestbook'), 'WordPress'
      ); ?></a>
    <span class="sep"> | </span>
    <?php printf(
      esc_html__('Theme: %1$s by %2$s.', 'guestbook'), 'guestbook',
      '<a href="https://automattic.com/" rel="designer">JazzMan</a>'
    ); ?>
  </div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->
<?php wp_footer(); ?>
</body>
</html>
