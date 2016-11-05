<?php
	if( file_exists( dirname(__FILE__) . '/../../../../wp-load.php' ) )
		require_once( realpath( dirname(__FILE__) . '/../../../../wp-load.php' ) );
	else
		require_once( realpath( dirname(__FILE__) . '/../../../../wp-config.php' ) );
	
    $args = array( 'child_of' => 0, 'sort_order' => 'ASC', 'sort_column' => 'post_title', 'hierarchical' => 1, 'exclude' => '', 
	'include' =>'' , 'meta_key' => '' , 'meta_value' => '' , 'authors' =>'' , 'parent' => -1, 'exclude_tree' => '', 
	'number' =>'' , 'offset' => 0, 'post_type' => 'page', 'post_status' => 'publish' );
	$pages = get_pages($args); 
	$xmlstr = "<links><group text=\"WP Pages\">";
	$siteurl =  get_site_url();
	if (is_ssl())
	{
		$siteurl = str_replace('http:', 'https:', $siteurl);
	}
	$xmlstr.="<link text=\"Home\" href=\"".$siteurl."\" />";
	foreach($pages as $pageitem)
	{
		$xmlstr.="<link text=\"".$pageitem->post_title."\" href=\"".get_page_link($pageitem->ID)."\" />";
	}	
	$xmlstr .= "</group></links>";
	header('Content-Type: text/xml');
	echo $xmlstr;
?>