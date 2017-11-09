<?php

namespace Main\Service\Session\handlers;

abstract class SessionHandlerAbstract implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private $sessionName;
    private $prefetchId;
    private $prefetchData;
    private $newSessionId;
    private $igbinaryEmptyData;
    private $locked = false;

    abstract protected function doRead(string $session_id): string;
    abstract protected function doWrite(string $session_id, string $session_data): bool;
    abstract protected function doDestroy(string $session_id): bool;
    abstract protected function doClose(): bool;
    abstract protected function sessionLock(string $key): bool;
    abstract protected function sessionUnLock(string $key);

    /**
     * {@inheritdoc}
     */
    public function validateId($session_id)
    {
        $this->prefetchData = $this->read($session_id);
        $this->prefetchId = $session_id;
        $result = '' !== $this->prefetchData;
        if ($result) {
            if (!$this->locked) {
                $this->sessionLock($session_id);
            }
            $this->locked = true;
        }
        return $result;
    }


    /** {@inheritdoc} */
    public function open($save_path, $name)
    {
        $this->sessionName = $name;
        return true;
    }

    /** {@inheritdoc} */
    public function read($session_id)
    {
        if (null !== $this->prefetchId) {
            $prefetchId = $this->prefetchId;
            $prefetchData = $this->prefetchData;
            $this->prefetchId = $this->prefetchData = null;
            if ($prefetchId === $session_id || '' === $prefetchData) {
                $this->newSessionId = '' === $prefetchData ? $session_id : null;
                $this->doRead($session_id);
            }
        }
        $data = $this->doRead($session_id);
        $this->newSessionId = '' === $data ? $session_id : null;
        return $data;
    }

    /** {@inheritdoc} */
    public function write($session_id, $session_data)
    {
        if (null === $this->igbinaryEmptyData) {
            // see https://github.com/igbinary/igbinary/issues/146
            $this->igbinaryEmptyData = \function_exists('igbinary_serialize') ? igbinary_serialize(array()) : '';
        }
        if ('' === $session_data || $this->igbinaryEmptyData === $session_data) {
            return $this->destroy($session_id);
        }
        $this->newSessionId = null;
        $r = $this->doWrite($session_id, $session_data);
        var_dump($r);
        return $r;
    }

    /** {@inheritdoc} */
    public function close()
    {
        var_dump('close');
        if ($this->locked) {
            $this->sessionUnLock(session_id());
            $this->locked = false;
        }
        var_dump($this->doClose());
        return $this->doClose();
    }

    /** {@inheritdoc} */
    public function destroy($session_id)
    {
        var_dump('destroy');
        if (!headers_sent() && ini_get('session.use_cookies')) {
            if (!$this->sessionName) {
                throw new \LogicException(sprintf('Session name cannot be empty, did you forget to call 
                    "parent::open()" in "%s"?.', get_class($this)));
            }
            $sessionCookie = sprintf(' %s=', urlencode($this->sessionName));
            $sessionCookieWithId = sprintf('%s%s;', $sessionCookie, urlencode($session_id));
            $sessionCookieFound = false;
            $otherCookies = array();
            foreach (headers_list() as $h) {
                if (0 !== stripos($h, 'Set-Cookie:')) {
                    continue;
                }
                if (11 === strpos($h, $sessionCookie, 11)) {
                    $sessionCookieFound = true;
                    if (11 !== strpos($h, $sessionCookieWithId, 11)) {
                        $otherCookies[] = $h;
                    }
                } else {
                    $otherCookies[] = $h;
                }
            }
            if ($sessionCookieFound) {
                header_remove('Set-Cookie');
                foreach ($otherCookies as $h) {
                    header('Set-Cookie:'.$h, false);
                }
            } else {
                setcookie(
                    $this->sessionName,
                    '',
                    0,
                    ini_get('session.cookie_path'),
                    ini_get('session.cookie_domain'),
                    ini_get('session.cookie_secure'),
                    ini_get('session.cookie_httponly')
                );
            }
        }
        return $this->newSessionId === $session_id || $this->doDestroy($session_id);
    }
}
