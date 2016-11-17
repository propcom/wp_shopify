<?

  /*
  * Render Abandoned Checkouts
  */

  $shopify = Wordpress_Shopify_Api::forge( ENDPOINT_CHECKOUTS );
  $checkouts = $shopify->get_ab_checkouts();

  $unique_id = 0;

  if( !empty($checkouts) && $_SERVER['REQUEST_METHOD'] == 'POST' ) {

    foreach($_POST as $key => $value) {

      $isValid = ( isset(explode('-', $key)[0]) && explode('-', $key)[0] == 'email' ? true : false );
      if($isValid) {

        $post_email = str_replace( '_', '.', explode('-', $key)[1] );
        $emailer = new Email_Helper( is_user_logged_in(), $post_email );

        $unique_id = explode('_', $value)[0];
        $checkout_url = explode('_', $value)[1];

        if( $emailer->get_status($unique_id) == false ) {

          $sent = $emailer->send_email( 'You have Abandoned your Cart', [

              'message' => 'Your shopping cart is waiting for you',
              'checkout_url' => $checkout_url

          ] );

        } else {

          print (

            '<div class="notice notice-error is-dismissible"><p>'.$post_email.' has already been notified.</p></div>'

          );

        }

        if($sent) {

          print (

            '<div class="notice notice-success is-dismissible"><p>'.$post_email.' has been notified.</p></div>'

          );

          $emailer->set_status(true, $unique_id);

        } elseif(!$sent && $emailer->get_status($unique_id) == false) {

          print (

            '<div class="notice notice-error is-dismissible"><p>Failed to send email to '.$post_email.'</p></div>'

          );

        }

      }

    }

  }

?>

<? if( !empty($checkouts) ): ?>

  <table class="wp-list-table  widefat  fixed  striped  posts">

    <thead>

      <tr>

        <td id="cb" class="manage-column  column-cb  check-column">
          <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
          <input id="cb-select-all-1" type="checkbox">
        </td>

        <th scope="col" id="checkout" class="manage-column  column-tags  column-primary">Checkout</th>

        <th scope="col" id="date" class="manage-column  column-tags  sortable  asc">
          <a href="<?= esc_url( site_url('/') ) ?>">
            <span>Date</span>
            <span class="sorting-indicator"></span>
          </a>
        </th>

        <th scope="col" id="placed-by" class="manage-column  column-tags  column-primary  sorted  desc">
          <a href="<?= esc_url( site_url('/') ) ?>">
            <span>Placed By</span>
            <span class="sorting-indicator"></span>
          </a>
        </th>

        <th scope="col" id="email-status" class="manage-column  column-tags">Email Status</th>

        <th scope="col" id="total" class="manage-column  column-author">Total</th>

      </tr>

    </thead>

    <tbody id="the-list">

      <? foreach($checkouts as $checkout): ?>

        <?
          $args = [ 'search_columns' => [ 'user_email' ] ];
          if( ($email = $checkout->customer->email) ) {

            $args['search'] = $email;

          }

          $wordpress_user = new WP_User_Query($args);
          if( !empty($wordpress_user->results) ) {

            $user_exists = ( isset($wordpress_user->results[0]->ID) ? true : false );
            $user_id = $wordpress_user->results[0]->ID;
            $user = get_user_meta( $wordpress_user->results[0]->ID, '_ws_user_meta_'.$checkout->id );

          }
        ?>

        <tr id="ab-checkout-<?= $checkout->id ?>">

          <th scope="row" class="check-column">
            <label class="screen-reader-text" for="cb-select-<?= $checkout->id ?>"></label>
            <input id="cb-select-<?= $checkout->id ?>" type="checkbox" name="email-<?= $checkout->email ?>" value="<?= $checkout->id ?>_<?= esc_url( $checkout->abandoned_checkout_url ) ?>">
          </th>

          <td class="checkout  column-tags  column-primary">
            <strong>
              <a class="row-title" href="<?= $checkout->abandoned_checkout_url ?>" target="_blank"><?= $checkout->name ?></a>
            </strong>
          </td>

          <td class="date  column-tags">
            <?= date_i18n( 'M j, h:m a T', strtotime($checkout->created_at) ) ?>
          </td>

          <td id="customer-<?= $checkout->customer->id ?>" class="placed-by  column-tags">
            <? if($user_exists): ?>
              <a href="<?= esc_url( site_url('/wp-admin/user-edit.php?user_id=') ).$user_id ?>"><?= $checkout->customer->first_name.' '.$checkout->customer->last_name ?></a>
            <? else: ?>
              <?= $checkout->customer->first_name.' '.$checkout->customer->last_name ?>
            <? endif; ?>
          </td>

          <td class="email-status  column-tags">
            <div class="status-label  <? if($user): ?>sent<? elseif(!$user && $user_exists): ?>pending<? endif; ?>">
              <? if($user): ?>
                <span>Sent</span>
              <? elseif(!$user && $user_exists): ?>
                <span>Not Sent</span>
              <? else: ?>
                <span>-</span>
              <? endif; ?>
            </div>
          </td>

          <td class="total  column-tags">
            <?= '£'.$checkout->total_price ?>
          </td>

        </tr>

      <? endforeach; ?>

    </tbody>

  </table>

<? endif; ?>
