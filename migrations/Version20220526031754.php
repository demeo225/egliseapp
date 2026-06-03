<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220526031754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detailcotisationdepartement ADD departement_id INT DEFAULT NULL, ADD cotisationdepartement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE detailcotisationdepartement ADD CONSTRAINT FK_A0D848BECCF9E01E FOREIGN KEY (departement_id) REFERENCES departement (id)');
        $this->addSql('ALTER TABLE detailcotisationdepartement ADD CONSTRAINT FK_A0D848BE354E919A FOREIGN KEY (cotisationdepartement_id) REFERENCES cotisationdepartement (id)');
        $this->addSql('CREATE INDEX IDX_A0D848BECCF9E01E ON detailcotisationdepartement (departement_id)');
        $this->addSql('CREATE INDEX IDX_A0D848BE354E919A ON detailcotisationdepartement (cotisationdepartement_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE detailcotisationdepartement DROP FOREIGN KEY FK_A0D848BECCF9E01E');
        $this->addSql('ALTER TABLE detailcotisationdepartement DROP FOREIGN KEY FK_A0D848BE354E919A');
        $this->addSql('DROP INDEX IDX_A0D848BECCF9E01E ON detailcotisationdepartement');
        $this->addSql('DROP INDEX IDX_A0D848BE354E919A ON detailcotisationdepartement');
        $this->addSql('ALTER TABLE detailcotisationdepartement DROP departement_id, DROP cotisationdepartement_id');
    }
}
