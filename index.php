<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	
<html>
	<head>
		<title>PSIG: Static Image Gallery</title>
		<link rel="stylesheet" href="./sys/default.css" type="text/css" media="screen" title="default css" charset="utf-8" />
		<script type="text/javascript" charset="utf-8" src="./sys/pajax.js"></script>
		<script type="text/javascript" charset="utf-8" src="./sys/call.js"></script>
	</head>
	<body>
		<div id="world">
		
			<h1>PSIG: Static Image Gallery</h1>
			
			<p>
				Welcome! This web application will create a stylish showcase for all the photos in a folder of your choice. Select a title for your gallery,
				type in the path to a folder containing one or more JPEG photos and choose where to save the created showcase.
			</p>
			
			<p>
				Once the gallery is created, you don't need a web server supporting PHP to show the photographs. You can upload the final showcase to any server
				and it will work as intended, as long as the visitor's web browser supports JavaScript.
			</p>
	
			<fieldset>
				<legend>Configuration</legend>
				<form action="./sys/generate.php" method="post" accept-charset="utf-8">
					<input type="hidden" name="psigKey" value="20120116" id="psigKey" />
					<dl>
						<dt><label for="title">Title</label></dt>
						<dd>
							<input type="text" name="title" value="" id="title" size="50" /><br />
							<span class="explain"><emph>The title of your gallery.</emph></span>
						</dd>
						<dt><label for="desc">Description</label></dt>
						<dd>
							<textarea id="desc" name="desc" cols="54" rows="7"></textarea><br />
							<span class="explain"><emph>A short description and/or presentation of your showcase.</emph></span>
						</dd>
						<dt><label for="folder">Gallery folder</label></dt>
						<dd>
							<input type="text" name="folder" value="<?=dirname(__FILE__)?>" id="folder" size="50" /><br />
							<span class="explain"><emph>Path to the folder containing your photographs.</emph></span>
						</dd>
						<dt><label for="outputFolder">Output folder</label></dt>
						<dd>
							<input type="text" name="outputFolder" value="<?=dirname(__FILE__)."/output"?>" id="outputFolder" size="50" />
							<input type="checkbox" name="overwriteOutput" value="" id="overwriteOutput" /> <span class="explain">Overwrite existing</span><br />
							<span class="explain"><emph>Your gallery will be created as a subfolder to this one, with a name based on your title.</emph></span>
						</dd>
					</dl>
					<p><input type="button" value="Create gallery &rarr;" onclick="AJAX_install();"></p>
				</form>
			</fieldset>
			
			<div id="div_installation_log">
				<fieldset>
					<legend>Thumbnail creation</legend>
					<p id="thumbnail_log"></p>
				</fieldset>
				<fieldset>
					<legend>Static files installation</legend>
					<p><pre id="installation_log"></pre></p>
				</fieldset>
			</div>
			
			<p id="footer">
				Created by Petter Rodhelind
			</p>
			
		</div>
		
		<p>&nbsp;</p>
			
	</body>
</html>
