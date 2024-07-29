<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240725112852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preseason_prediction ADD afcchampion_points INT NOT NULL, ADD nfcchampion_points INT NOT NULL, ADD super_bowl_winner_points INT NOT NULL, ADD most_passing_yards_points INT NOT NULL, ADD most_rushing_yards_points INT NOT NULL, ADD first_pick_points INT NOT NULL, ADD most_points_scored_points INT NOT NULL, ADD fewest_points_allowed_points INT NOT NULL, ADD highest_margin_of_victory_points INT NOT NULL, ADD oroy_points INT NOT NULL, ADD droy_points INT NOT NULL, ADD mvp_points INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preseason_prediction DROP afcchampion_points, DROP nfcchampion_points, DROP super_bowl_winner_points, DROP most_passing_yards_points, DROP most_rushing_yards_points, DROP first_pick_points, DROP most_points_scored_points, DROP fewest_points_allowed_points, DROP highest_margin_of_victory_points, DROP oroy_points, DROP droy_points, DROP mvp_points');
    }
}
