<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait MyCustomUuid
{
    /**
     * Boot the trait
     * 
     * @return void
     */
    // The method name must follow the boot{TraitName} pattern.
    public static function bootMyCustomUuid(): void
    {
        static::creating(function ($model) {
            //Get the value of the model's primary key and check if null or false.
            if (!$model->getKey()) {
                //getKeyName() to get primary key name
                // or Str::orderedUuid()  The orderedUuid() method will generate a timestamp first UUID for easier and more efficient database indexing.
                $model->setAttribute($model->getKeyName(), Str::uuid()->toString());
            }
        });
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     * disable auto increment id cus we use uuid is a string 
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     * change primary key type to string
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
