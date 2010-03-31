<?php 

class shailan_MultiDropDown extends WP_Widget {
    /** constructor */
    function shailan_MultiDropDown() {
		$widget_ops = array('classname' => 'shailan-dropdown-menu', 'description' => __( 'Dropdown page & category menu', 'shailan-dropdown-menu' ) );
		$this->WP_Widget('multi-dropdown-menu', __('Dropdown Multi', 'shailan-multi-dropdown'), $widget_ops);
		$this->alt_option_name = 'widget_multi_dropdown';	
		
    }
	
    /** @see WP_Widget::widget */
    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
		
		$include_pages = (bool) $instance['pages'];
		$include_categories = (bool) $instance['categories'];
		$include_links = (bool) $instance['links'];
		
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
							
					
					<?php if($include_pages){ ?>
					
						<?php 
						$page_walker = new shailan_PageWalker();
						wp_list_pages(array(
							'walker'=>$page_walker,
							'sort_column'=>'menu_order',
							'depth'=>'4',
							'title_li'=>'',
							'exclude'=>$exclude
							)); ?>
							
					<?php }; if($include_categories){ ?>
					
						<?php 
						$cat_walker = new shailan_CategoryWalker();
						wp_list_categories(array(
							'walker'=>$cat_walker,
							'order_by'=>'name',
							'depth'=>'4',
							'title_li'=>'',
							'exclude'=>$exclude
							)); ?>			
							
					<?php }; ?>
					
					<?php if($include_links){ ?>
					<li> <a href="#"><span>Links</span></a>
					<ul>
						<?php wp_list_bookmarks('title_li=&category_before=&category_after=&categorize=0'); ?>
					</ul>
					</li>
					<? } ?>
					
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
	
        $title = apply_filters('widget_title', $instance['title']);
		
		$include_pages = (bool) $instance['pages'];
		$include_categories = (bool) $instance['categories'];
		$include_links = (bool) $instance['links'];
		
		$exclude = $instance['exclude'];
		
		$inline_style = $instance['style'];
		
		$home = (bool) $instance['home'];
		$login = (bool) $instance['login'];
		$admin = (bool) $instance['admin'];
		
		$vertical = (bool) $instance['vertical'];
		
		$align = $instance['align'];
		
        ?>		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (won\'t be shown):', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
			
		<p> Includes: <br/>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('pages'); ?>" name="<?php echo $this->get_field_name('pages'); ?>"<?php checked( $include_pages ); ?> />
		<label for="<?php echo $this->get_field_id('pages'); ?>"><?php _e( 'Pages' , 'shailan-dropdown-menu' ); ?></label><br />
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('categories'); ?>" name="<?php echo $this->get_field_name('categories'); ?>"<?php checked( $include_categories ); ?> />
		<label for="<?php echo $this->get_field_id('categories'); ?>"><?php _e( 'Categories' , 'shailan-dropdown-menu' ); ?></label><br />
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('links'); ?>" name="<?php echo $this->get_field_name('links'); ?>"<?php checked( $include_links ); ?> />
		<label for="<?php echo $this->get_field_id('links'); ?>"><?php _e( 'Links' , 'shailan-dropdown-menu' ); ?></label><br />		
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('home'); ?>" name="<?php echo $this->get_field_name('home'); ?>"<?php checked( $home ); ?> />
		<label for="<?php echo $this->get_field_id('home'); ?>"><?php _e( 'Homepage link' , 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('login'); ?>" name="<?php echo $this->get_field_name('login'); ?>"<?php checked( $login ); ?> />
		<label for="<?php echo $this->get_field_id('login'); ?>"><?php _e( 'Login/logout' , 'shailan-dropdown-menu' ); ?></label><br />
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('admin'); ?>" name="<?php echo $this->get_field_name('admin'); ?>"<?php checked( $admin ); ?> />
		<label for="<?php echo $this->get_field_id('admin'); ?>"><?php _e( 'Register/Site Admin' , 'shailan-dropdown-menu' ); ?></label>
		</p>
		
				<p><label for="<?php echo $this->get_field_id('exclude'); ?>"><?php _e('Exclude:', 'shailan-dropdown-menu'); ?> <input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" /></label><br /> 
		<small>Page IDs, separated by commas.</small></p>
		
		<p>
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

} // class shailan_MultiDropDown

// register widget
add_action('widgets_init', create_function('', 'return register_widget("shailan_MultiDropDown");'));