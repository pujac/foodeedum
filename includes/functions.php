<?php
	
function menu_handler(){
	if(isset($_GET['q'])){
		$path = $_GET['q'];
	} 
	else{
		$path = 1;
	}
	return $path;
	
	
}
	
?>
