<?php

namespace App\Controllers;

use App\Models\User;

class UserController
{

  public static function store($request)
  {
    User::create([
      'firstname' => $request->firstname,
      'lastname' => $request->lastname,
      'phone' => $request->phone,
      'email' => $request->email,
    ]);
  }
}
