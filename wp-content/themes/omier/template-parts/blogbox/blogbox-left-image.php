<article class="themetechmount-box themetechmount-box-blog themetechmount-blogbox-format-<?php echo get_post_format() ?> <?php echo themetechmount_sanitize_html_classes(themetechmount_post_class()); ?> themetechmount-box-view-left-image themetechmount-blog-box-view-left-image">
	<div class="post-item">
		<div class="themetechmount-box-content">
			<div class="col-md-5 themetechmount-box-img-left">
				<div class="tm-featured-outer-wrapper tm-post-featured-outer-wrapper">
					<?php echo themetechmount_get_featured_media( '', 'themetechmount-img-blog-left' ); // Featured content ?>	
				</div>
			</div>
			<div class="themetechmount-box-content col-md-7">
					<div class="themetechmount-box-content-inner">
						<div class="entry-header">		
						<?php
							// Category list
							$categories_list = get_the_category_list( ' ' );
							if ( !empty($categories_list) ) { ?>
								<div class="tm-post-categories"><span class="tm-meta-line cat-links"><span class="screen-reader-text tm-hide"><?php echo esc_attr_x( 'Categories', 'Used before category names.', 'boldman' ); ?> </span><?php echo themetechmount_wp_kses($categories_list); ?></span></div>
						<?php } ?>
			
							<?php echo themetechmount_box_title(); ?>
						</div>			
						<div class="themetechmount-box-desc">
							<div class="themetechmount-box-desc-text"><?php echo themetechmount_blogbox_description(); ?></div>
						</div>					
					</div>
					<div class="tm-entry-footer">		
					<?php
					// Date
					$date_format =  get_option('date_format'); ?>
					<span class="themetechmount-meta-line posted-on">
						<span class="screen-reader-text themetechmount-hide"><?php echo esc_attr_x( 'Posted on', 'Used before publish date.', 'boldman' ); ?> </span>
						<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
						<time class="entry-date published" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date($date_format); ?></time>
						<time class="updated themetechmount-hide" datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo get_the_modified_date($date_format); ?></time>
						</a>
					</span>
					</div>
			</div>
		</div>
	</div>
</article>
