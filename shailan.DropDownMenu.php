<?php 
/*
Plugin Name: Shailan Dropdown Menu Widget
Plugin URI: http://shailan.com/wordpress/plugins/dropdown-menu
Description: A multi widget to generate drop-down menus from your pages and categories. This widget is best used in <a href="http://shailan.com">Shailan.com</a> themes. You can find more widgets, plugins and themes at <a href="http://shailan.com">shailan.com</a>.
Version: 1.1.0
Author: Matt Say
Author URI: http://shailan.com
*/

define('SHAILAN_DM_VERSION','1.1.0');
define('SHAILAN_DM_TITLE', 'Dropdown Menu');
define('SHAILAN_DM_FOLDER', 'dropdown-menu-widget');

/**
 * Shailan Dropdown Widget Class
 */
class shailan_DropdownWidget extends WP_Widget {
    /** constructor */
    function shailan_DropdownWidget() {
		$widget_ops = array('classname' => 'shailan-dropdown-menu', 'description' => __( 'Dropdown page/category menu' ) );
		$this->WP_Widget('dropdown-menu', __('Dropdown Menu'), $widget_ops);
		$this->alt_option_name = 'widget_dropdown_menu';
		
		// if ( is_active_widget(false, false, $this->id_base) ) disabled for the_widget support.
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
	$theme_tag = 'theme';
	
	$type_tag = 'shailan_dm_type';
	$exclude_tag = 'shailan_dm_exclude';
	$inline_style_tag = 'shailan_dm_style';
	$login_tag = 'shailan_dm_login';
	$admin_tag = 'shailan_dm_admin';
	$vertical_tag = 'shailan_dm_vertical';
	$width_tag = 'shailan_dm_width';	
	
	// Read options 
	$theme = get_option($theme_tag);
	
	$type = get_option($type_tag);
	$exclude = get_option($exclude_tag);
	$inline_style = get_option($inline_style_tag);
	$login = (bool) get_option($login_tag);
	$admin = (bool) get_option($admin_tag);	
	$vertical = (bool) get_option($vertical_tag);
	$width = (int) get_option($width_tag);
	
	if(wp_verify_nonce($_POST['_wpnonce'])){ // Form submitted. Save settings.
		
		$theme = $_POST[$theme_tag];  //get_option('theme');
		
		$type = $_POST[$type_tag];
		$exclude = $_POST[$exclude_tag];
		$inline_style = $_POST[$inline_style_tag];
		$login = (bool) $_POST[$login_tag];
		$admin = (bool) $_POST[$admin_tag];	
		$vertical = (bool) $_POST[$vertical_tag];
		$width = (int) $_POST[$width];
		
		update_option($theme_tag, $theme);
		update_option($type_tag, $type);
		update_option($exclude_tag, $exclude);
		update_option($inline_style_tag, $inline_style);
		update_option($login_tag, $login);
		update_option($admin_tag, $admin);
		update_option($vertical_tag, $vertical);
		update_option($width_tag, $width);
		
		?>
		<div class="updated"><p><strong><?php _e('Options saved.', 'shailanDropdownMenu_domain'); ?></strong></p></div>
		
		<?php
	}
	
	$themes = array(
			'None'=>'NONE',
			'Simple White'=>'simple',
			'Wordpress Default'=>'wpdefault',
			'Grayscale'=>'grayscale',
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

<p>Dropdown menu creates a beautiful CSS only dropdown menu from your wordpress pages or categories. You can customize dropdown menu theme and settings here: </p>

<form id="frmShailanDm" name="frmShailanDm" method="post" action="">

<?php wp_nonce_field(); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="<?php echo $theme_tag; ?>"><?php _e('Dropdown menu theme') ?></label></th>
<td><select name="<?php echo $theme_tag; ?>" id="theme">

<?php foreach($themes as $name=>$path){
				$selected = ($theme == $path ? 'selected' : '');  
				echo '<option value="'.$path.'" '.$selected.'>'.$name.'</option>';
			} ?>

</select> <span class="description"><?php _e('You can choose a theme for your dropdown menu here.') ?></span></td>
</tr>
</table>

<fieldset width="400">
<h2>Template tag options</h2>
<p>You can use following template tag in your themes to display the dropdown menu. <br />
<blockquote><code>&lt;?php if(function_exists('shailan_dropdown_menu')){ shailan_dropdown_menu(); } ?&gt;</code></blockquote> 
Here you can set template tag options: 
</p>
<div style="padding:10px; border:1px solid #ddd; width:275px; ">
<!-- <p><label for="<?php echo $title_tag; ?>"><?php _e('Title (won\'t be shown):'); ?> <input class="widefat" id="<?php echo $title_tag; ?>" name="<?php echo $title_tag; ?>" type="text" value="<?php echo $title; ?>" /></label></p> -->
			
		<p><?php _e('Type:'); ?> <label for="Pages"><input type="radio" id="Pages" name="<?php echo $type_tag; ?>" value="Pages" <?php if($type=='Pages'){ echo 'checked="checked"'; } ?> /> <?php _e('Pages'); ?></label> <label for="Categories"><input type="radio" id="Categories" name="<?php echo $type_tag; ?>" value="Categories" <?php if($type=='Categories'){ echo 'checked="checked"'; } ?>/> <?php _e('Categories'); ?></label></p>
			
		<p><label for="<?php echo $exclude_tag; ?>"><?php _e('Exclude:'); ?> <input class="widefat" id="<?php echo $exclude_tag; ?>" name="<?php echo $exclude_tag; ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small>Page IDs, separated by commas.</small></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $login_tag; ?>" name="<?php echo $login_tag; ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $login_tag; ?>"><?php _e( 'Add login/logout' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $admin_tag; ?>" name="<?php echo $admin_tag; ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $admin_tag; ?>"><?php _e( 'Add Register/Site Admin' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $vertical_tag; ?>" name="<?php echo $vertical_tag; ?>"<?php checked( $vertical ); ?> />
		<label for="<?php echo $vertical_tag; ?>"><?php _e( 'Vertical menu' ); ?></label>
		</p>
		
		<p><label for="<?php echo $inline_style_tag; ?>"><?php _e('Inline Style:'); ?> <input class="widefat" id="<?php echo $inline_style_tag; ?>" name="<?php echo $inline_style_tag; ?>" type="text" value="<?php echo $inline_style; ?>" /></label><br /> 
			<small>Applied to menu container &lt;div&gt;.</small></p>
			
<p><small>NOTE: Widgets have their own options. Those options won't affect widgets.</small></p>
</div>
</fieldset>

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
		$vertical = (bool) $instance['vertical'];
		
		$orientation = ($vertical ? 'dropdown-vertical' : 'dropdown-horizontal');
		
        ?>
              <?php echo $before_widget; ?>
                <?php /*if ( $title )
                        echo $before_title . $title . $after_title;*/  // Title is disabled for this widget 
				?>

			<div id="shailan-dropdown-wrapper-<?php echo $this->number; ?>" style="<?php echo $inline_style; ?>">
				<div> 
				  <table cellpadding="0" cellspacing="0"> 
					<tr><td> 
					<ul class="dropdown <?php echo $orientation; ?>">
					<?php if($type == 'Pages'){ ?>
						<li class="<?php if ( is_front_page() && !is_paged() ): ?>current_page_item<?php else: ?>page_item<?php endif; ?> blogtab"><a href="<?php echo get_option('home'); ?>/"><?php _e('Home'); ?></a></li>	
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
							'depth'=>'4'
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
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		$vertical = (bool) $instance['vertical'];
		
        ?>		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (won\'t be shown):'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			
		<p><?php _e('Type:'); ?> <label for="Pages"><input type="radio" id="Pages" name="<?php echo $this->get_field_name('type'); ?>" value="Pages" <?php if($type=='Pages'){ echo 'checked="checked"'; } ?> /> <?php _e('Pages'); ?></label> <label for="Categories"><input type="radio" id="Categories" name="<?php echo $this->get_field_name('type'); ?>" value="Categories" <?php if($type=='Categories'){ echo 'checked="checked"'; } ?>/> <?php _e('Categories'); ?></label></p>
			
		<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small>Page IDs, separated by commas.</small></p>
			
		<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $this->get_field_id('login'); ?>"><?php _e( 'Add login/logout' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('admin'); ?>" name="<?php echo $this->get_field_name('admin'); ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $this->get_field_id('admin'); ?>"><?php _e( 'Add Register/Site Admin' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('vertical'); ?>" name="<?php echo $this->get_field_name('vertical'); ?>"<?php checked( $vertical ); ?> />
		<label for="<?php echo $this->get_field_id('vertical'); ?>"><?php _e( 'Vertical menu ' ); ?></label>
		</p>
		
		<p><label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Inline Style:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>" type="text" value="<?php echo $inline_style; ?>" /></label><br /> 
			<small>Applied to menu container &lt;div&gt;.</small></p>
			
<div class="widget-control-actions alignright">
<p><small><a href="options-general.php?page=dropdown-menu">Menu Style</a> | <a href="http://shailan.com/wordpress/plugins/dropdown-menu">Visit plugin site</a></small></p>
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
add_action('admin_menu', array('shailan_DropdownWidget', 'adminMenu'));

include('shailan-page-walker.php'); // Load custom page walker
include('shailan-category-walker.php'); // Load custom category walker

function shailan_dropdown_menu(){
	$type = get_option('shailan_dm_type');
	$exclude = get_option('shailan_dm_exclude');
	$inline_style = get_option('shailan_dm_style');
	$login = (bool) get_option('shailan_dm_login');
	$admin = (bool) get_option('shailan_dm_admin');
	$vertical = (bool) get_option('shailan_dm_vertical');
	
	$args = array(
		'type' => $type,
		'exclude' => $exclude,
		'style' => $inline_style,
		'login' => $login,
		'admin' => $admin,
		'vertical' => $vertical
		);

	the_widget('shailan_DropdownWidget', $args);
}