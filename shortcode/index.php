<?php

function mixvisor_is_edit_page($new_edit = null){
  global $pagenow;
  //make sure we are on the backend
  if (!is_admin()) return false;

  if($new_edit == "edit")
  return in_array( $pagenow, array( 'post.php',  ) );
  elseif($new_edit == "new") //check for new post page
  return in_array( $pagenow, array( 'post-new.php' ) );
  else //check for either new or edit
  return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

if (mixvisor_is_edit_page()) {

  //add the button to the tinymce editor
  add_action('media_buttons_context','mixvisor_add_tinymce_media_button');

  function mixvisor_add_tinymce_media_button($context){
    $buttonText = __( 'Add', 'mixvisor-plugin' ) . ' Mixvisor ' . __( 'Player', 'mixvisor-plugin' );

    return $context.=__('
      <button class="button" type="button" id="mixvisor_shortcode_popup_button" title="Add Mixvisor Player"><span class="dashicons dashicons-controls-volumeon mixvisor-shortcode-button-icon"></span>' . $buttonText . '</button>');
  }

  //Generate inline content for the popup window when the mixvisor shortcode button is clicked
  add_action('admin_footer','mixvisor_shortcode_media_button_popup');

  function mixvisor_shortcode_media_button_popup() {
    //javascript code needed to make shortcode appear in TinyMCE edtor
    require_once( dirname(__file__).'/shortcode-media-popup.php');
  }

  function mixvisor_load_scripts() {
    wp_register_script( 'featherlight', plugins_url( '../assets/js/featherlight.min.js', __FILE__ ) );
    wp_enqueue_script( 'featherlight', false, array(), false, false );
    wp_register_script( 'mixvisor-shortcode', plugins_url( './mixvisor-shortcode.js', __FILE__ ) );
    wp_enqueue_script( 'mixvisor-shortcode', false, array('featherlight'), false, false  );

    // Get the pages ID
    global $post;

    wp_localize_script( 'mixvisor-shortcode', 'MVWP', array(
      'AJAXURL' =>  admin_url( 'admin-ajax.php' ),
      'postId' =>  $post->ID,
      'MVAT' =>  MVAT,
      'MVSID' =>  MVSID,
      'MVSERVERURL' =>  MVSERVERURL
    ));
  }

  add_action( 'admin_enqueue_scripts', 'mixvisor_load_scripts' );

  function mixvisor_load_shortcode_styles() {
    wp_register_style( 'mixvisor-shortcode-styles', plugins_url( '../assets/css/mixvisor-shortcode.css', __FILE__ ) );
    wp_register_style( 'featherlight-styles', plugins_url( '../assets/css/featherlight.min.css', __FILE__ ) );
    wp_enqueue_style( 'mixvisor-shortcode-styles' );
    wp_enqueue_style( 'featherlight-styles' );
  }

  add_action( 'admin_enqueue_scripts', 'mixvisor_load_shortcode_styles' );

}

// Add the mixvisor script to the page
function mixvisor_tag_func( $atts, $content = null ) {

  $a = shortcode_atts( array(
      'play_icons'     => 'true',
      'auto_detection' => 'true',
      'artists'        => '',
      'id'             => ''
  ), $atts );

  // Create the script config object
  $scriptConfig = new stdClass();

  // Assign the config object properties
  $scriptConfig->STORAGEURL     = MVSTORAGEURL;
  $scriptConfig->ID             = $a['id'];
  $scriptConfig->MVSID          = MVSID;
  $scriptConfig->MVAT           = MVAT;
  $scriptConfig->hide_fixed     = get_option('mixvisor_desktop_hide_player');
  $scriptConfig->player_type    = 'embedded';
  $scriptConfig->play_icons     = $a['play_icons'];
  $scriptConfig->auto_detection = $a['auto_detection'];
  $scriptConfig->artists        = $a['artists'];
  $scriptConfig->position       = '';
  $scriptConfig->last_modified  = get_the_modified_time('D, d M Y H:i:s T');

  $embedCode = mixvisor_generate_script_tag($scriptConfig);

  return $embedCode;
}

add_shortcode( 'mixvisor', 'mixvisor_tag_func' );
