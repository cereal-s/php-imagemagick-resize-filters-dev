<?php

/**
 * Template for the populated page
 * @var string
 */
$thumb_template = <<<EOD
	<li id="%s">
		<a href="./view.php?filter_name=%s&quality=%s&blur=%s" target="_blank"><img src="%s"></a>
		<p>%s, %sx%s pixels, qualità: %s%%, blur: %s</p>
		<input type="submit" name="resize_type" value="FILTER_%s">
	</li>
EOD;


/**
 * Template for the form
 * @var string
 */
$input_template = <<<EOD
	<li id="%s">
		<input type="submit" name="resize_type" value="FILTER_%s">
	</li>
EOD;


/**
 * Template for the large view page
 * @var string
 */
$large_template = <<<EOD
	<div id="%s">
		<img src="%s">
		<p>%s, %sx%s pixels, qualità: %s%%, blur: %s</p>
	</div>
EOD;
