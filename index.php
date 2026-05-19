<?php get_header(); ?>

<div class="page-title-bar">
  <h1><?php echo esc_html( get_bloginfo( 'name' ) ); ?> <span class="accent"><?php esc_html_e( 'Photos', 'sedehoo' ); ?></span></h1>
  <a href="<?php echo esc_url( get_post_type_archive_link( 'sedehoo_photo' ) ); ?>" class="btn-outline">
    <?php esc_html_e( 'Browse All', 'sedehoo' ); ?>
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
  </a>
</div>

<!-- Search -->
<div class="search-wrap">
  <div class="search-box">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7a7f8e" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" id="catSearch" placeholder="<?php esc_attr_e( 'Search categories…', 'sedehoo' ); ?>" oninput="sdFilterCats(this.value)" aria-label="<?php esc_attr_e( 'Search categories', 'sedehoo' ); ?>">
    <kbd>Ctrl+K</kbd>
  </div>
</div>

<!-- Photo Category Grid -->
<?php
$categories = get_terms( [
  'taxonomy'   => 'photo_category',
  'hide_empty' => false,
  'number'     => 0,
  'orderby'    => 'count',
  'order'      => 'DESC',
] );

if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) : ?>

  <div class="category-grid" id="catGrid">
    <?php foreach ( $categories as $cat ) :
      $thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
      $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'medium_large' ) : '';
    ?>
      <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="cat-card" data-name="<?php echo esc_attr( strtolower( $cat->name ) ); ?>">
        <div class="cat-card-thumb">
          <?php if ( $thumb_url ) : ?>
            <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $cat->name ); ?>" loading="lazy">
          <?php else : ?>
            <div class="cat-card-thumb-placeholder">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
          <?php endif; ?>
          <?php if ( $cat->count > 0 ) : ?>
            <span class="cat-card-badge"><?php printf( esc_html__( '%d photos', 'sedehoo' ), $cat->count ); ?></span>
          <?php endif; ?>
        </div>
        <div class="cat-card-body">
          <div class="cat-card-title"><?php echo esc_html( $cat->name ); ?></div>
          <?php if ( $cat->description ) : ?>
            <div class="cat-card-count"><?php echo esc_html( wp_trim_words( $cat->description, 12 ) ); ?></div>
          <?php else : ?>
            <div class="cat-card-count"><?php printf( esc_html__( '%d photos', 'sedehoo' ), $cat->count ); ?></div>
          <?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>

<?php else : ?>
  <!-- Fallback: show recent photos if no categories -->
  <?php
  $photos = new WP_Query( [ 'post_type' => 'sedehoo_photo', 'posts_per_page' => 12, 'orderby' => 'date', 'order' => 'DESC' ] );
  if ( $photos->have_posts() ) : ?>
    <div class="photo-grid" id="catGrid">
      <?php while ( $photos->have_posts() ) : $photos->the_post(); ?>
        <a href="<?php the_permalink(); ?>" class="photo-grid-item" data-name="<?php echo esc_attr( strtolower( get_the_title() ) ); ?>">
          <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy' ] ); ?>
          <div class="photo-grid-overlay">
            <div class="photo-grid-overlay-title"><?php the_title(); ?></div>
          </div>
        </a>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  <?php else : ?>
    <div class="empty-state">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
      <h3><?php esc_html_e( 'No photos yet', 'sedehoo' ); ?></h3>
      <p><?php esc_html_e( 'Add photo categories and photos from the WordPress admin.', 'sedehoo' ); ?></p>
    </div>
  <?php endif; ?>
<?php endif; ?>

<script>
function sdFilterCats(q) {
  q = q.toLowerCase();
  document.querySelectorAll('#catGrid [data-name]').forEach(function(el) {
    el.style.display = el.dataset.name.indexOf(q) !== -1 ? '' : 'none';
  });
}
</script>

<?php get_footer(); ?>
