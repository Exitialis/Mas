<?php namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use Exitialis\Mas\Exceptions\TexturesException;
use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;

class MasClientController extends Controller
{
    /**
     * Репозиторий пользователей.
     *
     * @var UserRepositoryInterface
     */
    protected $users;

    /**
     * Репозиторий ключей.
     *
     * @var KeyRepositoryInterface
     */
    protected $keys;

    /**
     * Менеджер текстур пользователя.
     *
     * @var TexturesManager
     */
    protected $texturesManager;

    /**
     * MasClientController constructor.
     * @param $users
     * @param $keys
     */
    public function __construct(UserRepositoryInterface $users, KeyRepositoryInterface $keys)
    {
        $this->users = $users;
        $this->keys = $keys;
        $this->texturesManager = new TexturesManager(config('mas.textures'));
    }
    
    /**
     * Обработать эвент входа на сервер.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function join(Request $request)
    {
        $this->validate($request, [
            'selectedProfile' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'accessToken' => 'required|regex:/^[a-zA-Z0-9:_-]+$/',
            'serverId' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        $session = $request->input("accessToken");
        $user_hash = $request->input("selectedProfile");
        $serverId = $request->input("serverId");

        if ( ! $key = $this->keys->where(['user_hash' => $user_hash])->findWhere(['session' => $session])) {
            return response()->json(["error" => "Bad login", "errorMessage" => "Bad Login"]);
        }

        $key->serverid = $serverId;
        $key->save();

        return response('', '204');
    }

    public function hasJoined(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'serverId' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        $username = $request->input("username");
        $serverId = $request->input("serverId");

        $error = ["error" => "Bad login", "errorMessage" => "Bad Login"];

        if ( ! $key = $this->keys->where(["username" => $username])->findWhere(['serverId' => $serverId])) {
            return response()->json($error);
        };

        $user = $key->user;
        $login = $user->login;

        try {
            $textures = $this->texturesManager->getTextures($user, $key);
        } catch(TexturesException $e) {

            \Log::error('Textures error: ', ['message' => $e->getMessage()]);

            return json_encode([
                'id' => $key->uuid,
                'name' => $login,
                'properties' => array(
                    [
                        'name' => 'textures',
                        'value' => base64_encode('Error: ' . $e->getMessage()),
                        'signature' => 'Cg=='
                    ]
                )
            ]);
        }


        $output = [
            'id' => $key->uuid,
            'name' => $login,
            'properties' => array(
                [
                    'name' => 'textures',
                    'value' => base64_encode($textures),
                    'signature' => 'Cg==',
                ]
            )
        ];
    
        return json_encode($output);
    }

    public function profile(Request $request, $user)
    {
        $key = $this->keys->findWhere(['user_hash' => $user]);

        if ( ! $key) {
            return response()->json(["error" => "Bad login", "errorMessage" => "Bad Login"]);
        }

        $realUser = $key->username;
        
        $base64 = $this->texturesManager->getTextures($key->user, $key);

        $output = [
            'id' => $key->uuid,
            'name' => $realUser,
            'properties' => array(
                [
                    'name' => 'textures',
                    'value' => base64_encode($base64),
                ]
            )
        ];

        return json_encode($output);
    }

    public function server(Request $request, Filesystem $file)
    {
        $client = $request->input("client");
        $clients = config("mas.path.clients");

        if ( ! file_exists(config("mas.path.clients") . "/$client"))
            return "Client not found";
        $file_path = config("mas.path.clients") . "/hash/" . $client;
        if (file_exists($file_path))
        {
            $content = $file->get($file_path);
            return $content;
        }
        else
        {
            $hash = hashc($client);
            $file->put($file_path, $hash);
            return $hash;
        }
    }
}