<?php

namespace App\Modules\Product;

use App\CrudRepository\CrudRepository;

class ProductService extends CrudRepository
{
    protected array $select_fields = [
        'id',
        'name',
        'code',
        'image',
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    public function __construct(Product $product){
        parent::__construct($product);
    }
}
