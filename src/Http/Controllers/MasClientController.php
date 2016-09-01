<?php namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use Exitialis\Mas\Managers\TexturesManager;
use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
use Faker\Provider\zh_TW\Text;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
     * MasClientController constructor.
     * @param $users
     * @param $keys
     */
    public function __construct(UserRepositoryInterface $users, KeyRepositoryInterface $keys)
    {
        $this->users = $users;
        $this->keys = $keys;
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
        $uuid = $request->input("selectedProfile");
        $serverId = $request->input("serverId");

        if ( ! $user = $this->keys->findWhere(['uuid' => $uuid, 'session' => $session])) {
            return response()->json(['error' => 'user not found']);
        }

        $user->serverid = $serverId;
        $user->save();

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

        if ( ! $key = $this->keys->findWhere(["username" => $username])) {
            return response()->json($error);
        };

        $user = $key->user;

        $realUser = $user->login;

        $manager = new TexturesManager(config('mas.textures'));

        $base64 ='
			{
				"timestamp":"'.time().'","profileId":"'.$user->user_hash.'","profileName":"'.$realUser.'","textures":
				{
				    ' . $manager->getTextures($user). '
				}
			}';
        return '
			{
				"id":"'.$user->user_hash.'","name":"'.$realUser.'","properties":
				[
				{
					"name":"textures","value":"'.base64_encode($base64).'","signature":"Cg=="
				}
				]
			}';
    }

    public function profile(Request $request, $user)
    {
        $key = $this->keys->findWhere(['user_hash' => $user]);

        $realUser = $key->username;
        
        $manager = new TexturesManager(config('mas.textures'));
        
        $base64 ='
		{
			"timestamp":"'.time().'","profileId":"'.$key->user_hash.'","profileName":"'.$realUser.'","textures":
			{
				'. $manager->getTextures($key->user) .'
			}
		}';
        return '
		{
			"id":"'.$key->user_hash.'","name":"'.$realUser.'","properties":
			[
			{
				"name":"textures","value":"'.base64_encode($base64).'"
			}
			]
		}';
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