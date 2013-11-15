<?php
/* 
Description: Useful custom functions for use in WordPress themes
Author: Riyadh Al Nur
URL: www.verticalaxisbd.com
Twitter: @riyadhalnur
Date: 25/09/2013
*/

// smart jquery inclusion (use if you want to use jquery from their official CDN or if you want to use a different version)
if (!is_admin()) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false);
	wp_enqueue_script('jquery');
}

// remove WP generated junk meta tags from head
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );

// enable threaded comments
function enable_threaded_comments() {
	if ( !is_admin() ) {
		if ( is_singular() and comments_open() and ( get_option( 'thread_comments' ) == 1 ) )
			wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'get_header', 'enable_threaded_comments' );

// add google analytics to footer
function add_google_analytics() {
	echo '<script src="http://www.google-analytics.com/ga.js" type="text/javascript"></script>';
	echo '<script type="text/javascript">';
	echo 'var pageTracker = _gat._getTracker("UA-XXXXX-X");'; // replace with own Analytics tracking number
	echo 'pageTracker._trackPageview();';
	echo '</script>';
}

add_action( 'wp_footer', 'add_google_analytics' );

// custom excerpt length
function custom_excerpt_length( $length ) {
	return 30;
}

add_filter( 'excerpt_length', 'custom_excerpt_length' );

// custom excerpt link (replaces the ... default link)
function custom_excerpt_more( $more ) {
	return 'Read On';
}

add_filter( 'excerpt_more', 'custom_excerpt_more' );

// add a favicon to your site
function blog_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo( 'wpurl' ).'img/favicon.ico" />';
}

add_action( 'wp_head', 'blog_favicon' );

// add a different favicon for your admin
function admin_favicon() {
	echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/images/favicon.png" />';
}
add_action('admin_head', 'admin_favicon');

// remove admin update notification for non-admins
global $user_login;
get_currentuserinfo();
if ( !current_user_can( 'update_plugins' ) ) {
	add_action( 'init', create_function( '$a', "remove_action('init', 'wp_version_check');" ), 2 );
	add_filter( 'pre_option_update_core', create_function( '$a', "return null;" ) );
}

// remove version info from head and feeds for security
function version_removal() {
	return ' ';
}

add_filter( 'the_generator', 'version_removal' );

// add feed links to header
if (function_exists('automatic_feed_links')) {
	automatic_feed_links();
} else {
	return;
}

// custom admin footer text
function custom_admin_footer() {
	// use this function to brand your WP installation
	echo 'Copyright &copy; 2013 <a href="http://twitter.com/riyadhalnur">Riyadh Al Nur</a>, <a href="http://verticalaxisbd.com">Vertical Axis BD</a>'; 
}

add_filter( 'admin_footer_text', 'custom_admin_footer' );

// change the logo in the login page
function custom_login_logo() { ?>
	<style type="text/css">
	body.login div#login h1 a {
		background-image: url(<?php echo get_bloginfo( 'template_directory' ) ?>/img/logo.png);
		padding-bottom: 30px;
	}
	</style>
<?php
}

add_action( 'login_enqueue_scripts', 'custom_login_logo' );

// custom link for login logo
function custom_login_url() {
	return home_url(); // replace with custom link
}

add_filter( 'login_headerurl', 'custom_login_url', 10, 4 );

// disable all widget areas (useful if you are building a custom theme)
function disable_all_widgets($sidebars_widgets) {
	//if (is_home())
		$sidebars_widgets = array(false);
	return $sidebars_widgets;
}
add_filter('sidebars_widgets', 'disable_all_widgets');

// get the first category id
function get_first_category_ID() {
	$category = get_the_category();
	return $category[0]->cat_ID;
}

//stop pinging yourself to reduce site load
function stop_self_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link )
	if ( 0 === strpos( $link, $home ) )
	unset($links[$l]);
}
add_action( 'pre_ping', 'stop_self_ping' );

// add site load time, DB queries, memory comsumption to admin footer
function perf( $visible = false ) {
	$results = sprintf( '%d queries took %.3f seconds, and used %.2fMB of memory.',
		get_num_queries(),
		timer_stop( 0, 3 ),
		memory_get_peak_usage() / 1024 / 1024
		);
	echo $visible ? $results : "<!-- {$results} -->" ;
}
add_action( 'admin_footer', 'perf', 20 );
?>