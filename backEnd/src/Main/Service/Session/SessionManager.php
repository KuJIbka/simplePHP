<?php

namespace Main\Service\Session;

use Main\Exception\BaseException;
use Main\Entity\User;
use Main\Repository\RoleRepository;
use Main\Repository\UserRepository;
use Main\Service\Config;
use Main\Service\PermissionService;
use Main\Service\Session\handlers\MainSessionHandlerInterface;
use Main\Service\Session\handlers\SessionMemcachedHandler;
use Main\Service\Session\handlers\SessionMemcacheHandler;
use Main\Service\Session\handlers\SessionRedisHandler;

class SessionManager
{
    const SAVE_HANDLER_FILES = 'files';
    const SAVE_HANDLER_REDIS = 'redis';
    const SAVE_HANDLER_MEMCACHE = 'memcache';
    const SAVE_HANDLER_MEMCACHED = 'memcached';

    const KEY_USER_ID = 'user_id';
    const KEY_CSRF_TOKEN = 'csrf_token';

    /** @var Config */
    protected $config;
    /** @var UserRepository */
    protected $userRepository;
    /** @var RoleRepository */
    protected $roleRepository;
    
    protected static $inst;
    protected $isOpened = false;
    protected $usedDriver = '';
    /** @var MainSessionHandlerInterface */
    protected $handler;

    /**
     * @var User
     */
    private $tempUser;

    public function __construct(
        Config $config,
        UserRepository $userRepository,
        RoleRepository $roleRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @throws BaseException
     */
    public function init()
    {
        $this->usedDriver = $this->config->getParam('session_save_handler');
        $sessionSavePath = $this->config->getParam('session_save_path');
        $sessionLifeTime = $this->config->getParam('session_lifetime');
        $sessionMaxLockTime = $this->config->getParam('session_max_lock_time');

        switch ($this->usedDriver) {
            case self::SAVE_HANDLER_FILES:
                ini_set('session.save_handler', $this->usedDriver);
                ini_set('session.save_path', $sessionSavePath);
                ini_set('session.gc_maxlifetime', $sessionLifeTime);
                $handler= null;
                break;

            case self::SAVE_HANDLER_REDIS:
                $redis = new \Redis();
                $parsedSavePath = explode('//', $sessionSavePath);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for redis');
                }
                $parsedSavePath = explode(':', $parsedSavePath[1]);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for redis');
                }
                $redis->connect(
                    $parsedSavePath[0],
                    $parsedSavePath[1],
                    0.0,
                    null,
                    0
                );
                $handler = new SessionRedisHandler($redis, $sessionLifeTime, $sessionMaxLockTime);
                $this->handler = $handler;
                break;

            case self::SAVE_HANDLER_MEMCACHE:
                $memcache = new \Memcache();
                $parsedSavePath = explode('//', $sessionSavePath);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for memcache');
                }
                $parsedSavePath = explode(':', $parsedSavePath[1]);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for memcache');
                }
                $memcache->connect($parsedSavePath[0], $parsedSavePath[1]);
                $handler = new SessionMemcacheHandler($memcache, $sessionLifeTime, $sessionMaxLockTime);
                break;

            case self::SAVE_HANDLER_MEMCACHED:
                $memcached = new \Memcached();
                $parsedSavePath = explode('//', $sessionSavePath);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for memcached');
                }
                $parsedSavePath = explode(':', $parsedSavePath[1]);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for memcached');
                }
                $memcached->addServer($parsedSavePath[0], $parsedSavePath[1]);
                $handler = new SessionMemcachedHandler($memcached, $sessionLifeTime, $sessionMaxLockTime);
                break;

            default:
                throw new BaseException('Handler as '.$this->usedDriver.' does not supported');
        }

        if ($handler) {
            session_set_save_handler($handler, true);
        }
    }

    public function open()
    {
        if (PHP_SAPI !== 'cli') {
            session_start([
                'cookie_httponly' => true,
                'use_strict_mode' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']) ? true : false,
            ]);
        }
        $this->isOpened = true;
    }

    public function getParam(string $name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public function setParam(string $name, $value)
    {
        $wasOpen = $this->isOpened;
        if (!$wasOpen) {
            $this->open();
        }
        $_SESSION[$name] = $value;
        if (!$wasOpen) {
            $this->close();
        }
    }

    public function removeParam(string $name)
    {
        $wasOpen = $this->isOpened;
        if (!$wasOpen) {
            $this->open();
        }
        unset($_SESSION[$name]);
        if (!$wasOpen) {
            $this->close();
        }
    }

    public function issetParam(string $name): bool
    {
        return isset($_SESSION[$name]);
    }

    public function close()
    {
        if (PHP_SAPI !== 'cli') {
            session_write_close();
        }
        $this->isOpened = false;
    }

    public function isLogged(): bool
    {
        return $this->issetParam(self::KEY_USER_ID);
    }

    public function getLoggedUser(): ?User
    {
        if ($this->isLogged()) {
            $userId = $this->getParam(self::KEY_USER_ID);
            if (!$this->tempUser || $this->tempUser->getId() !== $userId) {
                $this->tempUser = $this->userRepository->find($userId);
            }
        } else {
            if (!$this->tempUser || $this->tempUser->getLogin() !== session_id()) {
                $guestRole = $this->roleRepository->getByName(PermissionService::ROLE_USER_GUEST);
                $this->tempUser = (new User())->setId(0)->setLogin(session_id())->setRoles([$guestRole]);
            }
        }
        return $this->tempUser;
    }

    public function setLoggedUser(User $user = null)
    {
        $this->setParam(self::KEY_USER_ID, $user->getId());
    }

    public function regenerateId(bool $delete_old_session = false)
    {
        $wasOpen = $this->isOpened;
        if (!$wasOpen) {
            $this->open();
        }
        if (PHP_SAPI !== 'cli') {
            session_regenerate_id($delete_old_session);
        }
        if (!$wasOpen) {
            $this->close();
        }
    }

    public function destroySession()
    {
        $wasOpen = $this->isOpened;
        if (!$wasOpen) {
            $this->open();
        }
        if (PHP_SAPI !== 'cli') {
            session_destroy();
        }
        unset($_SESSION);
        if (!$wasOpen) {
            $this->close();
        }
    }

    /**
     * @throws BaseException
     */
    public function refreshSessionData()
    {
        $wasOpen = $this->isOpened;
        if ($wasOpen) {
            throw new BaseException('Can not refresh opened session');
        }
        $this->open();
        $this->close();
    }
}
