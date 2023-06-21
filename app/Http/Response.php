<?php

namespace App\Http;

class Response
{
  private $_httpStatusCode;
  private $_success;
  private $_messages = array();
  private $_data;
  private $_toCache = false;
  private $_responseData = array();

  public function set_success($_success)
  {
    $this->_success = $_success;
  }

  public function set_httpStatusCode($_httpStatusCode)
  {
    $this->_httpStatusCode = $_httpStatusCode;
  }

  public function set_message($_message)
  {
    $this->_messages[] = $_message;
  }

  public function set_data($_data)
  {
    $this->_data = $_data;
  }

  public function set_toCache($_toCache)
  {
    $this->_toCache = $_toCache;
  }


  public function send()
  {
    header('Content-type: application/json; charset=utf-8');

    if ($this->_toCache == true) {
      header('Cache-control: max-age=60'); // poslati zahtev serveru nakon 60 sekundi
    } else {
      header('Cache-control: no-cache, no-store'); // poslati zahtev serveru
    }

    if (($this->_success !== false && $this->_success !== true) || !is_numeric($this->_httpStatusCode)) {
      http_response_code(500);
      $this->_responseData['statusCode'] = 500;
      $this->_responseData['success'] = false;
      $this->_messages = array();
      $this->set_message("Response creation error");
      $this->_responseData['messages'] = $this->_messages;
    } else {
      http_response_code($this->_httpStatusCode);
      $this->_responseData['statusCode'] = $this->_httpStatusCode;
      $this->_responseData['success'] = $this->_success;
      $this->_responseData['messages'] = $this->_messages;
      $this->_responseData['data'] = $this->_data;
    }

    echo json_encode($this->_responseData);
  }
}
