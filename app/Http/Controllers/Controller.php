<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function fisherYatesShuffle($limit = 5) {
        $alphabet = range('a', 'z');
        $len = count($alphabet);
        
        for ($i = $len - 1; $i > 0; $i--) {
          $j = mt_rand(0, $i);
          [$alphabet[$i], $alphabet[$j]] = [$alphabet[$j], $alphabet[$i]];
        }
        
        $shuffled = array_slice($alphabet, 0, $limit);
        return implode('', $shuffled);
    }
}
