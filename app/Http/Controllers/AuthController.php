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
        $user = auth()->user();
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
            'user' => new UserResource(Auth::id()),
        ], 201);
    }

    public function change(ChangeRequest $request): JsonResponse
    {
        auth()->user()->update($request->validated());
        return response()->json([
            'message' => 'Данные успешно были изменены',
            'user' => new UserResource(Auth::id())
        ], 201);
    }

    public function forgot(ForgotRequest $request): JsonResponse
    {
        $user = User::query()
            ->when($request->email, fn($query) => $query->where('email', $request->email))
            ->when($request->phone, fn($query) => $query->where('phone', $request->phone))
            ->firstOrFail();

        $code = Str::random(6);
        $user->code = $code;
        $user->save();
        Mail::to($user->email)->send(new ForgotMail($code));
        return response()->json(['message' => "На почту $user->email был отпрввлен код"], 200);
    }

    public function code(CodeRequest $request): JsonResponse
    {
        $user = User::query()
            ->when($request->email, fn($query) => $query->where('email', $request->email))
            ->when($request->phone, fn($query) => $query->where('phone', $request->phone))
            ->firstOrFail();

        if ($user->code == $request->code) {
            return response()->json(['message' => 'Правильный код'], 200);
        } else {
            return response()->json(['message' => 'Не правильный код'], 404);
        }
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = User::query()
            ->when($request->email, fn($query) => $query->where('email', $request->email))
            ->when($request->phone, fn($query) => $query->where('phone', $request->phone))
            ->firstOrFail();

        if ($user->code != $request->code) {
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
        return response()->json(new UserResource(Auth::id()), 200);
    }
}

?>
