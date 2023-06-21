<?php

namespace App\Traits;

use App\Database\Db;
use App\Utilities\Query;

trait HasTokens
{
  public function token()
  {
    $token = Query::select('accesstoken')->table('sessions')->where('userId', '=', $this->id)->getArray();
    return $token[0]['accesstoken'] ?? null;
  }

  public function tokenExpiryTime()
  {
    $token = $this->token();
    $tokenExpiryTime = Query::select('accessexpiry')->table('sessions')->where('userId', '=', $this->id)->where('accesstoken', '=', $token)->getArray();
    return strtotime($tokenExpiryTime[0]['accessexpiry']);
  }

  public function deleteToken()
  {
    $conn = Db::connectDB();
    $query = Query::delete()->table('sessions')->where('userId', '=', $this->id)->getQuery();
    $conn->query($query);
  }
}
