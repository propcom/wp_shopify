<h1>Wordpress Shopify Plugin</h1>

<img src="https://github.com/propcom/wp_shopify/blob/master/admin/images/plugin-logo.png" alt="Plugin Logo"/>

<p>
 Basic plugin for connecting your Shopify store to a Wordpress site.
 Features Available:
 <ul>
  <li>Pull through all products.</li>
  <li>Pull through single product.</li>
  <li>Pull through collections.</li>
  <li>Pull through single collection.</li>
  <li>Pull through products from a collection.</li>
 </ul>
</p>

<p>
  There is also support for shortcodes, the following can be used:<br><br>
  [product id="(product_id)"] - This renders a single product on the front end.<br>
  [products id="(collection_id)"] - This renders all products belonging to the collection specified.
</p>

<h2>Pages</h2>

<h3>Shopify - Shopify Settings</h3>
<p>
  Here you setup your shop settings, to allow the plugin to connect to your shopify store, fields are:
  <ul>
   <li>Store Name - Your shopify store name</li>
   <li>Store API Key - Your shopify store api key</li>
   <li>Store Password - Your shopify store password key</li>
  </ul>
</p>

<b>Please Note:</b> Your shops API Key and Pass Key are generated through a private app on your store, see <a href="https://help.shopify.com/api/guides/api-credentials#get-credentials-through-the-shopify-admin" target="_blank">this doc</a> for info on how to do this

<h3>Checkouts - Abandoned Checkouts</h3>
<p>
  This allows the user to see there abandoned checkouts on there store, if there are abandoned checkouts this feature gives user the ability to send a reminder email notifiying the customer they have abandoned there cart, with a link to continue there purchase.
  <ul>
   <li>Checkout - Shopify generated checkout id</li>
   <li>Date - The date and time they started there Checkout</li>
   <li>Placed By - Customer who started there purchase</li>
   <li>Email Status - If user has already sent them the email or not</li>
   <li>Total - The total pruchase price in there cart</li>
  </ul>
</p>

<b>Please Note:</b> Email Status will have a state if the customer has an account on the users Wordpress site

<h3>Shortcodes - Collection/Product Shortcodes</h3>
<p>
  This allows user to generate shortcodes for a Collection or Product, then copy the shortcode and paste it into a text editor on pages etc.
  <ul>
   <li>Collection - List of Collections from there Shopify</li>
   <li>Products - List of products from the selected Collection</li>
   <li>Shortcode - Generated shortcode from either the Collection or Product</li>
   <li>Copy - Copy shortcode just generated</li>
  </ul>
</p>
