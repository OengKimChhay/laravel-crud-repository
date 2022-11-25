<?php

namespace App\Modules\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Product\Product;
use App\Modules\Auth\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $table = 'category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name'
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Auth::class, 'created_by');
    }
}
