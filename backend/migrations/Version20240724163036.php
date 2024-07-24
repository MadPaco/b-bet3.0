<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724163036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preseason_prediction CHANGE afcchampion_id afcchampion_id INT DEFAULT NULL, CHANGE nfcchampion_id nfcchampion_id INT DEFAULT NULL, CHANGE super_bowl_winner_id super_bowl_winner_id INT DEFAULT NULL, CHANGE most_passing_yards_id most_passing_yards_id INT DEFAULT NULL, CHANGE most_rushing_yards_id most_rushing_yards_id INT DEFAULT NULL, CHANGE first_pick_id first_pick_id INT DEFAULT NULL, CHANGE most_points_scored_id most_points_scored_id INT DEFAULT NULL, CHANGE fewest_points_allowed_id fewest_points_allowed_id INT DEFAULT NULL, CHANGE highest_margin_of_victory_id highest_margin_of_victory_id INT DEFAULT NULL, CHANGE oroy_id oroy_id INT DEFAULT NULL, CHANGE droy_id droy_id INT DEFAULT NULL, CHANGE mvp_id mvp_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preseason_prediction CHANGE afcchampion_id afcchampion_id INT NOT NULL, CHANGE nfcchampion_id nfcchampion_id INT NOT NULL, CHANGE super_bowl_winner_id super_bowl_winner_id INT NOT NULL, CHANGE most_passing_yards_id most_passing_yards_id INT NOT NULL, CHANGE most_rushing_yards_id most_rushing_yards_id INT NOT NULL, CHANGE first_pick_id first_pick_id INT NOT NULL, CHANGE most_points_scored_id most_points_scored_id INT NOT NULL, CHANGE fewest_points_allowed_id fewest_points_allowed_id INT NOT NULL, CHANGE highest_margin_of_victory_id highest_margin_of_victory_id INT NOT NULL, CHANGE oroy_id oroy_id INT NOT NULL, CHANGE droy_id droy_id INT NOT NULL, CHANGE mvp_id mvp_id INT NOT NULL');
    }
}
