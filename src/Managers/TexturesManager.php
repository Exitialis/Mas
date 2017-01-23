<?php

namespace Exitialis\Mas\Managers;

//TODO: реализовать выдачу из папки cache
use Exitialis\Mas\MasKey;
use Exitialis\Mas\User;

class TexturesManager
{
    /**
     * Путь до скинов.
     *
     * @var mixed
     */
    protected $skinPath;

    /**
     * Путь до плащей.
     *
     * @var mixed
     */
    protected $capePath;

    /**
     * Путь до стандартного скина.
     *
     * @var mixed
     */
    protected $skinDefault;

    /**
     * Путь до стандартного плаща.
     * 
     * @var mixed
     */
    protected $cloakDefaul;

    /**
     * TexturesManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->skinPath = $config['path']['skin'];
        $this->capePath = $config['path']['cloak'];
        $this->skinDefault = $config['skin_default'];
        $this->cloakDefault = $config['cloak_default'];
    }

    /**
     * Получить скин пользователя.
     *
     * @param User $user
     * @return string
     */
    public function getSkin(User $user)
    {
        $format = '.png';
        $basePath = $this->skinPath . '/';
        $path = public_path($basePath . $user->login . $format);

        // Если не найден скин у пользователя, то подставляем
        // стандартный скин сервера из конфигов.
        if ( ! file_exists($path)) {
            return asset($basePath . $this->skinDefault . $format);
        }

        $cache_path = public_path('cache/') . md5($user->login . 'skin') . $format;

        //Если нет пути с кэшем, создаем
        if ( ! file_exists(public_path('cache/'))) {
            mkdir(public_path('cache/'));
        }

        copy($path, $cache_path);

        return asset('cache/' . md5($user->login . 'skin') . $format);
    }

    /**
     * Получить плащ пользоваеля.
     *
     * @param User $user
     * @return bool|string
     */
    public function getCloak(User $user)
    {
        $format = '.png';
        $basePath = $this->capePath . '/';
        $path = public_path($basePath . $user->login . $format);

        // Если не найден плащ у пользователя, то возвращаем false.
        if ( ! file_exists($path)) {
            return asset($basePath . $this->cloakDefault . $format);
        }

        $cache_path = public_path('cache/') . md5($user->login . 'cloak') . $format;

        //Если нет пути с кэшем, создаем
        if ( ! file_exists(public_path('cache/'))) {
            mkdir(public_path('cache/'));
        }

        copy($path, $cache_path);

        return asset('cache/' . md5($user->login . 'cloak') . $format);
    }

    /**
     * Получить текстуры пользователя.
     *
     * @param User $user
     * @return string
     */
    public function getTextures(User $user, MasKey $key)
    {
        $skin = ['SKIN' => ['url' => $this->getSkin($user)]];
        $cloak = null;
        if ($cloakUrl = $this->getCloak($user)) {
            $cloak = ['CAPE' => ['url' => $cloakUrl]];
        }
        
        if ($cloak) {
            $skin = array_merge($skin, $cloak);
        }
        
        return json_encode([
            'timestamp' => time(),
            'profileId' => $key->uuid,
            'profileName' => $key->username,
            'textures' => $skin
        ]);
    }
}