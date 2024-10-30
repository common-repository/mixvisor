<?php

// Check whether a user has a SID and AT
define('MVSID', get_option('mixvisor_sid'));
define('MVAT', get_option('mixvisor_at'));

if (getenv('MV_WP_STORAGEURL')) {
  define('MVSTORAGEURL', getenv('MV_WP_STORAGEURL') . '/js/mv-latest.js');
}
else {
  define('MVSTORAGEURL', '//storage.mixvisor.com/mvjslibrary/v2/latest/mv-latest.js');
}

if (getenv('MV_WP_SERVERURL')) {
  define('MVSERVERURL', getenv('MV_WP_SERVERURL'));
}
else {
  define('MVSERVERURL', 'https://publisher.mixvisor.com');
}


// Create a hash that only the server can verify

define('MVEXTERNALHASH', '1234');
