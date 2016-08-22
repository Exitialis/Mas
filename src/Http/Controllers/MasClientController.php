<?php namespace Exitialis\Mas\Http\Controllers;

use App\Http\Controllers\Controller;
use Exitialis\Mas\MasKeysGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Exitialis\Mas\MasKeys;
use App\User;
use GuzzleHttp\Client;
use \Illuminate\Support\Facades\Route;
use Illuminate\Filesystem\Filesystem;

class MasClientController extends Controller
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

    public function index()
    {
        return view("pages.mas");
    }

    public function join()
    {
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");
        if(!$this->request->isJson())
            return $this->response->json(["error" => "wrongFormat", "errorMessage" => "Request format must be a json object"]);
        $json = $this->request->json();
        $session = $json->get("accessToken");
        $selectedProfile = $json->get("selectedProfile");
        $serverId = $json->get("serverId");
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $selectedProfile) || !preg_match("/^[a-zA-Z0-9:_-]+$/", $session) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverId)){
            return $this->response->json($error);
        }
        $user = $this->masKeys->getUserBySelectedProfile($session, $selectedProfile);
        if ($user == null)
            return $this->response->json($error);

        $user->serverid = $this->request->serverId;
        $user->save();
        return $this->response->make("", "204");
    }

    public function hasJoined()
    {
        $username = $this->request->input("username");
        $serverId = $this->request->input("serverId");
        $error = array("error" => "Bad login", "errorMessage" => "Bad Login");
        if (!preg_match("/^[a-zA-Z0-9_-]+$/", $username) || !preg_match("/^[a-zA-Z0-9_-]+$/", $serverId)){
            return $this->response->json($error);
        }
        $user = $this->masKeys->where("username", "=", $username)->where("serverid", "=", $serverId)->first();
        if ($user == null)
            return $this->response->json(["error" => "Bad login", "errorMessage" => "User not found"]);
        $realUser = $user->username;
        $request = $this->request->create(config("mas.route_prefix") . "/textures/" . $realUser);
        $textures = Route::dispatch($request)->getContent();
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
            return $this->response->json($error);
        }
        $user = $this->masKeys->where("user_hash", "=", $user_hash)->first();
        if ($user == null)
            return $this->response->json($error);
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