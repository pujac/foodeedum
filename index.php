<?php
/**
 * @file
 * The PHP page that serves all page requests 
 */
//include necessary files
require_once 'includes/define.php';
require_once 'includes/functions.php';
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
       <?php echo $output; ?>
    </div>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>