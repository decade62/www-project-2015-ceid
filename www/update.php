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
try {
	// 1. get all pages
	$sql="select * from pages";
	$res=mysqli_query($con, $sql);
	while($row=mysqli_fetch_array($res, MYSQLI_BOTH)) {
		// 2. update each one of them
		$location=NULL;
		$pagename = "/" .$row['page_id'];
		$pagename_total_events = $pagename."/events?since=now";
		$request_total_events = new FacebookRequest($session,'GET',$pagename_total_events);
		$request_page = new FacebookRequest($session,'GET',$pagename);
		$response_total_events = $request_total_events -> execute();
		$response_page = $request_page -> execute();
		$graphObject_total_events = $response_total_events -> getGraphObject();
		$graphObject_page = $response_page -> getGraphObject();
		$page_total_events = $graphObject_total_events -> getProperty('data');
		$page_category = $graphObject_page -> getProperty('category');
		$i=0;
		// while an event exists add it
		if(count($page_total_events)>0) {
			while ($page_total_events->getProperty($i)) {
				//////////////////////////////////////////////////////////////////////////////////
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
				///////////////////////////////////////////////////////////////////////////////////
				$request_event = new FacebookRequest($session, 'GET', '/'.$page_total_events->getProperty($i)->getProperty('id'));
				$request_photo = new FacebookRequest($session, 'GET', '/'.$page_total_events->getProperty($i)->getProperty('id').'?fields=cover');
				$response_event = $request_event -> execute();
				$response_photo = $request_photo -> execute();
				$graphObject_event = $response_event -> getGraphObject();
				$graphObject_photo = $response_photo -> getGraphObject();
				$sql2 = "insert into events (id , name , date , cover_url , owner_name , description , place , owner_id , loc_x , loc_y , category, last_updated) values ('".$page_total_events->getProperty($i)->getProperty('id')."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('name'))."', '".strtotime($graphObject_event->getProperty('start_time'))."', '".$graphObject_photo->getProperty('cover')->getProperty('source')."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('owner')->getProperty('name'))."', '".mysqli_real_escape_string($con, $graphObject_event->getProperty('description'))."', '".$page_location."', '".$row['page_id']."', '".$loc_x."', '".$loc_y."', '".$page_category."', '".strtotime($graphObject_event->getProperty('start_time'))."') ON DUPLICATE KEY UPDATE id=values(id), name=values(name), date=values(date), cover_url=values(cover_url), owner_name=values(owner_name), description=values(description), place=values(place), owner_id=values(owner_id), loc_x=values(loc_x), loc_y=values(loc_y), category=values(category), last_updated=values(last_updated)";
				$res2 = mysqli_query($con , $sql2);
				++$i;
			}
		}
	}
//"<error>SQL query fatal error!</error>\n"
$msg = "<success>".htmlspecialchars("Pages' events successfully updated!")."</success>\n";
// CLEANUP PAST EVENTS
mysqli_query($con, "delete from events where date < ".time());
// Generate RSS feed
$res=mysqli_query($con, "select distinct category from pages order by category");
while($tmp = mysqli_fetch_array($res, MYSQLI_BOTH)) {
	$rss=
'<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
	<channel>
	<title>'.'Συλλογή Εκδηλώσεων - '.htmlspecialchars($tmp['category']).'</title>
	<description>'.'Συλλογή Εκδηλώσεων - '.htmlspecialchars($tmp['category']).'</description>
	<link>'.'http://snf-464596.vm.okeanos.grnet.gr/test/home.php?cat='.htmlspecialchars($tmp['category']).'</link>
';
	$res2=mysqli_query($con, "select * from events where category = '".$tmp['category']."' and date > ".time()." order by last_updated desc");
	// if res2==false SQL ERROR!!!!
	$i=0;
	while(($tmp2 = mysqli_fetch_array($res2, MYSQLI_BOTH)) && $i<10) {
		$rss.=
'	<item>
		<title>'.htmlspecialchars($tmp2['name']).'</title>
		<description>'.htmlspecialchars($tmp2['name']).'</description>
		<link>http://snf-464596.vm.okeanos.grnet.gr/test/home.php?id='.$tmp2['id'].'</link>
		<pubDate>'.date("d-m-Y    H:i", $tmp2['last_updated']).'</pubDate>
	</item>
';
		$i++;
	}
$rss.=
'	</channel>
</rss>';
file_put_contents('/var/www/html/test/rss/'.preg_replace("([^\w\d\-_~.])", '', $tmp['category']).'.xml', $rss);
}
}
catch (Facebook\FacebookAuthorizationException $e) {
	$msg = "<error>".htmlspecialchars($row['name'])." does not exist on Facebook! Please delete it! Update aborted!</error>\n";
}

echo "<?xml version=\"1.0\" ?>\n" . $msg;
?>


