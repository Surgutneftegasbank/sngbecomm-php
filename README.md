PHP библиотека для интернет эквайринга Сургутнефтегазбанка
======================================================

Как реализовать техническую интеграцию с нашим сервисом интернет-эквайринг:
* Вы можете воспользоваться нашим HTTP API.
* Если ваша серверная часть написана на PHP, то вы можете воспользоваться нашим модулем sngbecomm-php.
 
sampleshop-php – это пример магазина, с модулем.
sngbecomm-php – это модуль, путь до модуля
 
Надо подключить модуль
    '''php
    require_once("/path/to/sngbecomm-php/lib/SNGBEcomm.php");
    '''


###Быстрый старт:

В личном кабинете создайте psk. (make psk)
Также в личном кабинете можно посмотреть свой terminal id и alias
    '''php
    SNGBEcomm::setApiKey('sdfkjhb23y82ybvybvkwubyv28'); // PSK
    SNGBEcomm::setMerchant('7000'); // terminal id 
    SNGBEcomm::setTerminalAlias('7000-alias'); // terminal-alias
    '''
 
Получить url, платежной страницы банка, на которую надо перенаправить пользователя.
    '''php
    $payment = new SNGBEcomm_Payment();
    //trackid – это ваш id операции платежа,
    //Amount это цена. Цену надо передавать в минимальных единицах валюты.(копейках). Фиксированная запятая. 2 знака после запятой. Это стандарт хранения денег в БД.
    $additional_fields = array(
    // Номер заказа
    "udf1" => $id,
    // Наш номер тех. поддержки
    "udf2" => "8 800 xxx xxx 88"
    );
    $url = $payment->create($trackid, $amount, $additional_fields);
    '''
 
Чтобы перейти на боевой сервер:
    '''php
    SNGBEcomm::setLiveMode(true);
    '''
 
Обработка ошибок в процессе оплаты:
 
    ($error – это сообщение об ошибке, которое приходит на notification url)
    '''php
    $errorhandler = new SNGBEcomm_Error($error, $result, $responsecode, $hashresponse);
    $errormessage = $errorhandler->isError($trackid, $amount, $action);
    '''
 
$errormessage если пустой, то все замечательно!
Если нет, то он хранит текст сообщение об ошибке.

Создайте notification url в личном кабинете (callback от нашего сервиса после попытки оплаты клиента на платежной странице банка на ваш сервер)
Пример скрипта notification.php
'''php
  $request = $app->request;

  $trackid = $request->params("trackid");

  // Получаем из бд нужную операцию платежа,
  // если конечно у нас есть trackid
  $payment_object = R::load('payment', $trackid);
  $action = $payment_object->action;
  $amount = $payment_object->amount;

  $error = $request->params('Error');
  $result = $request->params('result');
  $responsecode = $request->params('responsecode');
  $hashresponse = $request->params('udf5');

  $errorhandler = new SNGBEcomm_Error($error, $result, $responsecode, $hashresponse);
  $errormessage = $errorhandler->isError($trackid, $amount, $action);

  $log = $app->getLog();
  $log->info("REQUEST BODY: " . $request->getBody());
  if ($errormessage) {
    $reply = 'REDIRECT=' . $rootURL . '/payment/error?trackid=' . $trackid . '&errormessage=' .urlencode($errormessage);
  }
  else {
    $reply = 'REDIRECT=' . $rootURL . '/payment/success/' . $trackid;
  }
'''

Это базовое использование.
Все это находится еще в процессе доводки, и вы можете задавать любые вопросы.
