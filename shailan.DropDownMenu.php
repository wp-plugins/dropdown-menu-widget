<?php 
/*
Plugin Name: Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages, categories & navigation menus. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Tags: dropdown, menu, css, css-dropdown, navigation, widget, dropdown-menu, customization, theme
Version: 1.6.2
Author: Matt Say
Author URI: http://shailan.com
Text Domain: shailan-dropdown-menu
*/

define('SHAILAN_DM_VERSION','1.6.2');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');
define('SHAILAN_DM_FOLDER', 'dropdown-menu-widget');

/******************************************************************************
*  Shailan Dropdown Widget Class
******************************************************************************/

class shailan_DropdownWidget extends WP_Widget {

	function shailan_DropdownWidget(){
		global $pluginname, $pluginshortname, $pluginoptions;
		
		$widget_ops = array('classname' => 'shailan-dropdown-menu-widget', 'description' => __( 'Dropdown page/category menu', 'shailan-dropdown-menu' ) );
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu', 'shailan-dropdown-menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		$this->pluginname = "Dropdown Menu";
		$this->shortname = "shailan_dm";
		
		// Hook up styles
		add_action( 'wp_head', array(&$this, 'header') );
		add_action( 'wp_footer', array(&$this, 'footer'), 10, 1 );			
		
		// Hook up scripts 
		if(!is_admin()){ 
			wp_enqueue_script( 'jquery' ); 
			wp_enqueue_script( 'dropdown-ie-support', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/js/include.js', array('jquery') ); 
		}
		
		// Define themes
		$available_themes = array(
			'None' => '*none*',
			'From URL' => '*url*',
			'Custom CSS' => '*custom*',
			'Color Scheme' => 'color-scheme',
			'Simple White' => plugins_url('/themes/simple.css', __FILE__),
			'Wordpress Default' => plugins_url('/themes/wpdefault.css', __FILE__),
			'Grayscale' => plugins_url('/themes/grayscale.css', __FILE__),
			'Aqua' => plugins_url('/themes/aqua.css', __FILE__),
			'Blue gradient' => plugins_url('/themes/simple-blue.css', __FILE__),
			'Shiny Black' => plugins_url('/themes/shiny-black.css', __FILE__),
			'Flickr theme' =>  plugins_url('/themes/flickr.com/default.ultimate.css', __FILE__),
			'Nvidia theme' =>  plugins_url('/themes/nvidia.com/default.advanced.css', __FILE__),
			'Adobe theme' => plugins_url('/themes/adobe.com/default.advanced.css', __FILE__),
			'MTV theme' =>  plugins_url('/themes/mtv.com/default.ultimate.css', __FILE__),
			'Hulu theme' =>  plugins_url('/themes/hulu/hulu.css', __FILE__)
		);
		
		// Check for theme style file
		if( file_exists( trailingslashit( get_template_directory() ) . 'dropdown.css') ){
			$available_themes['Dropdown.css (theme)'] = get_template_directory_uri() . '/dropdown.css';
		}
		
		// Swap array for options page
		$themes = array();
		while(list($Key,$Val) = each($available_themes))
			$themes[$Val] = $Key;
		
		$overlays = array(
			'none'=>'none',
			'glassy'=>'glassy',
			'flat'=>'flat',
			'shadow'=>'shadow',
			'soft' =>'soft'
		);
		
		$alignment = array('left'=>'left', 'center' => 'center', 'right'=> 'right');
		$types = array('pages'=>'Pages', 'categories'=>'Categories');
		$effects = array('fade'=>'Fade In/Out', 'slide'=>'Slide Up/Down'/*, 'fade2'=>'Fade In/Out Moving Up'*/);
		$speed = array('400'=>'Normal', 'fast'=>'Fast', 'slow'=>'Slow');
		
		if( function_exists('wp_nav_menu') ){
			// Get available menus
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
			
			$navmenus = array();
			
			if($menus){
				foreach( $menus as $menu ){
					$navmenus[ 'navmenu_' . $menu->term_id ] = $menu->name;
				}
			}
			
			// Merge type with menu array
			$types = array_merge($types, $navmenus);
		}
		
		$this->menu_types = $types; // Back it up
		
		// Define plugin options	
		$this->admin_options = array(
			
			array(
				"name" => "Menu options",
				"type" => "section"
			),
		
			array(  "name" => "Dropdown Menu Theme",
			"desc" => "Skin for the menu",
			"id" => "shailan_dm_active_theme",
			"std" => "None",
			"options" => $themes,
			"type" => "select"),
			
			array(  "name" => "Theme URL",
			"desc" => "If From URL is selected you can specify theme URL here. <br />Warning : Please make sure this file only modifies dropdown theme. Otherwise your theme may be affected by this css file.",
			"id" => "shailan_dm_theme_url",
			"std" => "http://",
			"type" => "text"),
			
			array(  "name" => "Rename Homepage",
			"desc" => "You can change your homepage link here",
			"id" => "shailan_dm_home_tag",
			"std" => __("Home"),
			"type" => "text"),
			
			array("type" => "splitter"),
			
			array(  "name" => "Enable dropdown effects",
			"desc" => "If checked sub menus will use effects below",
			"id" => "shailan_dm_effects",
			"type" => "checkbox"),
			
			array(  "name" => "Effect",
			"desc" => "Select effect you want to use",
			"id" => "shailan_dm_effect",
			"type" => "select",
			"options" => $effects ),
			
			array(  "name" => "Effect Speed",
			"desc" => "Select effect speed",
			"id" => "shailan_dm_effect_speed",
			"type" => "select",
			"options" => $speed ),
			
			array("type" => "splitter"),
			
			array(  "name" => "Wrap long menu items",
			"desc" => "If checked long menu items will wrap",
			"id" => "shailan_dm_allowmultiline",
			"type" => "checkbox"),
			
			array(  "name" => "Remove title attributes from menu items",
			"desc" => "This will remove 'View all posts under..' title attributes from menu links",
			"id" => "shailan_dm_remove_title_attributes",
			"type" => "checkbox"),
			
			array(  "name" => "Remove links from top levels",
			"desc" => "This will remove links from top level pages/categories. So user can only click to sub-level menu.",
			"id" => "shailan_dm_remove_top_level_links",
			"type" => "checkbox"),
			
			array( "type" => "close" ),
			
			array(
				"name" => "Color Scheme (beta)",
				"type" => "section"
			),
			
			array(  "name" => "Use custom colors",
			"desc" => "If not checked custom colors won't work.",
			"id" => "shailan_dm_custom_colors",
			"std" => true,
			"type" => "checkbox"),
			
			array("type"=>"picker"),
			
			array(  "name" => "Menu Background Color",
			"desc" => "Background color of the dropdown menu",
			"id" => "shailan_dm_color_menubg",
			"std" => '#000000',
			"type" => "text"),
			
			array(  "name" => "Hover Background Color",
			"desc" => "Background color of list item link.",
			"id" => "shailan_dm_color_lihover",
			"std" => '#333333',
			"type" => "text"),
			
			array(  "name" => "Link Text Color",
			"desc" => "Default link color",
			"id" => "shailan_dm_color_link",
			"std" => '#FFFFFF',
			"type" => "text"),
			
			array(  "name" => "Link Text Color on mouse over",
			"desc" => "Secondary link color",
			"id" => "shailan_dm_color_hoverlink",
			"std" => '#FFFFFF',
			"type" => "text"),
			
			array(  "name" => "Overlay",
			"desc" => "Menu overlay (Works on browsers that support png transparency only.)",
			"id" => "shailan_dm_overlay",
			"std" => "glass",
			"type" => "select",
			"options" => $overlays ),
			
			array( "type" => "close" ),
			
			array(
				"name" => "Advanced Options",
				"type" => "section"
			),
			
			array(  "name" => "Dropdown Menu Font",
			"desc" => "Font family for the menu<br />Please leave blank to use your wordpress theme font.",
			"id" => "shailan_dm_font",
			"std" => '',
			"type" => "text"),
			
			array(  "name" => "Dropdown Menu Font Size",
			"desc" => "Font size of the menu items (Eg: 12px OR 1em) <br />Please leave blank to use your wordpress theme font-size.",
			"id" => "shailan_dm_fontsize",
			"std" => '',
			"type" => "text"),
			
			array(  "name" => "Custom css",
			"desc" => "You can paste your own customization file here.",
			"id" => "shailan_dm_custom_css",
			"std" => '',
			"type" => "textarea"),
			
			array(  "name" => "Show Empty Categories",
			"desc" => "If checked categories with no posts will be shown.",
			"id" => "shailan_dm_show_empty",
			"std" => false,
			"type" => "checkbox"),
			
			array( "type" => "close" ),
			
			array(
				"name" => "Template Tag Options",
				"type" => "section"
			),
			
			array(
				"desc" => "Settings here only effect menus inserted with template tag : <code>&lt;?php shailan_dropdown_menu(); ?&gt;</code>. Widget settings are NOT affected by these settings. ",
				"type" => "paragraph"
			),
			
			array(  "name" => "Menu Type",
			"desc" => "Dropdown Menu Type",
			"id" => "shailan_dm_type",
			"std" => "pages",
			"options" => $types,
			"type" => "select"),
			
			array(  "name" => "Home link",
			"desc" => "If checked dropdown menu displays home link",
			"id" => "shailan_dm_home",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Login",
			"desc" => "If checked dropdown menu displays login link",
			"id" => "shailan_dm_login",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Register / Site Admin",
			"desc" => "If checked dropdown menu displays register/site admin link.",
			"id" => "shailan_dm_login",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Vertical menu",
			"desc" => "If checked dropdown menu is displayed vertical.",
			"id" => "shailan_dm_vertical",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Exclude Pages",
			"desc" => "Excluded page IDs.",
			"id" => "shailan_dm_exclude",
			"std" => "",
			"type" => "text"),
			
			array(  "name" => "Alignment",
			"desc" => "Menu alignment.",
			"id" => "shailan_dm_align",
			"std" => "left",
			"options" => $alignment,
			"type" => "select"),
			
			array( "type" => "close" )
			
		);
		
		$this->defaults = array(
			'title' => '',
			'type' => 'pages',
			'exclude' => '',
			'home' => false,
			'login' => false,
			'admin' => false,
			'vertical' => false,
			'align' => 'left',
			'theme' => 'none',
			'show_title' => false
		);
		
		$pluginname = $this->pluginname;
		$pluginshortname = $this->shortname;
		$pluginoptions = $this->admin_options;
		
		/** Unused options */
		update_option('shailan_dm_customwalkers', false);
	}
	
/******************************************************************************
*  OPTIONS
******************************************************************************/
	function adminMenu(){
		global $pluginname, $pluginshortname, $pluginoptions;
		
		if( is_admin() ){ 
			wp_enqueue_style( 'dropdown-widget-styles', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/css/widgets.css' );
			wp_enqueue_script( 'dropdown-widgets', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/js/widgets.js', array('jquery') );
		};
 
		if ( isset($_GET['page']) && $_GET['page'] == 'dropdown-menu' ) {
		
			// Styles
			wp_admin_css( 'widgets' ); 
			wp_enqueue_style('farbtastic'); 
			wp_enqueue_style( 'dropdownMenuStyles', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/css/admin.css' );
			
			// Scripts
			wp_enqueue_script( 'admin-widgets' ); 
			wp_enqueue_script( 'farbtastic' ); 
			wp_enqueue_script( 'dropdown-colorpick', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/js/admin.js', array('jquery') );
		
			if ( @$_REQUEST['action'] && 'save' == $_REQUEST['action'] ) {
		 
				foreach ($pluginoptions as $value) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
		 
				foreach ($pluginoptions as $value) {
					if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
			 
				header("Location: admin.php?page=dropdown-menu&saved=true");
			die;
			 
			} 
		
		}
	
		if (function_exists('add_options_page')) {
			$page = add_options_page(__('Settings for Dropdown Menu', 'shailan-dropdown-menu') , __('Dropdown Menu', 'shailan-dropdown-menu'), 'edit_themes', 'dropdown-menu', array('shailan_DropdownWidget', 'getOptionsPage'));
			add_action('admin_print_styles-' . $page, array('shailan_DropdownWidget', 'header'));
		}
	}
	
	// Render options page
	function getOptionsPage(){	
		global $pluginname, $pluginshortname, $pluginoptions;
		$title = __('Dropdown Menu Options');
		include_once('options-page.php'); 
	}
	
/******************************************************************************
*  the WIDGET 
******************************************************************************/
    function widget($args, $instance) {		
        extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		// On and off
		$show_title = (bool) $show_title;		
		$orientation = ($vertical ? 'dropdown-vertical' : 'dropdown-horizontal');
		$custom_walkers = false; // (bool) get_option('shailan_dm_customwalkers'); disabled
		$show_empty = (bool) get_option('shailan_dm_show_empty');
		
        echo $args['before_widget']; 
		
		// Show title if option checked
		if ( $title && $show_title ){ echo $before_title . $title . $after_title; }
		
		$nl = "\n"; $indent = "\n\t"; $indent2 = "\n\t\t";
		
		echo $nl . "<!-- Dropdown Menu Widget by shailan (http://shailan.com)  v".SHAILAN_DM_VERSION." on wp".get_bloginfo( 'version' )." -->";
		echo $nl . "<!-- Menu Type : " . $type . " -->";
		echo $nl . "<div class=\"shailan-dropdown-menu\">";
			
			$dropdown_wrapper_open = $nl . '<div id="shailan-dropdown-wrapper-' . $this->number . '" >';
					
			$dropdown_open = $indent . '<div align="' . $align . '" class="'.$orientation.'-container dm-align-'.$align.'"><table cellpadding="0" cellspacing="0"><tr><td>';
			$list_open = $indent2 . '<ul id="dropdown-'. $this->number .'" class="dropdown dropdown-'. $this->number .' '. $orientation . ' dropdown-align-'.$align.'">' . $nl . "<!-- Menu elements start -->\n";
			
			if($home && ($type == 'pages' || $type == 'categories')){ 
			
						$home_item = $nl . '<li class="page_item cat-item blogtab '. (is_front_page() && !is_paged() ? 'current_page_item current-cat' : '' ) . '">
							<a href="'.get_option('home').'">';

						$home_tag = get_option('shailan_dm_home_tag'); 
						if(empty($home_tag)){ $home_tag = __('Home'); }
						
						$home_item .= $home_tag;
						$home_item .= '</a></li>';
						
						$list_open .= $home_item;
			}
					
			$list_close = ($admin ? wp_register('<li class="admintab">','</li>', false) : '') . ($login ? '<li class="page_item">'. wp_loginout('', false) . '</li>' : '')  . '
					</ul>';
			$dropdown_close = '</td>
				  </tr></table> 
				</div>';
					
			$dropdown_wrapper_close = '</div> ';
								
			$menu_defaults = array(
				'ID' => $this->number,
				'sort_column' => 'menu_order, post_title',
				'order_by' => 'name',
				'depth' => '4',
				'title_li' => '',
				'exclude' => $exclude
			);
			
			$menu_defaults = apply_filters( 'dropdown_menu_defaults', $menu_defaults );
			
			switch ( $type ) {

				/** Pages menu */
				case "pages": 
				
				if($custom_walkers){
					$page_walker = new shailan_PageWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$page_walker) , $menu_defaults ); }
					
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					echo $list_open;
					  do_action('dropdown_list_before');
					  wp_list_pages($menu_defaults);
					  do_action('dropdown_list_after');
					echo $list_close;
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;
				
				break; 
				
				/** Categories menu */
				case "categories": 
				
				if($custom_walkers){ 
					$cat_walker = new shailan_CategoryWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$cat_walker) , $menu_defaults ); }
					
					if($show_empty){$menu_defaults = wp_parse_args( array('hide_empty'=>'0') , $menu_defaults ); }
				
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					echo $list_open;
					  do_action('dropdown_list_before');
					  wp_list_categories($menu_defaults); 
					  do_action('dropdown_list_after');
					echo $list_close;
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;

				break;
				
				/** WP3 Nav menu */
				default:
					
					$location = '';
					$menu = '';
					
				
					// Replace navmenu_
					if( FALSE !== strpos( $type, 'navmenu_' ) ){
						$menu_id = str_replace( 'navmenu_', '', $type );
					}
					
					// Check if a menu exists with this id
					$menu = wp_get_nav_menu_object( $menu_id );
					if( $menu ){ $menu = $menu_id; }
					
					// Is that a location?
					if ( ! $menu && ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_id ] ) ){
						$location = $menu_id;
						$menu = '';
					}
						
					$menu_args = array(
					  'menu'            => $menu, 
					  'container'       => false, 
					  'container_class' => '', 
					  'container_id'    => '', 
					  'menu_class'      => 'dropdown '. $orientation . ' dropdown-align-'.$align, 
					  'menu_id'         => '',
					  'echo'            => true,
					  'fallback_cb'     => 'wp_page_menu',
					  'before'          => '',
					  'after'           => '',
					  'link_before'     => '',
					  'link_after'      => '',
					  'depth'           => 0,
					  'walker'          => '',
					  'theme_location'  => $location );
					  
				if($custom_walkers){
					$page_walker = new shailan_PageWalker();
					$menu_args = wp_parse_args( array('walker'=>$page_walker) , $menu_args ); }
					
					echo $dropdown_wrapper_open;
					do_action('dropdown_before');
					echo $dropdown_open;
					  wp_nav_menu($menu_args);
					echo $dropdown_close;
					do_action('dropdown_after');
					echo $dropdown_wrapper_close;
					
				} // switch ($type)

			echo $nl . "</div>";
			echo "\n\n<!--/ Dropdown Menu Widget -->";		?>
			
              <?php echo $after_widget; ?>
        <?php
    }
	
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }
	
/******************************************************************************
*  WIDGET FORM
******************************************************************************/
    function form($instance) {	
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$show_title = (bool) $show_title;
		$home = (bool) $home;
		$login = (bool) $login;
		$admin = (bool) $admin;
		$vertical = (bool) $vertical;
		
        ?>		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>"<?php checked( $show_title ); ?> />
		<label for="<?php echo $this->get_field_id('show_title'); ?>"><?php _e( 'Show widget title' , 'shailan-dropdown-menu' ); ?></label><br />
			
		<p><label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Menu:'); ?>
		<select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>">
		<?php foreach ($this->menu_types as $key=>$option) { ?>
				<option <?php if ($type == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key; ?>"><?php echo $option; ?></option><?php } ?>
		</select>
		</label></p>
			
		<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small>Page IDs, separated by commas.</small></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('home'); ?>" name="<?php echo $this->get_field_name('home'); ?>"<?php checked( $home ); ?> />
		<label for="<?php echo $this->get_field_id('home'); ?>"><?php _e( 'Add homepage link' , 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $this->get_field_id('login'); ?>"><?php _e( 'Add login/logout' , 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('admin'); ?>" name="<?php echo $this->get_field_name('admin'); ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $this->get_field_id('admin'); ?>"><?php _e( 'Add Register/Site Admin' , 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('vertical'); ?>" name="<?php echo $this->get_field_name('vertical'); ?>"<?php checked( $vertical ); ?> />
		<label for="<?php echo $this->get_field_id('vertical'); ?>"><?php _e( 'Vertical menu' , 'shailan-dropdown-menu' ); ?></label>
		</p>
		
		<p><?php _e('Align:', 'shailan-dropdown-menu'); ?> <label for="left"><input type="radio" id="left" name="<?php echo $this->get_field_name('align'); ?>" value="left" <?php if($align=='left'){ echo 'checked="checked"'; } ?> /> <?php _e('Left', 'shailan-dropdown-menu'); ?></label> <label for="center"><input type="radio" id="center" name="<?php echo $this->get_field_name('align'); ?>" value="center" <?php if($align=='center'){ echo 'checked="checked"'; } ?>/> <?php _e('Center', 'shailan-dropdown-menu'); ?></label> <label for="right"><input type="radio" id="right" name="<?php echo $this->get_field_name('align'); ?>" value="right" <?php if($align=='right'){ echo 'checked="checked"'; } ?>/> <?php _e('Right', 'shailan-dropdown-menu'); ?></label></p>
			
		<div class="widget-control-actions alignright">
		<p><small><a href="options-general.php?page=dropdown-menu"><?php esc_attr_e('Menu Style', 'shailan-dropdown-menu'); ?></a> | <a href="http://shailan.com/wordpress/plugins/dropdown-menu"><?php esc_attr_e('Visit plugin site', 'shailan-dropdown-menu'); ?></a></small></p>
		</div>
		<br class="clear" />
			
        <?php 
	}
	
/******************************************************************************
*  HEADER
******************************************************************************/
	function header($instance){
		global $pluginname, $pluginshortname, $pluginoptions;
		if(!is_admin()){
		
			$theme = get_option('shailan_dm_active_theme');
			if($theme == '*url*'){ $theme = get_option('shailan_dm_theme_url'); }
			
			$allow_multiline = (bool) get_option('shailan_dm_allowmultiline');
			
			// Colors
			$custom_colors = (bool) get_option('shailan_dm_custom_colors');
			$shailan_dm_color_menubg = get_option('shailan_dm_color_menubg');
			$shailan_dm_color_lihover = get_option('shailan_dm_color_lihover');
			$shailan_dm_color_link = get_option('shailan_dm_color_link');
			$shailan_dm_color_hoverlink = get_option('shailan_dm_color_hoverlink');
			
			$is_fx_active = (bool) get_option('shailan_dm_effects');
			
			echo "\n\n<!-- Dropdown Menu Widget Styles by shailan (http://shailan.com) v".SHAILAN_DM_VERSION." on wp".get_bloginfo( 'version' )." -->"; // For debug
			echo "\n<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/css/shailan-dropdown.css\" type=\"text/css\" />";
			
			
			
			if( $theme!='*none*' && $theme != '*custom*' ){
				if( false === strpos($theme, 'http') ){
					// Default
					echo "\n<link rel=\"stylesheet\" href=\"". plugins_url( '/themes/' . $theme . '.css', __FILE__)."\" type=\"text/css\" />";
				} else {
					// URL include
					echo "\n<link rel=\"stylesheet\" href=\"".$theme."\" type=\"text/css\" />";
				}
			}
			
			echo "\n<style type=\"text/css\" media=\"all\">";
			
			$indent = "\n\t";
			
			// Font family and font size
			$font_family = stripslashes(get_option('shailan_dm_font'));
			
			if(!empty($font_family)){ echo $indent. "ul.dropdown li a { font-family:$font_family; } "; }
			
			$font_size = get_option('shailan_dm_fontsize'); //'12px';			
			
			if(!empty($font_size)){ echo $indent. "ul.dropdown li a { font-size:$font_size; }"; }
			
			if(!$allow_multiline){
				echo $indent. "ul.dropdown { white-space: nowrap;	}";
			}
				
			if($custom_colors){
			// Custom color scheme is active
			
			// Overlay support 
			$overlay = get_option('shailan_dm_overlay');
			echo $indent . "/* Selected overlay: ". $overlay . " */";
			
			if($overlay!='none' && $theme=='color-scheme' ){
				$posvert = 0;
				switch ( $overlay ) {
					case "glass": 
						$posvert = 0;
					break; 
					case "flat": 
						$posvert = -100;
					break; 
					case "shadow": 
						$posvert = -200;
					break; 
					case "soft": 
						$posvert = -300;
					break; 
				}
				
				$apos = $posvert - 2;
			
			?>
			
	.shailan-dropdown-menu .dropdown-horizontal-container, 
	ul.dropdown li, ul.dropdown li.hover, ul.dropdown li:hover{ background-position:0px <?php echo $posvert; ?>px; }
	ul.dropdown li.hover a, ul.dropdown li:hover a{ background-position:0px <?php echo $apos; ?>px; }

	<?php } elseif($overlay == 'none') { ?>
	/* Clear background images */
	.shailan-dropdown-menu .dropdown-horizontal-container, ul.dropdown li, ul.dropdown li.hover, ul.dropdown li:hover, ul.dropdown li.hover a, ul.dropdown li:hover a { background-image:none; }		
	<?php } else {/* unidentified overlay ? */} ?>
	
	.shailan-dropdown-menu .dropdown-horizontal-container, ul.dropdown li{ background-color:<?php echo $shailan_dm_color_menubg; ?>; }
	ul.dropdown a:link,
	ul.dropdown a:visited,
	ul.dropdown li { color: <?php echo $shailan_dm_color_link; ?>; }
	ul.dropdown a:hover,
	ul.dropdown li:hover { color: <?php echo $shailan_dm_color_hoverlink; ?>; }
	ul.dropdown a:active	{ color: <?php echo $shailan_dm_color_hoverlink; ?>; }
			
	ul.dropdown li.hover a, ul.dropdown li:hover a{ background-color: <?php echo $shailan_dm_color_lihover; ?>; }
	ul.dropdown li.hover ul li, ul.dropdown li:hover ul li{ background-color: <?php echo $shailan_dm_color_menubg; ?>;
		color: <?php echo $shailan_dm_color_link; ?>; }
			
	ul.dropdown li.hover ul li.hover, ul.dropdown li:hover ul li:hover { background-image: none; }
	ul.dropdown li.hover a:hover, ul.dropdown li:hover a:hover { background-color: <?php echo $shailan_dm_color_lihover; ?>; }
	
	ul.dropdown ul{ background-image:none; background-color:<?php echo $shailan_dm_color_menubg; ?>; border:1px solid <?php echo $shailan_dm_color_menubg; ?>; }
	ul.dropdown-vertical li { border-bottom:1px solid <?php echo $shailan_dm_color_lihover; ?>; }
	<?php
			
			} // if($custom_colors)
			
			// If effects not active, embed CSS display:
			if(! $is_fx_active){ ?>
	
	/** Show submenus */
	ul.dropdown li:hover > ul, ul.dropdown li.hover ul{ display: block; }
	
	/** Show current submenu */
	ul.dropdown li.hover ul, ul.dropdown ul li.hover ul, ul.dropdown ul ul li.hover ul, ul.dropdown ul ul ul li.hover ul, ul.dropdown ul ul ul ul li.hover ul , ul.dropdown li:hover ul, ul.dropdown ul li:hover ul, ul.dropdown ul ul li:hover ul, ul.dropdown ul ul ul li:hover ul, ul.dropdown ul ul ul ul li:hover ul { display: block; } 
				
			<?php }
			
			// Insert Custom CSS last
			$custom_css = stripslashes(get_option('shailan_dm_custom_css'));
			if(!empty($custom_css)){ echo $custom_css; }
			echo "\n</style>";
			echo "\n<!-- /Dropdown Menu Widget Styles -->";
			echo "\n\n ";
		
		}
	} // -- End Header
	
/******************************************************************************
*  HEADER
******************************************************************************/
	function footer($instance){
		$indent = "\n\t";
		
		$remove_title_attributes = (bool) get_option('shailan_dm_remove_title_attributes');
		$remove_top_level_links = (bool) get_option('shailan_dm_remove_top_level_links');
		$is_fx_active = (bool) get_option('shailan_dm_effects');
		$speed = get_option('shailan_dm_effect_speed', '400');
		$effect = get_option('shailan_dm_effect', 'fade');
		
		if( $is_fx_active || $remove_title_attributes || $remove_top_level_links ){
		
		echo "\n\n<!-- Dropdown Menu Widget Effects by shailan (http://shailan.com) v".SHAILAN_DM_VERSION." on wp".get_bloginfo( 'version' )." -->"; // For debug
		echo "\n<script type=\"text/javascript\">/* <![CDATA[ */";
		echo "\n(function($){ \n";
		
		// Remove title attributes from links
		if($remove_title_attributes){
		?>
  $('ul.dropdown li a').removeAttr('title');
		<?php
		}
		
		?>
		
  jQuery.fn.alignSubs = function ( args ) {
	return this.each(function(){
		var $this = jQuery(this);
		oleft = ( $this.parent().width() - $this.width() ) / 2;		
		$this.css('left', oleft);
	});
  };
  
  jQuery('ul.dropdown li ul:first').alignSubs();
		
		<?php
		
		// Remove links from top-level elements
		if($remove_top_level_links){
		?>
  $('ul.children').parent().find('a:first').removeAttr('href');
		<?php
		}
		
		// Dropdown FX
		if( 'fade' == $effect ){
		?>
  $(".dropdown li").hover(function(){
	$(this).find("ul:first").fadeIn('<?php echo $speed; ?>');
  },
  function(){
	$(this).find("ul:first").fadeOut('<?php echo $speed; ?>');
  }); 
		<?php
		} elseif( 'slide' == $effect ) { ?>
  $(".dropdown li").hover(function(){
	$(this).find("ul:first").slideDown('<?php echo $speed; ?>');
  },
  function(){
	$(this).find("ul:first").slideUp('<?php echo $speed; ?>');
  }); 
		<?php 
		} elseif( 'fade2' == $effect ) { ?>
		
  $(".dropdown li").hover(function(){
	h = $(this).height() + 'px';
	$(this).find("ul:first").animate( {opacity:'show', top:h}, '<?php echo $speed; ?>');
  },
  function(){
	h = $(this).height() + 5 + 'px';
	$(this).find("ul:first").animate( {opacity:'hide', top:h}, '<?php echo $speed; ?>');
  }); 
  
	<?php }
		
		echo "\n})(jQuery);";
		echo "\n/* ]]> */</script>";
		echo "\n<!-- /Dropdown Menu Widget Styles -->";
		echo "\n\n ";

		} // fx active
		
	}

} 
// *** END OF CLASS ***

// Register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_DropdownWidget");'));

// Load translations
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'shailan-dropdown-menu', false, $plugin_dir . '/lang');

// Add admin menu
add_action('admin_menu', array('shailan_DropdownWidget', 'adminMenu'));

/* Includes */
include('shailan-page-walker.php'); // Load custom page walker
include('shailan-category-walker.php'); // Load custom category walker

/* Custom widget */	
include('shailan-multi-dropdown.php'); // Load multi-dropdown widget

// Template tag support
function shailan_dropdown_menu( $args = array() ){

	$type = get_option('shailan_dm_type');
	$exclude = get_option('shailan_dm_exclude');
	$inline_style = get_option('shailan_dm_style');
	$login = (bool) get_option('shailan_dm_login');
	$admin = (bool) get_option('shailan_dm_admin');
	$vertical = (bool) get_option('shailan_dm_vertical');
	$home = (bool) get_option('shailan_dm_home');
	$align = get_option('shailan_dm_align');
	
	$opts = array(
		'type' => $type,
		'exclude' => $exclude,
		'style' => $inline_style,
		'login' => $login,
		'admin' => $admin,
		'vertical' => $vertical,
		'home' => $home,
		'align' => $align
	);
	
	$options = wp_parse_args( $args, $opts );
	
	if(!empty($args['menu'])){ $options['type'] == $args['menu']; }

	the_widget( 'shailan_DropdownWidget', $options );
}

function get_latest_tweet($username){
    $url = "http://search.twitter.com/search.atom?q=from:$username&rpp=1";
    $content = file_get_contents($url);
    $content = explode('<content type="html">', $content);
    $content = explode('</content>', $content[1]);
    return html_entity_decode($content[0]);
}