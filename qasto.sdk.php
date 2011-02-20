<?php
/**
 * Qasto Class
 *
 * Work with remote servers via cURL much easier than using the native PHP bindings.
 *
 * @package         Qasto
 * @subpackage      SDK-Libraries
 * @category      Libraries
 * @author          Alex
 */
if (!function_exists('curl_init')) {
  throw new Exception('Qasto needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Qasto needs the JSON PHP extension.');
}

class Qasto {
  

   
   var $base_url = 'https://www.qasto.com/';
   
   var $api_url  = 'https://www.qasto.com/developer/api/';
   
   var $auth_url = 'https://www.qasto.com/developer/oauth/authorize?';
  
   var $logout_url = 'https://www.qasto.com/developer/oauth/sign_out?';
   
   var $token_url = 'https://www.qasto.com/developer/oauth/access_token?';
   
/**
   * The Application Client ID
   */
  protected $client_id;



  /**
   * The Application API Secret.
   */
  protected $client_secret;


  /**
   * The Application Signed Request
   */
  protected $signedRequest;

/* Internal functions start */

  public function __construct($config) {
    $this->setAppId($config['client_id']);
    $this->setApiSecret($config['client_secret']);


  }







/**
   * Set the Application ID.
   *
   * @param String $appId the Application ID
   */
  public function setAppId($appId) {
    $this->client_id = $appId;
    return $this;
  }

  /**
   * Get the Application ID.
   *
   * @return String the Application ID
   */
  public function getAppId() {
    return $this->client_id;
  }

  /**
   * Set the API Secret.
   *
   * @param String $appId the API Secret
   */
  public function setApiSecret($apiSecret) {
    $this->client_secret = $apiSecret;
    return $this;
  }

  /**
   * Get the API Secret.
   *
   * @return String the API Secret
   */
  public function getApiSecret() {
    return $this->client_secret;
  }

  /**









/* Gets current page url */
function currentPageURL() {
 $pageURL = 'http';
if (!empty($_SERVER['HTTPS'])) {$pageURL .= "s";}  
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

/*Redirects url to page */

function redirect($url){
  
header("Location: ".$url."");

  
}



/* Internal functions end */



/* Get Login URL */

function getLogoutURL($redirect_uri = FALSE)
{
 
 if(!empty($redirect_uri)):
   
 $redirect_uri = $redirect_uri;
 
 
 else:
   
 $redirect_uri =  $this->currentPageURL();
 

 

    
 endif;
 
 $options = array(
'client_id' => $this->client_id,
'redirect_uri' => $redirect_uri
);

$params = http_build_query($options);

$url = $this->logout_url.$params;
 

return urldecode($url);
  
}



function getLoginURL($redirect_uri = FALSE)
{
 
 if(!empty($redirect_uri)):
   
 $redirect_uri = $redirect_uri;
 
 else:
   
 $redirect_uri =  $this->currentPageURL();
    
 endif;
 
 $options = array(
'client_id' => $this->client_id,
'redirect_uri' => $redirect_uri
);

$params = http_build_query($options);

$url = $this->auth_url.$params;
 

return urldecode($url);
  
}














function make_request($url)
{
  $handle = curl_init();
$options = array( 
                  CURLOPT_RETURNTRANSFER => true,
                 // CURLOPT_HEADER         => true,
                  CURLOPT_FOLLOWLOCATION => false,
                  CURLOPT_SSL_VERIFYHOST => true,
                  CURLOPT_SSL_VERIFYPEER => true,
                  CURLOPT_CAINFO         => dirname(__FILE__)."/ca.pem",
                  CURLOPT_USERAGENT      => 'Qasto PHP-SDK v1',
                  CURLOPT_VERBOSE        => true,
                  CURLOPT_URL            => $url
           );

curl_setopt_array($handle, $options);
$result = curl_exec($handle);

if (curl_errno($handle)) {
  echo 'Error: ' . curl_error($handle);
}

curl_close($handle);

return $result;
  
}




function getToken($redirect_uri = FALSE,$code)
{

if($this->useCookieSupport()):
$code = $this->getCookie();
endif;  

if(isset($redirect_uri)):
$redirect_uri = $redirect_uri;
else:
$redirect_uri = $this->currentPageURL();  
endif;    
  

$options = array(
'client_id' => $this->client_id,
'client_secret' => $this->client_secret,
'code' => $code,
'redirect_uri' => $redirect_uri
);

$params = http_build_query($options,null,'&');

$url  =  $this->token_url.$params;

return json_decode($this->make_request($url));

}

function parseSignedRequest($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

  // decode the data
  $sig = $this->base64_url_decode($encoded_sig);
  $data = json_decode($this->base64_url_decode($payload), true);



  if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
     die('Unknown algorithm. Expected HMAC-SHA256');
    return null;
  }


  // check sig
  $expected_sig = hash_hmac('sha256',json_encode($data),$secret, $raw = true);
  if ($sig != $expected_sig) {
    die('Bad Signed JSON signature!');
    return null;
  }

  return $data;
}


function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_,', '+/='));
    }


 /**
* Get the data from a signed_request token
*
* @return String the base domain
*/
  public function getSignedRequest() {

      if (isset($_REQUEST['signed_request'])) {
        $this->signedRequest = $this->parseSignedRequest($_REQUEST['signed_request'],$this->client_secret);
      }

    return $this->signedRequest;
  }
  
  
 public function getStatus() {
  
  if(isset($_REQUEST['status']))
  {
    session_unset('access_token');
    
    return false;
  }
  else
    {
         if(isset($_SESSION['logged_in']))
         {
         return true;
         }
        else
       {
        
       $signedRequest = $this->getSignedRequest();
         if ($signedRequest['logged_in'] == TRUE)
          {
         // sig is good, use the signedRequest
         

        
        session_start(); 
        
          foreach($signedRequest as $k => $v)
          {
         
          $_SESSION[$k] = $v;
   
          }

          return true;
         }
         else
          {
            
            return false;
          }    
           
       }

    }

    }
 
 

  
  
function api($i)
{
  
switch ($i) {
    case 'info':
  
$options = array(
'access_token' => $_SESSION['access_token'],
);

$params = http_build_query($options);

$url  =  $this->api_url.'me/info?'.$params;


$json = $this->make_request($url);
 
 return json_decode($json);

        break;
    case 'subscriptions':
      
$options = array(
'access_token' => $_SESSION['access_token'],
);

$params = http_build_query($options);

$url  =  $this->api_url.'me/subscriptions?'.$params;


$json = $this->make_request($url);
 
 return json_decode($json);
 
        break;
default:
        echo "Sorry your API call was not valid";
        break;
}
  
}



}