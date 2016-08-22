<?php

namespace Exitialis\Mas\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\ResponseFactory;

class MasTexturesController extends Controller
{
    protected $request;
    protected $response;

    public function __construct(Request $request, ResponseFactory $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getSkin($username)
    {
        $uploaddirs = config("mas.path.uploaddirs");
        if (!file_exists($uploaddirs . "/$username.png"))
        {
            $skinurl = asset(config("mas.url.skin")) . "/default.png";
        }
        else
        {
            $skinurl = asset(config("mas.url.skin")) . "/$username.png";
        }

        return '
              "SKIN":
					{
						"url":"'.$skinurl.'"
					}
        ';
    }

    public function getCloak($username)
    {
        $uploaddirc = config("mas.path.uploaddirc");
        if (!file_exists($uploaddirc . "/$username.png"))
        {
            $cloakurl = asset(config("mas.url.cape")) . "/default.png";
        }
        else
        {
            $cloakurl = asset(config("mas.url.cape")) . "/$username.png";
        }

        return '
              "CAPE":
					{
						"url":"'.$cloakurl.'"
					}
        ';
    }

    public function getTextures($username)
    {
        $skin = $this->getSkin($username);
        $cloak = $this->getCloak($username);
        $separator = $cloak === "" ? "" : ",";
        return '
              ' . $skin . $separator .'
              ' . $cloak . '
        ';
    }
}
