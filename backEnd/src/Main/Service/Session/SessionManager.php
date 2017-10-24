<?php

namespace Main\Service\Session;

use Main\Exception\BaseException;
use Main\Entity\User;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\Session\handlers\MainSessionHandlerInterface;
use Main\Service\Session\handlers\SessionRedisHandler;
use Main\Utils\AbstractSingleton;

/**
 * @method static SessionManager get()
 */
class SessionManager extends AbstractSingleton
{
    const SAVE_HANDLER_FILES = 'files';
    const SAVE_HANDLER_REDIS = 'redis';

    const KEY_USER_ID = 'user_id';

    protected static $inst;
    protected $isOpened = false;
    protected $usedDriver = '';
    /** @var MainSessionHandlerInterface */
    protected $handler;

    public function init()
    {
        $this->usedDriver = Config::get()->getParam('session_save_handler');
        $sessionSavePath = Config::get()->getParam('session_save_path');
        $sessionLifeTime = Config::get()->getParam('session_lifetime');

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
                    null,
                    null,
                    0
                );
                $handler = new SessionRedisHandler($redis, $sessionLifeTime);
                $this->handler = $handler;
                break;

            default:
                throw new BaseException('Hander as '.$this->usedDriver.' does not supported');
        }

        if ($handler) {
            session_set_save_handler($handler, true);
        }
    }

    public function open()
    {
        session_start([
            'cookie_httponly' => true,
            'use_strict_mode' => true,
            'cookie_secure' => isset($_SERVER['HTTPS']) ? true : false,
        ]);
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
        session_write_close();
        $this->isOpened = false;
    }

    public function sessionLock(string $lockName)
    {
        if ($this->handler) {
            $this->handler->sessionLock($lockName);
        } else {
            $this->open();
        }
    }

    public function sessionUnlock(string $lockName)
    {
        if ($this->handler) {
            $this->handler->sessionUnlock($lockName);
        } else {
            $this->close();
        }
    }

    public function isLogged(): bool
    {
        return $this->issetParam(self::KEY_USER_ID);
    }

    public function getLoggedUser(): User
    {
        if ($this->isLogged()) {
            $userId = $this->getParam(self::KEY_USER_ID);
            return DB::get()->getUserRepository()->find($userId);
        }
        return null;
    }

    public function setLoggedUser(User $user)
    {
        $this->setParam(self::KEY_USER_ID, $user->getId());
    }

    public function clearSession()
    {
        $this->open();
        $_SESSION = [];
        setcookie(session_name(), '', time() - 42000, '/');
        session_regenerate_id(true);
        session_destroy();
        $this->close();
    }

    public function regenerateId(bool $delete_old_session = false)
    {
        $wasOpen = $this->isOpened;
        if (!$wasOpen) {
            $this->open();
        }
        session_regenerate_id($delete_old_session);
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
        session_destroy();
        if (!$wasOpen) {
            $this->close();
        }
    }

    public function refreshSessionData()
    {
        $wasOpen = $this->isOpened;
        if ($wasOpen) {
            $this->close();
            $this->open();
        } else {
            $this->open();
            $this->close();
        }
    }
}
