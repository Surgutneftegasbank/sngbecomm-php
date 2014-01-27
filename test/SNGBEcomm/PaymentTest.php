<?php

class PaymentTest extends UnitTestCase {

  public function testPaymentCreate()
  {
    authorizeFromEnv();
    $payment = new SNGBEcomm_Payment('qwe123!@#', '7000', '7000-alias');
    $url = $payment->create(2, 1000);
    $this->assertTrue(strpos($url, "https://ecm.sngb.ru:443/ECommerce/hppaction?formAction") == 0);
  }

  public function testUrl() {
    authorizeFromEnv();
    $payment = new SNGBEcomm_Payment();
    $url = $payment->paymentUrl();
    $this->assertEqual("https://ecm.sngb.ru/ECommerce/PaymentInitServlet", $url);
    SNGBEcomm::setLiveMode(true);
    $url = $payment->paymentUrl();
    $this->assertEqual("https://ecm.sngb.ru/Gateway/PaymentInitServlet", $url);
    SNGBEcomm::setLiveMode(false);
  }
}
