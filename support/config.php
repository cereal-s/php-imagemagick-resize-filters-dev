<?php

# set the redirects
$config['url_base']  = './';

# this is prefixed by the filters
$config['image'] 	 = 'image.jpg';

# the MPC file name
$config['mpc_file']	 = 'image.mpc';

# this file saves the compression quality rate
# and the blur for each version of the image
$config['meta_file']   = 'meta.txt';

# image width
$config['thumb_width'] = 400;
$config['large_width'] = 800;
$config['height'] 	 = 0;

# compression quality, defaults to 100
$config['quality'] 	 = filter_input(INPUT_GET, 'quality', FILTER_VALIDATE_INT, ['options' => ['default' => 100, 'min_range' => 1, 'max_range' => 100]]);

# IM filter name
$config['filter_name'] = filter_input(INPUT_GET, 'filter_name', FILTER_SANITIZE_STRING);

# Blur, defaults to 1.0
$config['blur'] 		 = filter_input(INPUT_GET, 'blur', FILTER_VALIDATE_FLOAT, ['options' => ['default' => 1.0, 'decimal' => '.']]);

# Best fit is disabled
$config['best_fit'] 	 = FALSE;