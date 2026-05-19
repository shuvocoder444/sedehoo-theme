<?php
/**
 * Template: Photo Category Archive
 */
get_header();

$term = get_queried_object();
$term_name = $term ? $term->name : __( 'All Photos', 'sedehoo' );
$term_desc = $term ? $term->description : '';
?>

<div class="archive-header">
  <h1><?php echo esc_html( $term_name ); ?></h1>
  <?php if ( $term_desc ) : ?>
    <p><?php echo esc_html( $term_desc ); ?></p>
  <?php endif; ?>
</div>

<!-- Filter bar: sub-categories or tags -->
<?php
$sub_cats = $term ? get_terms( [
  'taxonomy' => 'photo_category',
  'parent'   => $term->term_id,
  'hide_empty' => true,
] ) : [];
?>
<?php if ( ! is_wp_error( $sub_cats ) && ! empty( $sub_cats ) ) : ?>
  <div class="filter-bar">
    <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="filter-btn active"><?php esc_html_e( 'All', 'sedehoo' ); ?></a>
    <?php foreach ( $sub_cats as $sub ) : ?>
      <a href="<?php echo esc_url( get_term_link( $sub ) ); ?>" class="filter-btn"><?php echo esc_html( $sub->name ); ?></a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<!-- Sort bar -->
<div class="filter-bar" style="justify-content:space-between;margin-bottom:20px;">
  <div style="display:flex;gap:8px;align-items:center;">
    <span style="font-size:12px;color:var(--muted);">
      <?php printf( esc_html__( '%d photos found', 'sedehoo' ), $wp_query->found_posts ); ?>
    </span>
  </div>
  <div style="display:flex;gap:8px;">
    <button class="filter-btn active" onclick="sdSetLayout('masonry',this)"><?php esc_html_e( 'Masonry', 'sedehoo' ); ?></button>
    <button class="filter-btn" onclick="sdSetLayout('grid',this)"><?php esc_html_e( 'Grid', 'sedehoo' ); ?></button>
  </div>
</div>

<!-- Photo Grid -->
<?php if ( have_posts() ) : ?>
  <div class="photo-grid" id="photoGrid">
    <?php while ( have_posts() ) : the_post();
      $photo_id  = get_the_ID();
      $is_free   = sedehoo_photo_is_free( $photo_id );
      $thumb_url = get_the_post_thumbnail_url( $photo_id, 'medium_large' );
    ?>
      <a href="<?php the_permalink(); ?>" class="photo-grid-item" data-id="<?php echo esc_attr( $photo_id ); ?>">
        <?php if ( $thumb_url ) : ?>
          <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php else : ?>
          <div style="aspect-ratio:4/3;background:var(--bg3);display:flex;align-items:center;justify-content:center;color:var(--muted);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          </div>
        <?php endif; ?>

        <!-- Hover overlay -->
        <div class="photo-grid-overlay">
          <div class="photo-grid-overlay-title"><?php the_title(); ?></div>
          <div class="photo-grid-overlay-actions">
            <button class="overlay-icon-btn"
                    onclick="event.preventDefault();sdToggleCollection(<?php echo esc_attr( $photo_id ); ?>,this)"
                    title="<?php esc_attr_e( 'Add to Collection', 'sedehoo' ); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
              <span class="tooltip"><?php esc_html_e( 'Add to Collection', 'sedehoo' ); ?></span>
            </button>
            <button class="overlay-icon-btn"
                    onclick="event.preventDefault();sdDownloadPhoto(<?php echo esc_attr( $photo_id ); ?>)"
                    title="<?php esc_attr_e( 'Download', 'sedehoo' ); ?>">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
              <span class="tooltip"><?php esc_html_e( 'Download', 'sedehoo' ); ?></span>
            </button>
            <?php if ( ! $is_free ) : ?>
              <span class="overlay-icon-btn" style="cursor:default;background:rgba(255,200,50,.2);border:1px solid rgba(255,200,50,.3);color:#ffd43b;font-size:9px;font-weight:700;width:auto;padding:0 8px;">
                <?php esc_html_e( 'PRO', 'sedehoo' ); ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      </a>
    <?php endwhile; ?>
  </div>

  <!-- Pagination -->
  <div style="margin-top:40px;display:flex;justify-content:center;">
    <?php
    echo paginate_links( [
      'prev_text' => '&larr;',
      'next_text' => '&rarr;',
      'type'      => 'list',
      'before_page_number' => '<span>',
      'after_page_number'  => '</span>',
    ] );
    ?>
  </div>

<?php else : ?>
  <div class="empty-state">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
    <h3><?php esc_html_e( 'No photos found', 'sedehoo' ); ?></h3>
    <p><?php esc_html_e( 'Try a different category or check back later.', 'sedehoo' ); ?></p>
  </div>
<?php endif; ?>

<!-- Checkout Modal (reuse from single photo) -->
<div id="checkoutModal" class="modal-backdrop" style="display:none;" onclick="sdCloseModal('checkoutModal')">
  <div class="modal-box" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="sdCloseModal('checkoutModal')">&times;</button>
    <div class="modal-title"><?php esc_html_e( 'Complete Purchase', 'sedehoo' ); ?></div>
    <div class="modal-subtitle"><?php esc_html_e( 'Full-resolution download after payment.', 'sedehoo' ); ?></div>
    <div class="checkout-product-preview" id="checkoutPreview">
      <img id="checkoutThumb" src="" alt="" style="width:60px;height:60px;border-radius:8px;object-fit:cover;">
      <div>
        <div class="checkout-product-name" id="checkoutTitle"></div>
        <div class="checkout-product-price" id="checkoutPrice"></div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <a id="checkoutDirectBtn" href="#" class="btn-download" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l5 5L19 7"/></svg>
        <?php esc_html_e( 'Buy Now', 'sedehoo' ); ?>
      </a>
      <a id="checkoutCartBtn" href="#" class="btn-collection" style="text-decoration:none;display:flex;align-items:center;justify-content:center;gap:8px;">
        <?php esc_html_e( 'Add to Cart', 'sedehoo' ); ?>
      </a>
    </div>
  </div>
</div>

<script>
function sdSetLayout(type, btn) {
  var grid = document.getElementById('photoGrid');
  document.querySelectorAll('.filter-bar .filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  if (type === 'grid') {
    grid.style.columns = 'none';
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(220px, 1fr))';
    grid.style.gap = '14px';
  } else {
    grid.style.display = '';
    grid.style.gridTemplateColumns = '';
    grid.style.columns = '4 220px';
  }
}
</script>

<?php get_footer(); ?>
