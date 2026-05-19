<?php
/**
 * Template Name: Video Page
 * Template Post Type: page
 */
get_header();

$videos = new WP_Query( [
  'post_type'      => 'sedehoo_video',
  'posts_per_page' => 12,
  'orderby'        => 'date',
  'order'          => 'DESC',
] );

$featured = $videos->posts[0] ?? null;
?>

<div class="page-title-bar">
  <h1><?php esc_html_e( 'Videos', 'sedehoo' ); ?></h1>
</div>

<!-- Featured Video -->
<?php if ( $featured ) :
  $video_url = get_post_meta( $featured->ID, '_video_url', true );
  $thumb     = get_the_post_thumbnail_url( $featured->ID, 'large' );
?>
  <div class="video-featured" id="featuredVideoWrap">
    <?php if ( $video_url ) : ?>
      <div class="video-featured-overlay" id="featuredOverlay" onclick="sdPlayVideo('featuredVideoWrap', '<?php echo esc_url( $video_url ); ?>', '<?php echo esc_url( $thumb ); ?>')">
        <?php if ( $thumb ) : ?>
          <img src="<?php echo esc_url( $thumb ); ?>" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;">
        <?php endif; ?>
        <div class="video-play-btn" style="position:relative;z-index:2;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="#0a0f06"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        </div>
        <div style="position:absolute;bottom:20px;left:20px;z-index:2;">
          <div style="font-size:18px;font-weight:700;margin-bottom:4px;"><?php echo esc_html( get_the_title( $featured->ID ) ); ?></div>
          <div style="font-size:13px;color:rgba(255,255,255,.7)"><?php echo esc_html( date( 'M j, Y', strtotime( $featured->post_date ) ) ); ?></div>
        </div>
      </div>
    <?php else : ?>
      <?php if ( $thumb ) : ?>
        <img src="<?php echo esc_url( $thumb ); ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<!-- Video Grid -->
<?php if ( $videos->have_posts() ) : ?>
  <div class="video-grid" style="margin-top:28px;">
    <?php $count = 0; while ( $videos->have_posts() ) : $videos->the_post();
      $count++;
      if ( $count === 1 && $featured ) continue; // skip featured already shown
      $video_url = get_post_meta( get_the_ID(), '_video_url', true );
      $duration  = get_post_meta( get_the_ID(), '_video_duration', true );
      $thumb     = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
    ?>
      <div class="video-card" onclick="<?php echo $video_url ? "sdPlayVideoModal('" . esc_url( $video_url ) . "','" . esc_url( $thumb ) . "')" : "window.location.href='" . get_permalink() . "'"; ?>">
        <div class="video-card-thumb">
          <?php if ( $thumb ) : ?>
            <img src="<?php echo esc_url( $thumb ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
          <?php else : ?>
            <div style="width:100%;height:100%;background:var(--bg3);display:flex;align-items:center;justify-content:center;">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#7a7f8e" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
            </div>
          <?php endif; ?>
          <?php if ( $duration ) : ?>
            <span class="video-card-duration"><?php echo esc_html( $duration ); ?></span>
          <?php endif; ?>
          <div class="video-card-play">
            <div class="video-card-play-circle">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="#0a0f06"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </div>
          </div>
        </div>
        <div class="video-card-body">
          <div class="video-card-title"><?php the_title(); ?></div>
          <div class="video-card-meta">
            <span><?php echo get_the_date( 'M j, Y' ); ?></span>
          </div>
        </div>
      </div>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
<?php else : ?>
  <div class="empty-state">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
    <h3><?php esc_html_e( 'No videos yet', 'sedehoo' ); ?></h3>
    <p><?php esc_html_e( 'Add videos from the WordPress admin using the Video post type.', 'sedehoo' ); ?></p>
  </div>
<?php endif; ?>

<!-- Video Lightbox Modal -->
<div id="videoModal" class="modal-backdrop" style="display:none;" onclick="sdCloseModal('videoModal')">
  <div class="modal-box" style="max-width:800px;padding:0;overflow:hidden;background:#000;" onclick="event.stopPropagation()">
    <button class="modal-close" onclick="sdCloseModal('videoModal')" style="z-index:2;">&times;</button>
    <div id="videoModalContent" style="aspect-ratio:16/9;"></div>
  </div>
</div>

<script>
function sdPlayVideo(wrapId, url, thumb) {
  var wrap = document.getElementById(wrapId);
  var overlay = document.getElementById('featuredOverlay');
  if (overlay) overlay.style.display = 'none';
  var iframe = document.createElement('iframe');
  iframe.src = url + (url.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1';
  iframe.style.cssText = 'width:100%;height:100%;border:none;position:absolute;inset:0;';
  iframe.allow = 'autoplay; fullscreen';
  iframe.allowFullscreen = true;
  wrap.appendChild(iframe);
}
function sdPlayVideoModal(url, thumb) {
  var modal = document.getElementById('videoModal');
  var content = document.getElementById('videoModalContent');
  content.innerHTML = '<iframe src="' + url + (url.indexOf('?') > -1 ? '&' : '?') + 'autoplay=1" style="width:100%;height:100%;border:none;" allow="autoplay;fullscreen" allowfullscreen></iframe>';
  modal.style.display = 'flex';
}
</script>

<?php get_footer(); ?>
