<?php

global $pluginname, $shortname, $options;

$i=0;
 
//if ( @$_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings saved.</strong></p></div>';
//if ( @$_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$pluginname.' settings reset.</strong></p></div>';
 
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) { ?>
<div id="message" class="updated"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php } ?>
<?php if ( isset($_GET['error']) && isset($errors[$_GET['error']]) ) { ?>
<div id="message" class="error"><p><?php echo $errors[$_GET['error']]; ?></p></div>
<?php } ?>


<div class="widget-liquid-left">
<div id="widgets-left">


<?php foreach ($options as $value) {
switch ( $value['type'] ) {
 
case "open":
?>
 
<?php break;
 
case "close":
?>
 
	<br class="clear">
</div>
</div>

 
<?php break;
 
case "title":
?>
<p>To easily use the <?php echo $pluginname;?>, you can use the menu below.</p>

 
<?php break;
 
case 'text':
?>

<div class="rm_input rm_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
<?php
break;
 
case 'textarea':
?>

<div class="rm_input rm_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo $value['std']; } ?></textarea>
 <small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 
 </div>
  
<?php
break;
 
case 'select':
?>

<div class="rm_input rm_select">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
	<!-- <pre>
	<?php print_r($value['options']); ?>
	</pre> -->
	
<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $key=>$option) { ?>
		<option <?php if (get_option( $value['id'] ) == $key) { echo 'selected="selected"'; } ?> value="<?php echo $key; ?>"><?php echo $option; ?></option><?php } ?>
</select>

	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
</div>
<?php
break;
 
case "checkbox":
?>

<div class="rm_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	
<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />


	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
 </div>
<?php break; 
case "section":

$i++;

?>

<div id="available-widgets" class="widgets-holder-wrap">
<div class="sidebar-name"><h3><img src="<?php bloginfo('template_directory')?>/includes/functions/images/trans.png" class="inactive" alt="""><?php echo $value['name']; ?><span style="float:right;"><input name="save<?php echo $i; ?>" type="submit" class="button-primary menu-save" value="Save changes" />
</span></h3><div class="clearfix"></div></div>
<div class="widget-holder">

 
<?php break;
 
}
}
?>
 
<input type="hidden" name="action" value="save" />
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

</div>
</div> <!-- Left widgets -->

<div class="widget-liquid-right" style=""> 
<div id="widgets-right"> 

<div class="widgets-holder-wrap"> 
	<div class="sidebar-name"><h3>Links</h3></div> 

	<div id='widgets-entry-bottom' class='widgets-sortables'> 
	<div class='sidebar-description'><p class='description'>
		<ul>
			<li><a href="http://shailan.com/wordpress/plugins/dropdown-menu">Plugin page</a></li>
			<li><a href="http://shailan.com/">Author website</a></li>
			<li><a href="http://wordpress.org/tags/dropdown-menu-widget">Support</a></li>
		</ul>
		
		
		<?php
			
			$rss_options = array(
				'link' => 'http://shailan.com',
				'url' => 'http://feeds.feedburner.com/shailan',
				'title' => 'Recent blog posts',
				'items' => 3,
				'show_summary' => 0,
				'show_author' => 0,
				'show_date' => 1,
			);


		the_widget('WP_Widget_RSS', $rss_options); ?>
		
		
	</p></div> 
	</div> 
</div> 

<div class="widgets-holder-wrap"> 
	<div class="sidebar-name"><h3>Donate</h3></div> 

	<div id='widgets-entry-bottom' class='widgets-sortables'> 
	<div class='sidebar-description'><p class='description'>
		
	<p>
	Please support if you like this plugin:
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="10214058">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypal.com/tr_TR/i/scr/pixel.gif" width="1" height="1">
	</form>
	</p>
			
	</p></div> 
	</div> 
</div> 	

</div>
</div>
		
		
		<p>
		<a href="http://shailan.com/wordpress/plugins/dropdown-menu">Dropdown Menu <?php echo SHAILAN_DM_VERSION; ?></a> by <a href="http://shailan.com">shailan</a> &copy; 2010
		</p>
		
		</div>
		






