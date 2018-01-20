    // override Highslide settings here
    // instead of editing the highslide.js file
    hs.graphicsDir = 'highslide/graphics/';
	hs.showCredits = false;
//////////////////////////////////////////////////////////////////
var loc_x, loc_y ;
loc_x=loc_y=0.0;
var map;
function initialize() {
  map = new google.maps.Map(document.getElementById('map-canvas'), {
    zoom: 16,
    center: {lat: loc_x, lng: loc_y}
  });
}
function moveToLocation(lat, lng){
	var myLatlng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
    var center = myLatlng;
    // using global variable:
    map.panTo(center);
	if(parseFloat(lat)!=0 && parseFloat(lng)!=0)
	var marker = new google.maps.Marker({
      position: myLatlng,
      map: map
  });

}

google.maps.event.addDomListener(window, 'load', initialize);
//google.maps.event.addDomListener(getElementById('des_cell'), 'click', initialize);

///////////////////////////////////////////////////////////////////



function hide() {
if(document.getElementById('spoiler').style.display=='none') {
	document.getElementById('spoiler').style.display='';
	document.getElementById('hash').focus();
	}
else {
	document.getElementById('spoiler').style.display='none';
	}
}