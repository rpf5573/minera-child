<?php

class RPF_Helper {
  public static function is_blog() {
    // show post-ratings on blog only
    if ( !is_home() && !is_page() && is_single() && get_post_type() == 'post') {
      return true;
    } else {
      return false;
    }
  }
  public static function log($message) {
    echo "<pre>";
    var_dump($message);
    echo "</pre>";
  }
  public static function log_die($message) {
    RPF_Helper::log( $message );
    die();
  }
}

?>