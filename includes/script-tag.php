<?php
// ---------------------
// Create the script tag
// ---------------------

function mixvisor_generate_script_tag($scriptConfig) {
  $embedCode = '<script async defer src="' . $scriptConfig->STORAGEURL . '"
    data-mv-sid="' . esc_attr( $scriptConfig->MVSID ) . '"
    data-mv-at="' . esc_attr( $scriptConfig->MVAT ) . '"
    id="' . $scriptConfig->ID . '"
    data-player-type="' . $scriptConfig->player_type . '"
    data-play-icons="' . $scriptConfig->play_icons . '"
    data-auto-detection="' . $scriptConfig->auto_detection . '"
    data-hide-fixed="' . $scriptConfig->hide_fixed . '"
    data-last-modified="' . $scriptConfig->last_modified . '"
    data-position="' . $scriptConfig->position . '"
    data-artists="' . $scriptConfig->artists . '">
    </script>';

  return $embedCode;
}
