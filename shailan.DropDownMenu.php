<?php 
/*
Plugin Name: Shailan Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages and categories. This widget is best used in <a href="http://shailan.com">Shailan.com</a> themes. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Version: 0.2
Author: Matt Say
Author URI: http://shailan.com
*/

define('SHAILAN_DM_VERSION','0.1');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');

/**

	== CHANGELOG ==
	* 0.2 - Added login and register button options.
	* 0.1 - First release.

	== TODO == 
	* add option to select vertical/linear.
	* add some more themes.
	* optimize.

*/

/**
 * Shailan Dropdown Widget Class
 */
class shailan_DropdownWidget extends WP_Widget {
    /** constructor */
    function shailan_DropdownWidget() {
		$widget_ops = array('classname' => 'shailan_DropdownWidget', 'description' => __( 'Dropdown page/category menu' ) );
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		if ( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array(&$this, 'styles') );		
    }
	
	// Add settings page
	function adminMenu(){
		if (function_exists('add_options_page')) {
			add_options_page('Settings for Dropdown Menu by Shailan' , 'Dropdown Menu', 9, 'dropdown-menu', array('shailan_DropdownWidget', 'getOptionsPage'));
		}
	}
	
	function getOptionsPage(){	
	// Option names
	$theme_name = 'theme';
	
	// Read options 
	$theme = get_option($theme_name);
	
	
	
	if(wp_verify_nonce($_POST['_wpnonce'])){ // Form submitted. Save settings.
		
		$theme = $_POST[$theme_name];  //get_option('theme');
		
		update_option('theme', $theme);
		
		?>
		<div class="updated"><p><strong><?php _e('Options saved.', 'shailanDropdownMenu_domain'); ?></strong></p></div>
		
		<?php
	}
	
	$themes = array(
			'None'=>'NONE',
			'Simple'=>'simple',
			'Flickr'=>'flickr.com/default',
			'Nvidia'=>'nvidia.com/default.advanced',
			'Adobe'=>'adobe.com/default.advanced',
			'MTV'=>'mtv.com/default.advanced'
		);
	
	?>
	
<div class="wrap">
<h2><?php echo esc_html( SHAILAN_DM_TITLE . ' ' . SHAILAN_DM_VERSION ); ?></h2>

<p>Dropdown menu creates a beautiful CSS only dropdown menu from your wordpress pages or categories. You can customize dropdown menu theme and settings here: </p>

<form method="post" action="">

<?php wp_nonce_field(); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="theme"><?php _e('Dropdown menu theme') ?></label></th>
<td><select name="theme" id="theme">

<?php foreach($themes as $name=>$path){
				$selected = ($theme == $path ? 'selected' : '');  
				echo '<option value="'.$path.'" '.$selected.'>'.$name.'</option>';
			} ?>

</select><span class="description"><?php _e('You can choose a theme for your dropdown menu here.') ?></span></td>
</tr>	

</table>
	
	<p>NOTE : Onscreen theme edit will be available soon. Be sure to check <a href="http://shailan.com">shailan.com</a> regularly for updates.</p>
</div>
<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
<p>Visit <a href="http://shailan.com">shailan.com</a> for more wordpress themes, plugins and widgets.</p>
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
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		
        ?>
              <?php echo $before_widget; ?>
                <?php /*if ( $title )
                        echo $before_title . $title . $after_title;*/  // Title is disabled for this widget 
				?>
		
		<?php if($type == 'Pages'){ ?>
			<div id="shailan-dropdown-menu-<?php echo $this->number; ?>" style="<?php echo $inline_style; ?>">
				<div> 
				  <table cellpadding="0" cellspacing="0"> 
					<td> 
					<ul class="dropdown dropdown-horizontal dropdown-upward">
					
					<li class="<?php if ( is_front_page() && !is_paged() ): ?>current_page_item<?php else: ?>page_item<?php endif; ?> blogtab"><a href="<?php echo get_option('home'); ?>/"><?php _e('Home'); ?></a></li>					
		<?php wp_list_pages('sort_column=menu_order&depth=4&title_li=&exclude='.$exclude); ?>
		<?php if($admin){ wp_register('<li class="admintab">','</li>'); } if($login){ ?><li class="page_item"><?php wp_loginout(); ?></a> <?php } ?>
		</ul></td>
				  </table> 
				</div>
			</div> 		
		<?php } else { ?>
			<div id="shailan-dropdown-menu<?php echo $this->number; ?>" style="background:<?php echo $background; ?>; <?php echo $additional_styles ?>">
				<div> 
				  <table cellpadding="0" cellspacing="0"> 
					<td> 
					<ul class="dropdown dropdown-horizontal dropdown-upward">
		<?php wp_list_categories('order_by=name&depth=4&title_li=&exclude='.$exclude); ?>
		</ul></td> 
				  </table> 
				</div>
			</div> 				
		<?php } ?>
		
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
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (won\'t be shown):'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			
			<p><label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type:'); ?><input type="radio" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" value="Pages" <?php if($type=='Pages'){ echo 'checked="checked"'; } ?> /><?php _e('Pages'); ?> <input type="radio" id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" value="Categories" <?php if($type=='Categories'){ echo 'checked="checked"'; } ?>/><?php _e('Categories'); ?></label></p>
			
			<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></label></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $this->get_field_id('login'); ?>"><?php _e( 'Add login/logout' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('admin'); ?>" name="<?php echo $this->get_field_name('admin'); ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $this->get_field_id('admin'); ?>"><?php _e( 'Add Register/Site Admin' ); ?></label>
		</p>
			
			<p><label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Inline Style:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="text" value="<?php echo $inline_style; ?>" /></label></p>
			
			
<div class="widget-control-actions alignright">
<p><a href="options-general.php?page=dropdown-menu">Menu Style</a> | <a href="http://shailan.com/wordpress/plugins/dropdown-menu">Visit plugin site</a></p>
</div>
			
        <?php 
	}
	
	function styles($instance){
		$theme = get_option('theme');
		
		$font_family = '"Segoe UI",Calibri,"Myriad Pro",Myriad,"Trebuchet MS",Helvetica,Arial,sans-serif';
		$font_size = '12px';
		
		echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/shailan.DropDownMenu/shailan.DropdownStyles.css" type="text/css">';
		//echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/shailan.DropDownMenu/dropdown.limited.css" type="text/css">';
		
		if($theme!='NONE'){
			echo '<link rel="stylesheet" href="'.WP_PLUGIN_URL.'/shailan.DropDownMenu/themes/'.$theme.'.css" type="text/css">';
		}
		echo '<style type="text/css" media="all">';
		echo '    ul.dropdown {font-family: '.$font_family.' font-size:'.$font_size.'; }';
		echo '</style>';
	}

} // class shailan_DropdownWidget

// register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_DropdownWidget");'));
add_action('admin_menu', array('shailan_DropdownWidget', 'adminMenu'));