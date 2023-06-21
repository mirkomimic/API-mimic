<?php

namespace App\Models;

use App\Traits\HasTable;
use App\Traits\HasTokens;

// require_once "../Traits/HasTable.php";

class User
{
  use HasTable;
  use HasTokens;

  protected const TABLE = 'users';

  public $id;
  public $firstname;
  public $lastname;
  public $phone;
  public $email;
  public $dateCreate;
  public $dateEdit;

  public function __construct($id = null, $firstname = null, $lastname = null, $phone = null, $email = null)
  {
    $this->id = $id;
    $this->firstname = $firstname;
    $this->lastname = $lastname;
    $this->phone = $phone;
    $this->email = $email;
    // $this->dateCreate = $dateCreate;
    // $this->dateEdit = $dateEdit;
  }
}
