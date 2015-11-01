<?php
	//echo "here";
	$menu_details = menu_list(HEADER_MENU);
	if(!empty($menu_details)){
		$output = '';
		foreach($menu_details AS $value) {
			$path = get_path();
			if($path['path_type_id'] == $value['id']){
				$class = 'current';
			}
			else{
				$class = '';
			}
		  $output .= '<li><a href="?q=menu/' . $value['id'] . '" class="' . $class . '">' . $value['name'] . '</a></li>';
		}
	}
	else{
		//do nothing
	}

?>
<header>
	<nav>
		<ul id="nav">
		  <?php echo $output; ?>
		</ul>
	</nav>
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
	<br />
</header>