<?php

namespace App\Models;

use App\Traits\HasTable;

class Session
{
  use HasTable;

  protected const TABLE = 'sessions';

  public $id;
  public $userId;
  public string $accesstoken;
  public $accessexpiry;

  public function __construct($id = null, $userId = null, $accesstoken = "", $accessexpiry = null)
  {
    $this->id = $id;
    $this->userId = $userId;
    $this->accesstoken = $accesstoken;
    $this->accessexpiry = $accessexpiry;
  }
}
