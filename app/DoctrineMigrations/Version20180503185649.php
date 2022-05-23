<?php declare(strict_types = 1);

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20180503185649
 * @package Application\Migrations
 */
class Version20180503185649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE front_orm_physical_profile ADD leg_length SMALLINT DEFAULT NULL COMMENT \'In millimeters\', ADD waist_top SMALLINT DEFAULT NULL COMMENT \'In millimeters\', ADD waist_bottom SMALLINT DEFAULT NULL COMMENT \'In millimeters\'');
        $this->addSql('UPDATE front_orm_physical_profile AS p INNER JOIN front_orm_user AS u ON p.user_id = u.id SET p.waist_top = p.waist WHERE u.gender = \'female\'');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE front_orm_physical_profile DROP leg_length, DROP waist_top, DROP waist_bottom');
    }
}
