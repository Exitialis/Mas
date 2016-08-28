<?php

namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use Exitialis\Mas\Managers\AuthManager;
use Exitialis\Mas\MasKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasLoginController extends Controller
{
    /**
     * Менеджер авторизации пользователя.
     *
     * @var AuthManager
     */
    protected $auth;

    /**
     * MasLoginController constructor.
     * @param AuthManager $auth
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Проверка данных пользователя в лаунчере.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        if ( ! $key = $this->auth->login($request->input('login'), $request->input('password'))) {
            return response('false');
        }

        return response($key->uuid . "::" . $key->session);
    }

    /**
     * Обновить accessToken для пользователя.
     *
     * @param Request $request
     * @return mixed
     */
    public function refresh(Request $request)
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");


        $session = $request->input('accessToken');
        $uuid = $request->input('clientToken');

        $user = $this->masKeys->getUser($session, $uuid);
        $session = $this->generator->generateStr();
        $user->session = $session;
        $user->save();
        return $this->response->json(["accessToken" => $session, "clientToken" => $uuid]);
    }

    public function MasValidate(Request $request)
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");

        $json = $this->request->json();
        $session = $request->input('accessToken');
        $uuid = $request->input('clientToken');
        $user = $this->masKeys->where("uuid", "=", $uuid);
        if ($user == null)
            return $this->response->json($error);
        if ($user->session != $session)
        {
            return $this->response->json($error, "403");
        }
        return $this->response->make("", "204");

    }

}
