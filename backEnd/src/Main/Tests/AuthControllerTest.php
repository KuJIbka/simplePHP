<?php

namespace Main\Tests;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Main\Controller\AuthController;
use Main\Core\AppHttp;
use Main\Entity\Permission;
use Main\Entity\Role;
use Main\Entity\User;
use Main\Entity\UserLimit;
use Main\Factory\ResponseFactory;
use Main\Service\PermissionService;
use Main\Service\TranslationService;

class AuthControllerTest extends BaseDBTasteCase
{
    /** @var AuthController */
    protected $authController;
    /** @var AppHttp */
    protected $app;

    /**
     * @throws DBALException
     * @throws SchemaException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \Exception
     */
    public function setUp()
    {
        $this->app = new AppHttp(true);
        $this->authController = $this->app->getAppContainer()->get(AuthController::class);

        $em = $this->app->getAppContainer()->getEm();
        $this->createTables([
            User::class,
            UserLimit::class,
            Permission::class,
            Role::class,
        ], $em);

        $role = (new Role())->setName(PermissionService::ROLE_USER_GUEST);
        $permission = (new Permission())->setName(PermissionService::ACTION_MAIN_CAN_LOGIN);
        $role->setPermissions([$permission]);
        $em->persist($role);
        $em->persist($permission);
        for ($i = 1; $i <= 3; $i++) {
            $user = (new User())
                ->setName('userName'.$i)
                ->setLang(TranslationService::LANG_RU)
                ->setLogin('userLogin'.$i)
                ->setPassword(password_hash('userPassword'.$i, PASSWORD_BCRYPT, [ 'cost' => 10 ]))
            ;
            $em->persist($user);

            $userLimit = (new UserLimit())
                ->setUser($user);
            $user->setUserLimit($userLimit);
            $user->setRoles([$role]);
        }
        $em->flush();
    }

    /**
     * @throws DBALException
     * @throws SchemaException
     * @throws \Exception
     */
    public function tearDown()
    {
        $em = $this->app->getAppContainer()->getEm();
        $this->dropTables([
            Permission::class,
            Role::class,
            UserLimit::class,
            User::class,
        ], $em);
    }

    /**
     * @throws OptimisticLockException
     */
    public function testLogin()
    {
        $sitePath = '/ru/auth/login';

        $testData = [
            ['userLogin1', 'userPassword2', false],
            ['userLogin2', 'userPassword2', true],
            ['userLogin5', 'userPassword5', false],
        ];
        foreach ($testData as $data) {
            $login = $data[0];
            $password = $data[1];
            $expectedResult = $data[2];

            $_POST['login'] = $login;
            $_POST['password'] = $password;
            $this->authController->setAppRequest($this->app->getRouter()->getRequest($sitePath));
            $response = $this->authController->login();
            $responseData = json_decode($response->getContent(), true);
            $resultLogin = $responseData['type'] === ResponseFactory::RESP_TYPE_SUCCESS
                ? $responseData['data']['userData']['login']
                : ''
            ;
            if ($expectedResult) {
                $this->assertEquals($login, $resultLogin);
            } else {
                $this->assertNotEquals($login, $resultLogin);
            }
        }
    }
}
