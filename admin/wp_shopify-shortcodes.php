<?

  /*
  * Render Shopify shortcodes
  */

  $shopify = Wordpress_Shopify_Api::forge( ENDPOINT_COLLECTIONS );
  $collections = $shopify->get_collections();

?>

<? if($collections): ?>

  <script type="text/javascript">
    window.endpoint = '<?= get_home_url().'/wp-json/shopify/v1/products/' ?>';
  </script>

  <table class="wp-list-table  widefat  fixed  striped  posts  shortcodes">

    <thead>

      <tr>

        <th scope="col" id="checkout" class="manage-column  column-tags  column-primary">Collection</th>

        <th scope="col" id="date" class="manage-column  column-tags">Products</th>

        <th scope="col" id="placed-by" class="manage-column  column-tags">Shortcode</th>

        <th scope="col" id="email-status" class="manage-column  column-author">Copy</th>

      </tr>

    </thead>

    <tbody id="the-list">

      <tr id="collection-list">

        <td class="collection  column-tags  column-primary">
          <select class="js-collection-select" name="collection-select">
            <option value="">Select Collection</option>
            <? foreach($collections as $collection): ?>
              <option value="<?= $collection->id ?>"><?= $collection->title ?></option>
            <? endforeach; ?>
          </select>
        </td>

        <td class="products  column-tags">
          <select class="js-product-select" name="product-select">
            <option value="">---</option>
          </select>
        </td>

        <td class="shortcode  column-tags">
          <input class="js-shortcode-text" type="text" name="shortcode-none" value="">
        </td>

        <td class="total  column-author">
          <input class="button  button-primary  js-copy-shortcode" type="button" name="copy-shortcode" value="Copy Shortcode">
        </td>

      </tr>

    </tbody>

  </table>

<? endif; ?>
