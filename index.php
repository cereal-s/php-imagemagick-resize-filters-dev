<?php

/**
 * Upload and generate images for each algorithm
 */

require './support/helpers.php';
require './support/config.php';
require './support/templates.php';

$filters = require './support/filters.php';
$mimes   = require './support/mimes.php';
$paths   = require './support/paths.php';

list($source, $thumb_path, $large_path, $meta_path, $mpc_path) = paths($paths);

$source_image = $source . $image;
$mpc_source   = $mpc_path . $mpc_file;

# config
$page_title = 'Testing ImageMagick resize filters';

$li = [];

foreach($filters as $filter)
{
	$fname = 'filter_'.$filter;
	$image_file = get_file_by_filter($filter, $thumb_path, $image);

	if(file_exists($image_file))
	{
		list($w, $h) = getimagesize($image_file);

		# meta
		$meta_file_path = get_file_by_filter($filter, $meta_path, $meta_file);
		$metaf = file_exists($meta_file_path) ? explode(',', file_get_contents($meta_file_path)) : FALSE;

		if($metaf === FALSE)
		{
			$metaf[] = (int)$quality;
			$metaf[] = (float)$blur;
		}

		$li[$filter] = sprintf($thumb_template, $fname, $filter, $quality, $blur, $image_file, format_bytes(filesize($image_file)), $w, $h, $metaf[0], $metaf[1], strtoupper($filter));
	}

	else
		$li[$filter] = sprintf($input_template, $fname, strtoupper($filter));
}

sort($li);

/**
 * Upload
 */
if($_POST)
{
	if($_FILES)
	{
		if(array_key_exists('userfile', $_FILES) === TRUE)
		{
			if($_FILES['userfile']['error'] == 0)
			{
				$ftname = $_FILES['userfile']['tmp_name'];
				$finfo  = new finfo(FILEINFO_MIME_TYPE);
				$mime   = $finfo->file($ftname);

				if(in_array($mime, $mimes))
				{
					move_uploaded_file($ftname, $source_image);
					dump_to_mpc($source_image, $mpc_path);

					foreach($filters as $filter)
					{
						$type = 'FILTER_'.strtoupper($filter);

						# thumbs
						resize($mpc_source, $image, $thumb_path, $quality, $type, $thumb_width, 0, $blur, $best_fit, $filters);

						# large
						resize($mpc_source, $image, $large_path, $quality, $type, $large_width, 0, $blur, $best_fit, $filters);

						# meta
						w_meta_file($meta_file, $meta_path, $filter, $quality, $blur);
					}
				}
					move_uploaded_file($ftname, $source_image);
			}
			
			else
				error_log('Error: ' . $_FILES['userfile']['error'] . PHP_EOL, './error.log');
		}

		header('Location: ./'.$redirect);
		exit;
	}
}

/**
 * Clean
 */
if(file_exists($source_image))
{
	if(array_key_exists('change', $_GET))
	{
		rm($source_image);

		$files = glob($thumb_path . '*.jpg');
		array_map('rm', $files);

		$files = glob($large_path . '*.jpg');
		array_map('rm', $files);

		$files = glob($meta_path . '*.txt');
		array_map('rm', $files);

		$files = glob($mpc_path . '*.*');
		array_map('rm', $files);

		header('Location: ./'.$redirect);
		exit;
	}
}

/**
 * Resize
 */
if(file_exists($mpc_source) && array_key_exists('resize_type', $_GET)) {

	$type = filter_input(INPUT_GET, 'resize_type', FILTER_SANITIZE_STRING);

	# thumb
	$filter_name = resize($mpc_source, $image, $thumb_path, $quality, $type, $thumb_width, 0, $blur, $best_fit, $filters);

	if($filter_name === FALSE)
	{
		header('Location: ./'.$redirect.'?quality='.$quality.'&blur='.$blur);
		exit;
	}

	# large
	resize($mpc_source, $image, $large_path, $quality, $type, $large_width, 0, $blur, $best_fit, $filters);

	# meta
	w_meta_file($meta_file, $meta_path, $filter_name, $quality, $blur);

	header('Location: ./'.$redirect.'?quality='.$quality.'&blur='.$blur.'#filter_'.$filter_name);
	exit;
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

		<?php if( ! file_exists($source_image)) { ?>

			<p>Select an image to upload.</p>

			<form method="post" enctype="multipart/form-data">
				<p>
					<label for="userfile">Allowed mime types: image/jpeg, image/pjpeg</label>
					<input type="file" name="userfile" id="userfile" accept="image/jpeg,image/pjpeg">
				</p>
				<p>
					<input type="submit" name="submit" id="submit" value="upload">
				</p>
			</form>

		<?php } else { ?>

			<p>Select a resize filter:</p>

			<form method="get">
				<p>
					<label for="quality">Compression Quality (1-100), default: 100%</label>
					<input type="number" step="1" min="1" max="100" name="quality" id="quality" value="<?php echo $quality; ?>">
				</p>

				<p>
					<label for="blur">Blur (sharp &lt; 1.0 &gt; blurry) default: 1.0</label>
					<input type="number" step="0.1" min="0.0" max="3.0" name="blur" id="blur" value="<?php echo $blur; ?>">
				</p>

				<p>
					<label for="resize_type">Filters</label>
					<ul class="flex-list">
						<?php echo implode(PHP_EOL, $li); ?>
					</ul>
				</p>

				<p>
					<label for="change">Restart</label>
					<input type="submit" name="change" id="change" value="clear all">
				</p>
			</form>

		<?php } ?>

	</div>

</body>
</html>
