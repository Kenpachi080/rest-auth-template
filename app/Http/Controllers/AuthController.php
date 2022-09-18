<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RebootPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\AuthInterface;
use App\Mail\ForgotMail;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Kostum API",
 *      description="Документация"
 * )
 *
 */
class AuthController extends Controller implements AuthInterface
{

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->phone,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => Str::random(60)
        ]);
        return response()->json(new UserResource($user->id), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('name', '=', $request->phone)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Неверный пароль'
            ], 401);
        }
        return response()->json(new UserResource($user->id), 201);
    }

    public function rebootpassword(RebootPasswordRequest $request): JsonResponse
    {
        $user = User::where('id', '=', Auth::id())->first();
        if (!$user || !Hash::check($request->oldpassword, $user->password)) {
            return response()->json([
                'message' => "Неверный старый пароль"
            ], 401);
        }
        $user->password = bcrypt($request->newspassword);
        $user->api_token = Str::random(60);
        $user->save();
        return response()->json([
            'message' => 'Пароль был успешно заменён!',
            'user' => new UserResource($user->id),
        ], 201);
    }

    public function change(ChangeRequest $request): JsonResponse
    {

        $user = User::where('id', '=', Auth::id())->select('id', 'api_token', 'fio', 'email', 'telephone', 'address')->first();
        if (!$user) {
            return response()->json([
                'message' => 'Пользователь не был найден'
            ], 401);
        }
        $user->fio = $request->fio;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $user->address = $request->address;
        $user->birthday = $request->birthday;
        $user->save();
        return response()->json([
            'message' => 'Данные успешно были изменены',
            'user' => new UserResource($user->id)
        ], 201);
    }

    public function forgot(ForgotRequest $request): JsonResponse
    {
        if ($request->email) {
            $user = User::where('email', '=', $request->email)->first();
        } else if ($request->phone) {
            $user = User::where('phone', '=', $request->phone)->first();
        } else {
            $user = null;
        }

        if ($user == null || !$user) {
            return response()->json(['message' => 'Не найден пользователь'], 404);
        }
        $code = Str::random(6);
        $user->code = $code;
        $user->save();
        Mail::to($user->email)->send(new ForgotMail($code));
        return response()->json(['message' => "На почту $user->email был отпрввлен код"], 200);
    }

    public function code(CodeRequest $request): JsonResponse
    {
        if ($request->email) {
            $user = User::where('email', '=', $request->email)->first();
        } else if ($request->phone) {
            $user = User::where('phone', '=', $request->phone)->first();
        }
        if ($user != null) {
            if ($user->code == $request->code) {
                return response()->json(['message' => 'Правильный код'], 200);
            } else {
                return response()->json(['message' => 'Не правильный код'], 404);
            }
        }
        return response()->json(['message' => 'Пользователь не найден'], 404);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        if ($request->email) {
            $user = User::where('email', '=', $request->email)->first();
        } else if ($request->phone) {
            $user = User::where('telephone', '=', $request->phone)->first();
        }
        if (!$user->code == $request->code) {
            return response()->json(['message' => 'Не правильный код'], 404);
        }
        $user->password = bcrypt($request->password);
        $user->code = '';
        $user->api_token = Str::random(60);
        $user->save();
        $response = [
            'message' => 'Пароль успешно заменен',
            'user' => new UserResource($user->id),
        ];
        return response()->json($response, 200);
    }

    public function view(): JsonResponse
    {
        $user = User::where('id', '=', Auth::id())
            ->first();
        return response()->json(new UserResource($user->id), 200);
    }

    private function date_normalise($date, $format)
    {
        $res = date($format, $date);
        return $res->format($format);
    }
}

?>
