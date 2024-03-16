<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240316211118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_achievement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, achievement_id INT NOT NULL, date_earned DATETIME NOT NULL, INDEX IDX_3F68B664A76ED395 (user_id), INDEX IDX_3F68B664B3EC99FE (achievement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F9144F2211 FOREIGN KEY (chatroomID) REFERENCES chatroom (id)');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F9CBB85F35 FOREIGN KEY (senderID) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CBBFC6AEF FOREIGN KEY (homeTeam) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C22501995 FOREIGN KEY (awayTeam) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE nfl_team ADD primary_color VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE odd ADD CONSTRAINT FK_F7845EEDD73B976C FOREIGN KEY (gameID) REFERENCES game (id)');
        $this->addSql('ALTER TABLE result ADD CONSTRAINT FK_136AC113D73B976C FOREIGN KEY (gameID) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user ADD username VARCHAR(180) NOT NULL, ADD profile_picture VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD favTeam INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64985A6B90C FOREIGN KEY (favTeam) REFERENCES nfl_team (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE INDEX IDX_8D93D64985A6B90C ON user (favTeam)');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_identifier_email TO UNIQ_8D93D649E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664A76ED395');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664B3EC99FE');
        $this->addSql('DROP TABLE user_achievement');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CBBFC6AEF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C22501995');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64985A6B90C');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('DROP INDEX IDX_8D93D64985A6B90C ON user');
        $this->addSql('ALTER TABLE user DROP username, DROP profile_picture, DROP created_at, DROP favTeam');
        $this->addSql('ALTER TABLE user RENAME INDEX uniq_8d93d649e7927c74 TO UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('ALTER TABLE nfl_team DROP primary_color');
        $this->addSql('ALTER TABLE result DROP FOREIGN KEY FK_136AC113D73B976C');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BE48FD905');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BA76ED395');
        $this->addSql('ALTER TABLE odd DROP FOREIGN KEY FK_F7845EEDD73B976C');
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F9144F2211');
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F9CBB85F35');
    }
}
