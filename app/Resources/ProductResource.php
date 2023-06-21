<?php

namespace App\Resources;

class ProductResource
{
  public $object = [];
  public function __construct($object)
  {
    return $this->object = [
      'id' => $object->id,
      'name' => $object->name,
      'price' => $object->price,
    ];
  }

  public static function collection($objects)
  {
    $array['objects'] = [];

    foreach ($objects as $object) {
      $array['objects'][] = [
        'id' => $object->id,
        'name' => $object->name,
        'price' => $object->price,
      ];
    }

    return $array;
  }
}
