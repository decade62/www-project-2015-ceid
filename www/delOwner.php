<?php
/*echo "<script type=\"text/javascript\">alert('".$_GET['hash'].$_GET['id']."');</script>";*/
// requires_once $con global variable and connect.php
require_once('connect.php');
header('Content-Type: text/xml');
$msg="";
//if($_GET['hash'] == mysqli_fetch_array(mysqli_query($con, "select hash from password"), MYSQL_BOTH)) {
$pw = mysqli_fetch_array(mysqli_query($con, "select hash from password"), MYSQL_BOTH);
if($_POST['hash'] == $pw['hash']) {
	if($_POST['id']==-1) {
		if(mysqli_query($con, "delete from pages")) $msg="<response>All owners/pages successfully deleted!</response>";
		else $msg="<response>An error occured while trying to remove the owners/pages from database!</response>";
	}
	else {
		$row = mysqli_fetch_array(mysqli_query($con, "select name from pages where page_id = '".$_POST['id']."'"), MYSQL_BOTH);
		if($row['name']) {
			mysqli_query($con, "delete from pages where page_id = '".$_POST['id']."'");
			$msg="<response>".$row['name']." successfully deleted!</response>";
		}
		else $msg="<response>Someone is playing with post variables!</response>";
	}
}
else $msg='<response>Anauthorized connection attempt!</response>';

echo "<?xml version=\"1.0\" ?>\n" . $msg;
?>


