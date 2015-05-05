<?php
if ( ! defined( 'WPINC' ) ) { die; }

class MYPLUGIN_post_type{

    //Properties
        //Instance Tracking
        static $instances = array(); 

        private $hooks_single = array(); 
        private $hooks_archive = array(); 
        private $hooks_sc = array(); 

        var $name;
        var $name_s;
        var $pt_slug;
        var $classes;
    //Magic Methods
        public function __construct( $name , $name_s , $default = true , $classes = "" ){

            MYPLUGIN_post_type::$instances[] = $this; 

            $this->name = $name;
            $this->name_s = $name_s;
            $this->pt_slug = "pt_" . trim(strtolower($name_s));
            $this->classes = $classes;
            new MYPLUGIN_pt_sc($this->pt_slug, $this );//Creates Shortcodes

            add_action( 'init', array($this, 'initiate_cpt'), 0 );

            if ( $default ){
                $this->def_hooks_single();
                $this->def_hooks_archive();
                $this->def_hooks_shortcode();
            }
        }

    //Register Methods
        public function reg_tax($name, $name_s ){
            $a = new MYPLUGIN_pt_tax($name, $name_s, $this->pt_slug );
            return $a;
        }
        public function reg_meta($title, $desc, $hide = false , $typ = "text", $options = null){
            $a = new MYPLUGIN_pt_meta($title, $desc, $this->pt_slug , $hide , $typ, $options );
            return $a;
        }

    //Add hooks
        public function add_hook_single( $function = null ){
            if (  $function == null ){
                return $this->hooks_single; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_single[] = $function; 
                }
            }
        }

        public function add_hook_archive( $function = null ){
            if (  $function == null ){
                return $this->hooks_archive; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_archive[] = $function; 
                }
            }
        }

        public function add_hook_sc( $function = null ){
            if (  $function == null ){
                return $this->hooks_sc; 
            }else{
                if ( gettype($function) == "string" || gettype($function) == "array" ){
                    $this->hooks_sc[] = $function; 
                }
            }
        }

    //Register Hooks
        public function reg_hooks_single(){
            add_action("MYPLUGIN_pt_single", array($this, "hook_article_start") );
            foreach( $this->hooks_single as $hook ){
                 add_action("MYPLUGIN_pt_single" , $hook );
            } 
            add_action("MYPLUGIN_pt_single", array($this, "hook_article_end") );
        }

        public function reg_hooks_archive(){
            add_action("MYPLUGIN_pt_archive", array($this, "hook_article_start") );
            foreach( $this->hooks_archive as $hook ){
                 add_action("MYPLUGIN_pt_archive" , $hook );
            } 
            add_action("MYPLUGIN_pt_archive", array($this, "hook_article_end") );
        }

        public function reg_hooks_sc(){
            add_action( $this->name_s . "pt_shortcode" , array($this, "hook_article_start") );
            foreach( $this->hooks_sc as $hook ){
                 add_action( $this->name_s . "pt_shortcode" , $hook, 10,1 );
            } 
            add_action( $this->name_s . "pt_shortcode" , array($this, "hook_article_end") );
        }    

    //Remove Hooks
        public function remove_hook_single( $hook = null ){ 
            if ( $hook != null ){
                if(($key = array_search($hook, $this->hooks_single )) !== false) {
                    unset( $this->hooks_single[$key]);
                }          
            }else{
                $this->hooks_single = array(); 
            }
        }    

        public function remove_hook_archive( $hook = null ){ 
            if ( $hook != null ){
                if(($key = array_search($hook, $this->hooks_archive )) !== false) {
                    unset( $this->hooks_archive[$key]);
                }   
            }else{
                $this->hooks_archive = array(); 
            }      
        }   

        public function remove_hook_sc( $hook ){ 
            if ( $hook != null ){
                if(($key = array_search($hook, $this->hooks_sc )) !== false) {
                    unset( $this->hooks_sc[$key]);
                }
            }else{
                 $this->hooks_sc = array(); 
            }          
        }   

    //Default hooks
        public function def_hooks_single(){
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_title') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_fi') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_content') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_meta') );
            $this->add_hook_single( array("MYPLUGIN_pt_pcs",'pc_cats') );
        }

        public function def_hooks_archive(){
            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pc_title_a') );
            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pc_fimed') );
            $this->add_hook_archive( array("MYPLUGIN_pt_pcs",'pc_excerpt') );
        }

        public function def_hooks_shortcode(){
            $this->add_hook_sc( array("MYPLUGIN_pt_pcs",'pc_title_a') );
            $this->add_hook_sc( array("MYPLUGIN_pt_pcs",'pc_fimed') );
            $this->add_hook_sc( array("MYPLUGIN_pt_pcs",'pc_excerpt') );
        }

    //Special Hooks
        public function hook_article_start( $quer = null ){
            $post = MYPLUGIN_func::get_post( $quer );
            $classes = implode( " ", get_post_class( "", $post->ID ) );
            echo "<article class='" . $this->classes . " " . $classes . "'>";
        }

        public function hook_article_end(){
            echo "</article>";
        }


    //Core Methods
        public function initiate_cpt(){

            $name = $this->name;
            $name_s = $this->name_s;
            $pt_slug = $this->pt_slug; 

            $labels = array(
                'name'                => _x($name, 'Post Type General Name'), 
                'singular_name'       => _x($name_s, 'Post Type Singular Name', 'twentythirteen'),
                'menu_name'           => __($name, 'twentythirteen'),
                'parent_item_colon'   => __('Parent ' . $name_s, 'twentythirteen'),
                'all_items'           => __( 'All ' .  $name, 'twentythirteen' ),
                'view_item'           => __( 'View ' . $name_s, 'twentythirteen' ),
                'add_new_item'        => __( 'Add New ' . $name_s, 'twentythirteen' ),
                'add_new'             => __( 'Add New', 'twentythirteen' ),
                'edit_item'           => __( 'Edit ' . $name_s, 'twentythirteen' ),
                'update_item'         => __( 'Update ' . $name_s, 'twentythirteen' ),
                'search_items'        => __( 'Search ' . $name_s, 'twentythirteen' ),
                'not_found'           => __( 'There are currently no '. $name, 'twentythirteen' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
            );

            $args = array(
                'label'               => __( $name, 'twentythirteen' ),
                'description'         => __( 'Contains the post data for ' . $name_s . ".", 'twentythirteen' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'can_export'          => true,
                'menu_icon'           => "dashicons-media-document",
                'has_archive'         => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'page',
            );
            register_post_type( $pt_slug , $args );
        }
}