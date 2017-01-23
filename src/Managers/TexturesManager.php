<?php

namespace Exitialis\Mas\Managers;

//TODO: реализовать выдачу из папки cache
use Exitialis\Mas\Exceptions\TexturesException;
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
     * Активен ли скин по умолчанию.
     *
     * @var boolean
     */
    protected $skinDefaultActive;

    /**
     * Название скина по умолчанию.
     *
     * @var string
     */
    protected $skinDefaultName;

    /**
     * Включен ли плащ по умолчанию.
     * 
     * @var boolean
     */
    protected $cloakDefaultActive;

    /**
     * Имя плаща по умолчанию.
     *
     * @var string
     */
    protected $cloakDefaultName;

    /**
     * TexturesManager constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->skinPath = isset($config['path']['skin']) ? $config['path']['skin'] : 'textures/skin';
        $this->capePath = isset($config['path']['cloak']) ? $config['path']['cloak'] : 'textures/cloak';
        $this->skinDefaultActive = isset($config['skin_default']['active']) ? $config['skin_default']['active'] : false;
        $this->skinDefaultName = isset($config['skin_default']['name']) ? $config['skin_default']['name'] : 'default';
        $this->cloakDefaultActive = isset($config['cloak_default']['active']) ? $config['cloak_default']['active'] : false;
        $this->cloakDefaultName = isset($config['cloak_default']['name']) ? $config['cloak_default']['name'] : 'default';
    }

    /**
     * Получить скин пользователя.
     *
     * @param User $user
     * @param string $format
     * @return string
     */
    public function getSkin(User $user, $format = 'png')
    {
        return $this->getTexture($this->skinPath, $user, $this->skinDefaultActive, $this->skinDefaultName, 'skin', $format);
    }

    /**
     * Получить плащ пользоваеля.
     *
     * @param User $user
     * @param string $format
     * @return bool|string
     */
    public function getCloak(User $user, $format = 'png')
    {
        return $this->getTexture($this->capePath, $user, $this->cloakDefaultActive, $this->cloakDefaultName, 'cloak', $format);
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

    /**
     * Получить текстуру пользователя.
     *
     * @param $basePath
     * @param $defaultTextureName
     * @param User $user
     * @param string $textureType skin|cloak
     * @return string
     *
     * @throws TexturesException
     */
    private function getTexture($basePath, User $user, $defaultActive = false, $defaultTextureName = 'default', $textureType = 'skin', $format = 'png')
    {
        if (substr($basePath, -1) != '/') {
            $basePath = $basePath . '/';
        }

        if ( ! file_exists(public_path($basePath))) {
            throw new TexturesException('Texture path does not exists ' . $basePath);
        }

        $path = public_path($basePath . $user->login . '.' . $format);

        if ( ! file_exists($path)) {
            if($defaultActive) {
                return asset($basePath . $defaultTextureName . '.' . $format);
            } else {
                return false;
            }
        }

        $cache_path = 'cache/' . md5($user->login . $textureType) . '.' . $format;

        //Если нет пути с кэшем, создаем
        if ( ! file_exists(public_path('cache/'))) {
            mkdir(public_path('cache/'));
        }

        copy($path, public_path($cache_path));

        return asset($cache_path);
    }
}