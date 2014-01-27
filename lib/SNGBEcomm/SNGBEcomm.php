<?php

abstract class SNGBEcomm
{
  public static $apiKey;
  public static $testApiBase = 'https://ecm.sngb.ru/ECommerce';
  public static $productionApiBase = 'https://ecm.sngb.ru/Gateway';
  public static $livemode = false;
  public static $apiVersion = null;
  public static $verifySslCerts = false;
  const VERSION = '0.1.1';

  public static function getApiKey()
  {
      return self::$apiKey;
  }

  public static function setApiKey($apiKey)
  {
      self::$apiKey = $apiKey;
  }

  public static function getApiVersion()
  {
      return self::$apiVersion;
  }

  public static function setLiveMode($livemode)
  {
      self::$livemode = $livemode;
  }

  public static function getLiveMode()
  {
      return self::$livemode;
  }

  public static function setApiVersion($apiVersion)
  {
      self::$apiVersion = $apiVersion;
  }

  public static function getVerifySslCerts() {
      return self::$verifySslCerts;
  }

  public static function setVerifySslCerts($verify) {
      self::$verifySslCerts = $verify;
  }
}
