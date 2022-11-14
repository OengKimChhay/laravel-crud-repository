<?php
namespace App\Modules\Auth;

use App\CrudRepository\CrudRepository;
use Illuminate\Database\Eloquent\Collection;

class AuthService extends CrudRepository{

    protected array $select_fields = [
        'id',
        'name',
        'email'
    ];

    public function __construct(Auth $auth){
        parent::__construct($auth);
    }

    public function withPaginate($request)
    {
        $request['select_fields'] = $this->select_fields;
        return $this->getMany($request);
    }
}