<?php if ( function_exists('max_mega_menu_is_enabled') && max_mega_menu_is_enabled('tm-main-menu') ) : ?>

	<!-- Max Mega Menu is enabled so we are not showing our toggle menu -->
	
<?php else: ?>

<button id="menu-toggle" class="menu-toggle">
	<span class="tm-hide"><?php esc_attr_e( 'Toggle menu', 'boldman' ); ?></span><i class="tm-boldman-icon-bars"></i>
</button>

<?php endif; ?>

<?php wp_nav_menu( array( 'theme_location' => 'tm-main-menu', 'menu_class' => 'nav-menu', 'container_class' => 'nav-menu' ) ); ?>
