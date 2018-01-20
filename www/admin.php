<HTML>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<body>
<style type="text/css">
body {
	padding:20px;
}
#page {
	display:block;
	width:800px;
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
	opacity: 0;
}
</style>
<?php
require_once('connect.php');
// if _POST['hash'] == db hash
$pw = mysqli_fetch_array(mysqli_query($con, "select hash from password"), MYSQL_BOTH);
if ((isset($_POST['hash']))?($_POST['hash'] == $pw['hash']):false) {
	echo "<script type=\"text/javascript\" src=\"javascript/ajax.js\"></script>\n";
	echo '<div style="position:relative; display: inline-block;"><table border=1 cellspacing=0>
	<thead>
    	<th><strong><a href="javascript:if(confirm(\'Delete ALL owners?\'))delOwners(-1, getHash());">Delete ALL</a></strong></th>
		<th><strong>ID</strong></th>
		<th><strong>Name</strong></th>
		<th><strong>Category</strong></th>
    </thead>
    <tbody id="owners">';
	$query = mysqli_query($con, "select * from pages");
	while($owner = mysqli_fetch_array($query, MYSQL_BOTH))
	echo "\n".
    	 "<tr>
        	<td><a href=\""."javascript:delOwners('".$owner['page_id']."', getHash());"."\">Delete</a></td>
        	<td>".$owner['page_id']."</td>
            <td>".$owner['name']."</td>
            <td>".$owner['category']."</td>
    	</tr>";
	echo 
	"</tbody>
	</table>";
	echo "<br><br><span>Προσθήκη νέας σελίδας</span><br>";
	echo "<form action=\"javascript:getOwners(_('insertedname').value, getHash());\"><input type=text name=insertedname id=\"insertedname\">&nbsp<input type='submit' value='Add' name='insert'>
<img src=\"processing.gif\" width=16 height=16 id=\"loading\" style=\"display:none;\"></img></form>\n".
"<input type='button' style=\"position:absolute; right:0;\" id='update' value=\"Update events' database\" onClick=\"javascript:update(getHash());\">\n</div><br><br><br><br><br>";
?>
<div id="page">
<p id="title" style="text-align:center;font-family:'Arial Black', Gadget, sans-serif"></p>
<img id="image" src="">
<p id="location" style="font:'Trebuchet MS', Arial, Helvetica, sans-serif"></p>
<br>
<span>Add events of this page to database&#63;</span>
<input type="hidden" id="page_name" value="">
<input type="button" onClick="javascript:addOwners(_('page_name').value, getHash());" value="Yes!">&nbsp;&nbsp;
<input type="button" onClick='javascript:document.getElementById("page").style.opacity = 0;setTimeout(function(){document.getElementById("page").style.display = "none";}, 1000);' value="No!">
</div>

<?php
	echo "<br><br><br>Επιστροφή στην <a href='home.php'>Αρχική Σελίδα</a>";
		
	$_POST['pw_hash']='test';
	echo "<input type=\"hidden\" id=\"pw_hash\" value=\"".$_POST['pw_hash']."\">";
}
else {
echo 
'<script type="text/javascript">
alert("Only administrators allowed!");
window.location.assign("home.php");
</script>';
}
?>
</body>
</HTML>