<?php

namespace App\Resources;

use App\Controllers\OrderController;

class OrderResource
{
  public $object = [];
  public function __construct($object)
  {
    return $this->object = [
      'id' => $object->id,
      'userId' => $object->userId,
      'value' => $object->value,
      'dateCreate' => $object->dateCreate,
      'dateEdit' => $object->dateEdit,
      'products' => $object->products()
    ];
  }

  public static function collection($objects)
  {
    $array['objects'] = [];
    foreach ($objects as $object) {
      $array['objects'][] = [
        'id' => $object->id,
        'userId' => $object->userId,
        'value' => $object->value,
        'dateCreate' => $object->dateCreate,
        'dateEdit' => $object->dateEdit,
        'products' => $object->products()
      ];
    }

    return $array;
  }
}
