<?php
/**
 * Plugin Name: Mixvisor
 * Plugin URI: http://mixvisor.com
 * Description: Mixvisor helps your users discover the artists they read about in your content.
 * Version: 0.4.6
 * Author: Mixvisor
 * Author URI: http://mixvisor.com
 * Copyright: Copyright 2015 Mixvisor - Giles Butler
 */

 // -----
 // Vars
 // -----

 require_once( dirname(__file__).'/includes/vars.php' );

 // ----------
 // Script Tag
 // ----------

 require_once( dirname(__file__).'/includes/script-tag.php');

 // -----
 // Admin
 // -----

 require_once( dirname(__file__).'/admin/index.php');

 // ---------------------
 // Custom Ajax Functions
 // ---------------------

 require_once( dirname(__file__).'/custom-ajax-functions.php');

 // -------------
 // Add shortcode
 // -------------

 require_once( dirname(__file__).'/shortcode/index.php');

 // ------
 // Output
 // ------

 require_once( dirname(__file__).'/includes/add-script-to-page.php');
