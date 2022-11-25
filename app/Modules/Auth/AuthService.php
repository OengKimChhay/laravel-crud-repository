<?php

namespace App\Modules\Auth;

use App\CrudRepository\CrudRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Auth as User;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Helper\Mail\MailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthService extends CrudRepository
{
    protected $tokenName = 'API Personal Access Client';
    
    protected array $select_fields = [
        'id',
        'name',
        'email'
    ];

    public function __construct(User $auth, public MailService $mailService)
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
        DB::transaction(function () use ($email) {
            $existUser = User::where('email', $email)->first();
            $currentUserId = Auth::id();
            abort_if(!$existUser, 404, 'Email does not exist');
            abort_if($existUser->id !== $currentUserId, 403, 'U cannot reset other user\'s password');

            // $token = request()->bearerToken();
            $token = Str::random(200);
            $resetLink = route('auth.update.password', ['token' => $token, 'email' => $email]);

            $mailTemplate = 'mail.send';
            $mailData = [
                'title' => 'Update Password',
                'link' => $resetLink,
                'subject' => 'Update Password'
            ];
            $this->mailService->sendMail($mailTemplate, $mailData, $email);

            // save credentials to password_resets table
            PasswordReset::create([
                'email' => $email,
                'token' => $token
            ]);

            return true;
        });
    }

    public function updatePassword(string $password, string $token, string $email)
    { 
        $credential = PasswordReset::where([
            'email' => $email,
            'token' => $token
        ])->first();

        abort_if(!$credential, 404, 'Something went wrong please try again!');
        abort_if($credential->token !== $token || $credential->email !== Auth::user()->email, 403, 'U cannot reset other user\'s password');

        DB::transaction(function () use ($password, $token, $email) {
            $user = User::where('email', $email)->first();
            $user->password = bcrypt($password);
            $user->save();

            PasswordReset::where([
                'email' => $email,
                'token' => $token
            ])->delete();

            return true;
        });
    }
}
