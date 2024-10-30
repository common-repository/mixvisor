<?php
// --------------------------------
// Add settings link on plugin page
// --------------------------------

function mixvisor_settings_link($links) {
  $settings_link = '<a href="options-general.php?page=mixvisor">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

add_filter("plugin_action_links_mixvisor/mixvisor.php", 'mixvisor_settings_link' );
