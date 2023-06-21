<?php

use App\Controllers\Auth\SessionController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Http\Response;
use App\Resources\OrderResource;
use App\Database\DB;

require_once '../vendor/autoload.php';

session_start();

if (!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1) {
  $response = new Response();
  $response->set_httpStatusCode(401);
  $response->set_success(false);
  $response->set_message("Authorization token cannot be blank or must be set");
  $response->send();
  exit;
}

$accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

if (!SessionController::check($accesstoken)) {
  $response = new Response();
  $response->set_httpStatusCode(401);
  $response->set_success(false);
  $response->set_message("Access token not valid or it has expired");
  $response->send();
  exit;
};


// $rowCount = mysqli_num_rows($result);
// if ($rowCount == 0) {
//   $response = new Response();
//   $response->set_httpStatusCode(401);
//   $response->set_success(false);
//   $response->set_message("Access token not valid");
//   $response->send();
//   exit;
// }
// $row = $result->fetch_assoc();
// $userid = $row['userId'];

// $accessexpiry = $row['accessexpiry'];

// if (strtotime($accessexpiry) < time()) {
//   $response = new Response();
//   $response->set_httpStatusCode(401);
//   $response->set_success(false);
//   $response->set_message("Access token expired");
//   $response->send();
//   exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // create product
  if (isset($_GET['product'])) {

    if ($_SERVER['CONTENT_TYPE'] !== "application/json") {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Content type header not set to JSON");
      $response->send();
      exit();
    }


    $rawPostData = file_get_contents('php://input');
    if (!$jsonData = json_decode($rawPostData)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Request body is not valid JSON");
      $response->send();
      exit();
    }
    if (!isset($jsonData->name) || !isset($jsonData->price)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Name and price fields are mandatory and must be provided");
      $response->send();
      exit();
    }

    ProductController::store($conn, $jsonData->name, $jsonData->price);
    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_message("Product Added");
    $response->send();
  }

  // create order
  //   [
  //     {
  //         "id": "1"
  //     },
  //     {
  //         "id": "1"
  //     }
  // ]
  if (isset($_GET['order'])) {

    if ($_SERVER['CONTENT_TYPE'] !== "application/json") {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Content type header not set to JSON");
      $response->send();
      exit();
    }

    $rawPostData = file_get_contents('php://input');
    if (!$jsonData = json_decode($rawPostData)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Request body is not valid JSON");
      $response->send();
      exit();
    }


    foreach ($jsonData as $data) {
      if (!isset($data->id)) {
        $response = new Response();
        $response->set_httpStatusCode(400);
        $response->set_success(false);
        $response->set_message("ID field is mandatory and must be provided");
        $response->send();
        exit();
      }
    }

    if (!ProductController::checkIfProductsExists($jsonData)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Product id does not exist.");
      $response->send();
      exit;
    }

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_message("Order created!");
    $response->set_data(OrderController::store($jsonData));
    $response->send();
    exit();
  }
}
