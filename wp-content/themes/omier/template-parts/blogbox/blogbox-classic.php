<article <?php themetechmount_sanitize_html_classes( post_class( themetechmount_blog_classic_extra_class() )); ?>>
	<header class="entry-header">
		<?php if( !is_single() ) : ?>
			<?php if( 'quote' != get_post_format() && 'link' != get_post_format() ) : ?>
			
			<?php
				// Category list
				$categories_list = get_the_category_list( ', ' );
				if ( !empty($categories_list) ) { ?>
					<span class="tm-meta-line cat-links"><span class="screen-reader-text tm-hide"><?php echo esc_attr_x( 'Categories', 'Used before category names.', 'boldman' ); ?> </span><?php echo themetechmount_wp_kses($categories_list); ?></span>
			<?php } ?>
					
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<?php endif; ?>						
	</header><!-- .entry-header -->
			
	<div class="tm-featured-outer-wrapper tm-post-featured-outer-wrapper">
		 <?php echo themetechmount_get_featured_media( '', 'themetechmount-img-blog' ); // Featured content ?>
		<div class="tm-box-post-date">
			<?php themetechmount_entry_date(); ?>
		</div>
	</div>
	
	<div class="tm-blog-classic-box-content">
		<?php if( 'quote' != get_post_format() ) : ?>
		<div class="entry-content">
			<div class="themetechmount-box-desc-text">
				<?php the_content( '' ); ?>
			</div>
			<?php
			// pagination if any
			wp_link_pages( array(
				'before'      => '<div class="page-links">' . esc_attr__( 'Pages:', 'boldman' ),
				'after'       => '</div>',
				'link_before' => '<span class="page-number">',
				'link_after'  => '</span>',
			) );
			?>
			<div class="themetechmount-blogbox-desc-footer">
				<?php echo boldman_entry_meta('blogclassic');  // blog post meta details ?>
				<?php echo themetechmount_social_share_box('post'); ?>
				<div class="themetechmount-blogbox-footer-readmore">
					<?php echo themetechmount_blogbox_readmore(); ?>
				</div>
			</div>
			<div class="clear clr"></div>		
		</div><!-- .entry-content -->
		<?php endif; ?>
	</div>
	<?php endif; ?>
</article>