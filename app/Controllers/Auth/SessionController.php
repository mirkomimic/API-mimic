<?php

namespace App\Controllers\Auth;

use App\Database\DB;
use App\Models\Session;
use App\Models\User;
use App\Utilities\Query;

class SessionController
{

  // public static function checkToken($token)
  // {
  //   $conn = DB::connectDB();
  //   $query = "SELECT sessions.userId, sessions.accessexpiry
  //         FROM users, sessions
  //         WHERE sessions.userId = users.id
  //         AND sessions.accesstoken ='$token'";
  //   return $conn->query($query);
  // }

  public static function check(string $token): bool
  {
    $user = $_SESSION['user'] ?? null;
    if (isset($user) && $user->token() !== null && $user->token() == $token && ($user->tokenExpiryTime() > time()))
      return true;
    else {
      return false;
    }
  }

  // public static function getUserByEmail($conn, $email)
  // {
  //   $query = "SELECT * FROM users WHERE email='$email'";
  //   return $conn->query($query);
  // }

  public function getUserByEmail(string $email)
  {
    $user = Query::select()->table('users')->where('email', '=', $email)->getModel();

    if ($user == null)
      return false;

    return $user;
  }

  // public static function login2($conn, $userId, $token, $accessexpiry)
  // {
  //   $query = "INSERT INTO sessions (userId, accesstoken, accessexpiry) VALUES ($userId, '$token', DATE_ADD(NOW(), INTERVAL $accessexpiry SECOND))";
  //   return $conn->query($query);
  // }

  public static function login(User $user)
  {
    // $accessexpiry = 28000;
    (new self)->deleteTokens($user);

    $accesstoken = (new self)->createToken();

    Session::create([
      'userId' => $user->id,
      'accesstoken' => $accesstoken,
      'accessexpiry' => 'DATE_ADD(NOW(), INTERVAL 28000 SECOND)'
    ]);

    return $accesstoken;
  }

  public static function logout(string $token)
  {
    $conn = Db::connectDB();
    $query = Query::delete()->table('sessions')->where('accesstoken', '=', $token)->getQuery();

    if (isset($_SESSION['user'])) {
      unset($_SESSION['user']);
    }

    return $conn->query($query);
  }

  private function createToken(): string
  {
    $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)));
    return $accesstoken;
  }

  // brisanje tokena
  private function deleteTokens(User $user)
  {
    $conn = Db::connectDB();
    $query = Query::delete()->table('sessions')->where('userId', '=', $user->id)->getQuery();
    $conn->query($query);
  }
}
