<?php namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use Exitialis\Mas\MasKey;
use Exitialis\Mas\Repository\Contracts\KeyRepositoryInterface;
use Exitialis\Mas\Repository\Contracts\UserRepositoryInterface;
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

        $user->serverid = $this->request->serverId;
        $user->save();

        return response('', '204');
    }

    public function hasJoined()
    {
        $this->validate($request, [
            'username' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
            'serverId' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
        ]);

        $username = $this->request->input("username");
        $serverId = $this->request->input("serverId");

        $error = ["error" => "Bad login", "errorMessage" => "Bad Login"];

        if ( ! $user = $this->keys->findWhere(["username", $username, "serverid", $serverId])->first()) {
            return response()->json(compact('error'));
        };

        $request = route()->create(config("mas.route_prefix") . "/textures/" . $realUser);

        $time = time();
        $base64 ='
			{
				"timestamp":"'.$time.'","profileId":"'.$user->user_hash.'","profileName":"'.$realUser.'","textures":
				{
				    ' . $textures . '
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

    public function profile($user_hash)
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $user_hash)){
            return response()->json($error);
        }
        $user = $this->masKeys->where("user_hash", "=", $user_hash)->first();
        if ($user == null)
            return response()->json($error);
        $realUser = $user->username;
        $time = time();
        $textures = $this->request->create(config("mas.route_prefix" . "/textures/" . $realUser));
        $base64 ='
		{
			"timestamp":"'.$time.'","profileId":"'.$user->user_hash.'","profileName":"'.$realUser.'","textures":
			{
				'. $textures .'
			}
		}';
        return '
		{
			"id":"'.$user->user_hash.'","name":"'.$realUser.'","properties":
			[
			{
				"name":"textures","value":"'.base64_encode($base64).'"
			}
			]
		}';
    }

    public function server(Filesystem $file)
    {
        $client = $this->request->input("client");
        if (!file_exists(config("mas.path.clients") . "/$client"))
            return "Client not found";
        $file_path = config("mas.path.clients") . "/hash/" . $client;
        if (file_exists($file_path))
        {
            $content = $file->get($file_path);
            return $content;
        }
        else
        {
            $hash = $this->generator->hashc($client);
            $file->put($file_path, $hash);
            return $hash;
        }
    }
}