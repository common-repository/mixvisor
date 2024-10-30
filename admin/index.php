<?php
if ( is_admin() ) {

  // -----------------
  // Add settings page
  // -----------------

  require_once( dirname(__file__).'/settings-page.php');


}
  // -----------------
  // Add settings link
  // -----------------

  require_once( dirname(__file__).'/add-settings-link.php');
