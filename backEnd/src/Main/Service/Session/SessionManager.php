<?php

namespace Main\Service\Session;

use Main\Exception\BaseException;
use Main\Exception\CommonFatalError;
use Main\Entity\User;
use Main\Service\Config;
use Main\Service\DB;
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
                $parsedSavePath = explode('//', $sessionLifeTime);
                if (!isset($parsedSavePath[1])) {
                    throw new BaseException('Wrong sessions save path: '.$sessionSavePath.' for redis');
                }
                $parsedSavePath = explode(':', $sessionLifeTime[1]);
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
                break;

            default:
                throw new BaseException('Hander as '.$this->usedDriver.' does not supported');
        }

        if ($handler) {
            session_set_save_handler($handler, true);
        }
        ini_set('session.save_handler', $this->usedDriver);
        ini_set('session.save_path', Config::get()->getParam('session_save_path'));
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

    public function getParam($name, $default = null)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : $default;
    }

    public function setParam($name, $value)
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

    public function removeParam($name)
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

    public function issetParam($name)
    {
        return isset($_SESSION[$name]);
    }

    public function close()
    {
        session_write_close();
        $this->isOpened = false;
    }

    public function sessionLock($lockName)
    {
        $timeout = 15000;
        $lockSessionName = 'lock_' . $lockName;
        $this->refreshSessionData();
        $issetLock = $this->issetParam($lockSessionName);
        while ($issetLock && $timeout >= 0) {
            $this->refreshSessionData();
            $issetLock = $this->issetParam($lockSessionName);
            $usleepVal = rand(100, 300);
            usleep($usleepVal);
            $timeout -= $usleepVal;
        }
        if ($issetLock && $timeout < 0) {
            throw new CommonFatalError();
        }
        $this->setParam($lockSessionName, true);
    }

    public function sessionUnlock($lockName)
    {
        $lockSessionName = 'lock_' . $lockName;
        if ($this->issetParam($lockSessionName)) {
            $this->removeParam($lockSessionName);
        }
    }

    public function isLogged()
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

    public function regenerateId($delete_old_session = false)
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