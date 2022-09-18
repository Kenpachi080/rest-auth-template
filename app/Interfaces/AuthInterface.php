<?php

namespace App\Interfaces;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangeRequest;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RebootPasswordRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;

interface AuthInterface
{
    /**
     * @OA\Post(
     * path="/api/auth/register",
     * summary="Регистрация",
     * description="Регистрация",
     * operationId="authRegister",
     * tags={"Авторизация без api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Регистрация",
     *    @OA\JsonContent(
     *       required={"phone, password, email"},
     *       @OA\Property(property="phone", type="string", format="string", example="+7708"),
     *       @OA\Property(property="password", type="string", format="string", example="123"),
     *       @OA\Property(property="email", type="string", format="string", example="testemail@mail.ru"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=201,
     *    description="Возврощается полная информация про пользователя, и его токен для дальнейшей работы с юзером",
     *    @OA\JsonContent(
     *       type="object",
     *             @OA\Property(
     *                property="user",
     *                type="object",
     *               example={
     *                  }
     *              ),
     *     @OA\Property(
     *                property="token",
     *                type="string",
     *               example="18|TuQoXj84z5IxclUeRK89bSS4839sQfJ8KsQRVRVO",
     *              ),
     *     ),
     *        )
     *     )
     * )
     */
    public function register(RegisterRequest $request): JsonResponse;
    /**
     * @OA\Post(
     * path="/api/auth/login",
     * summary="Авторизация",
     * description="Авторизация по АПИ токену",
     * operationId="authLogin",
     * tags={"Авторизация без api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Апи Токен",
     *    @OA\JsonContent(
     *       required={"phone, password"},
     *       @OA\Property(property="phone", type="string", format="string", example="+7708"),
     *       @OA\Property(property="password", type="string", format="string", example="123"),
     *  ),
     * ),
     * @OA\Response(
     *    response=201,
     *    description="Возврощается полная информация про пользователя, и его токен для дальнейшей работы с юзером",
     *    @OA\JsonContent(
     *       type="object",
     *             @OA\Property(
     *                property="user",
     *                type="object",
     *               example={
     *                  }
     *              ),
     *     @OA\Property(
     *                property="token",
     *                type="string",
     *               example="FKOhXAr6Xhx2e6fMdaKZbTOCxCBwLuJDO3j8fYjRoDG9XoAYKQUSPzayU4BM",
     *              ),
     *     ),
     *        )
     *     )
     * )
     */
    public function login(LoginRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/rebootpassword",
     * summary="Поменять пароль",
     * description="Поменять пароль(для авторизированных пользователей",
     * operationId="rebootpassword",
     * tags={"Авторизация с api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Апи Токен",
     *    @OA\JsonContent(
     *       required={"oldpassword, newpassword"},
     *       @OA\Property(property="oldpassword", type="string", format="string", example="123"),
     *       @OA\Property(property="newspassword", type="string", format="string", example="321"),
     *  ),
     * ),
     *     @OA\Parameter(
     *         name="api_token",
     *         in="header",
     *         description="Токен авторизации(api_token)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * @OA\Response(
     *    response=201,
     *    description="Массив с: Message - Пароль был успешно заменён!, user - Данные юзера(новый апитокен)",
     *    @OA\JsonContent(
     *       type="object",
     *     @OA\Property(
     *                property="message",
     *                type="string",
     *               example="Пароль был успешно изменен",
     *              ),
     *     ),
     *        )
     *     )
     * )
     */
    public function rebootpassword(RebootPasswordRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/change",
     * summary="Поменять данные клиента",
     * description="Поменять данные клиента",
     * operationId="authChange",
     * tags={"Авторизация с api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Апи Токен",
     *    @OA\JsonContent(
     *       required={"fio, email, telephone, address"},
     *       @OA\Property(property="fio", type="string", format="string", example="123"),
     *       @OA\Property(property="email", type="string", format="string", example="321"),
     *       @OA\Property(property="telephone", type="string", format="string", example="321"),
     *       @OA\Property(property="address", type="string", format="string", example="321"),
     *       @OA\Property(property="birthday", type="date", format="date", example="23.10.2002"),
     *  ),
     * ),
     *     @OA\Parameter(
     *         name="api_token",
     *         in="header",
     *         description="Токен авторизации(api_token)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * @OA\Response(
     *    response=201,
     *    description="Массив с: Message - Данные успешно были изменены, user - Данные юзера",
     *    @OA\JsonContent(
     *       type="object",
     *             @OA\Property(
     *                property="user",
     *                type="object",
     *               example={
     *                  }
     *              ),
     *     @OA\Property(
     *                property="message",
     *                type="string",
     *               example="Данные успешно были изменены",
     *              ),
     *     ),
     *        )
     *     )
     * )
     */
    public function change(ChangeRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/forgot",
     * summary="Забыл пароль",
     * description="забыл пароль",
     * operationId="forgot",
     * tags={"Авторизация без api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="",
     *    @OA\JsonContent(
     *       required={"email, phone"},
     *       @OA\Property(property="email", type="string", format="string", example="321"),
     *       @OA\Property(property="phone", type="string", format="string", example="321"),
     *  ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="На почту был отправлен код",
     *    @OA\JsonContent(
     *       type="object",
     *        )
     *     )
     * )
     */
    public function forgot(ForgotRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/code",
     * summary="Подтвердить код",
     * description="Подтвердить код",
     * operationId="code",
     * tags={"Авторизация без api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Апи Токен",
     *    @OA\JsonContent(
     *       required={"code"},
     *       @OA\Property(property="email", type="string", format="string", example="321"),
     *       @OA\Property(property="phone", type="string", format="string", example="321"),
     *       @OA\Property(property="code", type="string", format="string", example=""),
     *  ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Правильный код",
     *    @OA\JsonContent(
     *       type="object",
     *        )
     *     )
     * )
     */
    public function code(CodeRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/changePassword",
     * summary="Помменять пароль",
     * description="Помменять пароль",
     * operationId="changePassword",
     * tags={"Авторизация без api_token"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Апи Токен",
     *    @OA\JsonContent(
     *       required={"password, email, phone, address"},
     *       @OA\Property(property="password", type="string", format="string", example="123"),
     *       @OA\Property(property="email", type="string", format="string", example="321"),
     *       @OA\Property(property="phone", type="string", format="string", example="321"),
     *       @OA\Property(property="code", type="string", format="string", example=""),
     *  ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Массив с: Message - Пароль был успешно заменён, user - Данные юзера(новый апитокен)",
     *    @OA\JsonContent(
     *       type="object",
     *        )
     *     )
     * )
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse;

    /**
     * @OA\Post(
     * path="/api/auth/view",
     * summary="Посмотреть данные",
     * description="Посмотреть данные",
     * operationId="viewauth",
     * tags={"Авторизация с api_token"},
     *     @OA\Parameter(
     *         name="api_token",
     *         in="header",
     *         description="Токен авторизации(api_token)",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * @OA\Response(
     *    response=200,
     *    description="CallBack с данными",
     *    @OA\JsonContent(
     *       type="object",
     *        )
     *     )
     * )
     *
     */
    public function view(): JsonResponse;
}
