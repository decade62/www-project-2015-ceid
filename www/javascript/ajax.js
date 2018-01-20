var del_id; // client side variable used to store the ID of page to be deleted
function getXMLHTTPRequest() {
	var req = false;
	try {
		// for firefox
		req = new XMLHttpRequest();
	} catch (err) {
		try {
			// for some IE versions
			req = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (err) {
			try {
				// for some older IE versions
				req = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (err) {
				req=false;
			}
		}
	}
	return req;
}

function getHash() {
	return document.getElementById('pw_hash').value;
}

function _(el) {
	return document.getElementById(el);
	}

function update(hash) {
	var updPage = 'update.php';
	//updRand   = parseInt(Math.random()*999999999999999);
	var updURL  = updPage;// + "?hash=" + hash + "&rand=" + updRand;
	//alert(updURL);
	_('loading').style.display='';
	updReq.open("POST", updURL, true);
	updReq.onreadystatechange = updHTTPResponse;
	updReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	updReq.send("hash=" + hash);
}

function delOwners(id, hash) {
	var delPage = 'delOwner.php';
	//delRand   = parseInt(Math.random()*999999999999999);
	var delURL  = delPage; //+ "?id=" + id + "&hash=" + hash + "&rand=" + delRand;
	del_id=id;
	//alert(delURL);
	_('loading').style.display='';
	delReq.open("POST", delURL, true);
	delReq.onreadystatechange = theHTTPResponse;
	delReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	delReq.send("id=" + id + "&hash=" + hash);
}

function getOwners(name, hash) {
	_("page").style.display="block";
	_('page_name').value=name;
	var getPage = 'getOwner.php';
	//getRand   = parseInt(Math.random()*999999999999999);
	var getURL  = getPage;// + "?name=" + name + "&hash=" + hash + "&rand=" + getRand;
	//alert(getURL);
	_('loading').style.display='';
	getReq.open("POST", getURL, true);
	getReq.onreadystatechange = getHTTPResponse;
	getReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	getReq.send("name=" + name + "&hash=" + hash);
}

function addOwners(name, hash) {
	document.getElementById("page").style.opacity = 0;
	setTimeout(function(){document.getElementById("page").style.display = "none";}, 1000);
	var addPage = 'addOwner.php';
	//addRand   = parseInt(Math.random()*999999999999999);
	var addURL  = addPage;// + "?name=" + name + "&hash=" + hash + "&rand=" + addRand;
	//alert(addURL);
	_('loading').style.display='';
	addReq.open("POST", addURL, true);
	addReq.onreadystatechange = addHTTPResponse;
	addReq.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	addReq.send("name=" + name + "&hash=" + hash);
}

function updHTTPResponse() {
	if(updReq.readyState == 4) {
		if(updReq.status == 200) {
			var response = updReq.responseXML.getElementsByTagName("error")[0];
			if(typeof response == "object") alert(response.childNodes[0].nodeValue);
			else {
				response = updReq.responseXML.getElementsByTagName("success")[0];
				if(typeof response == "object") {
					alert(updReq.responseXML.getElementsByTagName("success")[0].childNodes[0].nodeValue);
					}
			}
		_('loading').style.display='none';
		}
	}
}

function getHTTPResponse() {
	if(getReq.readyState == 4) {
		if(getReq.status == 200) {
			var response = getReq.responseXML.getElementsByTagName("error")[0];
			if(typeof response == "object") alert(response.childNodes[0].nodeValue);
			else {
				response = getReq.responseXML.getElementsByTagName("newpage")[0];
				if(typeof response == "object") {
					var name = getReq.responseXML.getElementsByTagName("name")[0].childNodes[0].nodeValue;
					var photo = getReq.responseXML.getElementsByTagName("photo")[0].childNodes[0].nodeValue;
					var location = "Διεύθυνση: " + getReq.responseXML.getElementsByTagName("location")[0].childNodes[0].nodeValue;
					_('title').innerHTML = name;
					_('image').setAttribute("src", photo);
					_('location').innerHTML=location;
					_('image').onload = document.getElementById("page").style.opacity = 1;
					}
			}
		_('loading').style.display='none';
		// clear text box
		_("insertedname").value="";
		}
	}
}

function addHTTPResponse() {
	if(addReq.readyState == 4) {
		if(addReq.status == 200) {
			var response = addReq.responseXML.getElementsByTagName("error")[0];
			if(typeof response == "object") alert(response.childNodes[0].nodeValue);
			else {
				response = addReq.responseXML.getElementsByTagName("newpage")[0];
				if(typeof response == "object") {
					var id = addReq.responseXML.getElementsByTagName("page_id")[0].childNodes[0].nodeValue;
					var name = addReq.responseXML.getElementsByTagName("name")[0].childNodes[0].nodeValue;
					var category = addReq.responseXML.getElementsByTagName("category")[0].childNodes[0].nodeValue;
					var pages_table = _("owners");
					var new_row = document.createElement("tr");
					var td_del  = document.createElement("td");
					var a_del   = document.createElement("a");
					var td_id   = document.createElement("td");
					var td_name = document.createElement("td");
					var td_cat  = document.createElement("td");
					a_del.setAttribute("href", "javascript:delOwners('"+ id + "', '"+ getHash() +"');");
					a_del.innerHTML="Delete";
					td_del.appendChild(a_del);
					td_id.innerHTML=id;
					td_name.innerHTML=name;
					td_cat.innerHTML=category;
					new_row.appendChild(td_del);
					new_row.appendChild(td_id);
					new_row.appendChild(td_name);
					new_row.appendChild(td_cat);
					// new row generated
					pages_table.appendChild(new_row);
					}
			}
		_('loading').style.display='none';
		// clear text box
		_("insertedname").value="";
		}
	}
}


function theHTTPResponse() {
	if(delReq.readyState == 4) {
		//alert("4");
		if(delReq.status == 200) {
			//alert("200");
			var response = delReq.responseXML.getElementsByTagName("response")[0];
			//alert(response.childNodes[0].nodeValue);
			// delete designated row
			var pages_table = _("owners");
			var rows = pages_table.getElementsByTagName("tr");
			var i=0;
			if(del_id==-1) {
				for(i=rows.length-1; i>=0; i--) {
					//alert(rows[i].innerHTML);
					pages_table.removeChild(rows[i]);
				}
			}
			else {
				for(; i<rows.length; i++) {
					//alert(rows[i].getElementsByTagName("td")[1].innerHTML);
					if(rows[i].getElementsByTagName("td")[1].innerHTML == del_id) pages_table.removeChild(rows[i]);
				}
			}
			_('loading').style.display='none';
		}
	}
}

var delReq = getXMLHTTPRequest();
var addReq = getXMLHTTPRequest();
var getReq = getXMLHTTPRequest();
var updReq = getXMLHTTPRequest();
