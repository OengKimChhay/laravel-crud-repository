<?php

namespace App\Modules\Category;

use App\CrudRepository\CrudRepository;

class CategoryService extends CrudRepository
{
    public function __construct(Category $category)
    {
        parent::__construct($category);
    }
}
