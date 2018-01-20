<?php
// $_POST['name'] required to add page
/*echo "<script type=\"text/javascript\">alert('".$_POST['hash'].$_POST['id']."');</script>";*/
// requires_once $con global variable and connect.php
header('Content-Type: text/xml');
require_once('connect.php');
require_once('autoload.php');
use Facebook\FacebookSession;
	use Facebook\FacebookRequest;
	FacebookSession::setDefaultApplication('472957652867456', '472957652867456|1SZFleLD-60O_WSdGnfFsya2nTg');
	$session = new FacebookSession('472957652867456|1SZFleLD-60O_WSdGnfFsya2nTg');
$msg="";
$pw = mysqli_fetch_array(mysqli_query($con, "select hash from password"), MYSQL_BOTH);
// secure page from haxorzzzz
if($_POST['hash']==$pw['hash']) {
$pagename = "/" .$_POST['name'];
$pagename_total_events = $pagename."/events?since=now";
try {
$request_page = new FacebookRequest($session, 'GET', $pagename);
$request_total_events = new FacebookRequest($session,'GET',$pagename_total_events);
$response_page = $request_page -> execute();
$response_total_events = $request_total_events -> execute();
$graphObject_page = $response_page -> getGraphObject();
$graphObject_total_events = $response_total_events -> getGraphObject();
$page_id = $graphObject_page -> getProperty('id');
$page_name = $graphObject_page -> getProperty('name');
$page_category = $graphObject_page -> getProperty('category');
$page_total_events = $graphObject_total_events -> getProperty('data');

$exists = "select * from pages where page_id = $page_id";
// check if page already exists
$res = mysqli_query($con , $exists);
// exception needed
if (!(mysqli_fetch_array($res , MYSQLI_BOTH))) {
	// if page doesn't exist, insert it
	$sql = "insert into pages values('$page_id', '$page_name', '$page_category')";
	if(mysqli_query($con , $sql)) {
		$i=0;
		// while an event exists add it
		if(count($page_total_events)>0) {
			while ($page_total_events->getProperty($i)) {
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$street=$city=$zip=$country='';
				$loc_x=$loc_y=0.0;
				$page_location=NULL;
				$all_properties = $graphObject_page -> getPropertyNames();
				foreach($all_properties as $name) {
					if($name=="location") {
						$page_location='';
						$location = $graphObject_page -> getProperty('location');
						$loc_prop = $location -> getPropertyNames();
						foreach($loc_prop as $name2) {
							if($name2=='street') $street = $location -> getProperty('street');
							if($name2=='city') $city = $location -> getProperty('city');
							if($name2=='zip') $zip = $location -> getProperty('zip');
							if($name2=='country') $country = $location -> getProperty('country');
							if($name2=='longitude') $loc_y = $location -> getProperty('longitude');
							if($name2=='latitude') $loc_x = $location -> getProperty('latitude');
						}
					}
				}
				if(!isset($location)) $page_location = "καμία διαθέσιμη";
				else $page_location =  $street . ', ' . $city . ', ' .$zip . '   '. $country;
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$request_event = new FacebookRequest($session, 'GET', '/'.$page_total_events->getProperty($i)->getProperty('id'));
				$cover_url="nocover.jpg";
				//try {
					$request_photo = new FacebookRequest($session, 'GET', '/'.$page_total_events->getProperty($i)->getProperty('id').'?fields=cover');
					$response_photo = $request_photo -> execute();
					$graphObject_photo = $response_photo -> getGraphObject();
					$cover = $graphObject_photo-> getPropertyNames();
					foreach($cover as $name) {
						if($name=="cover") $cover_url = $graphObject_photo->getProperty('cover')->getProperty('source');
					}
					
				//}
				//catch(Facebook\FacebookRequestException $e) {
				//}
				$response_event = $request_event -> execute();
				$graphObject_event = $response_event -> getGraphObject();
				$sql2 = "insert into events values ('".$page_total_events->getProperty($i)->getProperty('id')."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('name'))."', '".strtotime($graphObject_event->getProperty('start_time'))."', '".$cover_url."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('owner')->getProperty('name'))."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('description'))."', '".$page_location."', '".$page_id."', '".$loc_x."', '".$loc_y."', '".$page_category."', '".strtotime($graphObject_event->getProperty('updated_time'))."')";
				$res2 = mysqli_query($con , $sql2);
				++$i;
			}
		}
		$msg = "<newpage>\n". //returns page_id, name, category of a page
			   "<page_id>". $page_id . "</page_id>\n".
			   "<name>". $page_name . "</name>\n".
			   "<category>". $page_category . "</category>\n".
			   "</newpage>\n";
	}
	else $msg =  "<error>SQL query fatal error!</error>\n";
}
else $msg = "<error>Page already exists!</error>\n";

}
catch (Facebook\FacebookAuthorizationException $e) {
	$msg = "<error>Page does not exist!</error>\n";
}
}
else $msg = "<error>Someone is playing with POSTs!</error>\n";

echo "<?xml version=\"1.0\" ?>\n" . $msg;
?>


