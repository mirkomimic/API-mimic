<?php

namespace App\Controllers;

use App\Database\DB;
use App\Models\Product;
use App\Utilities\Query;
use App\Utilities\Paginator;
use App\Resources\ProductResource;

class ProductController
{

  public static function index(int $page = 1)
  {
    $products = Product::all();
    $products = ProductResource::collection($products);
    $products = Paginator::paginate($products, $page, 5);

    return $products;
  }

  public static function store($name, $price)
  {
    $conn = DB::connectDB();
    $query = "INSERT INTO products VALUES (null, '$name', $price)";
    return $conn->query($query);
  }

  public static function getProducts()
  {
    $conn = DB::connectDB();
    $productIds = [];
    $query = "SELECT * FROM products";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
      $productIds[] = $row['id'];
    }
    return $productIds;
  }

  public static function getProductValue($productId)
  {
    $conn = DB::connectDB();
    $productValue = 0;
    $query = "SELECT * FROM products WHERE id=$productId";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
      $productValue += $row['price'];
    }
    return $productValue;
  }

  public static function getProduct($productId)
  {
    $conn = DB::connectDB();
    $query = "SELECT * FROM products WHERE id=$productId";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $product = new Product($row['id'], $row['name'], $row['price']);

    return $product;
  }

  public static function checkIfProductsExists(array $array)
  {
    $productIds = array_column($array, 'id');
    $query = Query::select('id')->table('products')->getArray();
    $query = array_column($query, 'id');

    if (!empty(array_diff($productIds, $query))) {
      return false;
    }

    return true;
  }
}
