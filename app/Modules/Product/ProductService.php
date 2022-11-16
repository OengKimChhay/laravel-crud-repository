<?php

namespace App\Modules\Product;

use App\CrudRepository\CrudRepository;

class ProductService extends CrudRepository
{
    // declare $select_fields to tell CrudRepository which fields to selected
    // to retrieved any relations u must select foreign key EX: created_by,..
    protected array $select_fields = [
        'id',
        'name',
        'code',
        'image',
        'updated_at',
        'created_at',
        'deleted_at',
        'created_by',
    ];

    public function __construct(Product $product){
        parent::__construct($product);
    }
}
