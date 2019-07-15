<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'chld_thm_cfg_parent','twentyseventeen-style','twentyseventeen-block-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION


//Register Real Estate post type

//call the function
add_action( 'init', 'create_real_estate' );

function create_real_estate() {
	$labels = array(
		'name'               => _x( 'Real Estate', 'post type general name', 'textdomain' ),
		'singular_name'      => _x( 'Real State', 'post type singular name', 'textdomain' ),
		'menu_name'          => _x( 'Real Estate', 'admin menu', 'textdomain' ),
		'name_admin_bar'     => _x( 'Real Estate', 'add new on admin bar', 'textdomain' ),
		'add_new'            => _x( 'Add New', 'real_estate', 'textdomain' ),
		'add_new_item'       => __( 'Add New Real Estate', 'textdomain' ),
		'new_item'           => __( 'New Real Estate', 'textdomain' ),
		'edit_item'          => __( 'Edit Real Estate', 'textdomain' ),
		'view_item'          => __( 'View Real Estate', 'textdomain' ),
		'all_items'          => __( 'All Real Estate', 'textdomain' ),
		'search_items'       => __( 'Search Real Estate', 'textdomain' ),
		'parent_item_colon'  => __( 'Parent Real Estate:', 'textdomain' ),
		'not_found'          => __( 'No real estate found.', 'textdomain' ),
		'not_found_in_trash' => __( 'No real estate found in Trash.', 'textdomain' )
	);

	$args = array(
        'label'              => __( 'real_estate' ),
		'labels'             => $labels,
        'description'        => __( 'Description.', 'textdomain' ),
        'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'estate','with_front' => false ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
        'supports'           => array( 'title', 'image', 'author', 'thumbnail', 'revisions', 'custom-fields' ),
        'taxonomies'         => array( 'location', 'type' ),
	);

	register_post_type( 'real_estate', $args );
}

// hook into the init action and call create_real_estate_taxonomies when it fires
add_action( 'init', 'create_real_estate_taxonomies', 0 );

//Register taxonomies Location and Type
function create_real_estate_taxonomies() {
     
        // Add new taxonomy Location
        $labels = array(
            'name'                       => _x( 'Locations', 'taxonomy general name', 'textdomain' ),
            'singular_name'              => _x( 'Location', 'taxonomy singular name', 'textdomain' ),
            'search_items'               => __( 'Search Locations', 'textdomain' ),
            'popular_items'              => __( 'Popular Locations', 'textdomain' ),
            'all_items'                  => __( 'All Locations', 'textdomain' ),
            'parent_item'                => __( 'Parent Type' ),
            'parent_item_colon'          => __( 'Parent Type:' ),
            'edit_item'                  => __( 'Edit Location', 'textdomain' ),
            'update_item'                => __( 'Update Location', 'textdomain' ),
            'add_new_item'               => __( 'Add New Location', 'textdomain' ),
            'new_item_name'              => __( 'New Location Name', 'textdomain' ),
            'separate_items_with_commas' => __( 'Separate locations with commas', 'textdomain' ),
            'add_or_remove_items'        => __( 'Add or remove locations', 'textdomain' ),
            'choose_from_most_used'      => __( 'Choose from the most used locations', 'textdomain' ),
            'not_found'                  => __( 'No locations found.', 'textdomain' ),
            'menu_name'                  => __( 'Locations', 'textdomain' ),
        );
    
        $args = array(
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'location','with_front' => false ),
        );

        register_taxonomy( 'location', 'real_estate', $args );
    
        
    
        // Add new taxonomy Type
        $labels = array(
            'name'                       => _x( 'Types', 'taxonomy general name', 'textdomain' ),
            'singular_name'              => _x( 'Type', 'taxonomy singular name', 'textdomain' ),
            'search_items'               => __( 'Search Types', 'textdomain' ),
            'popular_items'              => __( 'Popular Types', 'textdomain' ),
            'all_items'                  => __( 'All Types', 'textdomain' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Type', 'textdomain' ),
            'update_item'                => __( 'Update Type', 'textdomain' ),
            'add_new_item'               => __( 'Add New Type', 'textdomain' ),
            'new_item_name'              => __( 'New Type Name', 'textdomain' ),
            'separate_items_with_commas' => __( 'Separate types with commas', 'textdomain' ),
            'add_or_remove_items'        => __( 'Add or remove types', 'textdomain' ),
            'choose_from_most_used'      => __( 'Choose from the most used types', 'textdomain' ),
            'not_found'                  => __( 'No types found.', 'textdomain' ),
            'menu_name'                  => __( 'Types', 'textdomain' ),
        );
    
        $args = array(
            'hierarchical'          => false,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'type','with_front' => false ),
        );
    
        register_taxonomy( 'type', 'real_estate', $args );
    }
