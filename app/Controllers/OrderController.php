<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Resources\OrderResource;
use App\Utilities\Paginator;
use App\Utilities\Query;

class OrderController
{

  public static function index(int $page = 1)
  {
    // $orders = Query::select()->table('orders')->getModels();
    $orders = Order::all();
    // $orders = Query::select()->table('orders')->get();
    $orders = OrderResource::collection($orders);
    $orders = Paginator::paginate($orders, $page, 5);

    return $orders;
  }

  public static function show(int $id)
  {
    return new OrderResource(Order::find($id));
  }

  public static function store(array $request)
  {
    $user = $_SESSION['user'];
    $order = Order::create([
      'userId' => $user->id,
      'value' => 0
    ]);

    $totalPrice = 0;
    foreach ($request as $prod) {
      $product = Product::find($prod->id);
      $totalPrice += $product->price;
      OrderItem::create([
        'orderId' => $order->id,
        'value' => $product->price,
        'productId' => $product->id
      ]);
    }

    $order = $order->update([
      'value' => $totalPrice
    ]);

    return new OrderResource($order);
  }
}
