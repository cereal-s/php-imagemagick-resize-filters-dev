<?php

/**
 * Display the image at large size
 */

require './support/helpers.php';
require './support/config.php';
require './support/templates.php';

$paths   	= require './support/paths.php';
$page_title = $config['filter_name'] . ' @ ' . $config['large_width'] . 'px';
$fname 		= 'filter_'.$config['filter_name'];

list($source, $thumb_path, $large_path, $meta_path, $mpc_path) = paths($paths);

$image_file = get_file_by_filter($config['filter_name'], $large_path, $config['image']);

if(file_exists($image_file))
{
	list($w, $h) = getimagesize($image_file);

	# meta
	$meta_file_path = get_file_by_filter($config['filter_name'], $meta_path, $config['meta_file']);
	$metaf = file_exists($meta_file_path) ? explode(',', file_get_contents($meta_file_path)) : FALSE;

	if($metaf === FALSE)
	{
		$metaf[] = (int)$config['quality'];
		$metaf[] = (float)$config['blur'];
	}

	$content = sprintf($large_template, $fname, $image_file, format_bytes(filesize($image_file)), $w, $h, $metaf[0], $metaf[1]);
}
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $page_title; ?></title>
	<style type="text/css">
		@import './styles.css';
	</style>
</head>
<body>

	<div class="wrap">
		<h1><?php echo $page_title; ?></h1>
		<?php echo $content; ?>
	</div>

</body>
</html>