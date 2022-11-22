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
use Illuminate\Contracts\Database\Query\Builder;
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

    // ----------------------global scope
    // protected static function booted()
    // {
    //     static::addGlobalScope('test', function (Builder $builder) {
    //         $builder->where(['name'=>'chhay','id'=>1]);
    //     });
    // }

    // ----------------------local scope
    // method name must follow the cope{Scope name} pattern.
    // to call scope method User::name('something')->...
    // public function scopeName($query, $type)
    // {
    //     return $query->where('name',$type);
    // }

    // use event class inside $dispatchesEvents model attr
    // protected $dispatchesEvents = [
        // 'retrieved' => Event\UserRegister::class,
        // 'creating' => Event\AuthDeleted::class,
        // ...............................more
    // ];
    // or
    // protected static function booted()
    // {
        // static::retrieved(function ($user) { // do something});
        // static::creating(function ($user) {// do something});
        // ...............................more
    // }
}
