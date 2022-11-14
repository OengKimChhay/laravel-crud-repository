<?php

namespace App\Modules\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Libraries\Traits\CreateUpdateAuthor;

class Product extends Model
{
    use SoftDeletes,HasFactory,CreateUpdateAuthor;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'code',
        'image'
    ];
}
