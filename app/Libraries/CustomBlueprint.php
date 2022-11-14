<?php

namespace App\Libraries;

use Illuminate\Database\Schema\Blueprint;

class CustomBlueprint extends Blueprint{

    public function authorField($createdBy = 'created_by', $updatedBy = 'updated_by')
    {
        // if users table use uuid as primary key
        // parent::foreignUuId($createdBy)->constrained('users');
        // parent::foreignUuId($updatedBy)->constrained('users');

        // if users table use bigInteger or integer or ...
        parent::foreignId($createdBy)->nullable()->constrained('users');
        parent::foreignId($updatedBy)->nullable()->constrained('users');
    }

    public function dropAuthorField($createdBy = 'created_by', $updatedBy = 'updated_by')
    {
        parent::dropColumn($createdBy);
        parent::dropColumn($updatedBy);
    }

}