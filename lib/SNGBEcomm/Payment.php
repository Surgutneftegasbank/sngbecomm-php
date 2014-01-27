<?php

class SNGBEcomm_Payment //extends Stripe_ApiResource
{
  //Types of transaction
  static $PURCHASE = 1;
  static $CREDIT = 2;
  static $AUTHORIZATION = 4;
  static $CAPTURE = 5;
  static $VOIDCREDIT = 6;
  static $VOIDCAPTURE = 7;
  static $VOIDAUTHORIZATION = 9;

  public function __construct($apiKey=null, $terminalid=null, $teminalAlias=null, $action=null)
  {
    if ($action!=null) {
      $this->_action = $action;
    }
    else {
      $this->_action = self::$PURCHASE;
    }
    if ($apiKey==null)
    {
      $apiKey==SNGBEcomm::getApiKey();
    }
    $this->checkLiveMode();
    
    $this->_apiKey = $apiKey;
    $this->_terminalid = $terminalid;
    $this->_terminalAlias = $teminalAlias;
  }

  public function create($trackid=null, $amount=null, $action=null)  {
    $url = $this->paymentUrl();
    $action = $this->checkAction($action);
    $hash = $this->signature($trackid, $amount, $action);

    $params = array(
      'merchant' => $this->_terminalid,
      'terminal' => $this->_terminalAlias,
      'action' => $action,
      'amt' => $amount,
      'trackid' => $trackid,
      'udf1' => 'test',
      'udf5' => $hash
    );
    
    // Параметры запроса 
    $postdata = "";
    foreach ( $params as $key => $value ) $postdata .= "&".rawurlencode($key)."=".rawurlencode($value);
    
    // POST
    // Do POST
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url );
    curl_setopt ($ch, CURLOPT_POST, 1 );
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata );
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec ($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    //if ($curl_errno > 0) {
        //echo "cURL Error ($curl_errno): $curl_error";
    //} else {
        //echo "Data received ";
    //}         
    //
    //trackId
    //$requestor = new Stripe_ApiRequestor($this->_apiKey);
    //$url = $this->instanceUrl() . '/capture';
    //list($response, $apiKey) = $requestor->request('post', $url, $params);
    //$this->refreshFrom($response, $apiKey);
    //return $this;
    return $result;
  }

  // Generate Endpoint URL for system status (test or production)
  public function paymentUrl() {
    $this->checkLiveMode();
    return $this->_url . '/PaymentInitServlet';      
  }

  private function checkLiveMode() {
    if (SNGBEcomm::getLiveMode()) {
      $this->_url = SNGBEcomm::$productionApiBase;
    }
    else {
      $this->_url = SNGBEcomm::$testApiBase;
    }
  }

  private function checkAction($action=null) {
    //TODO: Check intrance action value in acceptable set of actions;
    if($action != null) {
      $this->_action = $action;
    }
    if($action == null || $action == '') {
      $this->_action = self::$PURCHASE;
      $action = $this->_action;
    }
    return $action;
  }

  // Hash signature
  private function signature($trackid=null, $amount=null, $action=null) {
    if ($action==null) {
      $action = self::$PURCHASE; 
    }
    $hash_psk = sha1($this->_apiKey);
    $salt = $this->_terminalid . $amount . $trackid . $action . $hash_psk;
    return sha1($salt);
  }
}
