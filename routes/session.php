<?php

use App\Controllers\Auth\SessionController;
use App\Http\Response;

require_once '../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (isset($_GET['login'])) {

    sleep(1);

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

    session_start();
    $_SESSION['user'] = $user;
    // var_dump($_SESSION['userId']);

    $returnData = [];
    $returnData['accesstoken'] = SessionController::login($user);
    $response = new Response();
    $response->set_httpStatusCode(201); // za kreiranje 201
    $response->set_success(true);
    $response->set_message("User logged in, access token created");
    $response->set_data($returnData);
    $response->send();
    exit;
  } elseif (isset($_GET['logout'])) {

    $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

    if (SessionController::logout($accesstoken)) {
      $response = new Response();
      $response->set_httpStatusCode(201);
      $response->set_success(true);
      $response->set_message("User logged out.");
      $response->send();
      exit;
    }
  }
} else {
  $response = new Response();
  $response->set_httpStatusCode(405);
  $response->set_success(false);
  $response->set_message("Method not allowed");
  $response->send();
  exit();
}
