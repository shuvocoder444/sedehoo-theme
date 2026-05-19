<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php wp_head(); ?>
  <script>
    (function() {
      var savedTheme = localStorage.getItem('sedehoo_theme') || 'default';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
    function sdChangeTheme(theme) {
      document.documentElement.setAttribute('data-theme', theme);
      localStorage.setItem('sedehoo_theme', theme);
    }
  </script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="sidebar-overlay" onclick="sdCloseSidebar()"></div>

<!-- ===== SIDEBAR ===== -->
<aside id="sidebar" role="navigation" aria-label="<?php esc_attr_e( 'Sidebar Navigation', 'sedehoo' ); ?>">

  <div class="sidebar-logo">
    <?php if ( has_custom_logo() ) : ?>
      <?php the_custom_logo(); ?>
    <?php else : ?>
      <div class="logo-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="#0a0f06" stroke-width="2.5" stroke-linecap="round">
          <circle cx="12" cy="12" r="3"/><path d="M2 12s3.636-7 10-7 10 7 10 7-3.636 7-10 7-10-7-10-7z"/>
        </svg>
      </div>
      <div>
        <div class="logo-text"><?php echo esc_html( sedehoo_get( 'logo_text', get_bloginfo( 'name' ) ) ); ?></div>
        <?php $sub = sedehoo_get( 'logo_subtext', '' ); if ( $sub ) : ?>
          <span class="logo-sub"><?php echo esc_html( $sub ); ?></span>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Primary Navigation -->
  <?php if ( has_nav_menu( 'sidebar-primary' ) ) : ?>
    <div class="sidebar-section">
      <div class="sidebar-section-label"><?php esc_html_e( 'Browse', 'sedehoo' ); ?></div>
      <nav class="sidebar-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'sedehoo' ); ?>">
        <?php wp_nav_menu( [
          'theme_location' => 'sidebar-primary',
          'menu_class'     => 'sidebar-menu',
          'container'      => false,
          'depth'          => 2,
          'fallback_cb'    => false,
        ] ); ?>
      </nav>
    </div>
  <?php else : ?>
    <!-- Default fallback nav when no menu assigned -->
    <div class="sidebar-section">
      <div class="sidebar-section-label"><?php esc_html_e( 'Browse', 'sedehoo' ); ?></div>
      <nav class="sidebar-nav">
        <ul>
          <li <?php if ( is_front_page() ) echo 'class="current-menu-item"'; ?>>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
              <?php esc_html_e( 'Home', 'sedehoo' ); ?>
            </a>
          </li>



        </ul>
      </nav>
    </div>
  <?php endif; ?>

  <!-- Photo Categories -->
  <?php
  $photo_cats = get_terms( [ 'taxonomy' => 'photo_category', 'hide_empty' => true, 'number' => 7 ] );
  if ( ! is_wp_error( $photo_cats ) && ! empty( $photo_cats ) ) : ?>
    <div class="sidebar-section">
      <div class="sidebar-section-label"><?php esc_html_e( 'Categories', 'sedehoo' ); ?></div>
      <nav class="sidebar-nav">
        <ul>
          <?php foreach ( $photo_cats as $cat ) :
            $is_cur = is_tax( 'photo_category', $cat->term_id );
          ?>
            <li <?php if ( $is_cur ) echo 'class="current-menu-item"'; ?>>
              <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/></svg>
                <?php echo esc_html( $cat->name ); ?>
                <span style="margin-left:auto;font-size:10px;opacity:.5"><?php echo absint( $cat->count ); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>
  <?php endif; ?>

  <!-- Sidebar Widgets -->
  <?php if ( is_active_sidebar( 'sidebar-widget-area' ) ) : ?>
    <div class="sidebar-section"><?php dynamic_sidebar( 'sidebar-widget-area' ); ?></div>
  <?php endif; ?>

  <!-- Secondary / Footer links -->
  <?php if ( has_nav_menu( 'sidebar-secondary' ) ) : ?>
    <div class="sidebar-footer">
      <nav class="sidebar-nav" aria-label="<?php esc_attr_e( 'Secondary Navigation', 'sedehoo' ); ?>">
        <?php wp_nav_menu( [
          'theme_location' => 'sidebar-secondary',
          'menu_class'     => 'sidebar-menu',
          'container'      => false,
          'depth'          => 1,
          'fallback_cb'    => false,
        ] ); ?>
      </nav>
    </div>
  <?php else : ?>
    <!-- User account links -->
    <?php if ( is_user_logged_in() ) : ?>
      <div class="sidebar-footer">
        <nav class="sidebar-nav">
          <ul>
            <li>
              <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?php esc_html_e( 'Dashboard', 'sedehoo' ); ?>
              </a>
            </li>
            <li>
              <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <?php esc_html_e( 'Log Out', 'sedehoo' ); ?>
              </a>
            </li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>
  <?php endif; ?>

</aside>

<!-- ===== HEADER ===== -->
<header id="site-header" role="banner">

  <!-- Sidebar toggle (works on both desktop and mobile) -->
  <button id="sidebar-toggle" onclick="sdToggleSidebar()" aria-label="<?php esc_attr_e( 'Toggle Sidebar', 'sedehoo' ); ?>" aria-expanded="true">
    <svg id="toggle-icon-bars" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <line x1="3" y1="6"  x2="21" y2="6"/>
      <line x1="3" y1="12" x2="21" y2="12"/>
      <line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>

  <!-- Mobile Logo -->
  <div class="header-mobile-logo">
    <?php if ( has_custom_logo() ) : ?>
      <?php the_custom_logo(); ?>
    <?php else : ?>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mobile-logo-link">
        <?php echo esc_html( sedehoo_get( 'logo_text', get_bloginfo( 'name' ) ) ); ?>
      </a>
    <?php endif; ?>
  </div>

  <!-- Header nav (center) -->
  <?php if ( has_nav_menu( 'header-nav' ) ) : ?>
    <nav class="header-nav" aria-label="<?php esc_attr_e( 'Header Navigation', 'sedehoo' ); ?>">
      <?php wp_nav_menu( [ 'theme_location' => 'header-nav', 'container' => false, 'depth' => 1, 'fallback_cb' => false ] ); ?>
    </nav>
  <?php endif; ?>

  <!-- Mini search in header -->
  <div class="header-search-mini" role="search">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#7a7f8e" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" placeholder="<?php esc_attr_e( 'Search photos…', 'sedehoo' ); ?>" id="headerSearch" autocomplete="off" aria-label="<?php esc_attr_e( 'Search', 'sedehoo' ); ?>">
    <kbd>⌘K</kbd>
  </div>

  <!-- CTA Buttons -->
  <div class="header-actions">
    <select id="themeSelect" onchange="sdChangeTheme(this.value)" style="background: transparent; color: var(--text); border: 1px solid var(--border); border-radius: 8px; padding: 6px 10px; font-size: 13px; outline: none; cursor: pointer;">
      <option value="default" style="background: var(--bg2); color: var(--text);"><?php esc_html_e( 'Default Mode', 'sedehoo' ); ?></option>
      <option value="dark" style="background: var(--bg2); color: var(--text);"><?php esc_html_e( 'Dark Mode', 'sedehoo' ); ?></option>
      <option value="light" style="background: var(--bg2); color: var(--text);"><?php esc_html_e( 'Light Mode', 'sedehoo' ); ?></option>
    </select>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var saved = localStorage.getItem('sedehoo_theme') || 'default';
        var select = document.getElementById('themeSelect');
        if(select) select.value = saved;
      });
    </script>
    <?php if ( is_user_logged_in() ) :
      $current_user = wp_get_current_user(); ?>
      <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="btn-outline" title="<?php esc_attr_e( 'Dashboard', 'sedehoo' ); ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?php echo esc_html( $current_user->display_name ?: __( 'Account', 'sedehoo' ) ); ?>
      </a>
    <?php else : ?>
      <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn-outline">
        <?php echo esc_html( sedehoo_get( 'btn_signin_text', __( 'Sign In', 'sedehoo' ) ) ); ?>
      </a>
      <a href="<?php echo esc_url( sedehoo_get( 'btn_cta_url', '#' ) ); ?>" class="btn-accent">
        <?php echo esc_html( sedehoo_get( 'btn_cta_text', __( 'Get Pro', 'sedehoo' ) ) ); ?>
      </a>
    <?php endif; ?>
  </div>

</header>

<!-- ===== MAIN WRAP ===== -->
<div id="main-wrap">
<main id="main-content" role="main">
