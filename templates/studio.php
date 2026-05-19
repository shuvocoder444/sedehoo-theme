<?php
/**
 * Template Name: Studio Page
 * Template Post Type: page
 */
get_header(); ?>

<div class="studio-hero">
  <h1><?php the_title_parts( '<span class="accent">', '</span> Studio', '' ); ?></h1>
  <?php if ( get_the_content() ) : ?>
    <p><?php echo esc_html( wp_trim_words( get_the_content(), 30 ) ); ?></p>
  <?php else : ?>
    <p><?php esc_html_e( 'Upload, organize, and manage your photos. Edit metadata and prepare for publishing.', 'sedehoo' ); ?></p>
  <?php endif; ?>
  <?php if ( is_user_logged_in() ) : ?>
    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=sedehoo_photo' ) ); ?>" class="btn-accent" style="display:inline-flex;gap:8px;align-items:center;text-decoration:none;margin-top:8px;">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      <?php esc_html_e( 'Upload New Photo', 'sedehoo' ); ?>
    </a>
  <?php endif; ?>
</div>

<!-- Studio Tools Grid -->
<div class="studio-tools-grid">
  <?php
  $tools = [
    [ 'name' => __( 'Upload Photos',      'sedehoo' ), 'desc' => __( 'Upload new photos to your portfolio. Supports JPG, PNG, WebP up to 50MB.',       'sedehoo' ), 'icon' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>', 'badge' => '', 'url' => admin_url( 'post-new.php?post_type=sedehoo_photo' ) ],
    [ 'name' => __( 'Manage Photos',      'sedehoo' ), 'desc' => __( 'Edit titles, descriptions, categories, tags, and pricing for your photos.',          'sedehoo' ), 'icon' => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/>', 'badge' => '', 'url' => admin_url( 'edit.php?post_type=sedehoo_photo' ) ],
    [ 'name' => __( 'Categories',         'sedehoo' ), 'desc' => __( 'Organize photos into categories. Add thumbnails and descriptions.',                  'sedehoo' ), 'icon' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>', 'badge' => '', 'url' => admin_url( 'edit-tags.php?taxonomy=photo_category&post_type=sedehoo_photo' ) ],
    [ 'name' => __( 'Add Pricing',        'sedehoo' ), 'desc' => __( 'Set WooCommerce products for premium photos. Link product ID in photo settings.',    'sedehoo' ), 'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>', 'badge' => 'Pro', 'url' => admin_url( 'post-new.php?post_type=product' ) ],
    [ 'name' => __( 'Media Library',      'sedehoo' ), 'desc' => __( 'Browse and manage all uploaded media files.',                                          'sedehoo' ), 'icon' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>', 'badge' => '', 'url' => admin_url( 'upload.php' ) ],
    [ 'name' => __( 'Settings',           'sedehoo' ), 'desc' => __( 'Configure theme colors, sidebar, header and footer settings.',                         'sedehoo' ), 'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>', 'badge' => '', 'url' => admin_url( 'customize.php' ) ],
  ];
  foreach ( $tools as $tool ) : ?>
    <a href="<?php echo esc_url( $tool['url'] ); ?>" class="studio-tool-card" style="text-decoration:none;color:inherit;">
      <?php if ( $tool['badge'] ) : ?>
        <span class="studio-tool-badge badge-pro"><?php echo esc_html( $tool['badge'] ); ?></span>
      <?php endif; ?>
      <div class="studio-tool-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7fff5f" stroke-width="1.8"><?php echo $tool['icon']; ?></svg>
      </div>
      <div class="studio-tool-name"><?php echo esc_html( $tool['name'] ); ?></div>
      <div class="studio-tool-desc"><?php echo esc_html( $tool['desc'] ); ?></div>
    </a>
  <?php endforeach; ?>
</div>

<!-- Upload Drop Zone -->
<?php if ( is_user_logged_in() ) : ?>
  <div class="upload-area" id="uploadArea" onclick="window.location.href='<?php echo esc_url( admin_url( 'post-new.php?post_type=sedehoo_photo' ) ); ?>'">
    <div class="upload-icon">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
    </div>
    <div class="upload-title"><?php esc_html_e( 'Upload Photos', 'sedehoo' ); ?></div>
    <div class="upload-desc"><?php esc_html_e( 'Click to upload or drag and drop — JPG, PNG, WebP up to 50MB', 'sedehoo' ); ?></div>
  </div>
<?php else : ?>
  <div class="empty-state" style="background:var(--bg2);border:1px solid var(--border);border-radius:14px;padding:48px;">
    <h3><?php esc_html_e( 'Log in to access Studio', 'sedehoo' ); ?></h3>
    <p><?php esc_html_e( 'Create an account or log in to upload and manage your photos.', 'sedehoo' ); ?></p>
    <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>" class="btn-accent" style="display:inline-flex;margin-top:16px;text-decoration:none;"><?php esc_html_e( 'Log In', 'sedehoo' ); ?></a>
  </div>
<?php endif; ?>

<?php get_footer(); ?>
