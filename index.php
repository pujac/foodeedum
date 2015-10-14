<?php
/**
 * @file
 * The PHP page that serves all page requests 
 */
//include necessary files
require_once 'includes/header.php';
require_once 'includes/define.php';
require_once 'includes/functions.php';

$return = menu_handler();
// Menu status constants are integers; page content is a string.
if (is_int($return)) {
  switch ($return) {
    case MENU_NOT_FOUND:
      $output = "page not found";
      break;
  }
}
elseif (isset($return)) {

}
?>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Connoisseur</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="css/base.css" rel="stylesheet" type="text/css" media="screen" />
	<script type="text/javascript" src=" https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.js"></script>
	<script type="text/javascript" src="scripts/jquery.pikachoose.js"></script>
	</head>
	<body>
    <div id="container">
       <?php print $output; ?>
    </div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>