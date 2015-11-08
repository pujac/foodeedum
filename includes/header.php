<?php
	$menu_details = menu_list(HEADER_MENU);
	if(!empty($menu_details)){
		$menu_list = '';
		foreach($menu_details AS $value) {
			$path = get_path();
			if($path['path_type_id'] == $value['id']){
				$class = 'current';
			}
			else{
				$class = '';
			}
		  $menu_list .= '<li><a href="?q=menu/' . $value['id'] . '" class="' . $class . '">' . $value['name'] . '</a></li>';
		}
	}
	else{
		//do nothing
	}
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Foodeedum</title>
		<link href="css/style.css" rel="stylesheet" type="text/css" media="screen" />
		<link href="css/base.css" rel="stylesheet" type="text/css" media="screen" />
		<script type="text/javascript" src=" https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.js"></script>
		<script type="text/javascript" src="scripts/jquery.pikachoose.js"></script>
	</head>
	<body>
	  <div id="container">
			<header>
				<hgroup class="intro">
					<h1 class="title">Frenben</h1>
					<h3 class="tagline"></h3>
				</hgroup>
				<nav>
					<ul id="nav">
						<?php echo $menu_list; ?>
					</ul>
				</nav>
			</header> 
