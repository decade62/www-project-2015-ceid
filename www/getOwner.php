<?php
// $_GET['name'] required to add page
/*echo "<script type=\"text/javascript\">alert('".$_GET['hash'].$_GET['id']."');</script>";*/
// requires_once $con global variable and connect.php
header('Content-Type: text/xml');
require_once('autoload.php');
require_once('connect.php');
use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	FacebookSession::setDefaultApplication('472957652867456', '472957652867456|1SZFleLD-60O_WSdGnfFsya2nTg');
	$session = new FacebookSession('472957652867456|1SZFleLD-60O_WSdGnfFsya2nTg');
$msg="";
$pw = mysqli_fetch_array(mysqli_query($con, "select hash from password"), MYSQL_BOTH);
// secure page from haxorzzzz
if($_POST['hash']==$pw['hash']) {
$pagename = preg_split('@[/]@', $_POST['name']);
$pagename = (strlen($pagename[count($pagename)-1])<2)?$pagename[count($pagename)-2]:$pagename[count($pagename)-1];
$pagename = preg_split('@[?]@', $pagename);
$pagename = $pagename[0];
$pagename = "/" . $pagename;
try {
$request_page = new FacebookRequest($session, 'GET', $pagename);
$response_page = $request_page -> execute();
$graphObject_page = $response_page -> getGraphObject();
$page_name     = $graphObject_page -> getProperty('name');
$all_properties = $graphObject_page -> getPropertyNames();
$page_location=NULL;
$page_photo=NULL;
foreach($all_properties as $name) {
	if($name=="location") $location      = $graphObject_page -> getProperty('location');
	if($name=="cover")    $page_photo    = $graphObject_page -> getProperty('cover') -> getProperty('source');
}
if(!isset($page_photo)) $page_photo = "";
if(!isset($location)) $page_location = "καμία διαθέσιμη";
else $page_location =  $location -> getProperty('street') . ', ' .
	 $location -> getProperty('city') . ', ' .
	 $location -> getProperty('zip') . '   '.
	 $location -> getProperty('country');

		$msg = "<newpage>\n". //returns name, photo, location of a page
			   "<name>". htmlspecialchars($page_name) . "</name>\n".
			   "<photo>". htmlspecialchars($page_photo) . "</photo>\n".
			   "<location>". htmlspecialchars($page_location) . "</location>\n".
			   "</newpage>\n";
}
catch (Facebook\FacebookAuthorizationException $e) {
	$msg = "<error>Page does not exist!</error>\n";
}
}
else $msg = "<error>Someone is playing with POSTs!</error>\n";


echo "<?xml version=\"1.0\" ?>\n" . $msg;
?>


