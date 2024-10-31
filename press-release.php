<?php

/**

Plugin Name: Press Release 
Plugin URI: https://howtocreateapressrelease.com/press-release-editor 
Description: A press release editor with an included free press release template.
Version: 2.0 
Author: How To Create A Press Release 
Author URI: https://howtocreateapressrelease.com 
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

A press release editor with an included free press release template. 

**/

# Exit if accessed directly
if (!defined("ABSPATH"))
{
	exit;
}

# Constant

/**
 * Exec Mode
 **/
define("PRESSRELEASEEDITOR_EXEC",true);

/**
 * Plugin Base File
 **/
define("PRESSRELEASEEDITOR_PATH",dirname(__FILE__));

/**
 * Plugin Base Directory
 **/
define("PRESSRELEASEEDITOR_DIR",basename(PRESSRELEASEEDITOR_PATH));

/**
 * Plugin Base URL
 **/
define("PRESSRELEASEEDITOR_URL",plugins_url("/",__FILE__));

/**
 * Plugin Version
 **/
define("PRESSRELEASEEDITOR_VERSION","2.0"); 

/**
 * Debug Mode
 **/
define("PRESSRELEASEEDITOR_DEBUG",false);  //change false for distribution



/**
 * Base Class Plugin
 * @author PR Wire Pro
 *
 * @access public
 * @version 2.0
 * @package Press Release
 *
 **/

class PressRelease
{

	/**
	 * Instance of a class
	 * @access public
	 * @return void
	 **/

	function __construct()
	{
		add_action("plugins_loaded", array($this, "pressreleaseeditor_textdomain")); //load language/textdomain
		add_action("wp_enqueue_scripts",array($this,"pressreleaseeditor_enqueue_scripts")); //add js
		add_action("wp_enqueue_scripts",array($this,"pressreleaseeditor_enqueue_styles")); //add css
		add_action("init", array($this, "pressreleaseeditor_post_type_pressrelease_init")); // register a pressrelease post type.
		add_filter("the_content", array($this, "pressreleaseeditor_post_type_pressrelease_the_content")); // modif page for pressrelease
		add_action("after_setup_theme", array($this, "pressreleaseeditor_image_size")); // register image size.
		add_filter("image_size_names_choose", array($this, "pressreleaseeditor_image_sizes_choose")); // image size choose.
		add_action("init", array($this, "pressreleaseeditor_register_taxonomy")); // register register_taxonomy.
		add_action("wp_head",array($this,"pressreleaseeditor_dinamic_js"),1); //load dinamic js
		if(is_admin()){
			add_action("admin_enqueue_scripts",array($this,"pressreleaseeditor_admin_enqueue_scripts")); //add js for admin
			add_action("admin_enqueue_scripts",array($this,"pressreleaseeditor_admin_enqueue_styles")); //add css for admin
		}
	}


	/**
	 * Loads the plugin's translated strings
	 * @link http://codex.wordpress.org/Function_Reference/load_plugin_textdomain
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_textdomain()
	{
		load_plugin_textdomain("press-release", false, PRESSRELEASEEDITOR_DIR . "/languages");
	}


	/**
	 * Insert javascripts for back-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_admin_enqueue_scripts($hooks)
	{
		if (function_exists("get_current_screen")) {
			$screen = get_current_screen();
		}else{
			$screen = $hooks;
		}
	}


	/**
	 * Insert javascripts for front-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_script
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_enqueue_scripts($hooks)
	{
			wp_enqueue_script("pressreleaseeditor_main", PRESSRELEASEEDITOR_URL . "assets/js/pressreleaseeditor_main.js", array("jquery"),"2.0",true );
	}


	/**
	 * Insert CSS for back-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_admin_enqueue_styles($hooks)
	{
		if (function_exists("get_current_screen")) {
			$screen = get_current_screen();
		}else{
			$screen = $hooks;
		}
	}


	/**
	 * Insert CSS for front-end
	 * 
	 * @link http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @link http://codex.wordpress.org/Function_Reference/wp_enqueue_style
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_enqueue_styles($hooks)
	{
		// register css
		wp_register_style("pressreleaseeditor_main", PRESSRELEASEEDITOR_URL . "assets/css/pressreleaseeditor_main.css",array(),"2.0" );
			wp_enqueue_style("pressreleaseeditor_main");
	}


	/**
	 * Register custom post types (pressrelease)
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 * @access public
	 * @return void
	 **/

	public function pressreleaseeditor_post_type_pressrelease_init()
	{

		$labels = array(
			'name' => _x('Press Releases', 'post type general name', 'press-release'),
			'singular_name' => _x('Press Release', 'post type singular name', 'press-release'),
			'menu_name' => _x('Press Releases', 'admin menu', 'press-release'),
			'name_admin_bar' => _x('Press Releases', 'add new on admin bar', 'press-release'),
			'add_new' => _x('Add New', 'book', 'press-release'),
			'add_new_item' => __('Add New Press Release', 'press-release'),
			'new_item' => __('New Press Release', 'press-release'),
			'edit_item' => __('Edit Press Release', 'press-release'),
			'view_item' => __('View Press Release', 'press-release'),
			'all_items' => __('All Press Releases ', 'press-release'),
			'search_items' => __('Search Press Releases', 'press-release'),
			'parent_item_colon' => __('Parent Press Releases', 'press-release'),
			'not_found' => __('No Press Releases Found', 'press-release'),
			'not_found_in_trash' => __('No Press Releases Found In Trash', 'press-release'));

			$supports = array('title','editor','author','custom-fields','trackbacks','thumbnail','comments','revisions','post-formats','page-attributes');

			$args = array(
				'labels' => $labels,
				'description' => __('Press Releases', 'press-release'),
				'public' => true,
				'menu_icon' => 'dashicons-editor-table',
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'query_var' => true,
				'rewrite' => array('slug' => 'pressrelease'),
				'capability_type' => 'post',
				'has_archive' => true,
				'hierarchical' => true,
				'menu_position' => null,
				'taxonomies' => array(), // array('category', 'post_tag','page-category'),
				'supports' => $supports);

			register_post_type('pressrelease', $args);


	}


	/**
	 * Retrieved data custom post-types (pressrelease)
	 *
	 * @access public
	 * @param mixed $content
	 * @return void
	 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/the_content
	 **/

	public function pressreleaseeditor_post_type_pressrelease_the_content($content)
	{

		$new_content = $content ;
		if(is_singular("pressrelease")){
			if(file_exists(PRESSRELEASEEDITOR_PATH . "/includes/post_type.pressrelease.inc.php")){
				require_once(PRESSRELEASEEDITOR_PATH . "/includes/post_type.pressrelease.inc.php");
				$pressrelease_content = new Pressrelease_TheContent();
				$new_content = $pressrelease_content->Markup($content);
				wp_reset_postdata();
			}
		}

		return $new_content ;

	}


	/**
	 * Register a new image size.
	 * @link http://codex.wordpress.org/Function_Reference/add_image_size
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_image_size()
	{
	}


	/**
	 * Choose a image size.
	 * @access public
	 * @param mixed $sizes
	 * @return void
	 **/
	public function pressreleaseeditor_image_sizes_choose($sizes)
	{
		$custom_sizes = array(
		);
		return array_merge($sizes,$custom_sizes);
	}


	/**
	 * Register Taxonomies
	 * @https://codex.wordpress.org/Taxonomies
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_register_taxonomy()
	{
	}


	/**
	 * Insert Dinamic JS
	 * @param object $hooks
	 * @access public
	 * @return void
	 **/
	public function pressreleaseeditor_dinamic_js($hooks)
	{
		_e("<script type=\"text/javascript\">");
		_e("</script>");
	}
}


new PressRelease();
