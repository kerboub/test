<article class="themetechmount-box themetechmount-box-team themetechmount-teambox-view-style3">
	<div class="themetechmount-post-item">
		<div class="themetechmount-content-inner">
			<div class="themetechmount-team-image-box">
				<?php echo themetechmount_wp_kses(themetechmount_featured_image('themetechmount-img-team-member')); ?>
			</div>
			 <div class="themetechmount-overlay">
				<div class="themetechmount-box-social-links"><?php echo themetechmount_box_team_social_links(); ?></div>
				<div class="themetechmount-box-content">			
					<?php echo themetechmount_box_title(); ?>	
					<div class="themetechmount-team-position"><?php echo themetechmount_get_meta( 'themetechmount_team_member_details', 'tm_team_info' , 'team_details_line_position' ); ?></div>										
				</div>				
			</div>	
		</div>	
	</div>
</article>
 