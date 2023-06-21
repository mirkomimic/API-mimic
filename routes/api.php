<?php

use App\Http\Response;
use App\Controllers\UserController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\Auth\SessionController;

require_once '../vendor/autoload.php';

session_start();

// login
if (isset($_GET['login'])) {
  if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Content Type header not set to JSON");
      $response->send();
      exit;
    }

    $rawPostData = file_get_contents('php://input');

    if (!$jsonData = json_decode($rawPostData)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Request body is not valid JSON");
      $response->send();
      exit;
    }

    if (!isset($jsonData->email)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      $response->set_message("Missing email");
      $response->send();
      exit;
    }

    $user = (new SessionController())->getUserByEmail($jsonData->email);

    if ($user == null) {
      $response = new Response();
      $response->set_httpStatusCode(409);
      $response->set_success(false);
      $response->set_message("Email is not correct");
      $response->send();
      exit;
    }

    // session_start();
    $_SESSION['user'] = $user;

    $returnData = [];
    $returnData['accesstoken'] = SessionController::login($user);
    $response = new Response();
    $response->set_httpStatusCode(201); // za kreiranje 201
    $response->set_success(true);
    $response->set_message("User logged in, access token created");
    $response->set_data($returnData);
    $response->send();
    exit;
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Method not allowed");
    $response->send();
    exit();
  }
} // logout
elseif (isset($_GET['logout'])) {

  if ($_SERVER['REQUEST_METHOD'] == "POST") {

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

    if (SessionController::logout($accesstoken)) {
      $response = new Response();
      $response->set_httpStatusCode(201);
      $response->set_success(true);
      $response->set_message("User logged out.");
      $response->send();
      exit;
    }
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Method not allowed");
    $response->send();
    exit();
  }
} // register
elseif (isset($_GET['register'])) {

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    if (!isset($jsonData->firstname) || !isset($jsonData->lastname) || !isset($jsonData->phone) || !isset($jsonData->email)) {
      $response = new Response();
      $response->set_httpStatusCode(400);
      $response->set_success(false);
      if (!isset($jsonData->firstname))
        $response->set_message("Firstname field is mandatory and must be provided");
      if (!isset($jsonData->lastname))
        $response->set_message("Lastname field is mandatory and must be provided");
      if (!isset($jsonData->phone))
        $response->set_message("Phone field is mandatory and must be provided");
      if (!isset($jsonData->email))
        $response->set_message("Email field is mandatory and must be provided");
      $response->send();
      exit();
    }

    UserController::store($jsonData);

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_message("User registered!");
    $response->send();
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Method not allowed");
    $response->send();
    exit();
  }
} // orders by page
elseif (isset($_GET['orders']) && isset($_GET['page'])) {

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $page = $_GET['page'];

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::index($page));
    $response->send();
  }
} // order by id
elseif (isset($_GET['orders']) && isset($_GET['id'])) {

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id = $_GET['id'];

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::show($id));
    $response->send();
  }
} // orders
elseif (isset($_GET['orders'])) {
  // get orders
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::index());
    $response->send();
  } // post order
  elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
} // products
elseif (isset($_GET['products'])) {
  // post products
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
  } // get products
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(ProductController::index());
    $response->send();
  }
}
