<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
      $this->user = auth()->user();
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);

    }

     /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register (Request $request) {

        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,255',
                'last_name' => 'required|string|between:2,255',
                 'gender' =>  'required|string',
                  'email' => 'required|email|max:100|unique:users',
                  'password' => 'required|string|min:6',
                  'confirm_password' => 'required|same:password',
                  'image' => 'sometimes|string',
                  'date_of_birth' => 'nullable',
                  'academic_year' => 'nullable',

            ]);
              
            if ($Validator-fails()) {
                return response()->json($validator->errors()->toJson(),400);
            }

            $user = User::create(array_merge(
               $validator->validated(),
               ['password'=> bcrypt($request->password)]
            ));

            return response()-json([
                'message' => 'User successfully registred',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        

    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try{
            $token = JWTAuth::getToken();

            JWTAuth::invalidate($token);

            auth()->logout();

            return response()->json(['message'=> 'Successfully logged out']);

        }
        catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);

        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
       try {
        $oldToken = JWTAuth::getToken();
        $newToken = auth()->refresh();
         
        if($oldToken) {
            try {
             JWTAuth::invalidate($oldToken);
        } catch(\Exception $e) {
            \Login::warning("Token could be not invalidated");
        }
        }
        
         return $this->respondWithToken($newToken);
       } catch(Exception $e) {
        return response()->json(['error' =>  'Could not refresh token', "message" => $e->getMessage()], 401);
       }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 3600,
            'user' => auth()->user(),
        ]);
    }

    public function sendRegistrationInvite(Request $request) {
        $user = Auth::user(); // Get the currently authenticated user

        if ($user && $user->hasRole('Admin')) {
            try {
                $validator = Validator::make($request->all(), [
                    'invited_users' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json($validator->errors()->toJson(), 400);
                }

                $invited_users = array_map('trim', explode(',', $request->invited_users));
                $invited_users = array_unique($invited_users);
                $invited_users = array_filter($invited_users);
                $invited_users = array_values($invited_users);
                $invalid_emails = [];
                $valid_emails = [];
                $existing_users = [];

                foreach ($invited_users as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $invalid_emails[] = $email;
                    } else {
                     if (User::where('email', $email)->exists()) {
                            $existing_users[] = $email;
                        } else {
                            $valid_emails[] = $email;
                        }
                    }
                }
                $invalid_emails = implode(', ', $invalid_emails);
                $existing_users = implode(', ', $existing_users);

                if (count($valid_emails) > 0) {
                    // Send registaation invite email
                   // return count($valid_emails);
                }

                return response()->json([
                    'invited_users' => $valid_emails,
                    'invalid_emails' => $invalid_emails,
                    'existing_users' => $existing_users,
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'You do not have permission to access this method', 'user' => $this->user], 403);
        }
    }
    
      //constants
    const CODE_VERIFICATION_DURATION_MINUTES= 3.5;
    const TOKEN_VERIFICATION_DURATION_MINUTES = 60;
    const TOKEN_PASSWORD_RESET_DURATION_MINUTES = 5;
    


}
