<article class="themetechmount-box themetechmount-box-blog themetechmount-blogbox-format-<?php echo get_post_format() ?> <?php echo themetechmount_sanitize_html_classes(themetechmount_post_class()); ?> themetechmount-box-view-content-overlay themetechmount-blog-box-view-content-overlay">
	<div class="post-item">
		<div class="tm-featured-outer-wrapper tm-post-featured-outer-wrapper">
			<?php echo themetechmount_get_featured_media( '', 'themetechmount-img-blog' ); // Featured content ?>
		</div>
		<div class="themetechmount-box-content themetechmount-overlay-wrapper">
			<span class="tm-blogbox-inner-overlay"> </span>
			<div class="tm-overlay-content-inner">
				<?php echo themetechmount_get_post_format_icon(); ?>
				<?php echo themetechmount_box_title(); ?>
			</div>
			
		</div>
	</div>
</article>
