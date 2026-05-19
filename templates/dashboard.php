<?php
/**
 * Template Name: User Dashboard
 * Template Post Type: page
 */
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}
get_header();
$user     = wp_get_current_user();
$user_id  = $user->ID;
$col      = get_user_meta( $user_id, 'sedehoo_collection', true );
$dls      = get_user_meta( $user_id, 'sedehoo_downloads',  true );
$col      = is_array( $col ) ? $col : [];
$dls      = is_array( $dls ) ? $dls : [];
$tab      = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'collection';
?>

<div class="dashboard-grid">

  <!-- Sidebar -->
  <div>
    <div class="dashboard-sidebar-inner">
      <div class="dashboard-user">
        <div class="dashboard-avatar">
          <?php echo get_avatar( $user_id, 60 ); ?>
        </div>
        <div class="dashboard-name"><?php echo esc_html( $user->display_name ); ?></div>
        <div class="dashboard-email"><?php echo esc_html( $user->user_email ); ?></div>
      </div>
      <nav class="dashboard-nav">
        <a href="?tab=collection" class="<?php echo $tab === 'collection' ? 'active' : ''; ?>">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <?php esc_html_e( 'My Collection', 'sedehoo' ); ?>
          <span style="margin-left:auto;font-size:10px;background:var(--bg3);padding:2px 7px;border-radius:10px;"><?php echo count( $col ); ?></span>
        </a>
        <a href="?tab=downloads" class="<?php echo $tab === 'downloads' ? 'active' : ''; ?>">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          <?php esc_html_e( 'Downloads', 'sedehoo' ); ?>
          <span style="margin-left:auto;font-size:10px;background:var(--bg3);padding:2px 7px;border-radius:10px;"><?php echo count( $dls ); ?></span>
        </a>
        <a href="?tab=account" class="<?php echo $tab === 'account' ? 'active' : ''; ?>">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?php esc_html_e( 'Account', 'sedehoo' ); ?>
        </a>
        <a href="<?php echo esc_url( get_post_type_archive_link( 'sedehoo_photo' ) ); ?>">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <?php esc_html_e( 'Browse Photos', 'sedehoo' ); ?>
        </a>
        <a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" style="color:var(--danger)!important;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <?php esc_html_e( 'Log Out', 'sedehoo' ); ?>
        </a>
      </nav>
    </div>
  </div>

  <!-- Main content -->
  <div>

    <!-- Stats -->
    <div class="dashboard-stats">
      <div class="stat-card">
        <div class="stat-label"><?php esc_html_e( 'Collection', 'sedehoo' ); ?></div>
        <div class="stat-value"><?php echo count( $col ); ?><span class="stat-unit"><?php esc_html_e( 'photos', 'sedehoo' ); ?></span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label"><?php esc_html_e( 'Downloads', 'sedehoo' ); ?></div>
        <div class="stat-value"><?php echo count( $dls ); ?><span class="stat-unit"><?php esc_html_e( 'total', 'sedehoo' ); ?></span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label"><?php esc_html_e( 'Member Since', 'sedehoo' ); ?></div>
        <div class="stat-value" style="font-size:18px;"><?php echo esc_html( date( 'M Y', strtotime( $user->user_registered ) ) ); ?></div>
      </div>
    </div>

    <?php if ( $tab === 'collection' ) : ?>
      <!-- Collection -->
      <div class="section-title">
        <?php esc_html_e( 'My Collection', 'sedehoo' ); ?>
        <?php if ( ! empty( $col ) ) : ?>
          <a href="<?php echo esc_url( get_post_type_archive_link( 'sedehoo_photo' ) ); ?>"><?php esc_html_e( 'Browse More →', 'sedehoo' ); ?></a>
        <?php endif; ?>
      </div>

      <?php if ( ! empty( $col ) ) : ?>
        <div class="collection-grid">
          <?php foreach ( $col as $pid ) :
            $thumb = get_the_post_thumbnail_url( $pid, 'medium' );
            $title = get_the_title( $pid );
            $plink = get_permalink( $pid );
            if ( ! $title ) continue;
          ?>
            <div class="collection-item">
              <a href="<?php echo esc_url( $plink ); ?>">
                <?php if ( $thumb ) : ?>
                  <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                <?php else : ?>
                  <div style="width:100%;height:100%;background:var(--bg3);display:flex;align-items:center;justify-content:center;color:var(--muted);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                  </div>
                <?php endif; ?>
              </a>
              <div class="collection-item-overlay">
                <span class="collection-item-name"><?php echo esc_html( $title ); ?></span>
              </div>
              <button class="collection-remove"
                      onclick="sdRemoveFromCollection(<?php echo esc_attr( $pid ); ?>, this.closest('.collection-item'))"
                      title="<?php esc_attr_e( 'Remove', 'sedehoo' ); ?>">
                &times;
              </button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else : ?>
        <div class="empty-state">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <h3><?php esc_html_e( 'No saved photos yet', 'sedehoo' ); ?></h3>
          <p><?php esc_html_e( 'Click the heart icon on any photo to save it here.', 'sedehoo' ); ?></p>
          <a href="<?php echo esc_url( get_post_type_archive_link( 'sedehoo_photo' ) ); ?>" class="btn-accent" style="display:inline-flex;margin-top:16px;text-decoration:none;"><?php esc_html_e( 'Browse Photos', 'sedehoo' ); ?></a>
        </div>
      <?php endif; ?>

    <?php elseif ( $tab === 'downloads' ) : ?>
      <!-- Downloads -->
      <div class="section-title"><?php esc_html_e( 'Download History', 'sedehoo' ); ?></div>
      <?php if ( ! empty( $dls ) ) : ?>
        <table class="downloads-table">
          <thead>
            <tr>
              <th><?php esc_html_e( 'Photo', 'sedehoo' ); ?></th>
              <th><?php esc_html_e( 'Name', 'sedehoo' ); ?></th>
              <th><?php esc_html_e( 'Type', 'sedehoo' ); ?></th>
              <th><?php esc_html_e( 'Date', 'sedehoo' ); ?></th>
              <th><?php esc_html_e( 'Action', 'sedehoo' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ( $dls as $dl ) : ?>
              <tr>
                <td>
                  <?php if ( $dl['thumb'] ) : ?>
                    <img src="<?php echo esc_url( $dl['thumb'] ); ?>" class="dl-thumb" alt="">
                  <?php endif; ?>
                </td>
                <td><a href="<?php echo esc_url( get_permalink( $dl['photo_id'] ) ); ?>"><?php echo esc_html( $dl['title'] ); ?></a></td>
                <td><span class="dl-status <?php echo esc_attr( $dl['type'] ); ?>"><?php echo esc_html( ucfirst( $dl['type'] ) ); ?></span></td>
                <td><?php echo esc_html( date_i18n( get_option( 'date_format' ), $dl['date'] ) ); ?></td>
                <td>
                  <button class="filter-btn" onclick="sdDownloadPhoto(<?php echo esc_attr( $dl['photo_id'] ); ?>)" style="padding:5px 12px;font-size:11px;">
                    <?php esc_html_e( 'Re-download', 'sedehoo' ); ?>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else : ?>
        <div class="empty-state">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/></svg>
          <h3><?php esc_html_e( 'No downloads yet', 'sedehoo' ); ?></h3>
          <p><?php esc_html_e( 'Photos you download will appear here.', 'sedehoo' ); ?></p>
        </div>
      <?php endif; ?>

    <?php elseif ( $tab === 'account' ) : ?>
      <!-- Account settings -->
      <div class="section-title"><?php esc_html_e( 'Account Settings', 'sedehoo' ); ?></div>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:24px;">
        <p style="margin-bottom:14px;font-size:13px;color:var(--muted);"><?php esc_html_e( 'To update your account details, use the WordPress profile page.', 'sedehoo' ); ?></p>
        <a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>" class="btn-outline" style="display:inline-flex;text-decoration:none;">
          <?php esc_html_e( 'Edit Profile', 'sedehoo' ); ?>
        </a>
      </div>
    <?php endif; ?>

  </div>
</div>

<!-- Checkout modal for re-downloads -->
<div id="checkoutModal" class="modal-backdrop" style="display:none;" onclick="sdCloseModal('checkoutModal')">
  <div class="modal-box" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="sdCloseModal('checkoutModal')">&times;</button>
    <div class="modal-title"><?php esc_html_e( 'Purchase Required', 'sedehoo' ); ?></div>
    <div class="checkout-product-preview" id="checkoutPreview">
      <img id="checkoutThumb" src="" alt="">
      <div><div class="checkout-product-name" id="checkoutTitle"></div><div class="checkout-product-price" id="checkoutPrice"></div></div>
    </div>
    <a id="checkoutDirectBtn" href="#" class="btn-download" style="display:flex;align-items:center;justify-content:center;gap:8px;text-decoration:none;"><?php esc_html_e( 'Buy Now', 'sedehoo' ); ?></a>
  </div>
</div>

<?php get_footer(); ?>
