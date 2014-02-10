<?php

class SNGBEcomm_Error
{
  //Types of transaction
  //static $PURCHASE = 1;
  public static $CGWERRORS = array(
    'CGW000029' => 'Card Number Invalid',
    'CGW000030' => 'Card Number Missing'
    //TODO: Дополнить список ошибок
  );
  
  public function __construct($error=null, $result=null, $responsecode=null, $hashresponse=null) {
    //TODO: Create echo notifications for input values
    $this->error = $error; 
    $this->result = $result; 
    $this->responsecode = $responsecode; 
    $this->hashresponse = $hashresponse; 
  }

  public function isError($trackid=null, $amount=null, $action=null) {
    //TODO: Сделать возврат ошибок на EN и RU. И дать выбор языка через аргумент.
    if ($this->error) {
      if (array_key_exists($this->error, self::$CGWERRORS))
        return self::$CGWERRORS[$this->error];
      return "Оплата не удалась! Обратитесь в службу поддержки сайта.";
    }

    //TODO: Сделать конкретное сообщение по каждому аргументу
    if ($trackid == null or $amount == null or $action == null) {
      return "Вы забыли передать значения для функции проверки ошибок!
        Посмотрите в документацию.";    
    }

    $price = SNGBEcomm_Payment::convertMoneyToString($amount);
    $hash = SNGBEcomm_Payment::signature($trackid, $price, $action);

    if ($hash != $this->hashresponse) {
      return "Операция оплаты не удалась. Причина: неправильный сервер обработки платежа.";
    }

    if ($this->result=="CAPTURED" and $this->responsecode=="00") {
      return "";
    }

    if ($this->result=="CANCELED") {
      return "Операция отмены оплаты";
    }

    if ($this->result=="NOT APPROVED") {
      switch ($this->responsecode) {
        case "04":
          $outcome = "Ошибка. Недействительный номер карты.";
          break;
        case "14":
          $outcome = "Ошибка. Неверный номер карты.";
          break;
        case "33":
        case "54":
          $outcome = "Ошибка. Истек срок действия карты.";
          break;
        case "Q1":
          $outcome = "Ошибка. Неверный срок действия карты или карта просрочена.";
          break;
        case "51":
          $outcome = "Ошибка. Недостаточно средств.";
          break;
        case "56":
          $outcome = "Ошибка. Неверный номер карты.";
          break;
        default:
          $outcome = "Ошибка. Обратитесь в банк, выпустивший карту.";
      }
      return $outcome;
    }
  }
}
