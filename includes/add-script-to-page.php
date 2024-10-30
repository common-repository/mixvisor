<?php
// ------------------
// Add script to page
// ------------------

add_action( 'wp_footer', 'mixvisor_add_script_tag' );

function mixvisor_add_script_tag() {

    // Check if the global player is enabled
    $global_player = get_option('mixvisor_global_player') ? get_option('mixvisor_global_player') : 'true';
    if ( $global_player === 'false' ) {
      return false;
    }

    // Get the post
    global $post;

    // Check if the player is already on the page, if so don't load it
    // if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'mixvisor') ) {
  	// 	return false;
  	// }

    // Create the script config object
    $scriptConfig = new stdClass();

    // Assign the config object properties
    $scriptConfig->STORAGEURL     = MVSTORAGEURL;
    $scriptConfig->ID             = '_MXV_';
    $scriptConfig->MVSID          = MVSID;
    $scriptConfig->MVAT           = MVAT;
    $scriptConfig->hide_fixed     = get_option('mixvisor_desktop_hide_player');
    $scriptConfig->player_type    = 'fixed';
    $scriptConfig->play_icons     = 'true';
    $scriptConfig->auto_detection = get_option('mixvisor_auto_detect');
    $scriptConfig->artists        = get_option('mixvisor_artists');
    $scriptConfig->position       = get_option('mixvisor_position');
    $scriptConfig->last_modified  = get_the_modified_time('D, d M Y H:i:s T');

    $embedCode = mixvisor_generate_script_tag($scriptConfig);

    // Selected Categories to exclude
    $mixvisor_categories = get_option('mixvisor_exclude_categories');

    // If there are any categories excluded create an array
    if ( empty($mixvisor_categories) && $mixvisor_categories !== '0'  ) {
      $selected_categories = array();
    }
    else {
      $selected_categories = explode(",", $mixvisor_categories);
    }

    // Selected Pages to exclude
    $mixvisor_pages           = get_option('mixvisor_exclude_pages');
    $include_default_homepage = true;
    $code_embedded            = false;

    // If there are any pages excluded create an array
    if ( empty($mixvisor_pages) && $mixvisor_pages !== '0'  ) {
      $selected_pages = array();
    }
    else {
      $selected_pages = explode(",", $mixvisor_pages);
    }

    // Get the current Page ID
    $page_id = get_queried_object_id();

    // Get the current page categories
    $categories = get_the_category($post->ID);
    $cat_ids = array();
    foreach($categories as $category) {
      $cat_ids[] = $category->cat_ID;
    }

    // Check to see if default homepage is excluded
    if (($key = array_search('0', $selected_pages)) !== false) {
      // If it is excluded remove its key from the $selected_pages array as passing a false value (id: 0) to some wordpress functions can break them
      unset($selected_pages[$key]);
      // Set $include_default_homepage to false so we know not to include
      $include_default_homepage = false;
    }

    // 1. If a category has been excluded don't embed the code on the category landing page
    if ( is_category() ) {
      if ( !empty($selected_categories) && !is_category($selected_categories) ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 2. If a category has been excluded don't embed the code in any of its posts
    // Only exclude it if all it's categories are excluded
    $allCatsExcluded = (array_count_values($cat_ids) == array_count_values($selected_categories));
    if ( is_single() ) {
      // If they have excluded any categories and its not the homepage (wp bug with in_category)
      if ( !empty($selected_categories) && !$allCatsExcluded && !is_home() ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 3. If a page has been excluded don't embed the code on that page
    if ( is_page() ) {
      if ( !empty($selected_pages) && !is_page($selected_pages) && $page_id !== 0 ) {
        echo $embedCode;
        $code_embedded = true;
      }
    }

    // 4. If the default homepage hasn't been excluded embed the code
    if ( $page_id === 0 && $include_default_homepage ) {
      echo $embedCode;
      $code_embedded = true;
    }

    // 5. If no categories have been selected and no pages have been excluded add the code to every page
    if ( empty($selected_pages) && empty($selected_categories) && !$code_embedded && $page_id !== 0 ) {
      echo $embedCode;
      $code_embedded = true;
    }
}
