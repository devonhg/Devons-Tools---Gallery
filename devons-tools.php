<?php
if ( ! defined( 'WPINC' ) ) { die; }
/*
 * Plugin Name:       Devons Tools - Portfolio Gallery
 * Plugin URI:        
 * Description:       This is the Core for Devons Tools Framework
 * Version:           v1.0.0
 * Author:            Devon Godfrey
 * Author URI:        http://playfreygames.net
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
GitHub Plugin URI: https://github.com/devonhg/Devons-Tools---Gallery
GitHub Branch:     master

	*IMPORTANT*
	do a "find/replace" accross the directory for "DTGAL" and replace
	with your plugin name. 

	Plugin slug: DTGAL

*/

//Register the stylesheet. 
    function DTGAL_style(){
        wp_enqueue_style( 'DTGAL-Style',  plugin_dir_url( __FILE__ ) . "build/styles.min.css" );
    }
    add_action( 'wp_enqueue_scripts', 'DTGAL_style' );


//Include the core class of the post type api
    include_once('pt-api/class-core.php');
    register_activation_hook( __FILE__, 'DTGAL_ptapi_activate' );

//Create Post-Type Object
    $pt_port_gal = new DTGAL_post_type( "Portfolio Pieces", "Portfolio Piece", "This is the portfolio post-type. Using this you can add images to your site in an organized fashion." ); 

//Register Taxonomies
    $pt_port_gal->reg_tax("Categories", "Category" );

//Modify Hooks - Single
    $pt_port_gal->remove_hook_single();
    $pt_port_gal->add_hook_single( array("DTGAL_pt_pcs",'pc_meta') );
    $pt_port_gal->add_hook_single( array("DTGAL_pt_pcs",'pc_cats') );
    $pt_port_gal->add_hook_single( array("DTGAL_pt_pcs",'pc_content') );

//Modify Hooks - Shortcode and Gallery
    $pt_port_gal->remove_hook_archive();
    //$pt_port_gal->add_hook_archive( array("DTGAL_pt_pcs",'pc_fimed_a') ); 
    //$pt_port_gal->add_hook_archive( array("DTGAL_pt_pcs",'pc_title_a') ); 
    //$pt_port_gal->add_hook_archive( "DTGAL_tn_f" ); 

    $pt_port_gal->remove_hook_sc();
    //$pt_port_gal->add_hook_sc( array("DTGAL_pt_pcs",'pc_fimed_a') ); 
    $pt_port_gal->add_hook_sc( "DTGAL_tn_f" );
    //$pt_port_gal->add_hook_sc( array("DTGAL_pt_pcs",'pc_title_a') );

//Add Meta
    $pt_port_gal->reg_meta('Medium', 'The medium used for the piece.', false, 'text');
    $tn_img = $pt_port_gal->reg_meta('Custom Thumbnail', 'Specify a thumbnail image.', true, 'media');


//Functions
    function DTGAL_tn_f( $quer = null ){
        $post = DTGAL_func::get_post( $quer );
        global $tn_img;

        $img_link = $tn_img->get_val();
        $out = "";

        if ( $img_link != "" || $img_link != null ){
            $img_link = $tn_img->get_val();
        }else{
            $img_link_ar = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail');
            $img_link = $img_link_ar[0];
        }

        $out .= "<div class='DTGAL-tn'>";
            $out .= "<a title='" . $post->post_title . "'  href='" . get_permalink( $post->ID ) . "'>";
                $out .= "<img alt='" . $post->post_title . "' src='" . $img_link . "''>";
            $out .= "</a>";         

        $out .= "</div>";
        
        echo $out; 
    }