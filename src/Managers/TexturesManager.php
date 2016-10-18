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
     * TexturesManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->skinPath = $config['path']['skin'];
        $this->capePath = $config['path']['cloak'];
        $this->skinDefault = $config['skin_default'];
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
        $path = $basePath . $user->login . $format;

        // Если не найден скин у пользователя, то подставляем
        // стандартный скин сервера из конфигов.
        if ( ! file_exists(public_path($path))) {
            return asset($basePath . $this->skinDefault . $format);
        }

        $cache_path = public_path('cache/') . md5($user->login . 'skin') . $format;

        copy($path, $cache_path);

        return asset($cache_path);
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
        $path = $basePath . $user->login . $format;

        // Если не найден плащ у пользователя, то возвращаем false.
        if ( ! file_exists(public_path($path))) {
            return false;
        }

        $cache_path = public_path('cache/') . md5($user->login . 'cloak') . $format;

        copy($path, $cache_path);

        return asset($cache_path);
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