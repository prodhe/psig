/********************************************************

	AJAX call functions

********************************************************/

// PSIG install
function AJAX_install() {
	// get form fields
	var title = document.getElementById("title").value;
	var desc = document.getElementById("desc").value;
	var folder = document.getElementById("folder").value;
	var outputFolder = document.getElementById("outputFolder").value;
	var overwriteOutput = document.getElementById("overwriteOutput").checked;

	document.getElementById("div_installation_log").style.display = "block";

	if (title == "" || folder == "" || outputFolder == "")
		popup("installation_log","No fields are allowed to be empty!",true);
	else {
		new PajaxObj("installation_log","POST","./sys/generate.php",false,"psigKey=20120116&title="+title+"&desc="+desc+"&folder="+folder+"&outputFolder="+outputFolder+"&overwriteOutput="+overwriteOutput+"");
		new PajaxObj("thumbnail_log","POST","./sys/generate.php",false,"ajax=1&psigKey=20120116&title="+title+"&folder="+folder+"&outputFolder="+outputFolder+"&overwriteOutput="+overwriteOutput+"");
	}
	
}

// check for magic string result from generate.php !
function AJAX_parseresult(s) {
	var explode = s.split("###");
	if (explode[1] == "THUMBS") {
		var folder = explode[2];
		var thumbsFolder = explode[3];
		var pics = explode[4].split("|");
		var i=0;
		popup("thumbnail_log","",true);
		while (pics[i] != "") {
			new PajaxObj("thumbnail_log", "POST", "./sys/gen_thumb.php", true, "psigKey=20120116&folder="+folder+"&thumbsFolder="+thumbsFolder+"&thumbSize=80&srcFile="+pics[i]);
			i++;
		}
	}
}