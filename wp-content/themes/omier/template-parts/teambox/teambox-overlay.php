<article class="themetechmount-box themetechmount-box-team themetechmount-teambox-view-overlay">
	<div class="themetechmount-post-item">
		<div class="themetechmount-team-image-box">
			<?php echo themetechmount_wp_kses(themetechmount_featured_image('themetechmount-img-team-member')); ?>	
		</div>	
		<div class="themetechmount-box-content">
			<?php echo themetechmount_box_title(); ?>
			<div class="themetechmount-team-position"><?php echo themetechmount_get_meta( 'themetechmount_team_member_details', 'tm_team_info' , 'team_details_line_position' ); ?></div>
			<?php echo themetechmount_box_team_social_links(); ?>
		</div>
	</div>
</article>
 