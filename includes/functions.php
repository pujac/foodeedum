<?php
//check wehther db_config is present or not
function check_db_config(){
	foreach (glob('config/*.php') as $filename){
		$path_to_file_array = explode('/', $filename);
		$filename_array[] = $path_to_file_array[1];
  }
	if(empty($filename_array) ){
		die("No file in config folder. Add db_config.php file along with other relevant files ");
	}
	elseif(!in_array('db_config.php', $filename_array)){
		die("No db_config.php file in config folder. Add db_config.php file with proper db credentials");
	}
}
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
//created db connection
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
function menu_list($menu_type = NULL, $menu_id = NULL){	
  $sql = "SELECT MI.* FROM menu_items MI 
	        JOIN menu_type_mapping MTM ON MTM.menu_item_id = MI.id
					JOIN menu_type MT ON MT.id = MTM.menu_type_id 
					WHERE MT.name = '%s'";
  if(empty($menu_type)){
		$menu_type = HEADER_MENU;
	}
	$arg = array($menu_type);
	if(!empty($menu_id)){
		$sql .= " AND MI.id = %d";
		array_push($arg, $menu_id);
	}
	$sql = format_sql($sql, $arg);
	$conn = db_connection();
	$result = mysqli_query($conn, $sql);
	$count = 0;
	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
			$menu_links[$count]['id'] = $row['id'];
			$menu_links[$count]['name'] = $row['name'];
			$menu_links[$count]['content_type'] = $row['content_type'];
			$menu_links[$count]['created'] = $row['created'];
			$menu_links[$count]['updated'] = $row['updated'];
			$count ++;
		}
	} 
	else {
		$menu_links = array();
	}
	return $menu_links;	
}
//for the sql query
function format_sql($sql, $arg){	
	$sql = vsprintf($sql, $arg);
	return $sql;	
}
//menu handler function
function menu_handler(){
	$path = get_path();	
	if(isset($path['path_type'])){
		return call_user_func_array($path['path_type'], array($path['path_type_id']));
	} 
}
//get menu type
function get_menu_type($menu_id){
	$sql = "SELECT MT.* FROM menu_type MT
	        JOIN menu_type_mapping MTP ON MTP.menu_type_id = MT.id
					WHERE MTP.menu_item_id = %d";
	$sql = format_sql($sql, array($menu_id));
	$conn = db_connection();
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		// output data of each row
		while($row = mysqli_fetch_assoc($result)) {
      $menu_type_details = $row;
		}
	} 
	else {
		$menu_type_details = array();
	}				
	return $menu_type_details;
}
//menu details
function menu($menu_id){
	//get menu type
	$menu_type_details = get_menu_type($menu_id);
	//get details
	$menu_details_array = menu_list($menu_type_details['name'], $menu_id);
	$menu_details = $menu_details_array[0];
  $output = menu_content_details($menu_details);
	return $output;
}
//content details of a menu
function menu_content_details($menu_details){
	if(isset($menu_details['content_type'])){
	  switch($menu_details['content_type']){
			case 'page':
				$result = "pages";
				break;
			case 'product':
			  $result = "product";
				break;
			default :
			  $result = "form";
				break;
		}	
	}
	else{
		$result = "no content";
	}
	return $result;
	
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
