<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected function fisherYatesShuffle() {
    $array = [1, 2, 3, 4, 5, 6, 7, 8, 9];
    $count = count($array);

    for ($i = $count - 1; $i > 0; $i--) {
      $j = random_int(0, $i);
        
      $temp = $array[$i];
      $array[$i] = $array[$j];
      $array[$j] = $temp;
    }

    return substr(implode('', $array), 0, 5);
  }
}
