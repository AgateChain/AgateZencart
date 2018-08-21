<?php
namespace Agate\Merchant;


use Agate\Merchant;

class Order extends Merchant
{
    //Convert currency to iUSD
    public static function convertCurToIUSD($url, $amount, $api_key, $currencySymbol) {
       error_log("Entered into Convert CAmount");
       error_log($url.'?api_key='.$api_key.'&currency='.$currencySymbol.'&amount='. $amount);
       $ch = curl_init($url.'?api_key='.$api_key.'&currency='.$currencySymbol.'&amount='. $amount);
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json')
     );

     $result = curl_exec($ch);
     $data = json_decode( $result , true);
     error_log('Response =>'. var_export($data, TRUE));
     // Return the equivalent value acquired from Agate server.
     return (float) $data["result"];

     }

    public static function redirectPayment($baseUri, $amount_iUSD, $amount, $currencySymbol, $api_key, $redirect_url) {
      error_log("Entered into auto submit-form");
      error_log("Url ".$baseUri . "?api_key=" . $api_key);
      // Using Auto-submit form to redirect user
      return "<form id='form' method='post' action='". $baseUri . "?api_key=" . $api_key."'>".
              "<input type='hidden' autocomplete='off' name='amount' value='".$amount."'/>".
              "<input type='hidden' autocomplete='off' name='amount_iUSD' value='".$amount_iUSD."'/>".
              "<input type='hidden' autocomplete='off' name='callBackUrl' value='".$redirect_url."'/>".
              "<input type='hidden' autocomplete='off' name='api_key' value='".$api_key."'/>".
              "<input type='hidden' autocomplete='off' name='cur' value='".$currencySymbol."'/>".
             "</form>".
             "<script type='text/javascript'>".
                  "document.getElementById('form').submit();".
             "</script>";
    }

}
