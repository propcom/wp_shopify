<?
  $logo = $GLOBALS['image'];
  $content = $GLOBALS['info'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Wordpress Shopify Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>.btn{ padding:13px 23px;display: inline-block;text-decoration:none;text-transform:uppercase;border:2px solid #000;color:#000;font-size:15px; }</style>
  </head>
  <body>
    <table align="center" border="0" cellpadding="15" cellspacing="0" width="600" style="border-collapse: collapse;">
      <? if($content): ?>
        <tr>
          <td align="center">
            <img width="500" height="auto" src="<?= $logo ?>" alt="Site Logo" />
          </td>
        </tr>
        <tr>
          <td align="center">
            <h2><b><?= $content->message ?></b></h2>
          </td>
        </tr>
        <tr>
          <td align="center">
            <a class="btn" href="<?= esc_url( $content->checkout_url ) ?>">Continue Checkout</a>
          </td>
        </tr>
      <? endif; ?>
    </table>
  </body>
</html>
