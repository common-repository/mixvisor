jQuery(function() {
  'use strict';

  // Polyfill
  if (!('contains' in String.prototype)) {
    String.prototype.contains = function(str, startIndex) {
      return ''.indexOf.call(this, str, startIndex) !== -1;
    };
  }

  // Vars
  var autoSaved    = false;
  var buttonTimeoutID;

  var MVSC = {
    toggleLoader: function (e) {
      // Get the modal loader
      var $loader = jQuery(e.currentTarget).parents('.mvfl').find('.mixvisor-shortcode-popup-loader');

      $loader.toggleClass('is-active');
    },

    toggleMessageBox: function (e, toggle, type, message) {
      // Get the modal loader
      var $messageBox        = jQuery(e.currentTarget).parents('.mvfl').find('.mixvisor-shortcode-popup-messageBox');
      var $messageBoxMessage = $messageBox.find('.mixvisor-shortcode-popup-message');

      // Either clear or setup the message box
      if ( toggle === 'hide' ) {
        $messageBoxMessage.html('');
        $messageBox[0].className = 'mixvisor-shortcode-popup-messageBox';
      }
      else {
        // Set the message
        $messageBoxMessage.html(message);
        // Set the message type and Show the box
        $messageBox.addClass('is-active ' + type);
      }
    },

    checkUrl: function (url) {
      var re_weburl = new RegExp(
        '^' +
        // protocol identifier
        '(?:(?:https?|ftp)://)' +
        // user:pass authentication
        '(?:\\S+(?::\\S*)?@)?' +
        '(?:' +
        // IP address exclusion
        // private & local networks
        '(?!(?:10|127)(?:\\.\\d{1,3}){3})' +
        '(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})' +
        '(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})' +
        // IP address dotted notation octets
        // excludes loopback network 0.0.0.0
        // excludes reserved space >= 224.0.0.0
        // excludes network & broacast addresses
        // (first & last IP address of each class)
        '(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])' +
        '(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}' +
        '(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))' +
        '|' +
        // host name
        '(?:(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)' +
        // domain name
        '(?:\\.(?:[a-z\\u00a1-\\uffff0-9]-*)*[a-z\\u00a1-\\uffff0-9]+)*' +
        // TLD identifier
        '(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))' +
        // TLD may end with dot
        '\\.?' +
        ')' +
        // port number
        '(?::\\d{2,5})?' +
        // resource path
        '(?:[/?#]\\S*)?' +
        '$', 'i'
      );

      return re_weburl.test(url);
    },

    createPlayer: function(e, config, callback) {
      // Clear any previous errors
      MVSC.toggleMessageBox(e, 'hide');

      // Show the modal loader
      MVSC.toggleLoader(e);

      // Get the sample permalink
      var permalink = config.url;
      if ( !MVSC.checkUrl(permalink) ) {
        permalink = '';
      }

      var data = {
        access_token:  MVWP.MVAT,
        SID:           MVWP.MVSID,
        url:           permalink,
        origin:        window.location.hostname,
        autoDetection: config.autoDetection,
        playIcons:     config.playIcons,
        content:       config.content
      };

      jQuery.ajax({
        method:   'post',
        dataType: 'json',
        url:      MVWP.MVSERVERURL + '/player/external/create',
        data:     data
      })
      .done(function( results ) {
        // Hide the loader screen
        MVSC.toggleLoader(e);

        // Return the player ID
        callback(results.playerId);
      })
      .fail(function(data) {
        // Hide the loader
        MVSC.toggleLoader(e);

        var message = 'Sorry, we could not create a player, please try again.';

        if ( data.responseText === 'Unauthorized' ) {
          message = 'Sorry, we could not create a player, you need to sign up for Mixvisor and add your site at <a href="https://publisher.mixvisor.com">publisher.mixvisor.com</a>';
        }

        // Show a failure message
        MVSC.toggleMessageBox(e, 'show', 'mv-error', message);
      });
    },

    setupSamplePermalink: function () {
      var title = jQuery('#title').val();

      // If this is an existing post
      if ( title !== '' ) {
        // Get the sample permalink
        var permalink = jQuery('#view-post-btn a').attr('href');

        // Add the sample permalink to the mixvisor url field
        jQuery('.mixvisor-url').val(permalink);
      }
    },

    getSamplePermalink: function () {
      // Get the sample permalink
        jQuery.post(MVWP.AJAXURL, {
          action:               'get_permalink_sample',
          post_id:              MVWP.postId,
          post_name:            jQuery('#editable-post-name-full').text(),
          samplepermalinknonce: jQuery('#samplepermalinknonce').val()
        }, function (data) {
          // Add the sample permalink to the mixvisor url field
          jQuery('.mixvisor-url').val(data);
        });
    },

    init: function () {
      var $mixvisorShortcodeForm = jQuery('#mixvisor_shortcode_form');

      // Prevent adding a player till the user has created a title/permalink
      jQuery(document).on('click', '#mixvisor_shortcode_popup_button', function (e) {
        e.preventDefault();

        var title = jQuery('#title').val();

        if ( title === '' ) {
          alert('Please create a URL first by entering a post title.');
          return false;
        }

        MVSC.getSamplePermalink();

        jQuery.featherlight('#mixvisor_shortcode_popup', {'variant': 'mvfl'});
      });

      // Get the sample permalink
      MVSC.setupSamplePermalink();

      // Setup the click event
      jQuery(document).on('click', '#mixvisor_add_player', function (e) {
        e.preventDefault();

        // Clear the message box just encase
        MVSC.toggleMessageBox(e, 'hide');

        // Get the form
        var form = e.currentTarget.form;

        // Get the values
        var mixvisor_hide_play_icons = form.elements.mixvisor_hide_play_icons.value;
        var mixvisor_auto_detection  = form.elements.mixvisor_auto_detection.value;
        var mixvisor_artists         = form.elements.mixvisor_artists.value;
        var mixvisor_url             = form.elements.mixvisor_url.value;

        // Validate
        if ( mixvisor_url === '' ) {
          alert('Please enter a URL');
          return false;
        }

        // Setup the config
        var config = {
          autoDetection: mixvisor_auto_detection,
          playIcons:     mixvisor_hide_play_icons,
          url:           mixvisor_url,
          content:       {
            userArtists: mixvisor_artists
          }
        }

        // Create a player
        MVSC.createPlayer(e, config, function(playerId) {
          var shortcode  = '[mixvisor';

          if ( mixvisor_hide_play_icons === 'false' ) {
            shortcode += ' play_icons="false" ';
          }
          if ( mixvisor_auto_detection === 'false' ) {
            shortcode += ' auto_detection="false" ';
          }
          if ( mixvisor_artists !== '' ) {
            shortcode += ' artists="' + mixvisor_artists + '" ';
          }
          shortcode += ' id="' + playerId + '" ';
          shortcode += ']';

          // Add the result to the editor
          if( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
            // If we're in Text Mode - get the cursors position
            var cursorPosition = jQuery('textarea#content').prop('selectionStart');

            // Get the existing content
            var content = jQuery('textarea#content').val();

            // Insert the shortcode at the cursor position
            var newContent = content.substr(0, cursorPosition) + shortcode + content.substr(cursorPosition);

            // Update the content
            jQuery('textarea#content').val(newContent);
          } else {
            // If we're in WYSIWYG mode let tinyMCE handle the insertion
            tinyMCE.execCommand('mceInsertContent', false, shortcode);
          }

          // Close the modal
          var current = jQuery.featherlight.current();
          current.close();

          // Clear the message box
          MVSC.toggleMessageBox(e, 'hide');
        });
      });
    }
  };

  MVSC.init();
});
