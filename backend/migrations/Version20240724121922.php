<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240724121922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE preseason_prediction (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, afcchampion_id INT NOT NULL, nfcchampion_id INT NOT NULL, super_bowl_winner_id INT NOT NULL, most_passing_yards_id INT NOT NULL, most_rushing_yards_id INT NOT NULL, first_pick_id INT NOT NULL, most_points_scored_id INT NOT NULL, fewest_points_allowed_id INT NOT NULL, highest_margin_of_victory_id INT NOT NULL, oroy_id INT NOT NULL, droy_id INT NOT NULL, mvp_id INT NOT NULL, UNIQUE INDEX UNIQ_B49D17A76ED395 (user_id), INDEX IDX_B49D17A04BCAE4 (afcchampion_id), INDEX IDX_B49D17F6DF7E12 (nfcchampion_id), INDEX IDX_B49D1758C80A54 (super_bowl_winner_id), INDEX IDX_B49D1784BC7A72 (most_passing_yards_id), INDEX IDX_B49D179ECCBC44 (most_rushing_yards_id), INDEX IDX_B49D17E6C65C86 (first_pick_id), INDEX IDX_B49D178BBF84D9 (most_points_scored_id), INDEX IDX_B49D17D22C3744 (fewest_points_allowed_id), INDEX IDX_B49D17E9785660 (highest_margin_of_victory_id), INDEX IDX_B49D17322D5C10 (oroy_id), INDEX IDX_B49D17302A08EA (droy_id), INDEX IDX_B49D17810514F6 (mvp_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17A04BCAE4 FOREIGN KEY (afcchampion_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17F6DF7E12 FOREIGN KEY (nfcchampion_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D1758C80A54 FOREIGN KEY (super_bowl_winner_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D1784BC7A72 FOREIGN KEY (most_passing_yards_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D179ECCBC44 FOREIGN KEY (most_rushing_yards_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17E6C65C86 FOREIGN KEY (first_pick_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D178BBF84D9 FOREIGN KEY (most_points_scored_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17D22C3744 FOREIGN KEY (fewest_points_allowed_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17E9785660 FOREIGN KEY (highest_margin_of_victory_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17322D5C10 FOREIGN KEY (oroy_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17302A08EA FOREIGN KEY (droy_id) REFERENCES nfl_team (id)');
        $this->addSql('ALTER TABLE preseason_prediction ADD CONSTRAINT FK_B49D17810514F6 FOREIGN KEY (mvp_id) REFERENCES nfl_team (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17A76ED395');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17A04BCAE4');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17F6DF7E12');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D1758C80A54');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D1784BC7A72');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D179ECCBC44');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17E6C65C86');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D178BBF84D9');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17D22C3744');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17E9785660');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17322D5C10');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17302A08EA');
        $this->addSql('ALTER TABLE preseason_prediction DROP FOREIGN KEY FK_B49D17810514F6');
        $this->addSql('DROP TABLE preseason_prediction');
    }
}
