<div id="mixvisor_shortcode_popup" class="mixvisor-shortcode-popup">

  <div class="mixvisor-shortcode-popup-loader">
    <div class="mixvisor-shortcode-popup-loader-info">
      <p><?php _e( 'Hold tight, the music is coming', 'mixvisor-plugin' ) ?>&hellip;</p>
      <span class="dashicons dashicons-update mv-spinner"></span>
    </div>
    <!-- /.mixvisor-shortcode-popup-loader-icon -->
  </div>
  <!-- /.mixvisor-shortcode-popup-loader -->

  <div class="mixvisor-shortcode-popup-header">
    <img src="<?php echo plugins_url( '../assets/mv-logo.png', __FILE__ ) ?>" width="100" alt="Mixvisor" style="display: block; margin: 0 auto 20px;" />
  </div>
  <!-- /.mixvisor-shortcode-popup-header -->

  <div class="mixvisor-shortcode-popup-messageBox">
    <p class="mixvisor-shortcode-popup-message"></p>
  </div>
  <!-- /.mixvisor-shortcode-popup-messageBox -->

  <?php
  // Check whether a user has a SID and AT
  $MVSID = get_option('mixvisor_sid');
  $MVAT  = get_option('mixvisor_at');

  $auto_detection_disabled = '';
  $registered_user         = true;

  if ( !$MVAT || !$MVSID ) {
    $auto_detection_disabled = 'disabled';
    $registered_user         = false;
  }

  global $post;
  $postID = $post->ID;
  $permalink = get_sample_permalink($postID);
  ?>

  <div class="mixvisor-shortcode-popup-wrap">
    <form class="mixvisor-shortcode-popup-form" id="mixvisor_shortcode_form">
      <table class="form-table">

        <tr valign="top">
          <th scope="row"><label for="mixvisor_url"><?php _e( 'Url', 'mixvisor-plugin' ) ?>:</label></th>
          <td>
            <p class="mixvisor-description"><?php _e( 'What is the url of this post or page?', 'mixvisor-plugin' ) ?> <br><span class="mixvisor-description-note">( <?php _e( 'We recommend adding a title and permalink before adding a player', 'mixvisor-plugin' ) ?> )</span></p>

            <input type="text" class="large-text mixvisor-url" id="mixvisor_url" name="mixvisor_url" required />

            <p class="mixvisor-description-error mixvisor-url-error"><em><?php _e( 'Ot oh: It looks like we couldn\'t get a URL for this post. Please copy and paste the post URL here first.', 'mixvisor-plugin' ) ?></em></p>

            <p class="mixvisor-description-note"><em><?php _e( 'Note: If you update the permalink you will need to add a new player', 'mixvisor-plugin' ) ?></em></p>

            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><label for="mixvisor_artists"><?php _e( 'Artists', 'mixvisor-plugin' ) ?>:</label></th>
          <td>
            <p class="mixvisor-description"><?php _e( 'Which artists would you like to include?', 'mixvisor-plugin' ) ?> <br><span class="mixvisor-description-note">( <?php _e( 'Artist names must be separated by a comma', 'mixvisor-plugin' ) ?> <strong>,</strong> )</span></p>

            <textarea name="mixvisor_artists" rows="3" placeholder="e.g Disclosure, Coldplay, Taylor Swift..."></textarea>

            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><label for="mixvisor_hide_play_icons"><?php _e( 'Play Icons', 'mixvisor-plugin' ) ?>:</label></th>
          <td>
            <p class="mixvisor-description"><?php _e( 'Would you like to show play icons next to artist names?', 'mixvisor-plugin' ) ?> <br><span class="mixvisor-description-note">( <?php _e( 'Play icons will only appear next to artist names with the correct case - e.g Sia will not match sia', 'mixvisor-plugin' ) ?> )</span></p>

            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_hide_play_icons" value="true" checked="checked"> <?php _e( 'Show', 'mixvisor-plugin' ) ?></li>
              <li><input type="radio" name="mixvisor_hide_play_icons" value="false"> <?php _e( 'Hide', 'mixvisor-plugin' ) ?></li>
            </ul>

            <div class="clear"></div>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row"><label for="mixvisor_auto_detection"><?php _e( 'Auto Detection', 'mixvisor-plugin' ) ?>:</label></th>
          <td>
            <p class="mixvisor-description"><?php _e( 'Do you want to automatically detect artist names?', 'mixvisor-plugin' ) ?></p>

            <ul class="mixvisor-admin-inline-list">
              <li><input type="radio" name="mixvisor_auto_detection" value="true" checked="checked" <?php echo $auto_detection_disabled ?>> <?php _e( 'Yes', 'mixvisor-plugin' ) ?></li>
              <li><input type="radio" name="mixvisor_auto_detection" value="false" <?php echo $auto_detection_disabled ?>> <?php _e( 'No', 'mixvisor-plugin' ) ?></li>
            </ul>

            <div class="clear"></div>

            <?php if ( !$registered_user ) { ?>
              <div class="mixvisor-require-register">
                <p><?php _e( 'You need a', 'mixvisor-plugin' ) ?> <a href="https://publisher.mixvisor.com" target="_blank"><?php _e( 'free', 'mixvisor-plugin' ) ?> Mixvisor <?php _e( 'account', 'mixvisor-plugin' ) ?></a> <?php _e( 'to enable auto detection.', 'mixvisor-plugin' ) ?></p>
                <p><?php _e( 'It takes less than a minute to', 'mixvisor-plugin' ) ?> <a href="https://publisher.mixvisor.com" target="_blank"><?php _e( 'sign up!', 'mixvisor-plugin' ) ?></a></p>
              </div>
            <?php } ?>
          </td>
        </tr>

        <tr valign="top">
          <td></td>
          <td align="right">
            <button type="subtit" id="mixvisor_add_player" class="button button-primary button-large"><?php _e( 'Create Player', 'mixvisor-plugin' ) ?></button>
          </td>
        </tr>

      </table>
    </form>
  </div>
  <!-- /.mixvisor-shortcode-popup-wrap -->
</div>
