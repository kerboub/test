<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Boldman
 * @since Boldman 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( themetechmount_sanitize_html_classes(themetechmount_post_class()) ); ?>>
		
	<div class="tm-featured-outer-wrapper tm-post-featured-outer-wrapper">
		<?php echo themetechmount_get_featured_media(); // Featured content ?>
	</div>
		
	<?php if( 'quote' != get_post_format() && 'link' != get_post_format() ) : ?>
		<?php echo boldman_entry_meta('blogclassic');  // blog post meta details ?>
	<?php endif; ?>
		
	<header class="entry-header">
		<?php if( !is_single() ) : ?>
			<?php if( 'aside' != get_post_format() && 'quote' != get_post_format() && 'link' != get_post_format() ) : ?>
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<?php endif; ?>
		<?php endif; ?>
	</header><!-- .entry-header -->
		
	<?php if( 'quote' != get_post_format() ) : ?>
		<div class="entry-content">
			<?php
			the_content( sprintf(
				esc_attr__( 'Read More %s', 'boldman' ),
				the_title( '<span class="screen-reader-text">', '</span>', false )
			) );
			
			// pagination if any
			wp_link_pages( array(
				'before'      => '<div class="page-links">' . esc_attr__( 'Pages:', 'boldman' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			) );
			?>
		</div><!-- .entry-content -->
	<?php endif; ?>
	
	<?php
	// Author bio.
	if ( is_single() && get_the_author_meta( 'description' ) ) :
		get_template_part( 'template-parts/author-bio', 'customized' );
	endif;
	?>

	<?php
	// Edit link
	if( is_singular() ){
		edit_post_link( esc_attr__( 'Edit', 'boldman' ), '<div class="edit-link-wrapper clearfix"> <span class="edit-link">', '</span></div>' );
	}
	?>

</article><!-- #post-## -->
