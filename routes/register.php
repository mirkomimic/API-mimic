<?php

use App\Controllers\UserController;
use App\Database\DB;
use App\Http\Response;

$conn = DB::connectDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // register user
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

  UserController::store($conn, $jsonData->firstname, $jsonData->lastname, $jsonData->phone, $jsonData->email);
  $response = new Response();
  $response->set_httpStatusCode(200);
  $response->set_success(true);
  $response->set_message("User registered!");
  $response->send();
}
