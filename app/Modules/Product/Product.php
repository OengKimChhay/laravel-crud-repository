<?php

namespace App\Modules\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Libraries\Traits\CreateUpdateAuthor;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Auth\Auth as User;
use App\Modules\Category\Category;
class Product extends Model
{
    use SoftDeletes,HasFactory,CreateUpdateAuthor;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'code',
        'image'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
