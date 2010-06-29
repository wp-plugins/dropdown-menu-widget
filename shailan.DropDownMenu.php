<?php 
/*
Plugin Name: Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages, categories & navigation menus. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Version: 1.5.2
Author: Matt Say
Author URI: http://shailan.com
Text Domain: shailan-dropdown-menu
*/

define('SHAILAN_DM_VERSION','1.5.2');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');
define('SHAILAN_DM_FOLDER', 'dropdown-menu-widget');

/**
 * Shailan Dropdown Widget Class
 */
class shailan_DropdownWidget extends WP_Widget {
    /** constructor */
    function shailan_DropdownWidget() {
		global $pluginname, $shortname, $pluginoptions;
		
		$widget_ops = array('classname' => 'shailan-dropdown-menu', 'description' => __( 'Dropdown page/category menu', 'shailan-dropdown-menu' ) );
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu', 'shailan-dropdown-menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		$this->pluginname = "Dropdown Menu";
		$this->shortname = "shailan_dm";
		
		// Hook up styles
		add_action( 'wp_head', array(&$this, 'styles') );		
			
		// Define themes
		$available_themes = array(
			'None'=>'NONE',
			'Custom CSS' => 'custom',
			'Simple White'=>'simple',
			'Wordpress Default'=>'wpdefault',
			'Grayscale'=>'grayscale',
			'Aqua'=>'aqua',
			'Blue gradient'=>'simple-blue',
			/* 'Tabs Blue'=> 'tabs-blue', */
			'Shiny Black'=> 'shiny-black',
			'Flickr theme'=>'flickr.com/default.ultimate',
			'Nvidia theme'=>'nvidia.com/default.advanced',
			'Adobe theme'=>'adobe.com/default.advanced',
			'MTV theme'=>'mtv.com/default.ultimate'
		);
		
		// Swap array for options page
		$themes = array();
		while(list($Key,$Val) = each($available_themes))
			$themes[$Val] = $Key;
			
		$types = array('pages'=>'Pages', 'categories'=>'Categories');
		
		if(function_exists('wp_nav_menu')){
			// Get available menus
			$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
			$navmenus = array();
			if($menus){
				foreach($menus as $menu){
					$navmenus['navmenu_' . $menu->term_id] = $menu->name;
				}
			}
			
			// Merge type with menu array
			$types = array_merge($types, $navmenus);
		}
		
		$this->menu_types = $types; // Back it up
		
		// Option names
		$vertical_tag = 'shailan_dm_vertical';
		$width_tag = 'shailan_dm_width';	
		$custom_walkers_tag = 'shailan_dm_customwalkers';
		$allow_multiline_tag = 'shailan_dm_allowmultiline';

		// Define plugin options	
		$this->admin_options = array(
			
			array(
				"name" => "Menu options",
				"type" => "section"
			),
		
			array(  "name" => "Dropdown Menu Theme",
			"desc" => "Skin for the menu",
			"id" => $this->shortname."_active_theme",
			"std" => "None",
			"options" => $themes,
			"type" => "select"),
			
			array(  "name" => "Rename Homepage",
			"desc" => "You can change your homepage link here",
			"id" => $this->shortname."_home_tag",
			"std" => __("Home"),
			"type" => "text"),
			
			array( "type" => "close" ),
			
			array(
				"name" => "Template Tag Options",
				"type" => "section"
			),
			
			array(  "name" => "Menu Type",
			"desc" => "Dropdown Menu Type",
			"id" => $this->shortname."_type",
			"std" => "pages",
			"options" => $types,
			"type" => "select"),
			
			array(  "name" => "Home link",
			"desc" => "If checked dropdown menu displays home link",
			"id" => $this->shortname."_home",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Login",
			"desc" => "If checked dropdown menu displays login link",
			"id" => $this->shortname."_login",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Register / Site Admin",
			"desc" => "If checked dropdown menu displays register/site admin link.",
			"id" => $this->shortname."_login",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Vertical menu",
			"desc" => "If checked dropdown menu is displayed vertical.",
			"id" => $this->shortname."_vertical",
			"std" => true,
			"type" => "checkbox"),
			
			array(  "name" => "Exclude Pages",
			"desc" => "Excluded page IDs.",
			"id" => $this->shortname."_exclude",
			"std" => "",
			"type" => "text"),
			
			array( "type" => "close" ),
			
			array(
				"name" => "Advanced Options",
				"type" => "section"
			),
			
			array(  "name" => "Wrap long menu items",
			"desc" => "If checked long menu items will wrap",
			"id" => $this->shortname."_allowmultiline",
			"type" => "checkbox"),
			
			array(  "name" => "Dropdown Menu Font",
			"desc" => "Font family for the menu<br />Please leave blank to use your wordpress theme font.",
			"id" => $this->shortname."_font",
			"std" => '',
			"type" => "text"),
			
			array(  "name" => "Dropdown Menu Font Size",
			"desc" => "Font size of the menu items (Eg: 12px OR 1em) <br />Please leave blank to use your wordpress theme font-size.",
			"id" => $this->shortname."_fontsize",
			"std" => '',
			"type" => "text"),
			
			array(  "name" => "Custom css",
			"desc" => "You can paste your own customization file here.",
			"id" => $this->shortname."_custom_css",
			"std" => '',
			"type" => "textarea"),
			
			array(  "name" => "Show Empty Categories",
			"desc" => "If checked categories with no posts will be shown.",
			"id" => $this->shortname."_show_empty",
			"std" => false,
			"type" => "checkbox"),
			
			/*
			array(  "name" => "Use custom walkers",
			"desc" => "Custom walkers add more functionality for styling. <br />Some themes depend on this option.<br />Note: This option hides link titles.",
			"id" => $this->shortname."_customwalkers",
			"std" => false,
			"type" => "checkbox"),
			*/
			
			array( "type" => "close" ),
			
		);
		
		$this->defaults = array(
			'title' => '',
			'type' => 'pages',
			'exclude' => '',
			'home' => false,
			'login' => false,
			'admin' => false,
			'vertical' => false,
			'align' => 'left'
		);
		
		$pluginname = $this->pluginname;
		$shortname = $this->shortname;
		$pluginoptions = $this->admin_options;
		
		/** Unused options */
		update_option('shailan_dm_customwalkers', false);
			
    }
	
	// Add settings page
	function adminMenu(){
		global $pluginname, $shortname, $pluginoptions;
		
		wp_register_style('dropdownMenuStyles', WP_PLUGIN_URL . '/dropdown-menu-widget/admin.css');
 
		if ( @$_GET['page'] == 'dropdown-menu' ) {
		
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
			add_action('admin_print_styles-' . $page, array('shailan_DropdownWidget', 'styles'));
		}
	}
	
	function getOptionsPage(){	
		global $pluginname, $shortname, $pluginoptions;
		
		$title = __('Dropdown Menu Options');
		
		include_once('options-page.php'); 
	
	}
	
    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$orientation = ($vertical ? 'dropdown-vertical' : 'dropdown-horizontal');
		$custom_walkers = false; //(bool) get_option('shailan_dm_customwalkers');
		$show_empty = (bool) get_option('shailan_dm_show_empty');
		
        ?>
              <?php echo $args['before_widget']; ?>
                <?php /*if ( $title )
                        echo $before_title . $title . $after_title;*/  // Title is disabled for this widget 
				?>
				
				
			<?php 
			
			$dropdown_open = '<div id="shailan-dropdown-wrapper-' . $this->number . '" ><div class="'.$orientation.'-container" align="' . $align . '" >
				  <table cellpadding="0" cellspacing="0"> 
					<tr><td>';
					
			$list_open = '<ul class="dropdown '. $orientation . ' dropdown-align-'.$align.'">';
			
			if($home && ($type == 'pages' || $type == 'categories')){ 
			
						$home_item = '<li class="page_item cat-item blogtab '. (is_front_page() && !is_paged() ? 'current_page_item current-cat' : '' ) . '">
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
				</div>
			</div> ';
			
			$menu_defaults = array(
				'order_by'=>'name',
				'depth'=>'4',
				'title_li'=>'',
				'exclude'=>$exclude
			);
					
			switch ( $type ) {

				/* Pages menu */
				case "pages": 
				
				if($custom_walkers){
					$page_walker = new shailan_PageWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$page_walker) , $menu_defaults ); }
					
					echo $dropdown_open;
					echo $list_open;
					  wp_list_pages($menu_defaults);
					echo $list_close;
					echo $dropdown_close;
				
				break; 
				
				/** Categories menu */
				case "categories": 
				
				if($custom_walkers){ 
					$cat_walker = new shailan_CategoryWalker();
					$menu_defaults = wp_parse_args( array('walker'=>$cat_walker) , $menu_defaults ); }
					
					if($show_empty){$menu_defaults = wp_parse_args( array('hide_empty'=>'0') , $menu_defaults ); }
				
					echo $dropdown_open;
					echo $list_open;
					  wp_list_categories($menu_defaults); 
					echo $list_close;
					echo $dropdown_close;

				break;
				
				/** WP3 Nav menu */
				default:
					$menu_id = substr($type, 8, 3);
					
					$menu_args = array(
					  'menu'            => $menu_id, 
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
					  'theme_location'  => '');
					  
				if($custom_walkers){
					$page_walker = new shailan_PageWalker();
					$menu_args = wp_parse_args( array('walker'=>$page_walker) , $menu_args ); }
					
					echo $dropdown_open;
					  wp_nav_menu($menu_args);
					echo $dropdown_close;
					
				} // switch ($type)

				?>
						
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {	
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$home = (bool) $home;
		$login = (bool) $login;
		$admin = (bool) $admin;
		$vertical = (bool) $vertical;
		
        ?>		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (won\'t be shown):', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			
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
	
	function styles($instance){
	
		if(!is_admin()){
	
			$theme = get_option('shailan_dm_active_theme');
			$allow_multiline = (bool) get_option('shailan_dm_allowmultiline');
			
			echo "\n<!-- Start of Dropdown Menu Widget Styles by shailan (http://shailan.com) -->";
			
			echo "\n\t<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/shailan-dropdown.css\" type=\"text/css\" />";
			
			if($theme!='NONE' || $theme != 'Custom'){
				echo "\n\t<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/themes/".$theme.".css\" type=\"text/css\" />";
			}
			
			
			echo "\n\t<style type=\"text/css\" media=\"all\">";
			
			// Font family and font size
			$font_family = stripslashes(get_option('shailan_dm_font')); //'"Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif';
			
			if(!empty($font_family)){ echo "\n\t\tul.dropdown li a { font-family:$font_family; } "; }
			
			$font_size = get_option('shailan_dm_fontsize'); //'12px';			
			
			if(!empty($font_size)){ echo "\n\t\tul.dropdown li a { font-size:$font_size; }"; }
			
			if(!$allow_multiline){
				echo "\n\t\tul.dropdown { white-space: nowrap;	}\n";
			}
			
			// Custom css support
			/* if($theme == 'custom'){ */
				$custom_css = stripslashes(get_option('shailan_dm_custom_css'));
				
				if(!empty($custom_css)){ echo $custom_css; }
			/*}*/
			
			
			echo "\n\t</style>";
			
			/*
			echo "\n\t<!--[if lte IE 7]>";
			echo "\n\t<style type=\"text/css\" media=\"screen\">";
			echo "\n\t\tbody { behavior:url(\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/csshover.htc\"); }";
			echo "\n\t</style>";
			echo "\n\t<![endif]-->";
			*/
			
			echo "\n<!-- End of Wordpress Dropdown Menu Styles -->";
			echo "\n ";
		
		} else {
			wp_enqueue_style('dropdownMenuStyles');
		}
	}

} // class shailan_DropdownWidget

// register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_DropdownWidget");'));

// load translations
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'shailan-dropdown-menu', false, $plugin_dir . '/lang');

// add admin menu
add_action('admin_menu', array('shailan_DropdownWidget', 'adminMenu'));

if(is_admin()){ wp_admin_css( 'widgets' ); 	wp_enqueue_script('admin-widgets'); };
wp_enqueue_script( 'dropdown-ie-support', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/include.js', array('jquery') );

/* Includes */
	include('shailan-page-walker.php'); // Load custom page walker
	include('shailan-category-walker.php'); // Load custom category walker

/* Custom widget */	
	include('shailan-multi-dropdown.php'); // Load multi-dropdown widget

// template tag support
function shailan_dropdown_menu(){
	$type = get_option('shailan_dm_type');
	$exclude = get_option('shailan_dm_exclude');
	$inline_style = get_option('shailan_dm_style');
	$login = (bool) get_option('shailan_dm_login');
	$admin = (bool) get_option('shailan_dm_admin');
	$vertical = (bool) get_option('shailan_dm_vertical');
	$home = (bool) get_option('shailan_dm_home');
	
	$args = array(
		'type' => $type,
		'exclude' => $exclude,
		'style' => $inline_style,
		'login' => $login,
		'admin' => $admin,
		'vertical' => $vertical,
		'home' => $home
		);

	the_widget('shailan_DropdownWidget', $args);
}

function get_latest_tweet($username)
{
    $url = "http://search.twitter.com/search.atom?q=from:$username&rpp=1";
    $content = file_get_contents($url);
    $content = explode('<content type="html">', $content);
    $content = explode('</content>', $content[1]);
    return html_entity_decode($content[0]);
}