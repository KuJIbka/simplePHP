<?php

namespace Main\Service;

use Main\Exception\CommonFatalError;
use Main\Entity\User;
use Main\Utils\AbstractSingleton;

/**
 * @method static SessionManager get()
 */
class SessionManager extends AbstractSingleton
{
    const KEY_USER_ID = 'user_id';

    protected static $inst;
    protected $isOpened = false;

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
        $timeout = 15;
        $lockSessionName = 'lock_' . $lockName;
        $issetLock = $this->issetParam($lockSessionName);
        while ($issetLock && $timeout >= 0) {
            sleep(1);
            $timeout--;
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
}
