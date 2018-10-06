<?php
class RPF_User_Nick_Name {
  function __construct() {
    add_action( 'woocommerce_save_account_details', array( $this, 'update_user_nickname' ) );
  }
  /**
   * get user's nick name
   */
  function get_user_nickname( $user_id ) {
    $user_meta = get_user_meta( $user_id, 'nickname' );
    if ( ! isset( $user_meta['nickname'] ) ) {
      return null;
    }
    return $user_meta['nickname'];
  }

  /**
   * save & update user's nick name
   */
  function update_user_nickname( $user_id ) {
    if ( isset( $_POST['account_nickname'] ) ) {
      update_user_meta( $user_id, 'nickname', $_POST['account_nickname'] );
    }
  }
}
$GLOBALS['rpf']['user_nick_name'] = new RPF_User_Nick_Name();
?>