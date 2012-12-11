/*******************************************************

	Prodhe's AJAX object handling

*******************************************************/

function PajaxObj(eID,method,url,async,sendstring) {

	this.obj = new Object();

	this.eID = eID;
	this.method = method;
	this.url = url;
	this.howtosync = async;
	this.sendstring = sendstring;

	this.response = "";
	
	this.create();
	this.handle();
	this.send();
}

// create the XMLHttp object
PajaxObj.prototype.create = function() {
	// browser compatibility
	var xhr=null;
	if (window.XMLHttpRequest)
		xhr = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
	else
		xhr = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
		
	this.obj = xhr;
};

// what do to with the request once it's been returned
PajaxObj.prototype.handle = function() {
	var o = this.obj;
	var e = this.eID;
	o.onreadystatechange=function() {
		if (o.readyState==4 && (o.status==200 || o.status==0)) {
			// send it off to popup function
			// alert(o.responseText);
			popup(e,o.responseText,true);
			AJAX_parseresult(o.responseText);
		}
	}
};

// open and send
PajaxObj.prototype.send = function() {
	this.obj.open(this.method,this.url,this.howtosync);
	if (this.method == "POST") {
		this.obj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	}
	this.obj.send(this.sendstring);
};




/****************************

	Fancy looking popup

*****************************/

// box popup for information (errors and success)
function popup(eID, str, show) {
	var e = document.getElementById(eID);
	// var textNode = document.createTextNode(str);
	if (show == true) {
		// set content of given element
		// e.appendChild(textNode);
		e.innerHTML = str;
		
		// show popup and activate fade in
		e.style.display = "block";
		fade(eID,0,100,0);
		
		// timeout for fade
		// setTimeout("popup('"+eID+"','',false)", 4000);
	}
	else {
		e.style.display = "none";
	}
}

// fade an element in or out
function fade(eID,startOpacity,stopOpacity,duration) {
	var eS = document.getElementById(eID).style;
    var timer = 0;

	// fade in
    if (startOpacity < stopOpacity) {
		for (var i=startOpacity; i<=stopOpacity; i++) {
			setTimeout("setOpacity('"+eID+"',"+i+")", timer * Math.round(duration/100));
			timer++;
		} return;
    }
	// fade out
	for (var i=startOpacity; i>=stopOpacity; i--) {
		setTimeout("setOpacity('"+eID+"',"+i+")", timer * Math.round(duration/100));
		timer++;
	}
}
function setOpacity(eID,level) {
	var eS = document.getElementById(eID).style;
	eS.opacity = level/100;			// css3
	eS.MozOpacity = level/100;		// old firefox
	eS.KhtmlOpacity = level/100;	// old safari
	eS.filter = "alpha(opacity="+level+");";	// stupid IE
}