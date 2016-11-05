<?php
/*
Plugin Name: RichTextEditor For WordPress
Plugin URI: http://phphtmleditor.com
Description: Rich Text Editor for WordPress is by far the fastest, cleanest, most powerful wysiwyg content editor. It replaces default WordPress editor with a more advanced wysiwyg editor.
Version: 1.0.1
Author: CuteSoft
Author URI: http://phphtmleditor.com
*/


define( 'FV_RTE_NAME', 'RichTextEditor' );
define( 'FV_RTE_OPTIONS', 'rte_editor' );

add_action('init', 'rteeditor_init');

function rteeditor_init()
{
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	require_once( 'richtexteditor_wordpress_class.php' );

	if( $GLOBALS['wp_version'] > 2.9 ) {
	  add_action( 'init', array( &$rte_editor, 'check_featured_image_capability' ), 999 );
	}
	
	if(is_admin())
	{		
		add_action('admin_menu', array(&$rte_editor, 'add_option_page'));
		
		if( $GLOBALS['wp_version'] > 2.9 && $GLOBALS['wp_version'] < 3.3 ) {
			add_action('admin_print_scripts', array(&$rte_editor, 'add_post_editor'));
		}
	}

	add_action( 'wp_print_scripts', array(&$rte_editor, 'add_comment_editor'));

	if( $GLOBALS['wp_version'] >= 3.3 ) {
	  add_action( 'admin_print_footer_scripts', array( &$rte_editor, 'admin_print_footer_scripts' ) );
	}

	if( $GLOBALS['wp_version'] >= 3.0 ) {
	  add_action( 'admin_head', array( &$rte_editor, 'KillTinyMCE' ) );
	}
	else {
	  add_action( 'option_posts_per_page', array( &$rte_editor, 'KillTinyMCE' ) );
	}
}
?>