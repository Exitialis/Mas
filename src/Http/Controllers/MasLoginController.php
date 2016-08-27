<?php

namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Exitialis\Mas\Managers\AuthManager;
use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repositories\KeyRepository;
use Exitialis\Mas\Repositories\UserRepository;
use Illuminate\Http\Request;

class MasLoginController extends Controller
{
    /**
     * Репозиторий пользователей.
     *
     * @var UserRepository
     */
    protected $users;

    /**
     * Менеджер авторизации пользователя.
     *
     * @var AuthManager
     */
    protected $auth;

    /**
     * Репозиторий ключей пользователя.
     *
     * @var KeyRepository
     */
    protected $keys;

    /**
     * MasLoginController constructor.
     * @param UserRepository $users
     * @param KeyRepository $keys
     */
    public function __construct(UserRepository $users, KeyRepository $keys)
    {
        $this->users = $users;
        $this->keys = $keys;
        $this->auth = new AuthManager;
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

        if ($this->auth->login($request->input('login'), $request->input('password')))
        {
            $key = $this->keys->save($this->keys->findOrCreateByUser($user), $user);

            return response($key->uuid . "::" . $key->session);
        }

        return response('false');
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
