<?php
/**
 * amw_spectrum_transformation_group functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package amw_spectrum_transformation_group
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'amw_spectrum_transformation_group_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function amw_spectrum_transformation_group_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on amw_spectrum_transformation_group, use a find and replace
		 * to change 'amw_spectrum_transformation_group' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'amw_spectrum_transformation_group', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'amw_spectrum_transformation_group' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

	}
endif;
add_action( 'after_setup_theme', 'amw_spectrum_transformation_group_setup' );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function amw_spectrum_transformation_group_widgets_init() {
	$shared_args = array(
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
        'before_widget' => '<div class="footer-wrpp %2$s">',
        'after_widget'  => '</div>',
    );

    // Footer #1.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name'        => __( 'Footer widget №1', 'amw_spectrum_transformation_group' ),
                'id'          => 'footer-wid-1',
                'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'amw_spectrum_transformation_group' ),
            )
        )
    );

    // Footer #2.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name'        => __( 'Footer widget №2', 'amw_spectrum_transformation_group' ),
                'id'          => 'footer-wid-2',
                'description' => __( 'Widgets in this area will be displayed in the second column in the footer.', 'amw_spectrum_transformation_group' ),
            )
        )
    );

    // Footer #3.
    register_sidebar(
        array_merge(
            $shared_args,
            array(
                'name'        => __( 'Footer widget №3', 'amw_spectrum_transformation_group' ),
                'id'          => 'footer-wid-3',
                'description' => __( 'Widgets in this area will be displayed in the third column in the footer.', 'amw_spectrum_transformation_group' ),
            )
        )
    );

    // Sidebar #1.
    register_sidebar(
        array(
			'before_title'  => '<h3>',
        	'after_title'   => '</h3>',
        	'before_widget' => '<div class="aside-sticky %2$s">',
        	'after_widget'  => '</div>',
            'name'        => __( 'Sidebar widget №1', 'amw_spectrum_transformation_group' ),
            'id'          => 'sidebar-wid-1',
            'description' => __( 'Widgets in this area will be displayed on the right side of the page if the "sidebar" switcher is switched.', 'amw_spectrum_transformation_group' ),
        )
    );
}
add_action( 'widgets_init', 'amw_spectrum_transformation_group_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function amw_spectrum_transformation_group_scripts() {
	wp_enqueue_style( 'fontawesome', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/_css/bootstrap.min.css');
	wp_enqueue_style( 'slick', get_template_directory_uri() . '/assets/_css/slick.css');
	wp_enqueue_style( 'slick-theme', get_template_directory_uri() . '/assets/_css/slick-theme.css');
	wp_enqueue_style( 'styles', get_template_directory_uri() . '/assets/_css/styles.css');
	wp_enqueue_style( 'responsive', get_template_directory_uri() . '/assets/_css/responsive.css');

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/_js/bootstrap.min.js', array('jquery'), false, true  );
	wp_enqueue_script( 'slick', get_template_directory_uri() . '/assets/_js/slick.min.js', array('jquery'), false, true  );
	wp_enqueue_script( 'waypoints', get_template_directory_uri() . '/assets/_js/waypoints.min.js', array('jquery'), false, true  );
	wp_enqueue_script( 'counterup', get_template_directory_uri() . '/assets/_js/jquery.counterup.min.js', array('jquery'), false, true  );
	wp_enqueue_script( 'template', get_template_directory_uri() . '/assets/_js/template.js', array('jquery'), false, true  );
}
add_action( 'wp_enqueue_scripts', 'amw_spectrum_transformation_group_scripts' );

/**
 * Main menu filters
 */

//Changing the class of the nested ul of main menu
add_filter('nav_menu_submenu_css_class', 'filter_nav_menu_submenu_css_class', 10, 3);
function filter_nav_menu_submenu_css_class($classes, $args, $depth) {
	if($args->theme_location === 'menu-1'){
		$classes = [
			'sub-menu',
			'menu-level-' . ($depth + 2)
		];
	}
	return $classes;
}

/**
* ACF option page;
*/

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Theme Options',
		'menu_title'	=> 'Theme Options',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false,
	));
}

/**
 * Register button shortcode in ACF Wysiwyg editor.
 */

function ba_add_mce_button() {
	add_filter( 'mce_external_plugins', 'ba_add_tinymce_script' );
	add_filter( 'mce_buttons', 'ba_register_mce_button' );
}
add_action('admin_head', 'ba_add_mce_button');


function ba_add_tinymce_script( $plugin_array ) {
$plugin_array['ba_mce_button'] = get_stylesheet_directory_uri() .'/assets/_js/editor-scripts.js';
return $plugin_array;
}

function ba_register_mce_button( $buttons ) {
	array_push( $buttons, 'ba_mce_button' );
	return $buttons;
}

add_filter('mce_buttons', 'ba_register_mce_button');

function ba_url_external( $atts ) {
	$params = shortcode_atts( array(
		'title' => '',
		'link' => '',
	), $atts );
    return "<a href='{$params['link']}' class='btn'>{$params['title']}</a>";
}
add_shortcode( 'button', 'ba_url_external' );

/**
 * Register button shortcode in Widget Wysiwyg editor.
 */

function my_enqueue($hook) {
	if ( 'widgets.php' != $hook ) {
		return;
	}
	wp_enqueue_script( 'tinymce_custom_button_widget', get_template_directory_uri() . '/assets/_js/editor-widget.js', array( 'jquery' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );


/**
* Exclude standart post type;
*/
function remove_default_post_type($args, $postType) {
    if ($postType === 'post') {
        $args['public']                = false;
        $args['show_ui']               = false;
        $args['show_in_menu']          = false;
        $args['show_in_admin_bar']     = false;
        $args['show_in_nav_menus']     = false;
        $args['can_export']            = false;
        $args['has_archive']           = false;
        $args['exclude_from_search']   = true;
        $args['publicly_queryable']    = false;
        $args['show_in_rest']          = false;
    }

    return $args;
}
add_filter('register_post_type_args', 'remove_default_post_type', 0, 2);

/**
 * Registers a new post type
 * @uses $wp_post_types Inserts new post type object into the list
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string  See optional args description above.
 * @return object|WP_Error the registered post type object, or an error object
 */
function blog_register_name() {
	
	$blog = array(
		'name'               => __( 'News', 'text-domain' ),
		'singular_name'      => __( 'News', 'text-domain' ),
		'add_new'            => _x( 'Add New Post', 'text-domain', 'text-domain' ),
		'add_new_item'       => __( 'Add New Post', 'text-domain' ),
		'edit_item'          => __( 'Edit Post', 'text-domain' ),
		'new_item'           => __( 'New Post', 'text-domain' ),
		'view_item'          => __( 'View Post', 'text-domain' ),
		'search_items'       => __( 'Search News', 'text-domain' ),
		'not_found'          => __( 'No Posts found', 'text-domain' ),
		'not_found_in_trash' => __( 'No Posts found in Trash', 'text-domain' ),
		'parent_item_colon'  => __( 'Parent Post:', 'text-domain' ),
		'menu_name'          => __( 'News', 'text-domain' ),
	);
	
	$args_blog = array(
		'labels'              => $blog,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(''),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'trackbacks',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		),
	);

	register_post_type( 'blog', $args_blog );

	$jobs = array(
		'name'               => __( 'Jobs', 'text-domain' ),
		'singular_name'      => __( 'Jobs', 'text-domain' ),
		'add_new'            => _x( 'Add New Post', 'text-domain', 'text-domain' ),
		'add_new_item'       => __( 'Add New Post', 'text-domain' ),
		'edit_item'          => __( 'Edit Post', 'text-domain' ),
		'new_item'           => __( 'New Post', 'text-domain' ),
		'view_item'          => __( 'View Post', 'text-domain' ),
		'search_items'       => __( 'Search Jobs', 'text-domain' ),
		'not_found'          => __( 'No Posts found', 'text-domain' ),
		'not_found_in_trash' => __( 'No Posts found in Trash', 'text-domain' ),
		'parent_item_colon'  => __( 'Parent Post:', 'text-domain' ),
		'menu_name'          => __( 'Jobs', 'text-domain' ),
	);
	
	$args_jobs = array(
		'labels'              => $jobs,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(''),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'trackbacks',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		),
	);

	register_post_type( 'jobs', $args_jobs );

	$staff = array(
		'name'               => __( 'Staff', 'text-domain' ),
		'singular_name'      => __( 'Staff', 'text-domain' ),
		'add_new'            => _x( 'Add New Staff', 'text-domain', 'text-domain' ),
		'add_new_item'       => __( 'Add New Staff', 'text-domain' ),
		'edit_item'          => __( 'Edit Staff', 'text-domain' ),
		'new_item'           => __( 'New Staff', 'text-domain' ),
		'view_item'          => __( 'View Staff', 'text-domain' ),
		'search_items'       => __( 'Search Staff', 'text-domain' ),
		'not_found'          => __( 'No Staff found', 'text-domain' ),
		'not_found_in_trash' => __( 'No Staff found in Trash', 'text-domain' ),
		'parent_item_colon'  => __( 'Parent Staff:', 'text-domain' ),
		'menu_name'          => __( 'Staff', 'text-domain' ),
	);
	
	$args_staff = array(
		'labels'              => $staff,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(''),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => null,
		'menu_icon'           => null,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'trackbacks',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		),
	);

	register_post_type( 'staff', $args_staff );
	
}

add_action( 'init', 'blog_register_name' );

/**
* Disable post/page editor and other;
*/
add_action( 'init', 'my_remove_post_formats_support', 10 );
	function my_remove_post_formats_support() {
		remove_post_type_support( 'attachment', 'comments' );
		remove_post_type_support( 'page', 'editor' );
		remove_post_type_support( 'page', 'revisions' );
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'post', 'editor' );
		remove_post_type_support( 'blog', 'author' );
		remove_post_type_support( 'blog', 'trackbacks' );
		remove_post_type_support( 'blog', 'excerpt' );
		remove_post_type_support( 'blog', 'comments' );
		remove_post_type_support( 'blog', 'revisions' );
		remove_post_type_support( 'jobs', 'author' );
		remove_post_type_support( 'jobs', 'trackbacks' );
		remove_post_type_support( 'jobs', 'excerpt' );
		remove_post_type_support( 'jobs', 'comments' );
		remove_post_type_support( 'jobs', 'revisions' );
		remove_post_type_support( 'jobs', 'thumbnail' );
		remove_post_type_support( 'staff', 'author' );
		remove_post_type_support( 'staff', 'trackbacks' );
		remove_post_type_support( 'staff', 'excerpt' );
		remove_post_type_support( 'staff', 'comments' );
		remove_post_type_support( 'staff', 'revisions' );
}

/**
 * Excerpt of ACF content field 
 */
function excerpt($limit, $field) {
	$excerpt = explode(' ', $field, $limit);
	if (count($excerpt)>=$limit) {
	  array_pop($excerpt);
	  $excerpt = implode(" ",$excerpt).'...'.'</p>';
	} else {
	  $excerpt = implode(" ",$excerpt);
	}	
	$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
	return $excerpt;
}

/**
 * Get first paragraph from WYSIWYG content
 */

function content_excerpt( $arg ) {
    $text = $arg;
    if ( !empty($text) ) {
        $start = strpos($text, '<p>');
        $end = strpos($text, '</p>', $start);
        $text = substr($text, $start, $end-$start+4);
        $text = strip_shortcodes( $text );
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]>', $text);
    }
    return $text;
}

/**
 * Modal window staff page
 */
function modal_scripts() {
 
    wp_register_script('custom-script', get_template_directory_uri(). '/assets/_js/modal.js', array('jquery'), false, true);
    // Localize the script with new data
    $script_data_array = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'view_post' ),
    );
    wp_localize_script( 'custom-script', 'staff', $script_data_array );
    wp_enqueue_script('custom-script');
}
add_action( 'wp_enqueue_scripts', 'modal_scripts' );
 
function load_post_in_modal_by_ajax() {
    check_ajax_referer('view_post', 'security');
    $args = array(
        'post_type' => 'staff',
        'post_status' => 'publish',
        'p' => $_POST['id'],
    );
 
    $posts = new WP_Query( $args );
 
    $arr_response = array();
    if ($posts->have_posts()) {
 
        while($posts->have_posts()) {
 
            $posts->the_post();
 
            $arr_response = array(
                'title' => get_the_title(),
                'content' => get_the_content(),
				'photo' => wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' )[0],
				'subfield' => get_field('staff_specialization'),
            );
        }
        wp_reset_postdata();
    }
 
    echo json_encode($arr_response);
 
    wp_die();
}

add_action('wp_ajax_load_post_by_ajax', 'load_post_in_modal_by_ajax');
add_action('wp_ajax_nopriv_load_post_by_ajax', 'load_post_in_modal_by_ajax');