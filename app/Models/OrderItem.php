<?php

namespace App\Models;

use App\Traits\HasTable;

class OrderItem
{
  use HasTable;

  protected const TABLE = 'orderItem';

  public $id;
  public $orderId;
  public $value;
  public $productId;

  public function __construct($id = null, $orderId = null, $productId = null, $value = null)
  {
    $this->id = $id;
    $this->orderId = $orderId;
    $this->productId = $productId;
    $this->value = $value;
  }
}
