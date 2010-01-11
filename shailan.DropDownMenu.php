<?php 
/*
Plugin Name: Shailan Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages and categories. This widget is best used in <a href="http://shailan.com">Shailan.com</a> themes. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Version: 1.2.7
Author: Matt Say
Author URI: http://shailan.com
Text Domain: shailan-dropdown-menu
*/

define('SHAILAN_DM_VERSION','1.2.7');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');
define('SHAILAN_DM_FOLDER', 'dropdown-menu-widget');

/**
 * Shailan Dropdown Widget Class
 */
class shailan_DropdownWidget extends WP_Widget {
    /** constructor */
    function shailan_DropdownWidget() {
		$widget_ops = array('classname' => 'shailan-dropdown-menu', 'description' => __( 'Dropdown page/category menu', 'shailan-dropdown-menu' ) );
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu', 'shailan-dropdown-menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		// if ( is_active_widget(false, false, $this->id_base) ) 
		// @shailan: disabled for the_widget support.
			add_action( 'wp_head', array(&$this, 'styles') );		
    }
	
	// Add settings page
	function adminMenu(){
		if (function_exists('add_options_page')) {
			add_options_page(__('Settings for Dropdown Menu', 'shailan-dropdown-menu') , __('Dropdown Menu', 'shailan-dropdown-menu'), 9, 'dropdown-menu', array('shailan_DropdownWidget', 'getOptionsPage'));
		}
	}
	
	function getOptionsPage(){	
	// Option names
	$theme_tag = 'theme';
	
	$type_tag = 'shailan_dm_type';
	$exclude_tag = 'shailan_dm_exclude';
	$inline_style_tag = 'shailan_dm_style';
	$home_tag = 'shailan_dm_home';
	$login_tag = 'shailan_dm_login';
	$admin_tag = 'shailan_dm_admin';
	$vertical_tag = 'shailan_dm_vertical';
	$width_tag = 'shailan_dm_width';	
	
	// Read options 
	$theme = get_option($theme_tag);
	
	$type = get_option($type_tag);
	$exclude = get_option($exclude_tag);
	$inline_style = get_option($inline_style_tag);
	$home = (bool) get_option($home_tag);
	$login = (bool) get_option($login_tag);
	$admin = (bool) get_option($admin_tag);	
	$vertical = (bool) get_option($vertical_tag);
	$width = (int) get_option($width_tag);
	
	if(wp_verify_nonce($_POST['_wpnonce'])){ // Form submitted. Save settings.
		
		$theme = $_POST[$theme_tag];  //get_option('theme');
		
		$type = $_POST[$type_tag];
		$exclude = $_POST[$exclude_tag];
		$inline_style = $_POST[$inline_style_tag];
		$home = (bool) $_POST[$home_tag];
		$login = (bool) $_POST[$login_tag];
		$admin = (bool) $_POST[$admin_tag];	
		$vertical = (bool) $_POST[$vertical_tag];
		$width = (int) $_POST[$width];
		
		update_option($theme_tag, $theme);
		update_option($type_tag, $type);
		update_option($exclude_tag, $exclude);
		update_option($inline_style_tag, $inline_style);
		update_option($home_tag, $home);
		update_option($login_tag, $login);
		update_option($admin_tag, $admin);
		update_option($vertical_tag, $vertical);
		update_option($width_tag, $width);
		
		?>
		<div class="updated"><p><strong><?php _e('Options saved.', 'shailan-dropdown-menu'); ?></strong></p></div>
		
		<?php
	}
	
	$themes = array(
			'None'=>'NONE',
			'Simple White'=>'simple',
			'Wordpress Default'=>'wpdefault',
			'Grayscale'=>'grayscale',
			'Aqua'=>'aqua',
			'Tabs Blue'=> 'tabs-blue',
			'Shiny Black'=> 'shiny-black',
			'Flickr theme'=>'flickr.com/default.ultimate',
			'Nvidia theme'=>'nvidia.com/default.advanced',
			'Adobe theme'=>'adobe.com/default.advanced',
			'MTV theme'=>'mtv.com/default.ultimate'
		);
	
	?>
	
<div class="wrap">
<h2><?php echo esc_html( SHAILAN_DM_TITLE . ' ' . SHAILAN_DM_VERSION ); ?></h2>

<div style="width:140px; padding:10px; border:1px solid #ddd; float:right;">
Please support if you like this plugin:
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="10214058">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/tr_TR/i/scr/pixel.gif" width="1" height="1">
</form>
</div>

<p><?php _e('Dropdown menu creates a beautiful CSS only dropdown menu from your wordpress pages or categories. You can customize dropdown menu theme and settings here:', 'shailan-dropdown-menu'); ?></p>

<form id="frmShailanDm" name="frmShailanDm" method="post" action="">

<?php wp_nonce_field(); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo $theme_tag; ?>"><?php _e('Dropdown menu theme', 'shailan-dropdown-menu') ?></label></th>
<td><select name="<?php echo $theme_tag; ?>" id="theme">

<?php foreach($themes as $name=>$path){
				$selected = ($theme == $path ? 'selected' : '');  
				echo '<option value="'.$path.'" '.$selected.'>'.$name.'</option>';
			} ?>

</select> <span class="description"><?php _e('You can choose a theme for your dropdown menu here.', 'shailan-dropdown-menu'); ?></span></td>
</tr>
</table>

<fieldset width="400">
<h2><?php _e('Template tag options', 'shailan-dropdown-menu'); ?></h2>
<p><?php _e('You can use following template tag in your themes to display the dropdown menu.', 'shailan-dropdown-menu'); ?><br />
<blockquote><code>&lt;?php if(function_exists('shailan_dropdown_menu')){ shailan_dropdown_menu(); } ?&gt;</code></blockquote> 
<?php _e('Here you can set template tag options:', 'shailan-dropdown-menu'); ?>
</p>
<div style="padding:10px; border:1px solid #ddd; width:275px; ">
<!-- <p><label for="<?php echo $title_tag; ?>"><?php _e('Title (won\'t be shown):'); ?> <input class="widefat" id="<?php echo $title_tag; ?>" name="<?php echo $title_tag; ?>" type="text" value="<?php echo $title; ?>" /></label></p> -->
			
		<p><?php _e('Type:'); ?> <label for="Pages"><input type="radio" id="Pages" name="<?php echo $type_tag; ?>" value="Pages" <?php if($type=='Pages'){ echo 'checked="checked"'; } ?> /> <?php _e('Pages', 'shailan-dropdown-menu'); ?></label> <label for="Categories"><input type="radio" id="Categories" name="<?php echo $type_tag; ?>" value="Categories" <?php if($type=='Categories'){ echo 'checked="checked"'; } ?>/> <?php _e('Categories', 'shailan-dropdown-menu'); ?></label></p>
			
		<p><label for="<?php echo $exclude_tag; ?>"><?php _e('Exclude:', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $exclude_tag; ?>" name="<?php echo $exclude_tag; ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small><?php _e('Page IDs, separated by commas.', 'shailan-dropdown-menu'); ?></small></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $home_tag; ?>" name="<?php echo $home_tag; ?>"<?php checked( $home ); ?> />
		<label for="<?php echo $home_tag; ?>"><?php _e( 'Add homepage link', 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $login_tag; ?>" name="<?php echo $login_tag; ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $login_tag; ?>"><?php _e( 'Add login/logout', 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $admin_tag; ?>" name="<?php echo $admin_tag; ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $admin_tag; ?>"><?php _e( 'Add Register/Site Admin', 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $vertical_tag; ?>" name="<?php echo $vertical_tag; ?>"<?php checked( $vertical ); ?> />
		<label for="<?php echo $vertical_tag; ?>"><?php _e( 'Vertical menu', 'shailan-dropdown-menu' ); ?></label>
		</p>
		
		<p><label for="<?php echo $inline_style_tag; ?>"><?php _e('Inline Style:', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $inline_style_tag; ?>" name="<?php echo $inline_style_tag; ?>" type="text" value="<?php echo $inline_style; ?>" /></label><br /> 
			<small><?php _e('Applied to menu container &lt;div&gt;', 'shailan-dropdown-menu'); ?></small></p>
			
<p><small><?php _e('NOTE: Widgets have their own options. Those options won\'t affect widgets.', 'shailan-dropdown-menu'); ?></small></p>
</div>
</fieldset>

	<p><?php _e('NOTE : Onscreen theme edit will be available soon. Be sure to check <a href="http://shailan.com">shailan.com</a> regularly for updates.', 'shailan-dropdown-menu'); ?></p>
</div>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'shailan-dropdown-menu'); ?>" />
</p>
<p><?php _e('Visit <a href="http://shailan.com">shailan.com</a> for more wordpress themes, plugins and widgets.', 'shailan-dropdown-menu'); ?></p>
</form>
<p>
<a href="http://shailan.com/wordpress/plugins/dropdown-menu">Dropdown Menu <?php echo SHAILAN_DM_VERSION; ?></a> by <a href="http://shailan.com">shailan</a></a> &copy; 2009 
</p>
</div>  <?php
	
	}
	
    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		$type = $instance['type'];
		$exclude = $instance['exclude'];
		$inline_style = $instance['style'];
		$home = (bool) $instance['home'];
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		$vertical = (bool) $instance['vertical'];
		$align = $instance['align'];
		
		$orientation = ($vertical ? 'dropdown-vertical' : 'dropdown-horizontal');
		
        ?>
              <?php echo $before_widget; ?>
                <?php /*if ( $title )
                        echo $before_title . $title . $after_title;*/  // Title is disabled for this widget 
				?>

			<div id="shailan-dropdown-wrapper-<?php echo $this->number; ?>" style="<?php echo $inline_style; ?>">
				<div 
				<?php 
					switch($align){
						case 'right':
							echo ' align="right"';
						break;
						case 'center':
							echo ' align="center"';
						break;
						
						case 'left':
						default:						
					
					}
				
				?>
				> 
				  <table cellpadding="0" cellspacing="0"> 
					<tr><td> 
					<ul class="dropdown <?php echo $orientation; ?>">
					
					<?php if($home){ ?>						
						<li class="page_item cat-item blogtab <?php if ( is_front_page() && !is_paged() ){ ?>current_page_item current-cat<?php } ?>"><a href="<?php echo get_option('home'); ?>/"><span><?php _e('Home', 'shailan-dropdown-menu'); ?></span></a></li>	
					<?php } ?>
							
					
					<?php if($type == 'Pages'){ ?>
					
						<?php 
						$page_walker = new shailan_PageWalker();
						wp_list_pages(array(
							'walker'=>$page_walker,
							'sort_column'=>'menu_order',
							'depth'=>'4',
							'title_li'=>'',
							'exclude'=>$exclude
							)); ?>
							
					<?php } else { ?>
					
						<?php 
						$cat_walker = new shailan_CategoryWalker();
						wp_list_categories(array(
							'walker'=>$cat_walker,
							'order_by'=>'name',
							'depth'=>'4',
							'title_li'=>'',
							'exclude'=>$exclude
							)); ?>			
							
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
		$theme = get_option('theme');
		
		echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/'.SHAILAN_DM_FOLDER.'/shailan-dropdown.css" type="text/css" />';
		
		if($theme!='NONE'){
			echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/'.SHAILAN_DM_FOLDER.'/themes/'.$theme.'.css" type="text/css" />';
		}
		
		// Font family and font size
		$font_family = '"Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif';
		$font_size = '12px';
		echo '<style type="text/css" media="all">';
		echo '    ul.dropdown {font-family: '.$font_family.' font-size:'.$font_size.'; }';
		echo '</style>';
		
	}

} // class shailan_DropdownWidget

// register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_DropdownWidget");'));

// load translations
$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'shailan-dropdown-menu', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );

// add admin menu
add_action('admin_menu', array('shailan_DropdownWidget', 'adminMenu'));

include('shailan-page-walker.php'); // Load custom page walker
include('shailan-category-walker.php'); // Load custom category walker

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