<?php
// ----------------
// Setup admin page
// ----------------

add_action('admin_menu', 'mixvisor_menu');

function mixvisor_menu() {
  add_submenu_page('options-general.php', 'Mixvisor Settings', 'Mixvisor', 'manage_options', 'mixvisor', 'mixvisor_settings_page');
}

function mixvisor_settings_page() {
  // Get categories
  $cat_args = array(
    'hide_empty' => 0
  );
  $site_categories     = get_categories( $cat_args );
  $mixvisor_categories = get_option('mixvisor_exclude_categories');
  $selected_categories = explode(",", $mixvisor_categories);

  // Get pages
  $site_pages          = get_pages();
  $mixvisor_pages      = get_option('mixvisor_exclude_pages');
  $home_page           = get_option('page_on_front');
  $default_home_page   = true;
  if ( isset($mixvisor_pages) ) {
    $selected_pages    = explode(",", $mixvisor_pages);
  }
  else {
    $selected_pages    = array();
  }

  // Check if the homepage's ID is in the $site_pages array
  foreach ($site_pages as $page) {
    if ( $page->ID == $home_page ) {
      // Homepage is in site pages
      $default_home_page = false;
      // Remove defualt homepage from $selected_pages array
      if (($key = array_search('0', $selected_pages)) !== false) {
        unset($selected_pages[$key]);
        // Update the exlcuded pages option
        $updated_pages_option = implode(",", $selected_pages);
        update_option('mixvisor_exclude_pages', $updated_pages_option);
      }
      break;
    }
  }

  // If the default homepage is being used add it to the $site_pages array
  if ( $default_home_page ) {
    // Create the homepage object
    $default_home_page_object = new stdClass();
    // Assign the homepage object properties
    $default_home_page_object->ID = $home_page;
    $default_home_page_object->post_name = 'homepage';
    $default_home_page_object->post_title = 'Homepage';
    // Add the homepage object to the $site_pages array
    array_push($site_pages, $default_home_page_object);
  }

  // Set the default options for radio buttons
  $global_player       = get_option('mixvisor_global_player') ? get_option('mixvisor_global_player') : 'true';
  $desktop_hide_player = get_option('mixvisor_desktop_hide_player') ? get_option('mixvisor_desktop_hide_player') : 'true';
  $auto_detect         = get_option('mixvisor_auto_detect') ? get_option('mixvisor_auto_detect') : 'false';
  $position            = get_option('mixvisor_position') ? get_option('mixvisor_position') : 'top';

  // Get the SID & AT
  $MVSID = get_option('mixvisor_sid');
  $MVAT  = get_option('mixvisor_at');

  if ( !$MVAT || !$MVSID ) {
    $auto_detect = 'false';
  }
  ?>

  <!-- Output markup -->
  <div class="wrap">
    <h2>Mixvisor <?php _e( 'Settings', 'mixvisor-plugin' ) ?></h2>

    <form method="post" action="options.php" id="mixvisor_options">
      <?php settings_fields( 'mixvisor-settings-group' ); ?>
      <?php do_settings_sections( 'mixvisor-settings-group' ); ?>
      <table class="form-table">

        <tr valign="top">
          <th scope="row"><?php _e( 'Fixed Player', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose whether to show the fixed player on all pages? (It's shown by default)</p>
            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_global_player" value="true" <?php if (checked( $global_player, 'true' )); ?>> Show</li>
              <li><input type="radio" name="mixvisor_global_player" value="false" <?php if (checked( $global_player, 'false' )); ?>> Hide</li>
            </ul>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Player Position', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose where to position the fixed player? (It's at the top of the page by default)</p>
            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_position" value="top" <?php if (checked( $position, 'top' )); ?>> Top</li>
              <li><input type="radio" name="mixvisor_position" value="bottom" <?php if (checked( $position, 'bottom' )); ?>> Bottom</li>
            </ul>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Artists', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description" style="margin-bottom: 14px;">Add additional artists to the player. Seperate each artist with a comma <em>e.g Disclosure, Coldplay</em><br>(They will appear on every page)</p>
            <textarea name="mixvisor_artists" cols="85" rows="2"><?php echo esc_attr( get_option('mixvisor_artists') ); ?></textarea>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Categories', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose which categories to exclude the fixed player from.</p>
            <ul class="mixvisor-admin-inline-list">
            <?php
              foreach ($site_categories as $category) {
                $checked = '';
                if (in_array($category->term_id, $selected_categories)) {
                  $checked = 'checked';
                }
                ?>
                  <li>
                    <input
                      type="checkbox"
                      data-mv-type="categories"
                      class="js-mv-checkbox" <?php echo $checked; ?>
                      id="mv_<?php echo $category->slug; ?>"
                      name="<?php echo $category->slug; ?>"
                      value="<?php echo $category->term_id; ?>">

                    <label for="mv_<?php echo $category->slug; ?>"><?php echo $category->name; ?></label>
                  </li>
                <?php
              }
            ?>
            </ul>
            <input type="hidden" id="mixvisor_categories" name="mixvisor_exclude_categories" value="<?php echo esc_attr( get_option('mixvisor_exclude_categories') ); ?>">
            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Pages', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose which pages to exclude the fixed player from.</p>
            <ul class="mixvisor-admin-inline-list">
            <?php
              foreach ($site_pages as $page) {
                $checked = '';
                if (in_array($page->ID, $selected_pages)) {
                  $checked = 'checked';
                }
                ?>
                  <li>
                    <input
                      type="checkbox"
                      data-mv-type="pages"
                      class="js-mv-checkbox" <?php echo $checked; ?>
                      id="mv_<?php echo $page->post_name; ?>"
                      name="<?php echo $page->post_name; ?>"
                      value="<?php echo $page->ID; ?>">

                    <label for="mv_<?php echo $page->post_name; ?>"><?php echo $page->post_title; ?></label>
                  </li>
                <?php
              }
            ?>
            </ul>
            <input type="hidden" id="mixvisor_pages" name="mixvisor_exclude_pages" value="<?php echo esc_attr( get_option('mixvisor_exclude_pages') ); ?>">
            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Desktop', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose whether to hide the fixed player on desktop. (It's shown by default)</p>
            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_desktop_hide_player" value="false" <?php if (checked( $desktop_hide_player, 'false' )); ?>> Show</li>
              <li><input type="radio" name="mixvisor_desktop_hide_player" value="true" <?php if (checked( $desktop_hide_player, 'true' )); ?>> Hide</li>
            </ul>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Auto Detection', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <p class="description">Choose whether to automatically detect artist names.</p>
            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_auto_detect" value="true" <?php if (checked( $auto_detect, 'true' )); ?>> On</li>
              <li><input type="radio" name="mixvisor_auto_detect" value="false" <?php if (checked( $auto_detect, 'false' )); ?>> Off</li>
            </ul>

            <div class="clear"></div>

            <?php if ( !$MVAT || !$MVSID ) { ?>
              <div class="mixvisor-require-register">
                <p>You need a <a href="https://publisher.mixvisor.com" target="_blank">free Mixvisor account</a> to enable auto detection.</p>
                <p>It takes less than a minute to <a href="https://publisher.mixvisor.com" target="_blank">sign up!</a></p>
                <p>Then all you need to do is add your Site ID and Access Token below.</p>
              </div>
            <?php } ?>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Site ID', 'mixvisor-plugin' ) ?>:</th>
          <td>
          <textarea name="mixvisor_sid" cols="85" rows="1"><?php echo esc_attr( get_option('mixvisor_sid') ); ?></textarea>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><?php _e( 'Access Token', 'mixvisor-plugin' ) ?>:</th>
          <td>
            <textarea name="mixvisor_at" cols="85" rows="2"><?php echo esc_attr( get_option('mixvisor_at') ); ?></textarea>
          </td>
        </tr>

      </table>

      <?php submit_button(); ?>
    </form>

    <hr>

    <p><a href="http://mixvisor.com">Mixvisor</a> &#124; <a href="mailto:giles@mixvisor.com?subject=Mixvisor Wordpress Plugin Support" class="mv-feedback">Support</a> &#124; <a href="https://twitter.com/mixvisor">Twitter</a> &#124; <a href="https://www.facebook.com/mixvisor">Facebook</a></p>
  </div>


  <!-- Output JS -->
  <script>
    // Update the categories when the inputs are toggled
    (function ($) {
      // Vars
      var $mixvisorOptions = $('#mixvisor_options');

      // Events
      $mixvisorOptions.on('click', '.js-mv-checkbox', mvToggleCheckbox);

      // Functions
      function mvToggleCheckbox(e) {
        // Get the item type, ID and current values
        var mvType            = e.currentTarget.getAttribute('data-mv-type'),
            itemID            = e.currentTarget.value,
            currentItems      = document.getElementById('mixvisor_' + mvType),
            currentItemsArray = [];

        if ( currentItems.value !== '' ) {
          currentItemsArray = currentItems.value.split(',');
        }

        // Check if the item is already in the array
        if (currentItemsArray.indexOf(itemID) > -1) {
          // If it is remove it
          var index = currentItemsArray.indexOf(itemID);
          currentItemsArray.splice(index, 1);
        }
        else {
          // If not add the item to the array
          currentItemsArray.push(itemID);
        }
        // Set the hidden input field to the value of the array
        currentItems.value = currentItemsArray;
      }

    })(jQuery);
  </script>

  <?php
}

add_action( 'admin_init', 'mixvisor_settings' );

function mixvisor_settings() {
  register_setting( 'mixvisor-settings-group', 'mixvisor_sid' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_at' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_exclude_categories' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_exclude_pages' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_desktop_hide_player' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_global_player' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_auto_detect' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_artists' );
  register_setting( 'mixvisor-settings-group', 'mixvisor_position' );
}

function mixvisor_load_admin_styles() {
  wp_register_style( 'mixvisor-admin-styles', plugins_url( '../assets/css/mixvisor-admin.css', __FILE__ ) );
  wp_enqueue_style( 'mixvisor-admin-styles' );
}

add_action( 'admin_enqueue_scripts', 'mixvisor_load_admin_styles' );
