<?php

namespace App\Controllers;

use App\Database\DB;

class OrderItemController
{

  public static function createOrderItem($conn, $productId, $orderId, $value)
  {
    $query = "INSERT INTO order_item VALUES (null, $orderId, $value, $productId)";
    return $conn->query($query);
  }
}
