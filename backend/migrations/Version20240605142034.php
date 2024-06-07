<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605142034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bet (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, user_id INT NOT NULL, home_prediction INT DEFAULT NULL, away_prediction INT DEFAULT NULL, points INT DEFAULT NULL, last_edit DATETIME DEFAULT NULL, edit_count INT DEFAULT NULL, INDEX IDX_FBF0EC9BE48FD905 (game_id), INDEX IDX_FBF0EC9BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatroom (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chatroom_message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, chatroomID INT DEFAULT NULL, senderID INT DEFAULT NULL, INDEX IDX_3B50C9F9144F2211 (chatroomID), INDEX IDX_3B50C9F9CBB85F35 (senderID), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, week_number INT NOT NULL, date DATETIME NOT NULL, location VARCHAR(255) NOT NULL, homeOdds INT DEFAULT NULL, awayOdds INT DEFAULT NULL, overUnder NUMERIC(5, 1) DEFAULT NULL, homeScore INT DEFAULT NULL, awayScore INT DEFAULT NULL, homeTeam INT DEFAULT NULL, awayTeam INT DEFAULT NULL, INDEX IDX_232B318CBBFC6AEF (homeTeam), INDEX IDX_232B318C22501995 (awayTeam), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nfl_team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, shorthand_name VARCHAR(255) NOT NULL, logo VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, division VARCHAR(255) NOT NULL, conference VARCHAR(255) NOT NULL, primary_color VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_C74F2195C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, favTeam INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D64985A6B90C (favTeam), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_achievement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, achievement_id INT NOT NULL, date_earned DATETIME NOT NULL, INDEX IDX_3F68B664A76ED395 (user_id), INDEX IDX_3F68B664B3EC99FE (achievement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE bet ADD CONSTRAINT FK_FBF0EC9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F9144F2211 FOREIGN KEY (chatroomID) REFERENCES chatroom (id)');
        $this->addSql('ALTER TABLE chatroom_message ADD CONSTRAINT FK_3B50C9F9CBB85F35 FOREIGN KEY (senderID) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CBBFC6AEF FOREIGN KEY (homeTeam) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C22501995 FOREIGN KEY (awayTeam) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64985A6B90C FOREIGN KEY (favTeam) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BE48FD905');
        $this->addSql('ALTER TABLE bet DROP FOREIGN KEY FK_FBF0EC9BA76ED395');
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F9144F2211');
        $this->addSql('ALTER TABLE chatroom_message DROP FOREIGN KEY FK_3B50C9F9CBB85F35');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CBBFC6AEF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C22501995');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64985A6B90C');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664A76ED395');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664B3EC99FE');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE bet');
        $this->addSql('DROP TABLE chatroom');
        $this->addSql('DROP TABLE chatroom_message');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE nfl_team');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_achievement');
    }
}
