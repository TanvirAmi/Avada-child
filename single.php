<?php
/**
 * Template used for single posts and other post-types
 * that don't have a specific template.
 *
 * @package Avada
 * @subpackage Templates
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}
?>
<?php get_header(); ?>

<section id="content" <?php Avada()->layout->add_style( 'content_style' ); ?>>
	<?php if ( fusion_get_option( 'blog_pn_nav' ) ) : ?>
		<div class="single-navigation clearfix">
			<?php previous_post_link( '%link', esc_attr__( 'Previous', 'Avada' ) ); ?>
			<?php next_post_link( '%link', esc_attr__( 'Next', 'Avada' ) ); ?>
		</div>
	<?php endif; ?>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'post' ); ?>>
			<?php //if(get_field('post_layout', get_the_ID()) == 'layout-1'){
				/*if(get_field('second_feature_image', get_the_ID())){
					echo '<div class="second-feature-image"><img src="'.esc_url(get_field('second_feature_image', get_the_ID())).'" alt="Second feature image"/></div>';
				}
				if(get_field('shortcode', get_the_ID())){
					echo '<div class="shortcode">';
					echo do_shortcode(get_field('shortcode', get_the_ID()));
					echo '</div>';
				}*/

			//} ?>
			<?php //if(get_field('post_layout', get_the_ID()) == 'layout-2'){
				/*if(get_field('second_feature_image', get_the_ID())){
					echo '<div class="second-feature-image"><img src="'.esc_url(get_field('second_feature_image', get_the_ID())).'" alt="Second feature image"/></div>';
				}*/

			//} ?>
			<div class="post-content">
				<?php the_content(); ?>
				<?php fusion_link_pages(); ?>
				<div class="tags-link-wrap tagcloud">
	            <?php
		            if (has_tag()) {
		                the_tags('', '', '');
		            }
	            ?>
        		</div>
			</div>
			<?php avada_render_related_posts( get_post_type() ); // Render Related Posts. ?>
			
		</article>
		<?php avada_render_social_sharing(); ?>
		
	<?php endwhile; ?>
</section>
<?php do_action( 'avada_after_content' ); ?>
<?php get_footer(); ?>
