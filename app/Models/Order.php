<?php

namespace App\Models;

use App\Traits\HasTable;
use App\Utilities\Query;

class Order
{
  use HasTable;

  protected const TABLE = 'orders';

  public $id;
  public $userId;
  public $value;
  public $dateCreate;
  public $dateEdit;

  public function __construct($id = null, $userId = null, $value = null, $dateCreate = null, $dateEdit = null)
  {
    $this->id = $id;
    $this->userId = $userId;
    $this->value = $value;
    $this->dateCreate = $dateCreate;
    $this->dateEdit = $dateEdit;
  }

  public function products()
  {
    $products = Query::select("products.*")
      ->table("products")
      ->join("orderItem", "products.id", "=", "orderItem.productId")
      ->where("orderItem.orderId", "=", $this->id)
      ->getModels();

    return $products;
  }
}
