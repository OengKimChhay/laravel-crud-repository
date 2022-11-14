<?php
namespace App\Helper;

use App\Modules\Product\Product;

trait Generator{
    public static function productCodeGenerator($prefix){
        $code = mt_rand(10000, 99999);
        return Product::where('code',$prefix . '-' . $code)->exists()
        ? Generator::productCodeGenerator($prefix)
        :  $prefix . '-' . $code;
    }
}