<?php

/**
 * Return main paths
 * 
 * @param  array $paths
 * @return array
 */
function paths($paths)
{
	return [
		$paths['base'] . $paths['source'],
		$paths['base'] . $paths['thumbs'],
		$paths['base'] . $paths['large'],
		$paths['base'] . $paths['meta'],
		$paths['base'] . $paths['mpc']
	];
}

/**
 * Get file by filter
 * 
 * @param  string $filter
 * @param  string $path
 * @param  string $name
 * @return string
 */
function get_file_by_filter($filter, $path, $name = 'image.jpg')
{
	return $path . $filter . '_' . $name;
}


/**
 * Bytes for humans
 * @param  integer $size
 * @return string
 */
function format_bytes($size = 0)
{
	$base = log($size) / log(1024);
	$suffix = array("", "KB", "MB", "GB", "TB")[floor($base)];
	return round(pow(1024, $base - floor($base)), 2) . $suffix;
}


/**
 * Write meta file, contents are quality compression and blur
 * 
 * @param  string 	$file
 * @param  string 	$path
 * @param  string 	$type
 * @param  integer 	$quality
 * @param  float 	$blur
 * @return mixed
 */
function w_meta_file($file, $path, $type, $quality, $blur)
{
	$name = get_file_by_filter($type, $path, $file);
	return file_put_contents($name, $quality.','.$blur);
}


/**
 * Create MPC file for fast resizes.
 * 
 * @param  string $source
 * @param  string $mpc_path
 * @return void
 */
function dump_to_mpc($source, $mpc_path)
{
	$mpc = $mpc_path . 'image.mpc';
	$img = new Imagick();
	$img->readImage(realpath($source));
	$img->stripImage();
	$img->writeImage($mpc);
	$img->clear();
	$img->destroy();	

	return ;
}


/**
 * Resize the image
 * 
 * @param  string  $source
 * @param  string  $image
 * @param  string  $path
 * @param  integer $quality
 * @param  string  $type
 * @param  integer $width
 * @param  integer $height
 * @param  float   $blur
 * @param  boolean $bestfit
 * @return mixed 				when TRUE it returns the name of the used filter
 */
function resize($source, $image, $path, $quality, $type, $width, $height = 0, $blur = 1.0, $bestfit = FALSE, $filters)
{
	
	$filter_name = strtolower(substr($type, 7));
	$image_file  = get_file_by_filter($filter_name, $path, $image);

	if( ! in_array(strtolower($filter_name), $filters))
		return FALSE;

	$img = new \Imagick();
	$img->readImage(realpath($source));
	$img->setImageCompression(Imagick::COMPRESSION_JPEG);
	$img->setImageCompressionQuality((int)$quality);
	$img->stripImage();
	$img->resizeImage($width, $height, constant('Imagick::'.$type), $blur, $bestfit);
	$img->writeImage($image_file);
	$img->clear();
	$img->destroy();

	return $filter_name;
}


/**
 * Verify if file exists and delete.
 * 
 * @param  string  $file
 * @return boolean
 */
function rm($file)
{
	if(file_exists($file)) return unlink($file);

	return FALSE;
}
