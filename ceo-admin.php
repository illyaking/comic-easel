<?php

// actions
add_action('admin_menu', 'ceo_add_menu_pages');
add_action('admin_enqueue_scripts', 10, 1 );

function ceo_change_chapter_to_radio(){
	echo '<script type="text/javascript">';
	echo 'jQuery("#chapterschecklist input, .chapters-checklist input")';
	echo '.each(function(){this.type="radio"});</script>';
}

function ceo_add_menu_pages() {
	global $pagenow, $post_type;
	
	$menu_location = 'edit.php?post_type=comic';
	$plugin_title = __('Comic Easel', 'comiceasel');
	$config_title = __('Config', 'comiceasel');
	$debug_title = __('Debug', 'comiceasel');
	$import_title = __('Import', 'comiceasel');
	
	// the ceo_pluginfo used here actually initiates it.
	if (!defined('CEO_FEATURE_DISABLE_IMPORT'))
		$import_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $import_title, $import_title, 'edit_theme_options', 'comiceasel-import', 'ceo_import');	
	if (!defined('CEO_FEATURE_DISABLE_CONFIG')) {
		$config_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $config_title, $config_title, 'edit_theme_options', 'comiceasel-config', 'ceo_manager_config');
		add_action('admin_head-' . $config_hook, 'ceo_admin_page_head');
		add_action('admin_print_scripts-' . $config_hook, 'ceo_admin_print_scripts');
		add_action('admin_print_styles-' . $config_hook, 'ceo_admin_print_styles');
	}
	if (!defined('CEO_FEATURE_DISABLE_DEBUG'))
		$debug_hook = add_submenu_page($menu_location, $plugin_title . ' - ' . $debug_title, $debug_title, 'edit_theme_options', 'comiceasel-debug', 'ceo_debug');	
	ceo_enqueue_admin_cpt_style('comic', 'comic-admin-editor-style', ceo_pluginfo('plugin_url').'css/admin-editor.css');
	// Add contextual help
}

function ceo_load_scripts_chapter_manager() {
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
}

function ceo_admin_print_scripts() {
	wp_enqueue_script('utils');
	wp_enqueue_script('jquery');
}

function ceo_admin_print_styles() {
	wp_admin_css('css/global');
	wp_admin_css('css/colors');
	wp_admin_css('css/ie');
	wp_enqueue_style('comiceasel-options-style', ceo_pluginfo('plugin_url') . 'css/config.css');
}

function ceo_admin_page_head() { ?>
	<!--[if lt ie 8]> <style> div.show { position: static; margin-top: 1px; } #eadmin div.off { height: 22px; } </style> <![endif]-->
<?php }

// This is done this way to *not* load pages unless they are called, self sufficient code,
// but since attached to the ceo-admin it can use the library in core. so the global functions used in multiple areas
// go into the ceo-admin.php file, while local functions that are only run on the individual pages go on those pages
// the "forms" if there are any call the same page back up. - phil

function ceo_manager_config() {
	require_once('ceo-config.php');
}

function ceo_debug() {
	require_once('ceo-debug.php');
}

function ceo_import() {
	require_once('ceo-import.php');
}

function ceo_manager_adconfig() {
	require_once('ceo-adconfig.php');
}

function ceo_enqueue_admin_cpt_style( $cpt, $handle, $src = false, $deps = array(), $ver = false, $media = 'all' ) {
 
	/* Check the admin page we are on. */
	global $pagenow;
 
	/* Default to null to prevent enqueuing. */
	$enqueue = null;
 
	/* Enqueue style only if we are on the correct CPT editor page. */
	if ( isset($_GET['post_type']) && $_GET['post_type'] == $cpt && ($pagenow == "post-new.php" || $pagenow == 'edit.php')) {
		$enqueue = true;
	}
 
	/* Enqueue style only if we are on the correct CPT editor page. */
	if ( isset($_GET['post']) && ($pagenow == "post.php" || $pagenow == 'edit.php')) {
		$post_id = $_GET['post'];
		$post_obj = get_post( $post_id );
		if( $post_obj->post_type == $cpt )
			$enqueue = true;
	}
 
	/* Only enqueue if editor page is the correct CPT. */
	if ($enqueue) wp_enqueue_style( $handle, $src, $deps, $ver, $media );
}


