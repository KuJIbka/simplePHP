<?php declare(strict_types = 1);

namespace Main\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Main\Service\TranslationsService;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171218170315 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, login VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, balance NUMERIC(8, 2) NOT NULL, lang VARCHAR(3) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9AA08CB10 (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_limit (user_id INT UNSIGNED NOT NULL, login_try_count SMALLINT UNSIGNED NOT NULL, login_try_count_time INT UNSIGNED NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_limit ADD CONSTRAINT FK_9D541338A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');

        $testLogin = 'testUser';
        $testPassword = password_hash('testPassword', PASSWORD_BCRYPT, [ 'cost' => 10 ]);
        $this->addSql(
            'INSERT INTO users SET id = 1, name = :name, login = :login, password = :password, balance = 0.0, lang = :lang',
            [
                'name' => $testLogin,
                'login' => $testLogin,
                'password' => $testPassword,
                'lang' => TranslationsService::LANG_RU,
            ]
        );
        $this->addSql('INSERT INTO user_limit SET user_id = 1, login_try_count = 0, login_try_count_time = 0');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_limit DROP FOREIGN KEY FK_9D541338A76ED395');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_limit');
    }
}
