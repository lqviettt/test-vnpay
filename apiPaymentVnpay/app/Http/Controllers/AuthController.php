<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginWeb(Request $request)
    {
        $credentials = $request->only('name', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return back()->withErrors(['error' => 'Thông tin đăng nhập không đúng!']);
            }
        } catch (JWTException $e) {
            return back()->withErrors(['error' => 'Không thể tạo token đăng nhập!']);
        }

        $request->session()->put('jwt_token', $token);
        return redirect('/');
    }

    public function logoutWeb(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
    /**
     * register
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function register(AuthRequest $request): JsonResponse
    {
        $validateData = $request->validated();

        $user = User::create(array_merge($validateData, [
            'password' => bcrypt($validateData['password']),
            'remember_token' => Str::random(10),
        ]));

        return $this->created($user);
    }

    /**
     * login
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('name', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->sendError('Invalid credentials', 401);
            }
        } catch (JWTException $e) {
            return $this->sendError('Could not create token', 500);
        }

        return $this->respondWithToken($token);
    }

    /**
     * respondWithToken
     *
     * @param  mixed $token
     * @return JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    /**
     * logout
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return $this->sendError('Failed to logout, please try again', 500);
        }

        return $this->sendSuccess(null, 'Successfully logged out');
    }

    /**
     * profile
     *
     * @return JsonResponse
     */
    public function profile(): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->sendError('User not found', 404);
            }
            return $this->sendSuccess($user);
        } catch (JWTException $e) {
            return $this->sendError('Failed to fetch user profile', 500);
        }
    }
}
