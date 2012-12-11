<?php


/*****************
	FUNCTIONS
******************/

// log output
function say($text, $state=9) {
	switch($state) {
		//error
		case 2:
			printf("[E] %s\n", $text);
			exit();
			break;
		// warning
		case 1:
			printf("[!] %s\n", $text);
			break;
		// info with no header or line break
		case 0:
			printf("%s", $text);
			break;
		// info
		default:
			printf("[ ] %s\n", $text);
	}
}

// create a HTML skeleton file
function createHTML() {
	$str = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<title>%_HEADER_%</title>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" href="./psig.css" type="text/css" media="screen" title="PSIG-CSS" charset="utf-8" />
		<script type="text/javascript" src="./psig.js"></script>
		<script type="text/javascript">
		<!--
			var images = new Array(%_JAVASCRIPT_%);
		//-->
		</script>
	</head>
	<body onload="activate(images)">
		<div id="world">
			<h1>%_HEADER_%</h1>
			<p id="desc">
				%_DESCRIPTION_%
			</p>
			<div id="bigwrap">
				<div id="big">
					%_BIG_%
				</div>
			</div>
			<div id="thumbs">
%_THUMBS_%
			</div>
		</div>
		<div id="credits"><p>PSIG: Static Image Gallery<br />by Prodhe 2012</p></div>
	</body>
</html>
HTML;

	return $str;
}

// save string to file
function saveToFile($filename, $str) {
	if (is_writable(".")) {
		if ($fh = fopen($filename, "w")) {
			fwrite($fh, $str);
			fclose($fh);
			return true;
		} else return false;
	} else return false;
}

// get pictures from folder
function getpics($folder) {
	$pics = array();
	if ($dh = opendir($folder)) {
		while (($file = readdir($dh)) !== false) {
			$ext = explode(".", $file);
			if (!empty($ext[count($ext)-1])) {
				$ext = strtolower($ext[count($ext)-1]);
				if ($ext == "jpg" || $ext == "jpeg")
					$pics[] = $file;
			}
		}
		closedir($dh);
	}
	return $pics;
}

// return a magic string aimed for AJAX parsing and async creation of thumbs
function magic_string_to_ajax($f, $tf) {
	$picsstr = "";
	$pics = getpics($f);
	foreach ($pics as $pic) {
		$picsstr .= $pic . "|";
	}
	$str = "###THUMBS###".$f."/###".$tf."###".$picsstr;
	return $str;
}



/*******************
	SCRIPT ENTER
********************/


// check POST
if (!isset($_POST['psigKey'])) {
	exit("Can only be used within PSIG!");
}

// check specific POST data
if (!isset($_POST['title']) || !isset($_POST['folder']) || !isset($_POST['outputFolder']))
	say("Wrong POST data. Try again.", 2);
if (empty($_POST['title']) || empty($_POST['folder']) || empty($_POST['outputFolder']))
	say("Neither TITLE, GALLERY FOLDER or OUTPUT FOLDER can be empty.",2);

// set basic variables
$cfg['max_execution_time']              = 300;
$cfg['memory_limit']                    = "100M";
$cfg['header']                          = trim($_POST['title']);
$cfg['desc']                            = str_replace("\n", "<br />", trim($_POST['desc']));
$cfg['thumbSize']                       = 80;
$cfg['overwriteExistingOutputFolder']   = (isset($_POST['overwriteOutput']) && $_POST['overwriteOutput']=="true") ? true : false;

// set gallery folder
$cfg['galleryFolder']       = trim($_POST['folder']);

// set output folders (the output folder is created based on the title/header)
$cfg['outputFolder'] = trim($_POST['outputFolder']);
$cfg['outputFolder'] .= "/"; // add a trailing slash
$valid_chars = "abcdefghijklmnopqrstuvwxyz0123456789";
foreach(str_split(strtolower($cfg['header'])) as $char) {
	if (strpos($valid_chars,$char) !== false)
		$cfg['outputFolder'] .= $char;
}
$cfg['thumbsFolder']        = $cfg['outputFolder']."/photos/";


// if magic string to ajax is requested - return and exit
if (isset($_POST['ajax'])) {
	exit(magic_string_to_ajax($cfg['galleryFolder'], $cfg['thumbsFolder']));
}




/***************
	GENERATOR
****************/


say("The generator has started!\n");


/*
SET AND CHECK DEFAULT VALUES AND FOLDERS
*/

// high-def pictures will need much memory and probably long execution time
if ($a = ini_set("memory_limit", $cfg['memory_limit']))
	say("Setting PHP memory limit to ".$cfg['memory_limit'].".");
else
	say("Could not change PHP memory limit (current value: ".$a.").",1);
if ($a = ini_set("max_execution_time", $cfg['max_execution_time']))
	say("Setting script max execution time to ".$cfg['max_execution_time']." seconds.");
else
	say("Could not change script max execution time (current value: ".$a.").",1);

say("\n",0);



/*
	CHECK THE GALLERY FOLDER AND GET THE PHOTOS
*/

// check gallery folder
if (!is_dir($cfg['galleryFolder']))
	say("The gallery folder does not exist!", 2);
else if (is_dir($cfg['galleryFolder']) && is_readable($cfg['galleryFolder']))
	say("Gallery folder exists and is readable.");

// loop and search for valid pics
say("Starting to loop folder for valid images...");
$pics = getpics($cfg['galleryFolder']);
if (count($pics) > 0)
	say(count($pics)." images found.");
else
	say("0 images found. Aborting...", 2);

say("\n",0);



/*
	PREPARE THE OUTPUT FOLDER AND YELL FOR THUMB CREATION IF NEEDED
*/

// check output folder
if (is_dir($cfg['outputFolder'])) {
	say("Output folder already exists: ".$cfg['outputFolder'], 1);
	if ($cfg['overwriteExistingOutputFolder'])
		say("Will overwrite existing files...", 1);
	else
		say("Will not overwrite!", 2);
}
else {
	if (mkdir($cfg['outputFolder'])) {
		say("Output folder created: ".$cfg['outputFolder']."");
	}
	else
		say("Failed to create output folder. Check permissions!", 2);
}

// check for thumbnails, otherwise yell for creation which will be handed separately via AJAX and gen_thumb.php
if (!is_dir($cfg['thumbsFolder'])) {
	mkdir($cfg['thumbsFolder']);
}


/*
	SETUP THE HTML FILE
*/

// create HTML skeleton
$html = createHTML();
say("Created HTML document in memory.");

// personalize HTML with title
$html = str_replace("%_HEADER_%", $cfg['header'], $html);
say("Added title to document.");
$html = str_replace("%_DESCRIPTION_%", $cfg['desc'], $html);
say("Added description to document.");

// create html for thumbnails
say("Adding images...");
$html_thumbs = "";
$html_preloadJS = "";
$count = 0;
foreach ($pics as $img) {
	// first picture
	if ($count == 0) {
		$html = str_replace("%_BIG_%", '<img src="./loader.gif" id="bigpic" />', $html);
		// $html = str_replace("%_ONLOAD_%", "./photos/show_".$img, $html);
	}
	
	// preload data
	// $str = "\t\t\tsetTimeout(\"load('IMG_".$count."', './photos/tn_".$img."')\", 10);\n";
	// $str = "\t\t\t\tload('IMG_".$count."', './photos/tn_".$img."');\n";
	$str = "".'"'.$img.'",'."";
	$html_preloadJS .= sprintf("%s", $str);

	// insert a thumbnail img and link
	// $html_thumbs .= sprintf("\t\t\t\t<img id=\"IMG_%d\" title=\"%s\" src=\"%s\" width=\"%d\" height=\"%d\" onclick=\"load('bigpic','%s');\" />\n", $count, $img, "./loader2.gif", $cfg['thumbSize'], $cfg['thumbSize'], "./photos/show_".$img);
	$html_thumbs .= sprintf("\t\t\t\t<div class=\"thumb\"><a href=\"javascript:load('bigpic','%s');\"><img id=\"IMG_%d\" alt=\"%s\" src=\"%s\" /></a></div>\n", "./photos/show_".$img, $count, $img, "./loader2.gif");
	
	// count
	$count++;
}
$html_preloadJS = substr($html_preloadJS, 0, -1);
$html = str_replace("%_JAVASCRIPT_%", $html_preloadJS, $html);
$html = str_replace("%_THUMBS_%", $html_thumbs, $html);
say($count." images added with link and thumbnail.");

say("\n",0);


/*
	WRITE TO DISK
*/

// write HTML to file
if (saveToFile($cfg['outputFolder']."/index.html", $html))
	say("Written HTML file to disc.");
else
	say("Could not create HTML file in output folder. Check permissions!", 2);

// copy javascript, stylesheet and system images
if (copy("./psig.js",$cfg['outputFolder']."/psig.js"))
	say("Copied JavaScript file to output folder.");
else
	say("Could not copy JavaScript file (./sys/psig.js) to output folder. You must do this yourself!", 1);
if (copy("./psig.css",$cfg['outputFolder']."/psig.css"))
	say("Copied stylesheet to output folder.");
else
	say("Could not copy ./sys/psig.css to output folder. You must do this yourself!", 1);
if (copy("./imgs/loader.gif",$cfg['outputFolder']."/loader.gif") && copy("./imgs/loader2.gif",$cfg['outputFolder']."/loader2.gif") && copy("./imgs/missing.png",$cfg['outputFolder']."/missing.png") && copy("./imgs/missing2.png",$cfg['outputFolder']."/missing2.png"))
	say("Copied system images to output folder.");
else
	say("Could not copy system images to output folder. You must do this yourself!", 1);

say("\n",0);
say("Let the thumbnail creation finish and then it's done.\n\n",0);
say("\t".$cfg['outputFolder']."\n\n",0);
say("Enjoy! ;-)\n",0);


?>