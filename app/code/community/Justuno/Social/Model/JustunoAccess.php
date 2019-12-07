<?php
define('JUSTUNO_KEY', '467cd758-5745-4385-906b-6c76271c343a');
/**
 * @file
 * Class to perform operations with Justuno server.
 *
 * @version 13-08-2013
 */

class JustunoAccess {
  protected $apiKey;
  protected $domain;
  protected $email;
  protected $apiEndpointUrl;
  protected $guid;
  protected $password;

  /**
   * Set initial data in constructor.
   */
  public function __construct($settings) {
    $this->apiKey         = $settings['apiKey'];
    $this->domain         = $settings['domain'];
    $this->email          = $settings['email'];
    $this->guid           = isset($settings['guid']) ? $settings['guid'] : NULL;
    $this->password       = isset($settings['password']) ? $settings['password'] : NULL;
    $this->apiEndpointUrl = 'https://www.justuno.com/api/endpoint.html';
  }

  /**
   * Retreives Justuno widget data.
   */
  public function getWidgetConfig() {
    if (!extension_loaded("curl")) {
      throw new JustunoAccessException('Plug-in requires php `curl` extension which seems to be not activated on this server. Please activate it.');
    }
    $params = array(
      'key' => $this->apiKey,
      'email' => $this->email,
      'domain' => $this->domain,
      'action' => 'install',
    );
    if (isset($this->password)) {
      $params['password'] = $this->password;
    }
    $query  = http_build_query($params);
    $tucurl = curl_init();
    curl_setopt($tucurl, CURLOPT_URL, "{$this->apiEndpointUrl}?$query");
    curl_setopt($tucurl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($tucurl, CURLOPT_RETURNTRANSFER, 1);
    $tudata = curl_exec($tucurl);
    try {
      if (curl_errno($tucurl)) {
        throw new Exception(curl_error($tucurl));
      }
      $dom = new DOMDocument();
      $dom->loadXML($tudata);
      $nodes = $dom->getElementsByTagName('result');
      if (!$nodes || ($nodes->length == 0)) {
        throw new Exception('Incorrect response from remote server');
      }

      if ($nodes->item(0)->nodeValue == 0) {
        $nodes = $dom->getElementsByTagName('error');
        throw new Exception($nodes->item(0)->nodeValue);
      }
      $justuno_conf = array();
      $nodes = $dom->getElementsByTagName('guid');
      if ($nodes && $nodes->length !== 0) {
        $this->guid = $justuno_conf['guid'] = $nodes->item(0)->nodeValue;
      }
      $nodes = $dom->getElementsByTagName('embed');
      if ($nodes && $nodes->length !== 0) {
        $justuno_conf['embed'] = $nodes->item(0)->nodeValue;
      }
      $nodes = $dom->getElementsByTagName('conversion');
      if ($nodes && $nodes->length !== 0) {
        $justuno_conf['conversion'] = $nodes->item(0)->nodeValue;
      }
      curl_close($tucurl);
      return $justuno_conf;
    }
    catch (Exception $e) {
      curl_close($tucurl);
      throw new JustunoAccessException('Request error: ' . $e->getMessage());
    }

  }

  /**
   * Get link to Jutsuno dashbord link using API
   */
  public function getDashboardLink() {
    $params = array(
			'key'=>$this->apiKey,
			'email'=>$this->email,
			'domain'=>$this->domain,
			'action'=>'login',
			'guid'=>$this->guid
		);

		if(isset($this->password)){
			$params['password'] = $this->password;
		}
		$query = http_build_query($params);
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, "{$this->apiEndpointUrl}?$query");
		curl_setopt($tuCurl,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		$tuData = curl_exec($tuCurl);
		try{
			if(curl_errno($tuCurl)){
				throw new Exception(curl_error($tuCurl));
			}
			$dom = new DOMDocument;
			$dom->loadXML($tuData);
			$nodes = $dom->getElementsByTagName('result');
			if(!$nodes || $nodes->length == 0)
				throw new Exception('Incorrect response from remote server');

			if($nodes->item(0)->nodeValue == 0){
				$nodes = $dom->getElementsByTagName('error');
				throw new Exception($nodes->item(0)->nodeValue);
			}
			$nodes = $dom->getElementsByTagName('secure_login_url');
			if($nodes && $nodes->length !== 0){
				$secureLoginUrl = $nodes->item(0)->nodeValue;
			}
			curl_close($tuCurl);
			return $secureLoginUrl;
		}
		catch(Exception $e){
			curl_close($tuCurl);
			$msg = $e->getMessage();
      if($msg == "") $msg = 'Please check the credentials.';
      throw new JustunoAccessException('Request error: '.$msg);
		}
  }
}


/**
 * Exception child to throw.
 */
class JustunoAccessException extends Exception {

}
