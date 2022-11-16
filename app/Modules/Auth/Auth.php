<?php

namespace App\Modules\Auth;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\Modules\Auth\AuthFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Modules\Product\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auth extends Authenticatable
{
    // set up laravel passport https://laravel-guide.readthedocs.io/en/latest/passport/#:~:text=client%20%2D%2Dpassword-,Requesting%20Tokens,need%20to%20define%20it%20manually.
    use HasApiTokens,HasFactory, Notifiable, SoftDeletes;
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class,'created_by');
    }
}
