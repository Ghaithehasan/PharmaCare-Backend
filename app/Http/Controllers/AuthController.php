<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\VarificationEmail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function login(Request $request)
{
    $credentials = $request->only('email', 'password');

    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8|max:15'
    ]);

    // الحصول على المستخدم يدوياً من الحارس الصحيح
    $user = User::where('email', $credentials['email'])->first();

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'invalid credentials',
            'status' => 'error',
            'error_code' => 401
        ], 401);
    }

    // توليد التوكن بطريقة موثوقة
    $token = JWTAuth::fromUser($user);

    return response()->json([
        'status' => 'success',
        'message' => 'login success',
        'user_id' => $user->id,
        'token' => $token,
        'expires_in' => config('jwt.ttl') * 60,
        'user' => $user
    ], 200);
}
    public function register(Request $request)
    {
        try{
        $data = $request->validate([
            'name' => 'required|string|max:40',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:20|confirmed',
            'phone' => 'required|numeric|unique:users,phone|digits_between:10,15',
            'gender' => 'required|in:male,female'
        ]);
        $data['password'] = Hash::make($data['password']);
        $data['verification_code'] = Str::random(6);

        $user = User::create($data);

        $verification_link = url('/api/verify-email/' . Crypt::encryptString($user->id));
        Mail::to($user->email)->send(new VarificationEmail($user->name , $user->verification_code , $verification_link));

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.register_success'),
            'token' => $token,
            'user' => new UserResource($user),
            'user_id' => $user->id ,
            'local' => app()->getLocale() ,
            'status_code' => 201
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => __('messages.server_error'),
            'error_code' => 500,
            'details' => $e->getMessage()
        ], 500);
    }

    }


    public function verifyEmail(Request $request)
    {
        
        try {
            // dd(auth()->user());
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'verification_code' => 'required|string|min:6|max:6'
            ]);

            // $user = User::where('email', $request->email)
            //             ->where('verification_code', $request->verification_code)
            //             ->first();
            $user = auth()->user()->where('verification_code', $request->verification_code);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.invalid_verification_code')
                ], 400);
            }

            $user->update([
                'email_verified_at' => now(),
                'verification_code' => null,
                'is_verified' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.verification_success')
            ], 200);

        } catch (\Exception $e) {
            // Log::error('Verification Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('messages.server_error'),
                'error_code' => 500,
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function show_profile_details(Request $request)
    {
        try {
            // استرجاع المستخدم من التوكن المرسل في الهيدر
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم غير موجود أو التوكن غير صالح',
                    'error_code' => 404
                ], 404);
            }
    
            return response()->json([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'gender' => $user->gender,
                    'is_verified' => $user->is_verified,
                    'created_at' => $user->created_at
                ],
                'status_code' => 200
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'لم يتم التحقق من التوكن أو من الجلسة',
                'error_code' => 401,
                'details' => $e->getMessage()
            ], 401);
        }
    }

    public function updateProfile(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'المستخدم غير موجود أو التوكن غير صالح',
                'error_code' => 404
            ], 404);
        }

        // ✅ تحقق من البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'name'   => 'sometimes|required|string|max:40',
            'email'  => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone'  => 'sometimes|required|numeric|digits_between:10,15|unique:users,phone,' . $user->id,
            'gender' => 'sometimes|required|in:male,female'
        ]);
        

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'البيانات غير صحيحة',
                'errors' => $validator->errors(),
                'status_code' => 422
            ], 422);
        }

        // ✅ تحديث البيانات
        $user->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث البروفايل بنجاح',
            'user' => $user,
            'status_code' => 200
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'فشل في تحديث البروفايل',
            'error_code' => 500,
            'details' => $e->getMessage()
        ], 500);
    }
}


    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken()); // إبطال التوكن الحالي
            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الخروج بنجاح'
            ] , 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل تسجيل الخروج. حاول مرة أخرى!'
            ], 500);
        }
    }


    public function verifyEmailLink(Request $request)
    {
        try {
            // ✅ التحقق من وجود التوكن في الهيدر
            if (!$request->header('Authorization')) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.token_missing')
                ], 401);
            }

            // 🔥 استخراج معرف المستخدم من الـ JWT مباشرة
            $user = JWTAuth::parseToken()->authenticate();

            // 🛑 التحقق مما إذا كان الحساب قد تم تفعيله مسبقًا
            if ($user->email_verified_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('messages.already_verified')
                ], 400);
            }

            // ✅ تحديث حالة الحساب إلى "مُفعَّل"
            $user->update([
                'email_verified_at' => Carbon::now(),
                'is_verified' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => __('messages.verification_success')
            ], 200);

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.token_expired'),
                'error_code' => 401
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.invalid_token'),
                'error_code' => 401
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('messages.token_missing'),
                'error_code' => 401
            ], 401);
        } catch (\Exception $e) {
            Log::error('Verification Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('messages.server_error'),
                'error_code' => 500,
                'details' => $e->getMessage()
            ], 500);
        }
    }

    //---------------------------------------------------------------------------------------------------------------------------------------





    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->stateless()->redirect();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Internal Server Error'] , 500);
        }
    }

    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // البحث عن المستخدم
        $findUser = User::where('social_id', $googleUser->id)->first();

        if ($findUser) {
            Auth::login($findUser);

            $findUser->is_verified=true;
            // توليد JWT Token للمستخدم
            $token = JWTAuth::fromUser($findUser);

            return response()->json([
                'status' => 'success',
                'message' => 'مرحباً بعودتك!',
                'token' => $token, // التوكن
                'user' => $findUser,
                'status_code' => 200
            ], 200);
        } else {
            // معلومات إضافية للمستخدم الجديد
            $phone = null; // الهاتف غير مدعوم من Google بشكل مباشر
            $gender = null; // الجندر غير متوفر في OAuth افتراضياً
            $is_verified=true;
            // إنشاء مستخدم جديد
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'social_id' => $googleUser->id,
                'social_type' => 'google',
                'password' => Hash::make(Str::random(8)), // كلمة مرور عشوائية
                'phone' => $phone,
                'gender' => $gender,
                'is_verified' => $is_verified,
                'email_verified_at'=> now()
            ]);

            Auth::login($newUser);

            // توليد JWT Token للمستخدم الجديد
            $token = JWTAuth::fromUser($newUser);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيلك بنجاح!',
                'token' => $token, // التوكن
                'user' => $newUser,
                'status_code' => 200
            ], 200);
        }
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'حدث خطأ أثناء تسجيل الدخول',
            'details' => $e->getMessage(),
        ], 500);
    }
}


}
