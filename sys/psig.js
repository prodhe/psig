// preload an image and call show() if loaded, and notFound() on error
function preloadImage(eID, src) {
	var pic = new Image();
	
	pic.onerror = function() {
		notFound(eID);
	}
	
	pic.onload = function() {
		show(eID, src);
		pic.onload = function(){};
	}
	
	pic.src = src;
}

// what to do with an image once it's loaded
function show(eID, src) {
	document.getElementById(eID).src = src;
	if (eID == "bigpic")
		document.getElementById(eID).style.borderColor = "#eee";
	else
		document.getElementById(eID).style.borderWidth = "2px";
}

// oh noes!
function notFound(eID) {
	document.getElementById(eID).src = (eID == "bigpic") ? "./missing.png" : "./missing2.png";
}

// main function to load an image (this one will call the above)
function load(eID, src) {
	if (eID == "bigpic") {
		document.getElementById(eID).style.borderColor = "#222";
		document.getElementById(eID).style.display = "none";
		document.getElementById(eID).src = "./loader.gif";
		document.getElementById(eID).style.display = "inline";
	}

	preloadImage(eID, src);
	// setTimeout("preloadImage('"+eID+"', '"+src+"')", 1000);
}

// activate loading of thumbnails and first big one (this one is called from index.html: <body> onload)
function activate(imgs) {
	load('bigpic', './photos/show_'+imgs[0]);
	for (i=0; i < imgs.length; i++) {
		load("IMG_"+i, "./photos/tn_"+imgs[i]);
	}
}