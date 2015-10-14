<?php
	
function get_path(){
	if(isset($_GET['q'])){
		$path = $_GET['q'];
	} 
	else{
		$path = 'home';
	}
	return $path;
}	
function menu_list(){
	$items['home'] = array(
		'call_back' => 'home', 
		'arguments' => array(),
	);
	$items['about'] = array(
		'call_back' => 'about', 
		'arguments' => array(),
	);
	return $items;
}
function menu_handler(){
	$path = get_path();
	$all_paths = menu_list();
	if(isset($all_paths[$path])){
		return call_user_func_array($all_paths[$path]['call_back'], $all_paths[$path]['arguments']);
	}
	return $path;
	
	
}
function home(){
	return "In home";
}
function about(){
	return "In about";
}
?>
