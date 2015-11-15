<?php
/**
 * @file
 * The PHP page that serves all page requests 
 */
//include necessary files
foreach (glob('config/*.php') as $filename){
  require_once $filename;
}
require_once 'includes/define.php';
require_once 'includes/functions.php';
check_db_config();
require_once 'includes/header.php';
$return = menu_handler();
$output = '';
if (is_int($return)) {
  switch ($return) {
    case MENU_NOT_FOUND:
      $output = "page not found";
      break;
  }
}
elseif (isset($return)) {
  $output = $return;
}
echo $output; 
require_once 'includes/footer.php';
?>

