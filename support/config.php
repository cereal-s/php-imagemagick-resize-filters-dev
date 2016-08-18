<?php

$redirect 	 = '';

$image 		 = 'image.jpg';
$meta_file   = 'meta.txt';
$mpc_file	 = 'image.mpc';

$thumb_width = 400;
$large_width = 800;
$height 	 = 0;

$quality 	 = filter_input(INPUT_GET, 'quality', FILTER_VALIDATE_INT, ['options' => ['default' => 100, 'min_range' => 1, 'max_range' => 100]]);
$filter_name = filter_input(INPUT_GET, 'filter_name', FILTER_SANITIZE_STRING);
$blur 		 = filter_input(INPUT_GET, 'blur', FILTER_VALIDATE_FLOAT, ['options' => ['default' => 1.0, 'decimal' => '.']]);
$best_fit 	 = FALSE;