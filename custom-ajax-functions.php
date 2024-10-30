<?php

// Get the page or posts permalink via ajax
add_action('wp_ajax_get_permalink_sample', 'ajax_get_permalink_sample');
add_action('wp_ajax_nopriv_get_permalink_sample', 'ajax_get_permalink_sample');

function ajax_get_permalink_sample(){
  check_ajax_referer( 'samplepermalink', 'samplepermalinknonce' );
  $post_id   = isset($_POST['post_id'])? intval($_POST['post_id']) : 0;
  $post_name = isset($_POST['post_name'])? $_POST['post_name'] : '';

  $permalink = get_sample_permalink($post_id, $post_name);
  // If the get sample permalink returns html
  // Of if the post_name was empty
  // then just return an empty string
  if ( strpos($permalink[0], '<strong>Permalink:</strong>') !== false || $post_name == '' ) {
    $link = '';
  }
  else {
    $link1 = str_replace("%postname%", $post_name, $permalink[0]);
    $link  = str_replace("%pagename%", $post_name, $link1);
  }

  echo $link;
  die();
}
