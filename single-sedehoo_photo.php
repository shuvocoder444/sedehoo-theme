<?php
/**
 * Template: Single Photo Page
 */
get_header();

while ( have_posts() ) : the_post();

$photo_id     = get_the_ID();
$thumb_url    = get_the_post_thumbnail_url( $photo_id, 'large' );
$full_url     = get_the_post_thumbnail_url( $photo_id, 'full' );
$photographer = get_post_meta( $photo_id, '_photographer_name', true );
$resolution   = get_post_meta( $photo_id, '_photo_resolution', true );
$file_size    = get_post_meta( $photo_id, '_file_size_mb', true );
$product_id   = get_post_meta( $photo_id, '_wc_product_id', true );
$is_free      = sedehoo_photo_is_free( $photo_id );
$in_collection = is_user_logged_in() && sedehoo_is_in_collection( $photo_id );

$categories = get_the_terms( $photo_id, 'photo_category' );
$tags       = get_the_terms( $photo_id, 'photo_tag' );

$price_display = '';
if ( ! $is_free && $product_id && function_exists( 'wc_get_product' ) ) {
    $product = wc_get_product( $product_id );
    if ( $product ) $price_display = $product->get_price_html();
}
?>

<div class="photo-single-wrap">

  <!-- Left: Image + Discover Similar -->
  <div>

    <!-- Main Image -->
    <div class="photo-main-image" id="photoMainImage">
      <?php if ( $thumb_url ) : ?>
        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" id="photoMainImg">
      <?php else : ?>
        <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
      <?php endif; ?>

      <!-- Hover action icons on image -->
      <div class="photo-image-actions">
        <!-- Discover Similar -->
        <button class="photo-action-btn" onclick="sdDiscoverSimilar(<?php echo esc_attr( $photo_id ); ?>)" title="<?php esc_attr_e( 'Discover Similar', 'sedehoo' ); ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <span class="tooltip"><?php esc_html_e( 'Discover Similar', 'sedehoo' ); ?></span>
        </button>

        <!-- Add to Collection -->
        <button class="photo-action-btn <?php echo $in_collection ? 'active' : ''; ?>"
                id="imgCollectBtn"
                onclick="sdToggleCollection(<?php echo esc_attr( $photo_id ); ?>, this)"
                title="<?php esc_attr_e( 'Add to Collection', 'sedehoo' ); ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          <span class="tooltip"><?php esc_html_e( 'Add to Collection', 'sedehoo' ); ?></span>
        </button>

        <!-- Download -->
        <button class="photo-action-btn"
                onclick="sdDownloadPhoto(<?php echo esc_attr( $photo_id ); ?>)"
                title="<?php esc_html_e( 'Download', 'sedehoo' ); ?>">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          <span class="tooltip"><?php esc_html_e( 'Download', 'sedehoo' ); ?></span>
        </button>
      </div>
    </div><!-- /.photo-main-image -->

    <!-- Discover Similar Panel (hidden by default) -->
    <div class="discover-similar-panel" id="similarPanel">
      <div class="similar-panel-header">
        <h3><?php esc_html_e( 'Discover Similar', 'sedehoo' ); ?></h3>
        <button onclick="sdCloseSimilar()" class="btn-outline" style="padding:6px 12px;font-size:12px;">
          <?php esc_html_e( 'Close', 'sedehoo' ); ?>
        </button>
      </div>
      <div class="similar-grid" id="similarGrid">
        <!-- Filled by JS -->
        <?php for ( $i = 0; $i < 6; $i++ ) : ?>
          <div class="similar-item skeleton" style="aspect-ratio:1;border:none;"></div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- Photo Description -->
    <?php if ( get_the_content() ) : ?>
      <div class="entry-content" style="margin-top:28px;">
        <?php the_content(); ?>
      </div>
    <?php endif; ?>

  </div><!-- /left col -->

  <!-- Right: Info Panel -->
  <div class="photo-info-panel">

    <!-- Title -->
    <div class="photo-title"><?php the_title(); ?></div>

    <!-- Price Badge -->
    <?php if ( $is_free ) : ?>
      <div class="photo-price-badge">
        <div>
          <div class="photo-price-label"><?php esc_html_e( 'Price', 'sedehoo' ); ?></div>
          <div class="photo-price-free"><?php esc_html_e( 'Free', 'sedehoo' ); ?></div>
        </div>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7fff5f" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
      </div>
    <?php else : ?>
      <div class="photo-price-badge">
        <div>
          <div class="photo-price-label"><?php esc_html_e( 'Price', 'sedehoo' ); ?></div>
          <div class="photo-price-paid"><?php echo $price_display ?: esc_html__( 'Premium', 'sedehoo' ); ?></div>
        </div>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7fff5f" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      </div>
    <?php endif; ?>

    <!-- Meta Info -->
    <div class="photo-meta">
      <?php if ( $photographer ) : ?>
        <div class="photo-meta-row">
          <span class="photo-meta-label"><?php esc_html_e( 'Photographer', 'sedehoo' ); ?></span>
          <span class="photo-meta-value"><?php echo esc_html( $photographer ); ?></span>
        </div>
      <?php endif; ?>
      <?php if ( $resolution ) : ?>
        <div class="photo-meta-row">
          <span class="photo-meta-label"><?php esc_html_e( 'Resolution', 'sedehoo' ); ?></span>
          <span class="photo-meta-value"><?php echo esc_html( $resolution ); ?></span>
        </div>
      <?php endif; ?>
      <?php if ( $file_size ) : ?>
        <div class="photo-meta-row">
          <span class="photo-meta-label"><?php esc_html_e( 'File Size', 'sedehoo' ); ?></span>
          <span class="photo-meta-value"><?php echo esc_html( $file_size ); ?> MB</span>
        </div>
      <?php endif; ?>
      <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
        <div class="photo-meta-row">
          <span class="photo-meta-label"><?php esc_html_e( 'Category', 'sedehoo' ); ?></span>
          <span class="photo-meta-value">
            <?php echo implode( ', ', array_map( function( $t ) {
              return '<a href="' . esc_url( get_term_link( $t ) ) . '" style="color:var(--accent)">' . esc_html( $t->name ) . '</a>';
            }, $categories ) ); ?>
          </span>
        </div>
      <?php endif; ?>
      <div class="photo-meta-row">
        <span class="photo-meta-label"><?php esc_html_e( 'Published', 'sedehoo' ); ?></span>
        <span class="photo-meta-value"><?php echo get_the_date( 'M j, Y' ); ?></span>
      </div>
    </div>

    <!-- Tags -->
    <?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
      <div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px;"><?php esc_html_e( 'Tags', 'sedehoo' ); ?></div>
        <div class="photo-tags">
          <?php foreach ( $tags as $tag ) : ?>
            <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="photo-tag"><?php echo esc_html( $tag->name ); ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="photo-actions-row">
      <!-- Download button -->
      <button class="btn-download" id="photoDownloadBtn" onclick="sdDownloadPhoto(<?php echo esc_attr( $photo_id ); ?>)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        <?php echo $is_free ? esc_html__( 'Download Free', 'sedehoo' ) : esc_html__( 'Purchase & Download', 'sedehoo' ); ?>
      </button>

      <!-- Add to Collection -->
      <button class="btn-collection <?php echo $in_collection ? 'in-collection' : ''; ?>"
              id="photoCollectBtn"
              onclick="sdToggleCollection(<?php echo esc_attr( $photo_id ); ?>, this)">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="<?php echo $in_collection ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2" id="collectHeart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <span id="collectBtnText"><?php echo $in_collection ? esc_html__( 'Saved to Collection', 'sedehoo' ) : esc_html__( 'Add to Collection', 'sedehoo' ); ?></span>
      </button>

      <!-- Discover Similar -->
      <button class="btn-collection" onclick="sdDiscoverSimilar(<?php echo esc_attr( $photo_id ); ?>)" style="justify-content:center;gap:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <?php esc_html_e( 'Discover Similar', 'sedehoo' ); ?>
      </button>
    </div>

    <!-- License note -->
    <p style="font-size:11.5px;color:var(--muted);line-height:1.55;border-top:1px solid var(--border);padding-top:14px;">
      <?php if ( $is_free ) : ?>
        <?php esc_html_e( 'Free for personal and commercial use. Attribution appreciated but not required.', 'sedehoo' ); ?>
      <?php else : ?>
        <?php esc_html_e( 'Commercial license included. See license page for full terms.', 'sedehoo' ); ?>
      <?php endif; ?>
    </p>

  </div><!-- /.photo-info-panel -->

</div><!-- /.photo-single-wrap -->

<!-- WooCommerce Checkout Popup Modal -->
<div id="checkoutModal" class="modal-backdrop" style="display:none;" onclick="sdCloseModal('checkoutModal')">
  <div class="modal-box" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="sdCloseModal('checkoutModal')" aria-label="<?php esc_attr_e('Close', 'sedehoo'); ?>">&times;</button>
    <div class="modal-title"><?php esc_html_e( 'Complete Purchase', 'sedehoo' ); ?></div>
    <div class="modal-subtitle"><?php esc_html_e( 'You\'ll get full-resolution download immediately after payment.', 'sedehoo' ); ?></div>
    <div class="checkout-product-preview" id="checkoutPreview">
      <img id="checkoutThumb" src="" alt="" style="width:60px;height:60px;border-radius:8px;object-fit:cover;">
      <div>
        <div class="checkout-product-name" id="checkoutTitle"></div>
        <div class="checkout-product-price" id="checkoutPrice"></div>
      </div>
    </div>
    <div id="checkoutActions" style="display:flex;flex-direction:column;gap:10px;">
      <a id="checkoutDirectBtn" href="#" class="btn-download" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L19 7"/></svg>
        <?php esc_html_e( 'Buy Now', 'sedehoo' ); ?>
      </a>
      <a id="checkoutCartBtn" href="#" class="btn-collection" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <?php esc_html_e( 'Add to Cart', 'sedehoo' ); ?>
      </a>
    </div>
  </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
