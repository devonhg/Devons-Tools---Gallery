<?php
if ( ! defined( 'WPINC' ) ) { die; }

if ( ! get_theme_support( 'post-thumbnails' )) add_theme_support('post-thumbnails');


//Include all files in directory
	foreach (glob( plugin_dir_path( __FILE__ ) . "*." . "php" ) as $filename){
		include_once( $filename );
	}

//Front end hooks
	function DTGAL_pt_archive(){
		global $post;
		$cl = null; 
		foreach( DTGAL_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('DTGAL_pt_archive');
			$cl->reg_hooks_archive();

			$classes = get_post_class();

			do_action('DTGAL_pt_archive');

		}else{
			"No posts for this post-type.";
		}
	}

	function DTGAL_pt_single(){
		global $post;
		$cl = null; 

		foreach( DTGAL_post_type::$instances as $instance ){
			if ( $instance->pt_slug == $post->post_type ){
				$cl = $instance;
			}
		}

		if ( $cl !== null ){
			remove_all_actions('DTGAL_pt_single');
			$cl->reg_hooks_single();

			do_action('DTGAL_pt_single');
		}else{
			echo "No posts for this post-type.";
		}
	}

//Initation Hooks
	function DTGAL_ptapi_activate() {
	    if ( ! get_option( 'DTGAL_flush_flag' ) ) {
	        add_option( 'DTGAL_flush_flag', true );
	    }
	}

	add_action( 'init', 'DTGAL_ptapi_rewrite', 20 );
	function DTGAL_ptapi_rewrite() {
	    if ( get_option( 'DTGAL_flush_flag' ) ) {
	        flush_rewrite_rules();
	        delete_option( 'DTGAL_flush_flag' );
	    }
	}


//Create Admin Page
    new DTGAL_dash_page( "Post Types", "Post Type" );