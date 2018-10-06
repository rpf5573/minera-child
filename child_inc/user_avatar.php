<?php
/**
 * This class is for making user upload self avatar image on `My Account` page
 * 
 * Main process is this
 * 1. show avatar and upload form on `My Account` page -> use `woocommerce/myaccount/form-edit-account.php`
 * 2. user upload image file and server receive that file and store server HD by using Wordpress's File Handle API and then store user id with file path on DB
 * 3. reload ! -> find avatar image(file path) by user_id and meta_id -> you can see your avatar
 */

class RPF_User_Avatar {

  function __construct() {
    add_action( 'woocommerce_save_account_details', array( $this, 'upload_avatar' ) );
    add_filter( 'get_avatar', array( $this, 'filter_get_avatar' ), 11, 3 );
  }

  /**
   * Output the proper encoding type for the user edit form
   *
   * @since 0.1.0
   */
  function edit_profile_form_tag() {
    echo 'enctype="multipart/form-data"';
  }

  /**
   * Return a unique filename for uploaded avatars
   *
   * @since 0.1.0
   *
   * @param  string  $dir   Path for file
   * @param  string  $name  Filename
   * @param  string  $ext   File extension (e.g. ".jpg")
   *
   * @return string Final filename
   */
  function unique_filename_callback( $dir, $name, $ext ) {
    // Get user
    $user = get_user_by( 'id', $GLOBALS['rpf_user_id'] );
    // File suffix
    $suffix = time();
    // Override names
    $_name = $base_name = sanitize_file_name( 'avatar_user_' . $user->ID . '_' . $suffix );
    // Ensure no conflicts with existing file names
    $number = 1;
    while ( file_exists( $dir . "/{$_name}{$ext}" ) ) {
      $_name = $base_name . '_' . $number;
      $number++;
    }
    // Return the unique filename
    return $_name . $ext;
  }

  /**
   * Delete an avatar
   *
   * @since 0.1.0
   *
   * @param  int $user_id
   *
   * @return type
   */
  function delete_avatar( $user_id = 0 ) {
    // Bail if no avatars to delete
    $old_avatars = (array) get_user_meta( $user_id, 'rpf', true );
    if ( empty( $old_avatars ) ) {
      return;
    }

    // Don't erase media library files
    if ( array_key_exists( 'media_id', $old_avatars ) ) {
      unset( $old_avatars['media_id'], $old_avatars['full'] );
    }

    // Are there files to delete?
    if ( ! empty( $old_avatars ) ) {
      $upload_path = wp_upload_dir();

      // Loop through avatars
      foreach ( $old_avatars as $old_avatar ) {

        // Use the upload directory
        $old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatar );

        // Maybe delete the file
        if ( file_exists( $old_avatar_path ) ) {
          unlink( $old_avatar_path );
        }
      }
    }

    // Remove metadata
	  delete_user_meta( $user_id, 'rpf_user_avatar');
  }

  /**
   * Saves avatar image of user
   *
   * @since 0.1.0
   *
   * @param int        $user_id  ID of user to assign image to
   * @param int|string $media    Local URL for avatar or ID of attachment
   */
  function update_avatar( $user_id, $avatar ) {
    // Delete old avatar
    $this->delete_avatar( $user_id );

    // Setup empty meta array
    $meta_value = array();

    // Set full value to media URL
    $meta_value['full'] = esc_url_raw( $avatar['url'] );

    // Update user metadata
    update_user_meta( $user_id, 'rpf_user_avatar', $meta_value );
  }

  /**
   * receive avatar file and upload to server
   */
  function upload_avatar( $user_id ) {
    if ( ! empty($_FILES['wp-user-avatars']['name']) ) {
      if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
      }
  
      // use in unique_filename_callback
      $GLOBALS['rpf_user_id'] = $user_id;
  
      // Handle upload
      $avatar = wp_handle_upload( $_FILES['wp-user-avatars'], array(
        'mimes' => array(
          'jpg|jpeg|jpe' => 'image/jpeg',
          'gif'          => 'image/gif',
          'png'          => 'image/png',
        ),
        'test_form' => false,
        'unique_filename_callback' => array( $this, 'unique_filename_callback' )
      ) );
      
      // no need anymore
      unset( $GLOBALS['rpf_user_id'] );
      
      $this->update_avatar( $user_id, $avatar );
    }    
  }

  function get_avatar_url( $user_id = false, $size = 150 ) {
    if ( empty( $user_id ) ) {
      return null;
    }
  
    $avatar = get_user_meta( $user_id, 'rpf_user_avatar', true );
  
    if ( empty( $avatar['full'] ) ) {
      return null;
    }
  
    // Generate New Size Image
    if ( empty( $avatar[ $size ] ) ) {
      $upload_path = wp_upload_dir();
      // Get path for image by converting URL
      // Don't use just $avatar['full']
      $avatar_full_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $avatar['full'] );
      $editor = wp_get_image_editor( $avatar_full_path );
      if ( ! is_wp_error( $editor ) ) {
        $resized   = $editor->resize( $size, $size, true );
        $dest_file = $editor->generate_filename();
        $saved     = $editor->save( $dest_file );
        if ( ! is_wp_error( $saved ) ) {
          $avatar[$size] = str_replace( $upload_path['basedir'], $upload_path['baseurl'], $dest_file );
          update_user_meta( $user_id, 'rpf_user_avatar', $avatar );
        } else {
          echo "error in making resized image file";
        }
      } else {
        echo " error in making editor ";
      }
    }
  
    return $avatar[$size];
  }

  /**
   * Filter 'get_avatar_url' and maybe return a local avatar
   *
   * @since 1.0.0
   *
   * @param string $url
   * @param mixed $id_or_email
   * @param array $args
   *
   * @return string
   */
  function filter_get_avatar_url( $url, $id_or_email, $args ) {

    // Bail if forcing default
    if ( ! empty( $args['force_default'] ) ) {
      return $url;
    }

    // Bail if explicitly an md5'd Gravatar url
    // https://github.com/stuttter/wp-user-avatars/issues/11
    if ( is_string( $id_or_email ) && strpos( $id_or_email, '@md5.gravatar.com' ) ) {
      return $url;
    }

    // Look for local avatar
    $avatar_url = $this->get_avatar_url( $id_or_email, $args['size'] );

    // Override URL if avatar is found
    if ( ! empty( $avatar_url ) ) {
      $url = $avatar_url;
    }

    // Return maybe-local URL
    return $url;
  }

  /**
   * Filter 'get_avatar' and maybe return a local avatar
   *
   * @since 1.0.0
   *
   * @param string $url
   * @param mixed $id_or_email
   * @param array $args
   *
   * @return string
   */
  function filter_get_avatar( $avatar, $id_or_email, $size ) {

    // Bail if explicitly an md5'd Gravatar url
    // https://github.com/stuttter/wp-user-avatars/issues/11
    if ( is_string( $id_or_email ) && strpos( $id_or_email, '@md5.gravatar.com' ) ) {
      return $avatar;
    }

    // Look for local avatar
    $avatar_url = $this->get_avatar_url( $id_or_email, $size );
    
    // Override URL if avatar is found
    if ( ! empty( $avatar_url ) ) {
      $avatar = preg_replace( "/src='(.*?)'/i", "src='" . $avatar_url . "'", $avatar );
      // srcset override src
      $avatar = preg_replace( "/srcset='(.*?)'/i", "srcset='" . $avatar_url . "'", $avatar );
    }

    return $avatar;

  }

}
$GLOBALS['rpf']['user_avatar'] = new RPF_User_Avatar();