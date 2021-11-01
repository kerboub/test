<article class="themetechmount-box themetechmount-box-service themetechmount-box-view-top-image themetechmount-service-box-view-top-image <?php echo themetechmount_servicebox_class(); ?>">
	<div class="themetechmount-post-item">	
		<div class="themetechmount-post-item-inner">
			<?php echo themetechmount_get_featured_media( '', 'themetechmount-img-blog-top' ); // Featured content ?>
		</div>		
		<div class="themetechmount-box-bottom-content">		
			<?php echo themetechmount_box_title(); ?>
			<div class="themetechmount-box-desc">
				<?php if( has_excerpt() ){ ?>
				<div class="tm-short-desc">
					<?php $return  = nl2br( get_the_excerpt() );
					echo do_shortcode($return); ?>
				</div>
			<?php } ?>
			</div> 
			<div class="themetechmount-serviceboxbox-readmore">
				<?php echo themetechmount_servicebox_readmore_text(); ?>
			</div>
		</div>
	</div>
</article>