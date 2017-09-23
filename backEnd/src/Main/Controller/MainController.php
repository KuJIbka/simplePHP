<?php
namespace Main\Controller;

use Main\Exception\BaseException;
use Main\Service\CacheDriver;
use Main\Service\DB;
use Main\Service\SessionManager;

class MainController extends BaseController
{
    public function index()
    {
        DB::get()->getEm()->beginTransaction();
        try {
            return $this->render("main.html.twig");
        } catch (BaseException $e) {
            return $e->getMessage();
        }
    }

    public function in()
    {
        $user = SessionManager::get()->getLoggedUser();
        return $this->render("in.html.twig", [ 'user' => $user ]);
    }

    public function testCache()
    {
        $cacheService = CacheDriver::get();
        $cacheService->getCacheDriver()->flushAll();
        $key1 = 'key1';
        $value1 = 'value1';
        $cacheService->getCacheDriver()->save($key1, $value1);
        var_dump($cacheService->getCacheDriver()->fetch($key1));
        $cacheService->getCacheDriver()->delete($key1);
        echo "<hr />";
        $tag1 = 'tag1';
        $key2 = 'key2';
        $value2 = 'value2';
        $value3 = 'value3';
        $cacheService->saveWithTags($key2, $value2, [ $tag1 ]);
        var_dump($cacheService->getCacheDriver()->fetch($key2));
        var_dump($cacheService->fetchTags($key2, [ $tag1 ], function () use ($value2) {
            return $value2;
        }));
        echo "<hr />";
        $_SERVER['REQUEST_TIME'] ++;
        $cacheService->setTagsTimestamp([ $tag1 ]);
        var_dump($cacheService->fetchTags($key2, [ $tag1 ], function () use ($value3) {
            return $value3;
        }));
        echo "<hr />";
    }
}
