<?php

namespace Main\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170915125733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("
            CREATE TABLE `users` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
                `login` VARCHAR(50) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
                `password` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8_unicode_ci',
                `balance` DECIMAL(16,2) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE INDEX `login` (`login`)
            )
            COLLATE='utf8_unicode_ci'
            ENGINE=InnoDB
            ;
        ");
        $this->addSql("
            CREATE TABLE `user_limit` (
                `user_id` INT(10) UNSIGNED NOT NULL,
                `login_try_count` SMALLINT(10) UNSIGNED NOT NULL DEFAULT '0',
                `login_try_count_time` INT(10) UNSIGNED NOT NULL DEFAULT '0',
                PRIMARY KEY (`user_id`),
                CONSTRAINT `FK_user_limit_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
            )
            COLLATE='utf8_unicode_ci'
            ENGINE=InnoDB
            ;
        ");

        $testLogin = 'testUser';
        $testPassword = password_hash('testPassword', PASSWORD_BCRYPT, [ 'cost' => 10 ]);
        $this->addSql(
            'INSERT INTO users SET id = 1, name = :name, login = :login, password = :password, balance = 0.0',
            [
                'name' => $testLogin,
                'login' => $testLogin,
                'password' => $testPassword,
            ]
        );
        $this->addSql('INSERT INTO user_limit SET user_id = 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_limit');
        $this->addSql('DROP TABLE users');
    }
}
