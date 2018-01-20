<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style type="text/css">
body,td,th {
	color:#4775A3 ;
}
</style>
<body link="#FFFFCC" vlink="#FFFF9D">
<style type="text/css">

.table {
	top:40px;
	min-width:500px;
	max-width:calc(100% - 750px);
	margin-left:350px;
	position:absolute;
	word-wrap:break-word;
}

#map-container {
	min-width: 1100px;
	max-width:100%;
	height:35%;
	
}

#map-canvas {
		position: fixed;
        height:300px;
		width:300px;
        padding:0px;
		margin-left: 80%;
		margin-top:50px;
}

.admin {
	text-align:right;
	position:relative;
}
.btn {
	text-align:left;
	width:210px;
	/*max-width:180px;*/
	background: linear-gradient(#933051, #561028); 
	/*background: linear-gradient(#074056,#074359);*/
	text-shadow: 1px 1px 1px #0E86B2; 
	color: #FFEBC2;
	display: inline-block;
	border-radius: 4px;
	box-shadow: inset 0 1px 0 rgba(255,255,255,0.2);
	font-family:Georgia, "Times New Roman", Times, serif;
	font-size:14px;
	line-height: 2.5em;
	padding: 0 1em;
	text-decoration: none;

}

.btn:hover { 
  box-shadow: inset 0 1px 1px rgba(255,255,255,0.2), 
  inset 0 1.5em 1em rgba(255,255,255,0.3);
}

.btn:active { 
  box-shadow: inset 0 1px 1px rgba(255,255,255,0.2), 
  inset 0 1.5em 1em rgba(0,0,0,0.3); 
}
.title {
	text-align:center;
	color:#BA7B4E;
	font-size:30px;
	font-family: Palatino Linotype, Book Antiqua, Palatino, serif;
}
.main {
	position:relative;
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif
}

body {
	background-color:#191919;
}

.subtable {
	width:100%;
}

#photo {
	width:10%;
}

#event {
	width:100%;
}

.event_title {
	color:#FFEBC2;
	font-size:19px;
	text-align:left;
	padding-left:15px;
	padding-bottom:5px;
	padding-top:5px;
}

.date {
	min-width:110px;
	/*width:32%;*/
	text-align:right;
	padding-right:15px;
	padding-bottom:5px;
	padding-top:5px;
}

.default {
	text-align:left;
	padding-left:15px;
	padding-bottom:5px;
	padding-top:5px;
}

.description {
	display:block;
	cursor:pointer;
	max-height: 20px;
	overflow:hidden;
	transition-property: max-height;
	-webkit-transition-property: max-height;
       -moz-transition-property: max-height;
         -o-transition-property: max-height;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
       -moz-transition-duration: 1s;
         -o-transition-duration: 1s;
	transition-timing-function: linear;
	-webkit-transition-timing-function: linear;
       -moz-transition-timing-function: linear;
         -o-transition-timing-function: linear;
}

.description:focus {
	cursor:auto;
	max-height:500px;
    transition-property: max-height;
	-webkit-transition-property: max-height;
       -moz-transition-property: max-height;
         -o-transition-property: max-height;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
       -moz-transition-duration: 1s;
         -o-transition-duration: 1s;
	transition-timing-function: linear;
	-webkit-transition-timing-function: linear;
       -moz-transition-timing-function: linear;
         -o-transition-timing-function: linear;
}


.description:focus + .collapse {
	transition-property: opacity;
	-webkit-transition-property: opacity;
       -moz-transition-property: opacity;
         -o-transition-property: opacity;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
       -moz-transition-duration: 1s;
         -o-transition-duration: 1s;
	transition-timing-function: linear;
	-webkit-transition-timing-function: linear;
       -moz-transition-timing-function: linear;
         -o-transition-timing-function: linear;
	opacity:1;
}

.collapse {
	cursor:pointer;
	transition-property: opacity;
	-webkit-transition-property: opacity;
       -moz-transition-property: opacity;
         -o-transition-property: opacity;
	transition-duration: 1s;
	-webkit-transition-duration: 1s;
       -moz-transition-duration: 1s;
         -o-transition-duration: 1s;
	transition-timing-function: linear;
	-webkit-transition-timing-function: linear;
       -moz-transition-timing-function: linear;
         -o-transition-timing-function: linear;
	opacity:0;
	top:0;
	right:-20px;
	position:absolute;
}

</style>
<?php
require_once('connect.php');

function count_sort($a,$b) {
	if ($a['count']==$b['count']) return 0;
	return ($a['count']>$b['count'])?-1:1;
}


// create any sql queries
$events = array();
$search_events=array();


if(count($_GET)>0) {      // WE HAVE GET PARAMETERS
	if(isset($_GET['keys'])) {		// WE HAVE KEYWORDS (TOP PRIORITY)
		$res=mysqli_query($con, "select id, name, description from events");		//get  all events
		while($tmp = mysqli_fetch_array($res, MYSQLI_BOTH)) {
			$tmp['count']=0;														//set events' counter to 0
			array_push($events, $tmp);
		}
		$keys=explode(' ', $_GET['keys']);										// keywords array
		//var_dump($keys);
		foreach($keys as $word) {
			foreach($events as &$cur_event) {
				if(mb_strlen($word, 'utf-8')>3) {
					$cur_event['count'] += substr_count(strtolower($cur_event['name']) . ' '. strtolower($cur_event['description']), strtolower($word));
				}
			}
		}
		usort($events, "count_sort");
		//var_dump($events);
		if($events[0]['count']==0) {
			/*var_dump($events);
			echo "<script>alert('No events found with the given keywords!');</script>";*/
			// return error message
		}
		else {
			for($i=0; $i<count($events); $i++) {
				if($events[$i]['count']>0) {
					/*echo "<script>alert('".$events[$i]['name']."');</script>";*/
					array_push($search_events, mysqli_fetch_array(mysqli_query($con, "select * from events where id='".$events[$i]['id']."' and date >= ".time()), MYSQLI_BOTH));
				}
			}
		}
	}
	else if(isset($_GET['id'])) {
		array_push($search_events, mysqli_fetch_array(mysqli_query($con, "select * from events where id='".mysqli_real_escape_string($con, $_GET['id'])."'"), MYSQLI_BOTH));
		if($search_events[0]==NULL) $search_events=array();
	}
	else {			// WE HAVE FILTERS
		$qry='select * from events where ';
		if(isset($_GET['cat'])) {
		$qry.='category = "'.$_GET['cat'].'" AND ';
		}
		if(isset($_GET['from'])) {
			$qry.='date > "'.strtotime($_GET['from']).'" AND ';
		}
		if(isset($_GET['to'])) {
			$qry.='date < "'.strtotime($_GET['to']).'" AND ';
		}
		$qry.=' date >= '.time().' ORDER BY date';
		$res=mysqli_query($con, $qry);
		while($row = mysqli_fetch_array($res, MYSQLI_BOTH)) {
			array_push($search_events, $row);
		}
	}
}
else {  // NO GET PARAMETERS
	$res=mysqli_query($con, "select * from events where date >= ".time().' order by date desc');
	while($tmp = mysqli_fetch_array($res, MYSQLI_BOTH)) array_push($search_events, $tmp);
}

// PAGING
if(!isset($_GET['page'])) $_GET['page']=0;
$last_page=(count($search_events) <= $_GET['page']*5 + 5)?true:false;
$search_events=array_slice($search_events, intval($_GET['page'])*5, 5);



?>

<head>
<link rel="stylesheet" type="text/css" href="cal/datepicker.css" /> 
<link rel="stylesheet" type="text/css" href="highslide/highslide.css" />
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
<script type="text/javascript" src="cal/datepicker.js"></script>
<script type="text/javascript" src="highslide/highslide.js"></script>
<script type="text/javascript" src="javascript/home.js"></script>

<title>Πλατφόρμα Συλλογής Εκδηλώσεων Από Κοινωνικά Δίκτυα</title>
</head>



<body text="#CC6600">
<a style="float:left" href="http://www.freedomain.co.nr/" target="_blank" title="FreeDomain.co.nr" rel="nofollow"><img src="http://rraonra.imdrv.net/co-nr.gif" width="88" height="31" border="0" alt="FreeDomain.co.nr" /></a>
<p class="admin"><a href="javascript:hide();">Σύνδεση Διαχειριστή</a>
<form action="admin.php" method='post'>
<span id="spoiler" style="display:none; position:absolute; top:36px; right:8px;"><input type="password" maxlength="100" size="10px" id="hash" name="hash"><input type="submit" value="Log In"></span>
</form>
<br><br><span style="text-align:left;"><a href="home.php">Αρχική Σελίδα</a></span>
</p>
<form action="home.php" method="get">
<span style="text-align:right; margin-left:1000px; display:block; white-space:nowrap;"><p style="color:#FFE0A3;font-size:18px">Αναζητήστε:&nbsp;&nbsp;<input type="search" name="keys" id="searchbox">&nbsp;&nbsp;<input type="submit" value="Αναζητήστε!">&nbsp;&nbsp;&nbsp;&nbsp;</p></span>
</form>
<div class="main">
<!-- btn Class changes width and max-width proportionally to categories' length -->
<table style="position:absolute;" border="0">

<?php
// get all categories - access DB to get all
$res=mysqli_query($con, "select distinct category from pages where category in (select distinct category from events where date > ".time().") order by category");
while($tmp = mysqli_fetch_array($res, MYSQLI_BOTH))
	echo '<tr>
    <th scope="row"><a href="'.'home.php?cat='.htmlspecialchars($tmp['category']).'" class="btn">'.$tmp['category'].'</a></th>
	<td><a  type="application/rss+xml" href="rss/'.preg_replace("([^\w\d\-_~.])", '', $tmp['category']).'.xml'.'"><img src="feed-icon.gif"></a></td>
  	</tr>';
?>

</table>
<form action="">
<div style="color:#FFE0A3; font-size:17px; margin-left:350">
<span>Από: </span><input type="text" class='datepicker' id="from" name="from">
<span> Έως: </span><input type="text" class='datepicker' id="to" name="to">
<input type="submit" value="Ψάξε">
</div>
</form>


<div id="map-container">	<div id="map-canvas">  </div></div>

<table class="table" style="border-spacing:0 25px">
    <tbody>
    <br>
<?php
//var_dump($search_events);
if(count($search_events)>0) {
	for($i=0; $i<count($search_events); $i++)
echo '
    	<tr>
        	<td style="border-style:solid; border-width:thin;">
    			<table id="event">
                	<tr>
                    	<td>
                        	<table class="subtable">
                            	<tr>
                                	<td style="" id="photo"><a class="highslide" onClick="return hs.expand(this)" href="'. $search_events[$i]['cover_url'] . '"><img  width="177" height="66"  src="'. $search_events[$i]['cover_url'] . '"/></a>
                                    </td>
                               		<td>
                                    	<table class="subtable" style="border-collapse: collapse;">
                                        	<tr style="border-bottom-style:solid;border-bottom-width:thin;border-bottom-color:#999;">
                                            	<td>
                                                	<table class="subtable">
                                                    	<tr>
                                                        	<td class="event_title">'. $search_events[$i]['name'] . '</td>
                                                            <td class="date">'. date("d-m-Y    H:i", $search_events[$i]['date']) . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr style="border-bottom-style:solid; border-bottom-width:thin; border-bottom-color:#999;">
                                            	<td class="default">'. $search_events[$i]['owner_name'] . '</td>
                                            </tr>
                                            <tr>
                                            	<td class="default">'. $search_events[$i]['category'] . '</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                <tr>
                	<td id="des_cell" onClick="'."javascript:moveToLocation(this.childNodes[2].value, this.childNodes[3].value);".'" style="position:relative; border-top-style:solid; border-top-width:thin; padding-top:10px;"><div tabindex="0" class="description" id="des">'. $search_events[$i]['description'] . '</div><img height="14px" width="14px" src="x.png" class="collapse"><input type="hidden" id="lat" value="'.$search_events[$i]['loc_x'].'"><input type="hidden" id="lon" value="'.$search_events[$i]['loc_y'].'">
</td>
                </tr>
            </table>
        </td>
    </tr>
';
}
else {
	echo "<tr>
<td align=\"center\">
<span>Δε βρέθηκαν αποτελέσματα για τα κριτήρια αναζήτησης!</span>
</td>
</tr>
";}
?>

    <!-- end of event1 //-->
</tbody>
<tfoot style="border:none">
<tr style="border:none">
<td style="border:none">
<?php
$next=$prev='?';
if($_GET['page']==0) {
	foreach($_GET as $key=>$value) {
		if($key=='page') {
			$next.=$key.'='.strval(intval($value)+1) . '&';
		}
		else {
			$next.=$key.'='.$value . '&';
			$prev.=$key.'='.$value . '&';
		}
	}
}
else if($_GET['page']>0 && !$last_page) {
	foreach($_GET as $key=>$value) {
		if($key=='page') {
			$next.=$key.'='.strval(intval($value)+1) . '&';
			$prev.=$key.'='.strval(intval($value)-1) . '&';
		}
		else {
			$next.=$key.'='.$value . '&';
			$prev.=$key.'='.$value . '&';
		}
	}
}
else if($last_page) {
	foreach($_GET as $key=>$value) {
		if($key=='page') {
			$prev.=$key.'='.strval(intval($value)-1) . '&';
		}
		else {
			$next.=$key.'='.$value . '&';
			$prev.=$key.'='.$value . '&';
		}
	}
}
else {
//ERROR
}
$next=substr($next, 0, -1);
$prev=substr($prev, 0, -1);

?>
<div align="center"><a href=<?php echo '"'.$prev.'"'; ?>><img width="75px" height="75px" id="prev" src="prev.png"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href=<?php echo '"'.$next.'"'; ?>><img width="75px" height="75px" id="next" src="next.png"></a></div>
</td>
</tr>
</tfoot>
</table>

<?php
if($_GET['page']==0) {
	echo
'<script type="text/javascript">
var prev = document.getElementById("prev");
prev.setAttribute("onClick", "return false");
prev.setAttribute("href", "");
prev.style.pointerEvents="none";
prev.style.cursor="default";
prev.style.opacity="0.4";
</script>';
}
if($last_page) {
	echo
'<script type="text/javascript">
var next = document.getElementById("next");
next.setAttribute("onClick", "return false");
next.setAttribute("href", "");
next.style.pointerEvents="none";
next.style.cursor="default";
next.style.opacity="0.4";
</script>';
}
 ?>
</div>
</body>
</html>