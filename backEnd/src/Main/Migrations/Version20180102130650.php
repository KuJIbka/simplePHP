<?php declare(strict_types = 1);

namespace Main\Migrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180102130650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE permissions (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_2DEDCC6F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles_permissions (role_id INT UNSIGNED NOT NULL, permission_id INT UNSIGNED NOT NULL, INDEX IDX_CEC2E043D60322AC (role_id), INDEX IDX_CEC2E043FED90CCA (permission_id), PRIMARY KEY(role_id, permission_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, login VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, balance NUMERIC(8, 2) NOT NULL, lang VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL, INDEX IDX_51498A8EA76ED395 (user_id), INDEX IDX_51498A8ED60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_limit (user_id INT UNSIGNED NOT NULL, login_try_count SMALLINT UNSIGNED NOT NULL, login_try_count_time INT UNSIGNED NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE roles_permissions ADD CONSTRAINT FK_CEC2E043D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE roles_permissions ADD CONSTRAINT FK_CEC2E043FED90CCA FOREIGN KEY (permission_id) REFERENCES permissions (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE user_limit ADD CONSTRAINT FK_9D541338A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');

        $this->addSql('
            INSERT INTO users SET
              `id` = 1, 
              `name` = "testUser",
              `login` = "testUser",
              `password` = "'.password_hash('testPassword', PASSWORD_BCRYPT, [ 'cost' => 10 ]).'",
              `balance` = 0,
              `lang` = "ru"
        ;');

        $this->addSql('
            INSERT INTO user_limit SET
              user_id = 1,
              login_try_count = 0,
              login_try_count_time = 0
        ;');

        $roles = ['ROLE_ADMIN', 'ROLE_USER_SIMPLE', 'ROLE_USER_GUEST'];
        foreach ($roles as $role) {
            $this->addSql('INSERT INTO roles SET `name` = :name', ['name' => $role]);
        }

        $permissions = ['ACTION_ADMIN_LOGIN', 'ACTION_MAIN_IS_AUTHENTICATED_FULLY', 'ACTION_CAN_LOGIN'];
        foreach ($permissions as $permission) {
            $this->addSql('INSERT INTO permissions SET `name` = :name', ['name' => $permission]);
        }

        $rolePerms = [[1,1], [2,2], [3,3]];
        foreach ($rolePerms as $rolePerm) {
            $this->addSql('INSERT INTO roles_permissions SET role_id=:roleId, permission_id=:permissionId', [
                'roleId' => $rolePerm[0],
                'permissionId' => $rolePerm[1],
            ]);
        }

        $usersRoles = [[1,1], [1,2]];
        foreach ($usersRoles as $usersRole) {
            $this->addSql('INSERT INTO users_roles SET user_id = :userId, role_id = :roleId', [
                'userId' => $usersRole[0],
                'roleId' => $usersRole[1],
            ]);
        }
    }

    /**
     * @param Schema $schema
     * @throws AbortMigrationException
     * @throws DBALException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE roles_permissions DROP FOREIGN KEY FK_CEC2E043FED90CCA');
        $this->addSql('ALTER TABLE roles_permissions DROP FOREIGN KEY FK_CEC2E043D60322AC');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8ED60322AC');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EA76ED395');
        $this->addSql('ALTER TABLE user_limit DROP FOREIGN KEY FK_9D541338A76ED395');
        $this->addSql('DROP TABLE permissions');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE roles_permissions');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE user_limit');
    }
}
