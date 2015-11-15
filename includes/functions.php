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
	if(isset($path[1])){
		$result['path_type_id'] = filter_var($path[1], FILTER_SANITIZE_NUMBER_INT);
	}	
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
	mysqli_close($conn);
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
		if(isset($path['path_type_id'])){
			return call_user_func_array($path['path_type'], array($path['path_type_id']));
		}
		else{
			return call_user_func_array($path['path_type'], array());
		}
		
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
  mysqli_close($conn);	
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
				$result = page($menu_details);
				break;
			case 'product':
			  $result = product($menu_details);
			default :
			  $result = 'form';
				break;
		}	
	}
	else{
		$result = "no content";
	}
	return $result;
	
}

//page details
function page($page_details){
	$header_menu = header_menu();
	return $header_menu . $page_details['id'];
}
//product_details
function product($product_details){
	$header_menu = header_menu();
	return $header_menu . $product_details['id'];
}
//register form
function register(){
	session_start();
	if((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['uid']) && ($_SESSION['uid'] != ''))){
		//user already logged in
		  if(isset($_GET['from'])){
			  header("Location: " . $_GET['from']);
			}
			else{
			   header("Location: " . BASE_URL);				
			}
	}
	elseif(isset($_POST['btn-signup'])){
		$form_values = $_POST;
		$form_values['uname'] = sanitise_input($form_values['uname']);
		$form_values['email'] = sanitise_input($form_values['email']);
		$form_values['pass'] = sanitise_input($form_values['pass']);
		$output_validate = register_validate($form_values);
		if($output_validate['status']){
			//save data
			$save_status = register_submit($form_values);
			if($save_status){
				if(isset($_GET['from'])){
					header("Location: " . $_GET['from']);
				}
				else{
					header("Location: " . BASE_URL);				
				}
			}
			else{
				$body = register_form($form_values);
				$body .= '<div class="wrapper"><article><ul><li>' . SAVING_ERROR . '</li></ul></article></div>';
			}
		}
		else{
			$body = register_form($form_values);
			$body .= $output_validate['message'];
		}
	}
	else{
		$body = register_form(array());
	}
	$heading = heading();
	$output = '<header>' . $heading . '</header>';
	$output .= $body;
	return $output;
}
//register form
function register_form($form_values){
	if(!empty($form_values)){
		$uname = 'value="' . $form_values['uname'] . '"';
		$email = 'value="' . $form_values['email'] . '"';
	}
	else{
		$uname = '';
		$email = '';
	}
	$form = '<div id="register" class="wrapper">
	          <article>
								<form id="register" method="post" class="register">
								<table align="center" width="30%" border="0">
								<tr>
								<td><input type="text" name="uname" placeholder="User Name" required ' . $uname . '/></td>
								</tr>
								<tr>
								<td><input type="email" name="email" placeholder="Your Email" required ' . $email .'/></td>
								</tr>
								<tr>
								<td><input type="password" name="pass" placeholder="Your Password" required /></td>
								</tr>
								<tr>
								<td><button type="submit" name="btn-signup">Sign Me Up</button></td>
								</tr>
								</table>
								</form>
							</article>
						</div>';
	return $form;

}
//register validate
function register_validate($form_values){
	$uname = $form_values['uname'];
	$email = $form_values['email'];
	$pass = $form_values['pass'];
	$error = 0;
	$message = array();
  //check uname lenght
	if(empty($uname)){
		$error ++;
		$message[] = EMPTY_UNAME_ERROR_MSG;
	}
	elseif(strlen($uname) > UNAME_MAX_LENGTH){
		$error ++;
		$message[] = UNAME_LEN_ERROR_MSG;
	}
	elseif(empty($email)){
		$error ++;
		$message[] = EMPTY_EMAIL_ERROR_MSG;
	}	
	elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$error ++;
		array_push($message, INVALID_EMAIL_ERROR_MSG);
	}
	elseif(duplicate_email($email)){
		$error ++;
		array_push($message, DUPLICATE_EMAIL_ERROR_MSG);
	}
	elseif(empty($pass)){
		$error ++;
		array_push($message, EMPTY_PASS_ERROR_MSG);
	}
	elseif(strlen($pass) > PASS_MAX_LENGTH){
		$error ++;
		array_push($message, PASS_LEN_ERROR_MSG);
	}
	if($error){
		$output['status'] = 0;
		$message_list = '';
		foreach($message AS $value){
			$message_list .= '<li>' . $value . '</li>';
		}
		$output['message'] = '<div class="wrapper"><article><ul>' . $message_list . '</ul></article></div>';
	}
	else{
		$output['status'] = 1;
	}
	return $output;
}
//register a user submit
function register_submit($form_values){
	$params['columns'] = array('username','email','password', 'created', 'updated', 'status');
	$params['values_modifiers'] = array("'%s'", "'%s'", "'%s'", "'%s'", "'%s'", '%d');
	$params['args'] = array($form_values['uname'], $form_values['email'], md5($form_values['pass']), 'NOW()', 'NOW()', ACTIVE_USER_STATUS);
	$params['table_name'] = 'users';
	$uid = insert_information($params);
	if($uid){
		session_start();
		$_SESSION['uid'] = $uid;
    return TRUE;		
	}
	else{
		return FALSE;
	}
}

//header  menu
function header_menu(){
	$heading = heading();
	$output = '<header>' . $heading . '<nav><ul id="nav">';
	$menu_details = menu_list(HEADER_MENU);
	if(!empty($menu_details)){
		$menu_list = '';
		foreach($menu_details AS $value) {
			$path = get_path();
			if(isset($path['path_type_id']) && ($path['path_type_id'] == $value['id'])){
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
	$output .= $menu_list . '</ul></nav></header>';
	return $output;
}

//site heading
function heading(){
	session_start();
	if(isset($_SESSION['uid'])){
		$link = BASE_URL . '?q=logout';
		$login_link = '<ul><li><a href="' . $link . '">Logout</a></li></ul>';
	}
	else{
		$register_link = BASE_URL . '?q=register';
		$login = BASE_URL . '?q=login';
		$login_link = '<ul><li><a href="' . $register_link . '">Register</a></li><li><a href="' . $login . '">Login</a></li></ul>';
	}
	$html_output = $login_link . '<hgroup class="intro">
		<h1 class="title">Frenben</h1>
		<h3 class="tagline"></h3></hgroup>';
	return $html_output;
}
//sanitise input
function sanitise_input($data){
	$data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
//check whether duplicate email exists or not
function duplicate_email($email){
	$sql = "SELECT uid FROM users WHERE email = '%s'";
	$sql = format_sql($sql, array($email));
	$conn = db_connection();
	$result = mysqli_query($conn, $sql);
	if(mysqli_num_rows($result) > 0){
		//email exists
		$output =  TRUE;
	}
	else{
		$output = FALSE;
	}
	mysqli_close($conn);
	return $output;
}
//insert information
function insert_information($params, $single_insert = TRUE){
	$column_string = '(' . implode(',', $params['columns']) . ')';
	if($single_insert){
		$values_modifiers = '(' . implode(',', $params['values_modifiers']) . ')';
	}
	else{
		//do later
	}
	$sql = "INSERT INTO " . $params['table_name'] . $column_string . " VALUES " . $values_modifiers;
	$sql = format_sql($sql, $params['args']);
	$conn = db_connection();
	$result = mysqli_query($conn, $sql);
	$last_id = mysqli_insert_id($conn);	
	mysqli_close($conn);
	return $last_id;
}
//login
function login(){
	session_start();
  if((session_status() == PHP_SESSION_ACTIVE) && (isset($_SESSION['uid']) && ($_SESSION['uid'] != ''))){
		//user already logged in
		  if(isset($_GET['from'])){
			  header("Location: " . $_GET['from']);
			}
			else{
			   header("Location: " . BASE_URL);				
			}
	}
	elseif(isset($_POST['btn-signin'])){
		$form_values = $_POST;
		$form_values['email'] = sanitise_input($form_values['email']);
		$form_values['pass'] = sanitise_input($form_values['pass']);
		$output_validate = login_validate($form_values);
		if($output_validate['status']){
			$uid = $output_validate['uid'];
			login_submit($uid);
			if(isset($_GET['from'])){
			  header("Location: " . $_GET['from']);
			}
			else{
			  header("Location: " . BASE_URL);				
			}
		}
		else{
			$body = login_form($form_values);
			$body .= $output_validate['message'];
		}
	}
	else{
		$body = login_form(array());
	}
	$heading = heading();
	$output = '<header>' . $heading . '</header>';
	$output .= $body;
	return $output;
}
//login form
function login_form($form_values){
	if(!empty($form_values)){
		$email = 'value="' . $form_values['email'] . '"';
	}
	else{
    //do nothing
		$email = '';
	}
	$form = '<div id="login" class="wrapper">
	          <article>
								<form id="login" method="post" class="login">
								<table align="center" width="30%" border="0">
								<tr>
								<td><input type="email" name="email" placeholder="Your Email" required ' . $email .'/></td>
								</tr>
								<tr>
								<td><input type="password" name="pass" placeholder="Your Password" required /></td>
								</tr>
								<tr>
								<td><button type="submit" name="btn-signin">Login</button></td>
								</tr>
								</table>
								</form>
							</article>
						</div>';
	return $form;
}
//login validate
function login_validate($form_values){
	$email = $form_values['email'];
	$pass = $form_values['pass'];
	$error = 0;
	$message = array();
	if(empty($email)){
		$error ++;
		$message[] = EMPTY_EMAIL_ERROR_MSG;
	}	
	elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$error ++;
		array_push($message, INVALID_EMAIL_ERROR_MSG);
	}
	elseif(empty($pass)){
		$error ++;
		array_push($message, EMPTY_PASS_ERROR_MSG);
	}
	else{
		$user_exists = check_email_pass_combo($form_values);
		if(!$user_exists['status']){
			$error ++;
		  array_push($message, INVALID_EMAIL_PASS_COMBO);
		}
		else{
			$uid = $user_exists['uid'];
		}
	}
	if($error){
		$output['status'] = 0;
		$message_list = '';
		foreach($message AS $value){
			$message_list .= '<li>' . $value . '</li>';
		}
		$output['message'] = '<div class="wrapper"><article><ul>' . $message_list . '</ul></article></div>';
	}
	else{
		$output['status'] = 1;
		$output['uid'] = $uid;
	}
	return $output;
}

function login_submit($uid){
	session_start();
	$_SESSION['uid'] = $uid;
}

//check email password combo
function check_email_pass_combo($form_values){
	$sql = "SELECT uid FROM users WHERE email = '%s' AND password = '%s'";
	$sql = format_sql($sql, array($form_values['email'], MD5($form_values['pass'])));
	$conn = db_connection();
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		while($row = mysqli_fetch_assoc($result)){
			$output['status'] = 1;
			$output['uid'] = $row['uid'];
		}
	}
	else{
		$output['status'] = 0;
	}
	mysqli_close($conn);
	return $output;
}

//logout 
function logout(){
	session_start();
	session_destroy();
	header("Location: " . BASE_URL );
}
?>
