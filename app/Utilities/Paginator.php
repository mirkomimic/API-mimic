<?php

namespace App\Utilities;

use Exception;

class Paginator
{

  public static function paginate(array $array, int $page, int $perPage): array
  {
    $itemsCount = count($array['objects']);
    $numOfPages = ceil($itemsCount / $perPage);

    if ($numOfPages == 0) {
      $numOfPages = 1;
    }

    if ($page < 1 || $page > $numOfPages) {
      throw new Exception('Page not found.', http_response_code(404));
    }

    $startingPoint = ($page * $perPage) - $perPage;
    $newArray = array_slice($array['objects'], $startingPoint, $perPage, true);

    $data['objects'] = $newArray;
    $data['meta']['current_page'] = $page;
    $data['meta']['rows_returned'] = count($newArray);
    $data['meta']['total_rows'] = $itemsCount;
    $data['meta']['total_pages'] = $numOfPages;
    $data['meta']['has_next_page'] = ($page < $numOfPages) ? true : false;
    $data['meta']['has_previous_page'] = ($page > 1) ? true : false;

    if ($data['meta']['has_next_page']) {
      $nextPage = $page + 1;
      $data['links']['next_page'] = self::getUrl() . $nextPage;
    } else {
      $data['links']['next_page'] = null;
    }
    if ($data['meta']['has_previous_page']) {
      $prevPage = $page - 1;
      $data['links']['prev_page'] = self::getUrl() . $prevPage;
    } else {
      $data['links']['prev_page'] = null;
    }

    return $data;
  }

  private static function getUrl()
  {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (!str_contains($url, "?"))
      return $url . "?page=";

    $url = "http://" . substr($url, 0, strpos($url, "=")) . "=";
    return $url;
  }
}
