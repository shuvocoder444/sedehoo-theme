</main><!-- /#main-content -->


<!-- ===== SITE FOOTER ===== -->
<footer id="site-footer" role="contentinfo">

  <div class="footer-copyright">
    <?php echo wp_kses_post( sedehoo_get( 'footer_copyright', '&copy; ' . date( 'Y' ) . ' ' . get_bloginfo( 'name' ) . '. All rights reserved.' ) ); ?>
  </div>

  <?php if ( has_nav_menu( 'footer-nav' ) ) : ?>
    <nav class="footer-nav" aria-label="<?php esc_attr_e( 'Footer Navigation', 'sedehoo' ); ?>">
      <?php wp_nav_menu( [ 'theme_location' => 'footer-nav', 'container' => false, 'depth' => 1, 'fallback_cb' => false ] ); ?>
    </nav>
  <?php else : ?>
    <nav class="footer-nav">
      <ul>
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'sedehoo' ); ?></a></li>
        <li><a href="<?php echo esc_url( get_post_type_archive_link( 'sedehoo_photo' ) ); ?>"><?php esc_html_e( 'Photos', 'sedehoo' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/studio/' ) ); ?>"><?php esc_html_e( 'Studio', 'sedehoo' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'sedehoo' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'sedehoo' ); ?></a></li>
      </ul>
    </nav>
  <?php endif; ?>

</footer>

</div><!-- /#main-wrap -->

<!-- Notification Toast -->
<div id="sdNotif" class="notification" role="alert" aria-live="polite">
  <span class="notif-dot" id="sdNotifDot"></span>
  <span id="sdNotifMsg"></span>
</div>

<?php wp_footer(); ?>
</body>
</html>
