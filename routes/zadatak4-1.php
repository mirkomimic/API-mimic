<?php

use App\Controllers\OrderController;
use App\Http\Response;

require_once '../vendor/autoload.php';

if (empty($_GET)) {
  // return all orders
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::index());
    $response->send();
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Request method not allowed");
    $response->send();
    exit();
  }
} elseif (isset($_GET['id'])) {
  // get order by id
  $id = $_GET['id'];

  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::show($id));
    $response->send();
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Request method not allowed");
    $response->send();
    exit();
  }
} elseif (isset($_GET['page'])) {
  // return orders with pagination
  $page = $_GET['page'];
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $response = new Response();
    $response->set_httpStatusCode(200);
    $response->set_success(true);
    $response->set_data(OrderController::index($page));
    $response->send();
  } else {
    $response = new Response();
    $response->set_httpStatusCode(405);
    $response->set_success(false);
    $response->set_message("Request method not allowed");
    $response->send();
    exit();
  }
}
