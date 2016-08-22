<?php

namespace Exitialis\Mas\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\ResponseFactory;
use Exitialis\Mas\MasKeys;
use App\User;
use Exitialis\Mas\MasKeysGenerator;

class MasLoginController extends Controller
{

    protected $masKeys;
    protected $user;
    protected $request;
    protected $response;
    protected $generator;

    public function __construct(MasKeys $masKeys, User $user, Request $request, ResponseFactory $response, MasKeysGenerator $generator)
    {
        $this->masKeys = $masKeys;
        $this->user = $user;
        $this->request = $request;
        $this->response = $response;
        $this->generator = $generator;
    }

    public function auth($login, $password)
    {
        $login_column = config("mas.user.login_column");
        $password_column = config("mas.user.password_column");
        $hash = config('mas.hash');
        $user = $this->user->where($login_column, "=", $login)->with('MasKeys')->first();
        if ($user == null)
            return $this->response->make("false");
        $realPass = $user->$password_column;
        $masKeys = $user->MasKeys;
        switch ($hash) {
            case 'wp':
                $bool = $realPass == $this->generator->HashPassword($password, $realPass);
                break;
            case 'dle':
                $bool = $realPass == md5(md5($password));
                break;
            default:
                $bool = $realPass == $this->generator->HashPassword($password, $realPass);        
                break;
        }
        if ($bool)
        {
            if ($masKeys == null)
            {
                $masKeys = new MasKeys;
                $uuid = $this->generator->uuidConvert($user->$login_column);
                $masKeys->uuid = $uuid;
                $masKeys->user_hash = str_replace("-", "", $uuid);
                $masKeys->session = $this->generator->generateStr();
                $masKeys->user_id = $user->{$user->getKeyName()};
                $masKeys->username = $user->$login_column;
                $masKeys->save();
                return $this->response->make($uuid . "::" . $masKeys->session);
            }
            else
            {
                $masKeys->session = $this->generator->generateStr(); // Regenerate session
                $user_hash = $masKeys->user_hash;
                $uuid = $masKeys->uuid;
                if ($user_hash != str_replace("-", "", $uuid))
                    $masKeys->user_hash = str_replace("-", "", $uuid);
                $masKeys->save();

                return $this->response->make($masKeys->uuid . "::" . $masKeys->session);
            }

        }
        return $this->response->make("false");
    }

    public function refresh()
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");
        if(!$this->request->isJson())
            return $this->response->json($error);
        $json = $this->request->json();
        $session = $json->accessToken;
        $uuid = $json->clientToken;
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $uuid) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $session)){
            return $this->response->json($error);
        }
        $user = $this->masKeys->getUser($session, $uuid);
        $session = $this->generator->generateStr();
        $user->session = $session;
        $user->save();
        return $this->response->json(["accessToken" => $session, "clientToken" => $uuid]);
    }

    public function MasValidate()
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");
        if(!$this->request->isJson())
            return $this->response->json($error);
        $json = $this->request->json();
        $session = $json->accessToken;
        $uuid = $json->clientToken;
        $user = $this->masKeys->where("uuid", "=", $uuid);
        if ($user == null)
            return $this->response->json($error);
        if ($user->session != $session)
        {
            return $this->response->json($error, "403");
        }
        return $this->response->make("", "204");

    }

    public function invalidate()
    {

    }
}
