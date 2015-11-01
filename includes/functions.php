<?php
//get the path	
function get_path(){
	if(isset($_GET['q'])){
		$path = $_GET['q'];
	} 
	else{
		$path = 'menu/1';
	}
	$path =  explode('/', $path);
	$result['path_type'] = filter_var($path[0], FILTER_SANITIZE_STRING);
	$result['path_type_id'] = filter_var($path[1], FILTER_SANITIZE_NUMBER_INT);
	return $result;
}	
//
function db_connection(){
	// Create connection
	$conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	// Check connection
	if (!$conn) {
	  die("Connection failed: " . mysqli_connect_error());
	}
	else{
		return $conn;
	}
}
//get the list of menus for a particular menu type
function menu_list($menu_type = NULL){
	if(empty($menu_type)){
		$menu_type = HEADER_MENU;
	}
	$conn = db_connection();
  $sql = sprintf("SELECT MI.id, MI.name FROM menu_items MI 
	        JOIN menu_type_mapping MTM ON MTM.menu_item_id = MI.id
					JOIN menu_type MT ON MT.id = MTM.menu_type_id 
					WHERE MT.name = '%s'", $menu_type);
	$result = mysqli_query($conn, $sql);
	$count = 0;
	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$menu_links[$count]['id'] = $row['id'];
			$menu_links[$count]['name'] = $row['name'];
			$count ++;
		}
	} 
	else {
		$menu_links = array();
	}
	return $menu_links;	
}
function menu_handler(){
	$path = get_path();	
	if(isset($path['path_type'])){
		return call_user_func_array($path['path_type'], array($path['path_type_id']));
	} 
}
//menu details
function menu($menu_id){
	return $menu_id;
}
//page details
function page($page_id){
	return $page_id;
}
//product_details
function product($product_id){
	return $product_id;
}
?>
