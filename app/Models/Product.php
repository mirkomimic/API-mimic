<?php

namespace App\Models;

use App\Traits\HasTable;

class Product
{
  use HasTable;

  protected const TABLE = 'products';

  public $id;
  public $name;
  public $price;

  public function __construct($id = null, $name = null, $price = null)
  {
    $this->id = $id;
    $this->name = $name;
    $this->price = $price;
  }
}
