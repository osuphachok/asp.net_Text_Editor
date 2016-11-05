<?php 

/**
 * Including wordpress
 */ 
require_once ('richtexteditor/include_rte.php');
class richtexteditor_wordpress_class {
	private static $instance;
	public $plugin_path = "";
	public $plugin_version = "1.0.1";
	
	public static function get_instance()
	{
		if (!isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}
	
	public function __construct()
	{
		if (DEFINED('WP_PLUGIN_URL')) {
			$this->plugin_path = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/';
		} else if (DEFINED('WP_PLUGIN_DIR')) {
			$this->plugin_path = $siteurl . '/' . WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/';
		} else {
			$this->plugin_path = $siteurl . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/';
		}
		if (is_ssl()) {
			$siteurl = str_replace('http:', 'https:', $siteurl);
			$this->plugin_path = str_replace('http:', 'https:', $this->plugin_path);
		}
		define('RTEEDITOR_PLUGIN_URL', $this->plugin_path);
		
		if (defined('WP_DEBUG') && WP_DEBUG == true)
		{
			add_action('init', array($this, 'error_reporting'));
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['saved']) && $_GET['saved'] == '1')
		{
			$this->update_options($_POST);
		}
	}
	
	public function add_option_page()
	{
		add_menu_page('RichTextEditor Settings', 'RichTextEditor', 'administrator', 'rte_settings', array(&$this, 'rte_overview'), $this->plugin_path . 'menuicon.gif');
		$basic_page = add_submenu_page('rte_settings', 'RichTextEditor Settings', 'RTE Settings', 'administrator', 'rte_basic_options', array(&$this, 'rte_option_page'));
		$advance_page = add_submenu_page('rte_settings', 'RichTextEditor Advance Settings', 'Advance Settings', 'administrator', 'rte_advance_options', array(&$this, 'rte_advance_page'));
	}
	/**
	 * Adds Options page to Wordpress.
	 */
	function AddOptionPage(){
		add_options_page( FV_RTE_NAME, FV_RTE_NAME, 'activate_plugins', 'rte_editor', array( &$this, 'OptionsMenuPage' ) );
		
		
	}
	public function rte_overview() {
		$rte = new RichTextEditor();
		echo "<div style=\"margin-right:20px;\">\n";
		
		echo "<div class=\"metabox-holder\" id=\"Control_RTE_OverView\">
			<div class=\"postbox\">
				<h3>RichTextEditor</h3>
				<table class='form-table'>
					<tr>
						<th scope=\"row\">RichTextEditor Version</th><td>".$rte->Version."</td>
					</tr>
					<tr><th scope=\"row\">Plugin Version</th><td>".$this->plugin_version."</td></tr>
					<tr><th scope=\"row\" valign=\"top\"><b>About RichTextEditor</b></th>
						<td>
							Rich Text Editor for WordPress is by far the fastest, cleanest, most powerful wysiwyg content editor. It replaces default WordPress editor with a more advanced wysiwyg editor. 
						</td>
					</tr>
				</table>
			</div>
		</div>
		";
		
		echo "</div>\n";
	}
	
	public function rte_advance_page()
	{
		echo "<div style=\"margin-right:20px;\">";
		if (isset($_GET['saved']))
		{
			echo "<div id=\"changes_saved_info\" class=\"updated below-h2\"><p>RichTextEditor settings saved successfully.</p></div>";
		}
		echo "<div class=\"metabox-holder\" id=\"Control_RTE_Setting\">

			<div class=\"postbox\">
				<form method=\"post\" id=\"rte_settings_form\" action=\"?page=rte_advance_options&saved=1\">
					<h3>RichTextEditor Advance Settings</h3>
					<div class='postbox_content'>
					<table class='form-table'>
					<tr>
						<td scope=\"row\" style=\"width:160px;\"><label for=\"RTE_TextDirection\">Text Direction:</label></td>
						<td>
							<select name=\"RTE_TextDirection\" id=\"RTE_TextDirection\">
								<option value=\"\">Default</option>
								<option value=\"ltr\">LeftToRight</option>
								<option value=\"rtl\">RightToLeft</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_ContentCss\">Content Css File:</label></td>
						<td><input type=\"text\" name=\"RTE_ContentCss\" id=\"RTE_ContentCss\" value=\"".get_option('RTE_ContentCss')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_EditorBodyStyle\">Editor Body Style:</label></td>
						<td><input type=\"text\" name=\"RTE_EditorBodyStyle\" id=\"RTE_EditorBodyStyle\" value=\"".get_option('RTE_EditorBodyStyle')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_GalleryPath\">Gallery Image Path:</label></td>
						<td><input type=\"text\" name=\"RTE_GalleryPath\" id=\"RTE_GalleryPath\" value=\"".get_option('RTE_GalleryPath')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_VideoPath\">Video Path:</label></td>
						<td><input type=\"text\" name=\"RTE_VideoPath\" id=\"RTE_VideoPath\" value=\"".get_option('RTE_VideoPath')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_DocumentPath\">Document Path:</label></td>
						<td><input type=\"text\" name=\"RTE_DocumentPath\" id=\"RTE_DocumentPath\" value=\"".get_option('RTE_DocumentPath')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_TemplatePath\">Template Path:</label></td>
						<td><input type=\"text\" name=\"RTE_TemplatePath\" id=\"RTE_DocumentPath\" value=\"".get_option('RTE_TemplatePath')."\"/></td>
					</tr>
					
					<tr>
						<td colspan=\"2\">
							<input type=\"hidden\" name=\"changes_saved\" value=\"1\">
							<input type=\"hidden\" name=\"settings_form\" value=\"1\">
							<input type=\"submit\" class=\"button-primary\" value=\"Save changes\" />
						</td>
					</tr>
					</table>
				</form>
			</div>
		</div>";
		
		echo "<script type=\"text/javascript\">				
				document.getElementById('RTE_TextDirection').value='".get_option('RTE_TextDirection')."'||'';
				
			</script>";
		echo "</div>";
	}
	
	public function rte_option_page() {
		echo "<div style=\"margin-right:20px;\">";
		if (isset($_GET['saved']))
		{
			echo "<div id=\"changes_saved_info\" class=\"updated below-h2\"><p>RichTextEditor settings saved successfully.</p></div>";
		}
		echo "<div class=\"metabox-holder\" id=\"Control_RTE_Setting\">

			<div class=\"postbox\">
				<form method=\"post\" id=\"rte_settings_form\" action=\"?page=rte_basic_options&saved=1\">
					<h3>RichTextEditor Basic Settings</h3>
					<div class='postbox_content'>
					<table class='form-table'>
					<tr>
						<td scope=\"row\" style=\"width:160px;\"><label for=\"RTE_Admin_Width\">Administrator Width:</label></td>
						<td><input type=\"text\" name=\"RTE_Admin_Width\" id=\"RTE_Admin_Width\" value=\"".get_option('RTE_Admin_Width')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Admin_Height\">Administrator Height:</label></td>
						<td><input type=\"text\" name=\"RTE_Admin_Height\" id=\"RTE_Admin_Height\" value=\"".get_option('RTE_Admin_Height')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Admin_Skin\">Administrator Skin:</label></td>
						<td>
							<select name=\"RTE_Admin_Skin\" id=\"RTE_Admin_Skin\">
								<option value=\"\">Default</option>
								<option value=\"office2007blue\">office2007blue</option>
								<option value=\"office2007silver\">office2007silver</option>
								<option value=\"office2010blue\">office2010blue</option>
								<option value=\"office2010silver\">office2010silver</option>
								<option value=\"office2010black\">office2010black</option>
								<option value=\"office2003blue\">office2003blue</option>
								<option value=\"office2003silver\">office2003silver</option>
								<option value=\"office2003silver2\">office2003silver2</option>
								<option value=\"officexpblue\">officexpblue</option>
								<option value=\"officexpsilver\">officexpsilver</option>
								<option value=\"smartblue\">smartblue</option>
								<option value=\"smartsilver\">smartsilver</option>
								<option value=\"smartgray\">smartgray</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Admin_Toolbar\">Administrator Toolbar:</label></td>
						<td>
							<select name=\"RTE_Admin_Toolbar\" id=\"RTE_Admin_Toolbar\">
								<option value=\"\">Default</option>
								<option value=\"custom\">custom</option>
								<option value=\"ribbon\">ribbon</option>
								<option value=\"full\">full</option>
								<option value=\"lite\">lite</option>
								<option value=\"forum\">forum</option>
								<option value=\"email\">email</option>
								<option value=\"minimal\">minimal</option>
								<option value=\"none\">none</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Site_UseRTE\">Use RTE as comment editor:</label></td>
						<td><input type=\"checkbox\" name=\"RTE_Site_UseRTE\" id=\"RTE_Site_UseRTE\"/></td>
					</tr>
					<tr>
						<td scope=\"row\" style=\"width:160px;\"><label for=\"RTE_Site_Width\">Comment Width:</label></td>
						<td><input type=\"text\" name=\"RTE_Site_Width\" id=\"RTE_Site_Width\" value=\"".get_option('RTE_Site_Width')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Site_Height\">Comment Height:</label></td>
						<td><input type=\"text\" name=\"RTE_Site_Height\" id=\"RTE_Site_Height\" value=\"".get_option('RTE_Site_Height')."\"/></td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Site_Skin\">Comment Skin:</label></td>
						<td>
							<select name=\"RTE_Site_Skin\" id=\"RTE_Site_Skin\">
								<option value=\"\">Default</option>
								<option value=\"office2007blue\">office2007blue</option>
								<option value=\"office2007silver\">office2007silver</option>
								<option value=\"office2010blue\">office2010blue</option>
								<option value=\"office2010silver\">office2010silver</option>
								<option value=\"office2010black\">office2010black</option>
								<option value=\"office2003blue\">office2003blue</option>
								<option value=\"office2003silver\">office2003silver</option>
								<option value=\"office2003silver2\">office2003silver2</option>
								<option value=\"officexpblue\">officexpblue</option>
								<option value=\"officexpsilver\">officexpsilver</option>
								<option value=\"smartblue\">smartblue</option>
								<option value=\"smartsilver\">smartsilver</option>
								<option value=\"smartgray\">smartgray</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Site_Toolbar\">Comment Toolbar:</label></td>
						<td>
							<select name=\"RTE_Site_Toolbar\" id=\"RTE_Site_Toolbar\">
								<option value=\"\">Default</option>
								<option value=\"custom\">custom</option>
								<option value=\"ribbon\">ribbon</option>
								<option value=\"full\">full</option>
								<option value=\"lite\">lite</option>
								<option value=\"forum\">forum</option>
								<option value=\"email\">email</option>
								<option value=\"minimal\">minimal</option>
								<option value=\"none\">none</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_EnterKeyTag\">Enter Key Tag:</label></td>
						<td>
							<select name=\"RTE_EnterKeyTag\" id=\"RTE_EnterKeyTag\">
								<option value=\"\">Default</option>
								<option value=\"P\">P</option>
								<option value=\"BR\">BR</option>
								<option value=\"DIV\">DIV</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_Language\">Language:</label></td>
						<td>
							<select name=\"RTE_Language\" id=\"RTE_Language\">
								<option value=\"\">Auto Detect</option>
								<option value=\"en\">English </option>
								<option value=\"fr-FR\">French</option>
								<option value=\"de-de\">German</option>
								<option value=\"nl-NL\">Dutch</option>
								<option value=\"es-ES\">Spanish</option>
								<option value=\"it-IT\">Italian</option>
								<option value=\"nb-NO\">Norwegian</option>
								<option value=\"ru-RU\">Russian</option>
								<option value=\"ja-JP\">Japanese</option>
								<option value=\"zh-cn\">Chinese</option>
								<option value=\"sv-SE\">Swedish</option>
								<option value=\"pt-BR\">Portuguese</option>
								<option value=\"da\">Danish</option>
								<option value=\"he-IL\">Hebrew</option>
								<option value=\"ar\">Arabic</option>
								<option value=\"cs\">CZech</option>
								<option value=\"tr-TR\">Turkey</option>
								<option value=\"vi\">Vietnam</option>
								<option value=\"th\">Thai</option>
								<option value=\"ko-KR\">Korean</option>
							</select>
						</td>
					</tr>
					<tr>
						<td scope=\"row\"><label for=\"RTE_UrlType\">Url Type:</label></td>
						<td>
							<select name=\"RTE_UrlType\" id=\"RTE_UrlType\">
								<option value=\"\">Default</option>
								<option value=\"absolute\">Absolute URLs</option>
								<option value=\"siterelative\">SiteRelative URLs</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan=\"2\">
							<input type=\"hidden\" name=\"changes_saved\" value=\"1\">
							<input type=\"hidden\" name=\"settings_form\" value=\"1\">
							<input type=\"submit\" class=\"button-primary\" value=\"Save changes\" />
						</td>
					</tr>
					</table>
				</form>
			</div>
		</div>";
		
		echo "<script type=\"text/javascript\">				
				document.getElementById('RTE_Admin_Skin').value='".get_option('RTE_Admin_Skin')."'||'';
				document.getElementById('RTE_Admin_Toolbar').value='".get_option('RTE_Admin_Toolbar')."'||'';	
				document.getElementById('RTE_Site_UseRTE').checked = ('".get_option('RTE_Site_UseRTE')."'||'')==''?false:true;			
				document.getElementById('RTE_Site_Skin').value='".get_option('RTE_Site_Skin')."'||'';
				document.getElementById('RTE_Site_Toolbar').value='".get_option('RTE_Site_Toolbar')."'||'';
				document.getElementById('RTE_Language').value='".get_option('RTE_Language')."'||'';
				document.getElementById('RTE_EnterKeyTag').value='".get_option('RTE_EnterKeyTag')."'||'';
				document.getElementById('RTE_UrlType').value='".get_option('RTE_UrlType')."'||'';
				
			</script>";
		echo "</div>";
	}
	
	public function add_post_editor() {
		
		//$this->remove_tinymce();
		if (has_filter('admin_print_footer_scripts', 'wp_tiny_mce') || has_filter('before_wp_tiny_mce', 'wp_print_editor_js') || has_filter('after_wp_tiny_mce', 'wp_preload_dialogs')) {
			remove_filter('admin_print_footer_scripts', 'wp_tiny_mce', 25);
			remove_filter('before_wp_tiny_mce', 'wp_print_editor_js');
			remove_filter('after_wp_tiny_mce', 'wp_preload_dialogs');
		}
			remove_filter('before_wp_tiny_mce', 'wp_print_editor_js');
			remove_filter('after_wp_tiny_mce', 'wp_preload_dialogs');
		// if W3 Total Cache is enabled, turn off minify for page with CKEditor in comments
		if ( is_plugin_active('w3-total-cache/w3-total-cache.php') ) {
			define('DONOTMINIFY', true);
		}
		echo "<style type='text/css'>.tbcontrol img {margin:0px!important;} 
		.jsml_textbox textarea{padding:0px!important;border-radius:0px!important;-webkit-border-radius:0px!important;width:100%!important; height:100%!important;}</style>\n";
	
		echo "<script type='text/javascript' src=\"".$this->plugin_path . "includes/rte.post.js?t=RTE"."\"></script>\n";		
		
		$rte=new RichTextEditor();
		$rte->Name="RTE_Post";
		$rte->Width = "100%";
		if(strpos($_SERVER["REQUEST_URI"],"edit-comments")>0)
		{
			$rte->Width = "775px";
		}
		
		$rte_width = get_option('RTE_Admin_Width');
		if($rte_width!=null && strlen($rte_width)>0)
		{
			if(strpos($rte_width,"px")>0 || strpos($rte_width,"%")>0)
			{}
			else
			{
				$rte_width.="px";
			}
			$rte->Width = $rte_width;
		}
		$rte_height = get_option('RTE_Admin_Height');
		if($rte_height!=null && strlen($rte_height)>0)
		{
			if(strpos($rte_height,"px")>0 || strpos($rte_height,"%")>0)
			{}
			else
			{
				$rte_height.="px";
			}
			$rte->Height = $rte_height;
		}
		
		$rte_site_skin = get_option('RTE_Admin_Skin');
		$rte_site_toolbar = get_option('RTE_Admin_Toolbar');
		if($rte_site_skin!="")
			$rte->Skin = $rte_site_skin;
		if($rte_site_toolbar!="")
			$rte->Toolbar = $rte_site_toolbar;
		$rte_language = get_option('RTE_Language');
		$rte_enterkeytag = get_option('RTE_EnterKeyTag');
		$rte_urltype = get_option('RTE_UrlType');		
		$rte_textdirction = get_option('RTE_TextDirection');
		$rte_contentcss = get_option('RTE_ContentCss');
		$rte_editorbodystyle = get_option('RTE_EditorBodyStyle');
		if($rte_language!="")
			$rte->Language = $rte_language;
		if($rte_urltype!="")
			$rte->UrlType = $rte_urltype;
		if($rte_enterkeytag!="")
			$rte->EnterKeyTag = $rte_enterkeytag;
		if($rte_textdirction!="")
			$rte->TextDirection = $rte_textdirction;
		if($rte_contentcss!="")
			$rte->ContentCss = $rte_contentcss;
		if($rte_editorbodystyle!="")
			$rte->EditorBodyStyle = $rte_editorbodystyle;
		
		$rtesecurl = "name=".$rte->Name;
		if (get_option('RTE_GalleryPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "imagepath=".urlencode(get_option('RTE_GalleryPath'));
		}
		if (get_option('RTE_VideoPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "videopath=".urlencode(get_option('RTE_VideoPath'));
		}
		if (get_option('RTE_DocumentPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "docpath=".urlencode(get_option('RTE_DocumentPath'));
		}
		if (get_option('RTE_TemplatePath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "temppath=".urlencode(get_option('RTE_TemplatePath'));
		}
		if($rtesecurl != "")
			$rtesecurl = "?".$rtesecurl;
		
		$rte->AjaxPostbackUrl = $this->plugin_path . "includes/wpajaxpost.php".$rtesecurl;
		echo "<div id='RTE_Post_Container' style='display:none;'>".$rte->GetString()."</div>\n";	
		// changed by ken, use to replace wppagelinks.php function
		// the internal link adds in file rte.comment.js and rte.post.js now
		echo "<script>";
		$siteurl = get_site_url ();
		if (is_ssl ()) {
			$siteurl = str_replace ( 'http:', 'https:', $siteurl );
		}
		$args = array (
				'child_of' => 0,
				'sort_order' => 'ASC',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'parent' => - 1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
		);
		$pages = get_pages ( $args );
		$titles;
		$urls;
		foreach ( $pages as $pageitem ) {
			$titles .= $pageitem->post_title;
			$titles .= ";";
			$urls .= get_page_link ( $pageitem->ID );
			$urls .= ";";
		}
		echo "var homeurl=\"$siteurl\";";
		echo "var pagetitles=\"$titles\";";
		echo "var pageurls=\"$urls\";";
		echo "</script>";
		wp_enqueue_script("jquery");
		wp_enqueue_script('rte_editor_post', $this->plugin_path . "includes/rte.post.ready.js?t=RTE");
		wp_enqueue_script('rte_editor_move', $this->plugin_path . "includes/rte.util.js?t=RTE");
	}
	
	public function add_comment_editor() {
		if (!(is_page() || is_single())) {
			return;
		}
		if(get_option('RTE_Site_UseRTE')==null){
			return;
		}
		// if W3 Total Cache is enabled, turn off minify for page with CKEditor in comments
		if ( is_plugin_active('w3-total-cache/w3-total-cache.php') ) {
			define('DONOTMINIFY', true);
		}
		echo "<style type='text/css'>.tbcontrol img {margin:0px!important;} 
		.jsml_textbox textarea{padding:0px!important;border-radius:0px!important;-webkit-border-radius:0px!important;width:100%!important; height:100%!important;}</style>\n";
	
		echo "<script type='text/javascript' src=\"".$this->plugin_path . "includes/rte.comment.js?t=RTE"."\"></script>\n";		
		
		$rte=new RichTextEditor();
		$rte->Name="RTE_Comment";
		$rte->Width = "100%";
		$rte->Toolbar = "full";
		
		$rte_width = get_option('RTE_Site_Width');
		if($rte_width!=null && strlen($rte_width)>0)
		{
			if(strpos($rte_width,"px")>0 || strpos($rte_width,"%")>0)
			{}
			else
			{
				$rte_width.="px";
			}
			$rte->Width = $rte_width;
		}
		$rte_height = get_option('RTE_Site_Height');
		if($rte_height!=null && strlen($rte_height)>0)
		{
			if(strpos($rte_height,"px")>0 || strpos($rte_height,"%")>0)
			{}
			else
			{
				$rte_height.="px";
			}
			$rte->Height = $rte_height;
		}
		
		$rte_site_skin = get_option('RTE_Site_Skin');
		$rte_site_toolbar = get_option('RTE_Site_Toolbar');
		if($rte_site_skin!="")
			$rte->Skin = $rte_site_skin;
		if($rte_site_toolbar!="")
			$rte->Toolbar = $rte_site_toolbar;
		$rte_language = get_option('RTE_Language');
		$rte_enterkeytag = get_option('RTE_EnterKeyTag');
		$rte_urltype = get_option('RTE_UrlType');		
		$rte_textdirction = get_option('RTE_TextDirection');
		$rte_contentcss = get_option('RTE_ContentCss');
		$rte_editorbodystyle = get_option('RTE_EditorBodyStyle');
		if($rte_language!="")
			$rte->Language = $rte_language;
		if($rte_urltype!="")
			$rte->UrlType = $rte_urltype;
		if($rte_enterkeytag!="")
			$rte->EnterKeyTag = $rte_enterkeytag;
		if($rte_textdirction!="")
			$rte->TextDirection = $rte_textdirction;
		if($rte_contentcss!="")
			$rte->ContentCss = $rte_contentcss;
		if($rte_editorbodystyle!="")
			$rte->EditorBodyStyle = $rte_editorbodystyle;
		
		$rtesecurl = "name=".$rte->Name;
		if (get_option('RTE_GalleryPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "imagepath=".urlencode(get_option('RTE_GalleryPath'));
		}
		if (get_option('RTE_VideoPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "videopath=".urlencode(get_option('RTE_VideoPath'));
		}
		if (get_option('RTE_DocumentPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "docpath=".urlencode(get_option('RTE_DocumentPath'));
		}
		if (get_option('RTE_TemplatePath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "temppath=".urlencode(get_option('RTE_TemplatePath'));
		}
		if($rtesecurl != "")
			$rtesecurl = "?".$rtesecurl;
		
		$rte->AjaxPostbackUrl = $this->plugin_path . "includes/wpajaxpost.php".$rtesecurl;
		echo "<div id='RTE_Comment_Container' style='display:none;'>".$rte->GetString()."</div>\n";	
		// changed by ken, use to replace wppagelinks.php function
		// the internal link adds in file rte.comment.js and rte.post.js now
		echo "<script>";
		$siteurl = get_site_url ();
		if (is_ssl ()) {
			$siteurl = str_replace ( 'http:', 'https:', $siteurl );
		}
		$args = array (
				'child_of' => 0,
				'sort_order' => 'ASC',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'parent' => - 1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
		);
		$pages = get_pages ( $args );
		$titles;
		$urls;
		foreach ( $pages as $pageitem ) {
			$titles .= $pageitem->post_title;
			$titles .= ";";
			$urls .= get_page_link ( $pageitem->ID );
			$urls .= ";";
		}
		echo "var homeurl=\"$siteurl\";";
		echo "var pagetitles=\"$titles\";";
		echo "var pageurls=\"$urls\";";
		echo "</script>";
		wp_enqueue_script("jquery");
		wp_enqueue_script('rte_editor_comment', $this->plugin_path . "includes/rte.comment.ready.js?t=RTE");
		wp_enqueue_script('rte_editor_move', $this->plugin_path . "includes/rte.util.js?t=RTE");
		
	}
	
	/**
	 * This function disables TinyMCE and sets {@link $bUseFCK} to true or false depending on which page is loaded
	 */
	function KillTinyMCE( $in ){
		global $current_user;

		if ( 'true' == $current_user->rich_editing && strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false && strpos($_SERVER['REQUEST_URI'], 'wp-admin/profile.php') === false ){
			$current_user->rich_editing = 'false';
		}
		return $in;
	}
	
	function admin_print_footer_scripts() {     		
		if( $this->loading ) {
			remove_action( 'admin_print_footer_scripts', array( '_WP_Editors', 'editor_js'), 50 );
			remove_action( 'admin_footer', array( '_WP_Editors', 'enqueue_scripts'), 1 );  
		}
	}
	
	function check_featured_image_capability() {
		$uploads = wp_upload_dir();  			
		$domain = preg_replace( '~^(.*?//.*?)/.*$~', '$1', get_bloginfo('url' ) );
		$wp_uploads = str_replace( $domain, '', $uploads['baseurl'] );

		if( current_theme_supports('post-thumbnails') && rtrim( $wp_uploads, '/' ) == rtrim( $this->aOptions["images"], '/' ) ) {
			$this->process_featured_images = true; 
		}	 
	}
	
	
	protected function update_options($data)
	{
		if (isset($_GET['page']))
		{
			if($_GET['page'] =="rte_basic_options")
			{
				update_option('RTE_Admin_Width', isset($data['RTE_Admin_Width']) ? $data['RTE_Admin_Width'] : "");
				update_option('RTE_Admin_Height', isset($data['RTE_Admin_Height']) ? $data['RTE_Admin_Height'] : "");
				update_option('RTE_Admin_Skin', isset($data['RTE_Admin_Skin']) ? $data['RTE_Admin_Skin'] : "");
				update_option('RTE_Admin_Toolbar', isset($data['RTE_Admin_Toolbar']) ? $data['RTE_Admin_Toolbar'] : "");
				update_option('RTE_Site_UseRTE', isset($data['RTE_Site_UseRTE']) ? $data['RTE_Site_UseRTE'] : "");
				update_option('RTE_Site_Width', isset($data['RTE_Site_Width']) ? $data['RTE_Site_Width'] : "");
				update_option('RTE_Site_Height', isset($data['RTE_Site_Height']) ? $data['RTE_Site_Height'] : "");
				update_option('RTE_Site_Skin', isset($data['RTE_Site_Skin']) ? $data['RTE_Site_Skin'] : "");
				update_option('RTE_Site_Toolbar', isset($data['RTE_Site_Toolbar']) ? $data['RTE_Site_Toolbar'] : "");
				update_option('RTE_Language', isset($data['RTE_Language']) ? $data['RTE_Language'] : "");
				update_option('RTE_EnterKeyTag', isset($data['RTE_EnterKeyTag']) ? $data['RTE_EnterKeyTag'] : "");
				update_option('RTE_UrlType', isset($data['RTE_UrlType']) ? $data['RTE_UrlType'] : "");
			}
			
			if($_GET['page'] =="rte_advance_options")
			{
				update_option('RTE_TextDirection', isset($data['RTE_TextDirection']) ? $data['RTE_TextDirection'] : "");
				update_option('RTE_ContentCss', isset($data['RTE_ContentCss']) ? $data['RTE_ContentCss'] : "");
				update_option('RTE_EditorBodyStyle', isset($data['RTE_EditorBodyStyle']) ? $data['RTE_EditorBodyStyle'] : "");
				update_option('RTE_GalleryPath', isset($data['RTE_GalleryPath']) ? $data['RTE_GalleryPath'] : "");
				update_option('RTE_VideoPath', isset($data['RTE_VideoPath']) ? $data['RTE_VideoPath'] : "");
				update_option('RTE_DocumentPath', isset($data['RTE_DocumentPath']) ? $data['RTE_DocumentPath'] : "");
				update_option('RTE_TemplatePath', isset($data['RTE_TemplatePath']) ? $data['RTE_TemplatePath'] : "");
			}
		}

		//if (isset($data['changes_saved']) && $data['changes_saved'] == '1')
		//{
		//	$this->changes_saved = true;
		//}		
	}
}

final class _WP_Editors {

	public static function editor_settings($editor_id, $set) {

	}

	public static function parse_settings($editor_id, $settings) {
		$set = wp_parse_args($settings, array(
			'wpautop' => true, // use wpautop?
			'media_buttons' => true, // show insert/upload button(s)
			'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
			'tabindex' => '',
			'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
			'editor_class' => '', // add extra class(es) to the editor textarea
			'teeny' => false, // output the minimal editor config used in Press This
			'dfw' => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)
			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		));

		$set['tinymce'] = ( $set['tinymce'] && user_can_richedit() );
		$set['quicktags'] = (bool) $set['quicktags'];

		return $set;
	}

	public static function editor($content, $editor_id, $settings = array()) {
		$set = self::parse_settings($editor_id, $settings);
		
		$rte=new RichTextEditor();
		$rte->Name=$editor_id;		
		if($content!=null)
			$rte->LoadFormData(html_entity_decode($content));
		$rte->Width="100%";
		$rte->Skin="office2010silver";
		$rte->DisabledItems = "save";
		
		$rte_width = get_option('RTE_Admin_Width');
		if($rte_width!=null && strlen($rte_width)>0)
		{
			if(strpos($rte_width,"px")>0 || strpos($rte_width,"%")>0)
			{}
			else
			{
				$rte_width.="px";
			}
			$rte->Width = $rte_width;
		}
		$rte_height = get_option('RTE_Admin_Height');
		if($rte_height!=null && strlen($rte_height)>0)
		{
			if(strpos($rte_height,"px")>0 || strpos($rte_height,"%")>0)
			{}
			else
			{
				$rte_height.="px";
			}
			$rte->Height = $rte_height;
		}
		
		$rte_admin_skin = get_option('RTE_Admin_Skin');
		$rte_admin_toolbar = get_option('RTE_Admin_Toolbar');
		if($rte_admin_skin!=null && strlen($rte_admin_skin)>0)
		{
			$rte->Skin = $rte_admin_skin;
		}
		if($rte_admin_toolbar!=null && strlen($rte_admin_toolbar)>0)
		{
			$rte->Toolbar = $rte_admin_toolbar;
		}
		$rte_language = get_option('RTE_Language');
		$rte_enterkeytag = get_option('RTE_EnterKeyTag');
		$rte_urltype = get_option('RTE_UrlType');		
		$rte_textdirction = get_option('RTE_TextDirection');
		$rte_contentcss = get_option('RTE_ContentCss');
		$rte_editorbodystyle = get_option('RTE_EditorBodyStyle');
		if($rte_language!="")
			$rte->Language = $rte_language;
		if($rte_urltype!="")
			$rte->UrlType = $rte_urltype;
		if($rte_enterkeytag!="")
			$rte->EnterKeyTag = $rte_enterkeytag;
		if($rte_textdirction!="")
			$rte->TextDirection = $rte_textdirction;
		if($rte_contentcss!="")
			$rte->ContentCss = $rte_contentcss;
		if($rte_editorbodystyle!="")
			$rte->EditorBodyStyle = $rte_editorbodystyle;
		
		$rtesecurl = "name=".$rte->Name;
		if (get_option('RTE_GalleryPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "imagepath=".urlencode(get_option('RTE_GalleryPath'));
		}
		if (get_option('RTE_VideoPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "videopath=".urlencode(get_option('RTE_VideoPath'));
		}
		if (get_option('RTE_DocumentPath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "docpath=".urlencode(get_option('RTE_DocumentPath'));
		}
		if (get_option('RTE_TemplatePath') != '')
		{
			if($rtesecurl!="")
				$rtesecurl .="&";
			$rtesecurl .= "temppath=".urlencode(get_option('RTE_TemplatePath'));
		}
		if($rtesecurl != "")
			$rtesecurl = "?".$rtesecurl;
			
		$plugin_path = "";
		if (DEFINED('WP_PLUGIN_URL')) {
			$plugin_path = WP_PLUGIN_URL . '/' . basename(dirname(__FILE__)) . '/';
		} else if (DEFINED('WP_PLUGIN_DIR')) {
			$plugin_path = $siteurl . '/' . WP_PLUGIN_DIR . '/' . basename(dirname(__FILE__)) . '/';
		} else {
			$plugin_path = $siteurl . 'wp-content/plugins/' . basename(dirname(__FILE__)) . '/';
		}
		if (is_ssl()) {
			$siteurl = str_replace('http:', 'https:', $siteurl);
			$plugin_path = str_replace('http:', 'https:', $this->plugin_path);
		}
		
		//$rte->ContentCss = get_stylesheet_directory_uri()."/css/editor-style.css";

		$rte->ContentCss = $plugin_path . "richtexteditor/wp-content.css";

		
		$rte->AjaxPostbackUrl = $plugin_path . "includes/wpajaxpost.php".$rtesecurl;
		echo "<style type='text/css'>.jsml_textbox textarea{padding:0px!important;border-radius:0px!important;-webkit-border-radius:0px!important;width:100%!important; height:100%!important;}</style>\n";
	
		echo "<script type='text/javascript' src=\"".$plugin_path . "includes/rte.comment.js?t=RTE"."\"></script>\n";	
		echo $rte->GetString();
		// changed by ken, use to replace wppagelinks.php function
		// the internal link adds in file rte.comment.js and rte.post.js now
		echo "<script>";
		$siteurl = get_site_url ();
		if (is_ssl ()) {
			$siteurl = str_replace ( 'http:', 'https:', $siteurl );
		}
		$args = array (
				'child_of' => 0,
				'sort_order' => 'ASC',
				'sort_column' => 'post_title',
				'hierarchical' => 1,
				'exclude' => '',
				'include' => '',
				'meta_key' => '',
				'meta_value' => '',
				'authors' => '',
				'parent' => - 1,
				'exclude_tree' => '',
				'number' => '',
				'offset' => 0,
				'post_type' => 'page',
				'post_status' => 'publish'
		);
		$pages = get_pages ( $args );
		$titles;
		$urls;
		foreach ( $pages as $pageitem ) {
			$titles .= $pageitem->post_title;
			$titles .= ";";
			$urls .= get_page_link ( $pageitem->ID );
			$urls .= ";";
		}
		echo "var homeurl=\"$siteurl\";";
		echo "var pagetitles=\"$titles\";";
		echo "var pageurls=\"$urls\";";
		echo "</script>";
		wp_enqueue_script("jquery");
		wp_enqueue_script('rte_editor_move', $plugin_path . "includes/rte.util.js?t=RTE");
		
	}

	public static function wp_link_query( $args = array() ) {
		$pts = get_post_types( array( 'public' => true ), 'objects' );
		$pt_names = array_keys( $pts );

		$query = array(
			'post_type' => $pt_names,
			'suppress_filters' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'post_status' => 'publish',
			'order' => 'DESC',
			'orderby' => 'post_date',
			'posts_per_page' => 20,
		);

		$args['pagenum'] = isset( $args['pagenum'] ) ? absint( $args['pagenum'] ) : 1;

		if ( isset( $args['s'] ) )
			$query['s'] = $args['s'];

		$query['offset'] = $args['pagenum'] > 1 ? $query['posts_per_page'] * ( $args['pagenum'] - 1 ) : 0;

		// Do main query.
		$get_posts = new WP_Query;
		$posts = $get_posts->query( $query );
		// Check if any posts were found.
		if ( ! $get_posts->post_count )
			return false;

		// Build results.
		$results = array();
		foreach ( $posts as $post ) {
			if ( 'post' == $post->post_type )
				$info = mysql2date( __( 'Y/m/d' ), $post->post_date );
			else
				$info = $pts[ $post->post_type ]->labels->singular_name;

			$results[] = array(
				'ID' => $post->ID,
				'title' => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
				'permalink' => get_permalink( $post->ID ),
				'info' => $info,
			);
		}

		return $results;
	}

}

$rte_editor = richtexteditor_wordpress_class::get_instance();

?>