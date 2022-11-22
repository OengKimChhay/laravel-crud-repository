<?php

namespace App\Modules\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\Auth\Request\{AuthRequest, AuthUpdateRequest, AuthLoginRequest,AuthForgotPasswordRequest};
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Modules\Auth\Event\UserRegisterEvent;

class AuthController extends Controller
{

    public function __construct(protected AuthService $authService)
    {
        $this->middleware('auth:api')->except(['index','login','store']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return AuthResource
     */
    public function index(Request $request)
    {
        $auth = $this->authService->paginate($request->all());
        
        return AuthResource::collection($auth);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return AuthResource
     */
    public function store(AuthRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Bcrypt($request->password),
        ];

        $auth = $this->authService->createOne($data);

        // send email to a new user by using event listeners
        event(new UserRegisterEvent($auth));
        // or u can use event dispatch method 
        // UserRegisterEvent::dispatch($auth);
        // UserRegisterEvent::dispatchIf($condition, $auth);
        // or u can register event in $dispatchesEvents model attribute
        return new AuthResource($auth);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return AuthResource
     */
    public function show($id)
    {
        $auth = $this->authService->getOneOrFail($id);
        return new AuthResource($auth);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return AuthResource
     */
    public function update(AuthUpdateRequest $request, $id)
    {
        $auth = $this->authService->getOneOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ];
        $authUpdated = $this->authService->updateOne($auth, $data);
        return new AuthResource($authUpdated);
    }

    public function login(AuthLoginRequest $request)
    {
        $success = $this->authService->login($request->email, $request->password);

        !$success && throw ValidationException::withMessages([
            'invalid' => 'Email or Password incorrect!!',
        ]);

        abort_if(!$success, Response::HTTP_UNAUTHORIZED, 'Unauthorized please try again');

        return $success;
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $this->authService->logout($user);

        return new AuthResource($user);
    }

    public function me(){
        $user = $this->authService->me();
        return new AuthResource($user);
    }

    public function forgotPassword(AuthForgotPasswordRequest $request){
        $user = $this->authService->forgotPassword($request->email);
        return $user;
    }

    public function updatePassword(Request $request, $token, $email){
        $user = $this->authService->updatePassword($request->password, $token, $email);
        return $user;
    }
}
