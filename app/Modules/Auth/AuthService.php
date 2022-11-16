<?php

namespace App\Modules\Auth;

use App\CrudRepository\CrudRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Auth as User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Helper\Mail\MailService;
class AuthService extends CrudRepository
{
    protected $tokenName = 'API Personal Access Client';
    protected array $select_fields = [
        'id',
        'name',
        'email'
    ];

    public function __construct(User $auth,public MailService $mailService)
    {
        parent::__construct($auth);
    }

    /**
     * Login user with email and password
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): ?array
    {
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken($this->tokenName)->accessToken;
        
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expired_at' => env('PASSPORT_LIFETIME') * 24 * 60 * 60,
            'user' => $user
        ];
    }

    /**
     * Logout the logged user
     *
     * @param User $user
     * @return User
     */
    public function logout(User $user): User
    {
        $user->token()->revoke();
        return $user;
    }

    /**
     * return the logged user
     * @return User
     */
    public function me(): User
    {
        $userId = Auth::id();
        $user = User::with('products')->find($userId);
        return $user;
    }

    /**
     * return link for reset password
     * @return string $email
     * @return array
     */
    public function forgotPassword(string $email)
    {
        
        $existUser = User::where('email', $email)->first();
        $currentUserId = Auth::id();
        abort_if(!$existUser,404,'Email does not exist');
        abort_if($existUser->id !== $currentUserId ,403,'U cannot reset other user\'s password');
        
        $token = request()->bearerToken();
        $resetLink = route('auth.update.password',['token'=>$token,'email'=>$email]);
        
        $mailTemplate = 'mail.send';
        $mailData = [
            'title' => 'Update Password',
            'link' => $resetLink,
            'subject' => 'Update Password'
        ];
        $this->mailService->sendMail($mailTemplate, $mailData, $email);
    }

    public function updatePassword($token, $email){
        $currentToken = request()->bearerToken();
        abort_if(!$token || $email || $currentToken !== $token ,403,'U cannot reset other user\'s password');
    }
}
