<?
session_start();
$myname = '/';
// ====================================
require_once('L/pagenator.inc');
$PGN = new Pagenator();
// array with pages urls and it's titles.
// page url's (ex: one) and files names in directory "C" must be same. (ex: one.php)
$PGN->setAdminPages(array (
				'one'	=>	'Title ONE',
				'two'		=>	'Title TWO',
				'tree'		=>	'Another title'

			));
$PGN->init();
// ====================================
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Your title</title>
</head>

<body>


<?
// #CCFFFF
$M = $PGN->getMenuArray(); // Returns array of menu items for building links.
/*
 page - for creating HTTP link
 class - CSS class, for link hightligting
 	regular_menu_item - CSS class for not active menu
 	selected_menu_item - .. active menu item
 title - just a title for page.
*/
foreach($M as $c => $item) {
	if (substr($item['page'],0,1)!='-') { // hide pages with leading "-" in file name
		echo '<a href="'.$item['page'].'" class="'.$item['class'].'">'.$item['title'].'</a>&nbsp;'."\n";
	}
}

$PGN->buildPage(); // draw content of page here.

?>
</body>
</html>