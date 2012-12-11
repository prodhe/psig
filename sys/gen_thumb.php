<?php

/****************
	FUNCTIONS
*****************/


// loop through folder and create thumbnails (one small, and one big)
function createthumbnail($srcFile, $folder, $thumbsFolder, $thumbSize) {

	// global $cfg;
	// $folder			= $cfg['galleryFolder'];
	// $thumbsFolder	= $cfg['thumbsFolder'];
	// $thumbSize		= $cfg['thumbSize'];
	
	// if thumbnail already exists, skip
	if (file_exists($thumbsFolder."tn_".$srcFile) && file_exists($thumbsFolder."show_".$srcFile) || !is_readable($folder.$srcFile) || !is_writable($thumbsFolder))
		return false;

	// read source file and size
	$src_img  = imagecreatefromjpeg($folder . $srcFile);
	$src_width = imageSX($src_img);
	$src_height = imageSY($src_img);
	
	// make a centered square small thumbnail
	$dst_img  = imagecreatetruecolor($thumbSize, $thumbSize);
	
	// check width and height and offset the cutout if needed
	$offset_x = 0;
	$offset_y = 0;
	if ($src_width > $src_height) {
		$offset_x = ($src_width - $src_height)/2; // offset with half the difference (hence, centered)
		$width = $height = $src_height;
	}
	else if ($src_width < $src_height) {
		$offset_y = ($src_height - $src_width)/2; // same here: half the difference
		$height = $width = $src_width;
	}
	
	// create the actual image and then clean up
	imagecopyresampled($dst_img, $src_img, 0, 0, $offset_x, $offset_y, $thumbSize, $thumbSize, $width, $height);
	imagejpeg($dst_img, $thumbsFolder."tn_".$srcFile);
	imagedestroy($dst_img);
	
	// make a big "thumbnail", in case we have a picture bigger than 640 in any direction
	if ($src_width > 640 || $src_height > 640) {
		// width bigger than height
		if ($src_width > $src_height) {
			$big_width = 640;
			$big_height = $src_height * (640 / $src_width);
		} else {
			$big_width = $src_width * (640 / $src_height);
			$big_height = 640;
		}
	}
	else {
		$big_width = $src_width;
		$big_height = $src_height;
	}
	$dst_img = imagecreatetruecolor($big_width, $big_height);
	imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $big_width, $big_height, $src_width, $src_height);
	imagejpeg($dst_img, $thumbsFolder."show_".$srcFile);
	imagedestroy($dst_img);
	
	// clean up
	imagedestroy($src_img);
	
	return true;
	
}


/********************
	SCRIPT ENTER
*********************/


// check POST
if (!isset($_POST['psigKey'])) {
	exit("Can only be used within PSIG!");
}

// check specific POST data
if (!isset($_POST['folder']) || !isset($_POST['thumbsFolder']) || !isset($_POST['srcFile']) || !isset($_POST['thumbSize']))
	exit("Wrong POST data. Try again.");
if (empty($_POST['folder']) || empty($_POST['thumbsFolder']) || empty($_POST['srcFile']) || empty($_POST['thumbSize']))
	exit("At least one of the required POST values were empty.");

// set variables
$folder			= trim($_POST['folder']);
$thumbsFolder	= trim($_POST['thumbsFolder']);
$srcFile		= trim($_POST['srcFile']);
$thumbSize		= trim($_POST['thumbSize']);

ini_set("memory_limit", "100M") or null;
ini_set("max_execution_time", "300") or null;

// create thumbnails
if (createthumbnail($srcFile,$folder,$thumbsFolder,$thumbSize))
	echo ("Creating thumbnail for ".$srcFile."...");
else
	echo ("Thumbnail for ".$srcFile." already exists...");

?>