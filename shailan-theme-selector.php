<script type="text/javascript">
function selectTheme(theme){
	alert(theme);
}
</script>
<?php foreach($themes as $name=>$path){
				$selected = ($theme == $path ? 'selected' : '');  
				echo '<div style="display:inline; float:left;text-align:center;font-size:0.8em; border:1px solid #ddd; margin:2px; padding:3px;"><a href="#" onclick="selectTheme(\''.$path.'\');return false;"><img src="'.WP_PLUGIN_URL.'/'.SHAILAN_DM_FOLDER.'/themes/'.$path.'.jpg" class="'.$selected.'" style="width:100px; border:1px solid #999;" title="'.$name.'" alt="'.$name.'"/></a><br />'.$name.'</div>';
			} ?>