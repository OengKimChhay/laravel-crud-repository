<?php

namespace App\Libraries\Traits;

use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Auth as User;

trait CreateUpdateAuthor
{
    /**
     * Boot the trait
     * 
     * @return void
     */
    // The method name must follow the boot{TraitName} pattern.
    public static function bootCreateUpdateAuthor(): void
    {
        static::creating(function ($model) {
            $model->created_by = Auth::check() ? Auth::id() : null;
            $model->updated_by = NULL;
            // NULL vs null
            // NULL is type
            // null is value
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::check() ? Auth::id() : null;
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
