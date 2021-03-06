<?php

namespace Main\Controller;

use Main\Entity\User;
use Main\Service\CacheDriver;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\Session\SessionManager;

class TestController
{
    public function testSession()
    {
        ob_start();
        echo "Start check sessions... <br />\n";
        $needRegenerate = isset($_GET['regenerate']);
        if ($needRegenerate) {
            SessionManager::get()->open();
            SessionManager::get()->regenerateId();
        }
        echo "Session handler = ".Config::get()->getParam('session_save_handler')."<br />\n";
        echo "Session_id = ".session_id()."<br />\n";
        SessionManager::get()->setParam('sessionParam', 'sessionValue');
        if (SessionManager::get()->getParam('sessionParam') === 'sessionValue') {
            echo "Sessions - OK<br />\n";
            echo "
                <form>
                    <input type='hidden' name='regenerate' value='1' />
                    <button>Regenerate</button>
                </form>
            ";
            SessionManager::get()->destroySession();
        } else {
            echo "Sessions - ERROR<br />\n";
        }
        ob_end_flush();
    }

    public function testDB()
    {
        echo "Start check DB...<br />\n";
        $user = new User();
        $user->setName('someName');
        DB::get()->getEm()->persist($user);
        try {
            DB::get()->getEm()->flush();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        if ($user->getId()) {
            echo "DB - OK<br />\n";
            DB::get()->getEm()->remove($user);
        } else {
            echo "DB - ERROR<br />\n";
        }
    }

    public function stepSess()
    {
        ob_start();
        $step = @(int) $_GET['step'];
        $sm = SessionManager::get();
        if ($step === -2) {
            $sm->open();
            $sm->destroySession();
            $sm->close();
            @var_dump($_SESSION);
        }
        if ($step === -1) {
            $sm->open();
            $sm->close();
            @var_dump($_SESSION);
        }
        if (!$step) {
            var_dump('-------open---------');
            $sm->open();
            var_dump('------set--------');
            $sm->setParam('some1', 'value1');
            $sm->setParam('some2', 'value2');
            $sm->setParam('some3', 'value3');
            var_dump('-------close--------');
            $sm->close();
            var_dump('-------------------');
        }

        if ($step === 1) {
            $sm->open();
            var_dump($_SESSION);
            sleep(5);
            $sm->setParam('some4', 'value4');
            $sm->close();
        }

        if ($step === 2) {
            $sm->open();
            var_dump($_SESSION);
#            sleep(8);
            $sm->setParam('some5', 'value5');
            $sm->close();
        }
        ob_end_flush();
    }
}
