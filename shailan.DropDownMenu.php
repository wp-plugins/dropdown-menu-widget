<?php 
/*
Plugin Name: Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages and categories. This widget is best used in <a href="http://shailan.com">Shailan.com</a> themes. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Version: 1.4.1
Author: Matt Say
Author URI: http://shailan.com
Text Domain: shailan-dropdown-menu
*/

define('SHAILAN_DM_VERSION','1.4.1');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');
define('SHAILAN_DM_FOLDER', 'dropdown-menu-widget');

/**
 * Shailan Dropdown Widget Class
 */
class shailan_DropdownWidget extends WP_Widget {
    /** constructor */
    function shailan_DropdownWidget() {
		global $pluginname, $shortname, $options;
		
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
			'Simple White'=>'simple',
			'Wordpress Default'=>'wpdefault',
			'Mystique'=>'mystique',
			'Grayscale'=>'grayscale',
			'Aqua'=>'aqua',
			'Blue gradient'=>'simple-blue',
			'Tabs Blue'=> 'tabs-blue',
			'Shiny Black'=> 'shiny-black',
			'Flickr theme'=>'flickr.com/default.ultimate',
			'Nvidia theme'=>'nvidia.com/default.advanced',
			'Adobe theme'=>'adobe.com/default.advanced',
			'MTV theme'=>'mtv.com/default.ultimate'
		);
		
		$themes = array();
		while(list($Key,$Val) = each($available_themes))
			$themes[$Val] = $Key;
			
		// Get available menus
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$navmenus = array();
		if($menus){
			foreach($menus as $menu){
				$navmenus['navmenu_' . $menu->term_id] = $menu->name;
			}
		}
		
		// Merge type with menu array
		$types = array('pages'=>'Pages', 'categories'=>'Categories');
		$types = array_merge($types, $navmenus);
		
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
			"std" => "Pages",
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
			
		);
		
		$this->widget_options = array(
			
		);
		
		$pluginname = $this->pluginname;
		$shortname = $this->shortname;
		$options = $this->admin_options;
			
    }
	
	// Add settings page
	function adminMenu(){
		global $pluginname, $shortname, $options;
		
		wp_register_style('dropdownMenuStyles', WP_PLUGIN_URL . '/dropdown-menu-widget/admin.css');
 
		if ( @$_GET['page'] == 'dropdown-menu' ) {
		
			if ( @$_REQUEST['action'] && 'save' == $_REQUEST['action'] ) {
		 
				foreach ($options as $value) {
					update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
		 
				foreach ($options as $value) {
					if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }
			 
				header("Location: admin.php?page=dropdown-menu&saved=true");
			die;
			 
			} 
		
		/* TODO: Add reset option
		 
			else if( @$_REQUEST['action'] && 'reset' == $_REQUEST['action'] ) {

				foreach ($options as $value) {
					delete_option( $value['id'] ); }
			 
				header("Location: admin.php?page=controlpanel.php&reset=true");
			die;
			 
			}
		*/
		
		}
	
	
	
		if (function_exists('add_options_page')) {
			$page = add_options_page(__('Settings for Dropdown Menu', 'shailan-dropdown-menu') , __('Dropdown Menu', 'shailan-dropdown-menu'), 'edit_themes', 'dropdown-menu', array('shailan_DropdownWidget', 'getOptionsPage'));
			add_action('admin_print_styles-' . $page, array('shailan_DropdownWidget', 'styles'));
		}
	}
	
	function getOptionsPage(){	
		global $pluginname, $shortname, $options;
		
		$title = __('Dropdown Menu Options');
		
		include_once('options-page.php'); 
	
	}
	
    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
		
		$defaults = array(
			'title' => '',
			'type' => 'pages',
			'exclude' => '',
			'home' => false,
			'login' => false,
			'admin' => false,
			'vertical' => false,
			'align' => ''
		);
		
		$widget_options = wp_parse_args( $instance, $defaults );
		extract( $widget_options, EXTR_SKIP );
		
        /*$title = isset( $instance['title'] ) ? apply_filters('widget_title', $instance['title']) : '';
		$type = isset( $instance['type'] ) ? $instance['type'] : '';
		$exclude = isset( $instance['exclude'] ) ? $instance['exclude'] : '';//$instance['exclude'];
		$home = (bool) isset( $instance['home'] ) ? $instance['home'] : false;//  $instance['home'];
		$login = (bool) isset( $instance['type'] ) ? $instance['type'] : ''; //$instance['login'];
		$admin = (bool) isset( $instance['type'] ) ? $instance['type'] : ''; // $instance['admin'];
		$vertical = (bool) isset( $instance['type'] ) ? $instance['type'] : ''; // $instance['vertical'];
		$align = isset( $instance['type'] ) ? $instance['type'] : ''; //$instance['align'];*/
		
		$orientation = ($vertical ? 'dropdown-vertical' : 'dropdown-horizontal');
		$custom_walkers = (bool) get_option('shailan_dm_customwalkers');
		
        ?>
              <?php echo $args['before_widget']; ?>
                <?php /*if ( $title )
                        echo $before_title . $title . $after_title;*/  // Title is disabled for this widget 
				?>

			<div id="shailan-dropdown-wrapper-<?php echo $this->number; ?>" style="<?php echo $inline_style; ?>">
				<div <?php switch($align){
						case 'right':
							echo ' align="right"';
						break;
						case 'center':
							echo ' align="center"';
						break;
						
						case 'left':
						default:						
					
					}; ?>> 
				  <table cellpadding="0" cellspacing="0"> 
					<tr><td> 
					<ul class="dropdown <?php echo $orientation; ?>">
					
					<?php if($home){ ?>						
						<li class="page_item cat-item blogtab <?php if ( is_front_page() && !is_paged() ){ ?>current_page_item current-cat<?php } ?>"><a href="<?php echo get_option('home'); ?>/"><span><?php _e('Home', 'shailan-dropdown-menu'); ?></span></a></li>	
					<?php } ?>
							
					
					<?php if($type == 'Pages'){ ?>
					
						<?php 
						if($custom_walkers){
							echo "<!-- custom walkers ON -->";
						
							$page_walker = new shailan_PageWalker();
							wp_list_pages(array(
								'walker'=>$page_walker,
								'sort_column'=>'menu_order',
								'depth'=>'4',
								'title_li'=>'',
								'exclude'=>$exclude
								)); 
						} else {
						
							echo "<!-- custom walkers OFF -->";
						
							wp_list_pages(array(
								'sort_column'=>'menu_order',
								'depth'=>'4',
								'title_li'=>'',
								'exclude'=>$exclude
								)); 						
						} ?>
							
					<?php } else { ?>
					
						<?php 
						if($custom_walkers){	
							$cat_walker = new shailan_CategoryWalker();
							wp_list_categories(array(
								'walker'=>$cat_walker,
								'order_by'=>'name',
								'depth'=>'4',
								'title_li'=>'',
								'exclude'=>$exclude
								)); 
						} else {
							wp_list_categories(array(
								'order_by'=>'name',
								'depth'=>'4',
								'title_li'=>'',
								'exclude'=>$exclude
								)); 								
						} ?>			
							
					<?php } ?>
					
						<?php if($admin){ wp_register('<li class="admintab">','</li>'); } if($login){ ?><li class="page_item"><?php wp_loginout(); ?><?php } ?>
						
					</ul></td>
				  </tr></table> 
				</div>
			</div> 				
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {				
        $title = esc_attr($instance['title']);
		$type = $instance['type'];
		$exclude = $instance['exclude'];
		$inline_style = $instance['style'];
		$home = (bool) $instance['home'];
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		$vertical = (bool) $instance['vertical'];
		$align = $instance['align'];
		
        ?>		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (won\'t be shown):', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			
		<p><?php _e('Type:'); ?> <label for="Pages"><input type="radio" id="Pages" name="<?php echo $this->get_field_name('type'); ?>" value="Pages" <?php if($type=='Pages'){ echo 'checked="checked"'; } ?> /> <?php _e('Pages', 'shailan-dropdown-menu'); ?></label> <label for="Categories"><input type="radio" id="Categories" name="<?php echo $this->get_field_name('type'); ?>" value="Categories" <?php if($type=='Categories'){ echo 'checked="checked"'; } ?>/> <?php _e('Categories', 'shailan-dropdown-menu'); ?></label></p>
			
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
		
		<p><label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Inline Style:', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="text" value="<?php echo $inline_style; ?>" /></label><br /> 
			<small><?php _e('Applied to menu container &lt;div&gt;.', 'shailan-dropdown-menu'); ?></small></p>
			
<div class="widget-control-actions alignright">
<p><small><a href="options-general.php?page=dropdown-menu"><?php esc_attr_e('Menu Style', 'shailan-dropdown-menu'); ?></a> | <a href="http://shailan.com/wordpress/plugins/dropdown-menu"><?php esc_attr_e('Visit plugin site', 'shailan-dropdown-menu'); ?></a></small></p>
</div>
			
        <?php 
	}
	
	function styles($instance){
	
		wp_enqueue_style('dropdownMenuStyles');
	
		if(!is_admin()){
	
			$theme = get_option('shailan_dm_active_theme');
			$allow_multiline = (bool) get_option('shailan_dm_allowmultiline');
			
			echo "\n<!-- Start of Dropdown Menu Widget Styles by shailan (http://shailan.com) -->";
			
			echo "\n\t<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/shailan-dropdown.css\" type=\"text/css\" />";
			
			if($theme!='NONE'){
				echo "\n\t<link rel=\"stylesheet\" href=\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/themes/".$theme.".css\" type=\"text/css\" />";
			}
			
			// Font family and font size
			$font_family = get_option('shailan_dropdown_font'); //'"Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif';
			$font_size = get_option('shailan_dropdown_fontsize'); //'12px';
			
			echo "\n\t<style type=\"text/css\" media=\"all\">";
			echo "\n\t\tul.dropdown {font-family:$font_family; font-size:$font_size; }";
			
			if(!$allow_multiline){
				echo "\n\t\t.shailan-dropdown-menu ul.dropdown { white-space: nowrap;	}";
			}
			
			echo "\n\t</style>";
			
			echo "\n\t<!--[if lte IE 7]>";
			echo "\n\t<style type=\"text/css\" media=\"screen\">";
			echo "\n\t\tbody { behavior:url(\"".WP_PLUGIN_URL."/".SHAILAN_DM_FOLDER."/csshover.htc\"); }";
			echo "\n\t</style>";
			echo "\n\t<![endif]-->";
			
			echo "\n<!-- End of Wordpress Dropdown Menu Styles -->";
			echo "\n ";
		
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

wp_admin_css( 'widgets' );
wp_enqueue_script( 'dropdown-ie-support', WP_PLUGIN_URL . '/' . SHAILAN_DM_FOLDER . '/include.js', array('jquery') );

/* Includes */

	// include_once('simon-exclude-pages.php'); // Exclude page plugin
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