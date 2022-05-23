<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20170824000000
 * Initial ORM Tables.
 */
class Version20170829200000 extends AbstractMigration
{
    /**
     * Create ORM Table.
     *
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE front_orm_fromback_brand (id INT AUTO_INCREMENT NOT NULL, back_id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, logo VARCHAR(512) NOT NULL, UNIQUE INDEX UNIQ_D80A5583E9583FF0 (back_id), UNIQUE INDEX UNIQ_D80A5583989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_cart (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, products LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_23D3F1B5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_fromback_category (id INT AUTO_INCREMENT NOT NULL, back_id INT NOT NULL, back_parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, gender VARCHAR(16) DEFAULT NULL, description LONGTEXT DEFAULT NULL, path VARCHAR(512) NOT NULL, depth SMALLINT NOT NULL, position SMALLINT DEFAULT NULL, enabled TINYINT(1) NOT NULL, icon VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E092E9F7E9583FF0 (back_id), UNIQUE INDEX UNIQ_E092E9F7B548B0F (path), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_newsletter (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_9AEE11B9E7927C74 (email), UNIQUE INDEX UNIQ_9AEE11B9A76ED395 (user_id), INDEX enabled_idx (enabled), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_physical_profile (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, current TINYINT(1) NOT NULL, height SMALLINT DEFAULT NULL COMMENT \'In millimeters\', weight SMALLINT DEFAULT NULL COMMENT \'In grams\', waist_2d SMALLINT DEFAULT NULL COMMENT \'In millimeters\', hip_2d SMALLINT DEFAULT NULL COMMENT \'In millimeters\', arm SMALLINT DEFAULT NULL COMMENT \'In millimeters\', bra SMALLINT DEFAULT NULL COMMENT \'In millimeters\', chest SMALLINT DEFAULT NULL COMMENT \'In millimeters\', calf SMALLINT DEFAULT NULL COMMENT \'In millimeters\', finger SMALLINT DEFAULT NULL COMMENT \'In millimeters\', foot_length SMALLINT DEFAULT NULL COMMENT \'In millimeters\', foot_width SMALLINT DEFAULT NULL COMMENT \'In millimeters\', hand_length SMALLINT DEFAULT NULL COMMENT \'In millimeters\', hand_width SMALLINT DEFAULT NULL COMMENT \'In millimeters\', head SMALLINT DEFAULT NULL COMMENT \'In millimeters\', hip SMALLINT DEFAULT NULL COMMENT \'In millimeters\', inside_leg SMALLINT DEFAULT NULL COMMENT \'In millimeters\', neck SMALLINT DEFAULT NULL COMMENT \'In millimeters\', shoulder SMALLINT DEFAULT NULL COMMENT \'In millimeters\', thigh SMALLINT DEFAULT NULL COMMENT \'In millimeters\', waist SMALLINT DEFAULT NULL COMMENT \'In millimeters\', wrist SMALLINT DEFAULT NULL COMMENT \'In millimeters\', shoulder_to_hip SMALLINT DEFAULT NULL COMMENT \'In millimeters\', hollow_to_floor SMALLINT DEFAULT NULL COMMENT \'In millimeters\', skin VARCHAR(32) DEFAULT NULL, hair VARCHAR(16) DEFAULT NULL, eyes VARCHAR(16) DEFAULT NULL, size VARCHAR(8) DEFAULT NULL, morphotype VARCHAR(15) DEFAULT NULL, morphoprofile VARCHAR(4) DEFAULT NULL, morpho_weight VARCHAR(8) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX user_idx (user_id), INDEX current_idx (user_id, current), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_review (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, model_id VARCHAR(64) NOT NULL, enabled TINYINT(1) NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, note SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_62AC02ACA76ED395 (user_id), INDEX model_idx (model_id, enabled), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_tracking (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, model LONGTEXT NOT NULL, view DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_43DF2844A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', firstname VARCHAR(128) DEFAULT NULL, lastname VARCHAR(128) DEFAULT NULL, gender VARCHAR(16) NOT NULL, description LONGTEXT DEFAULT NULL, address VARCHAR(512) DEFAULT NULL, zipCode VARCHAR(32) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, birthDate DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_A5E3AF4B92FC23A8 (username_canonical), UNIQUE INDEX UNIQ_A5E3AF4BA0D96FBF (email_canonical), UNIQUE INDEX UNIQ_A5E3AF4BC05FB297 (confirmation_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_wishlist (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(512) DEFAULT NULL, models LONGTEXT DEFAULT NULL COMMENT \'A model is not a product !(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_77426069A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE front_orm_wishlist_liked (wishlist_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_964BE09AFB8E54CD (wishlist_id), INDEX IDX_964BE09AA76ED395 (user_id), PRIMARY KEY(wishlist_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE front_orm_cart ADD CONSTRAINT FK_23D3F1B5A76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_newsletter ADD CONSTRAINT FK_9AEE11B9A76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_physical_profile ADD CONSTRAINT FK_33DB55E8A76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_review ADD CONSTRAINT FK_62AC02ACA76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_tracking ADD CONSTRAINT FK_43DF2844A76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_wishlist ADD CONSTRAINT FK_77426069A76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id)');
        $this->addSql('ALTER TABLE front_orm_wishlist_liked ADD CONSTRAINT FK_964BE09AFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES front_orm_wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE front_orm_wishlist_liked ADD CONSTRAINT FK_964BE09AA76ED395 FOREIGN KEY (user_id) REFERENCES front_orm_user (id) ON DELETE CASCADE');
    }


    /**
     * Delete ORM Tables.
     *
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE front_orm_cart DROP FOREIGN KEY FK_23D3F1B5A76ED395');
        $this->addSql('ALTER TABLE front_orm_newsletter DROP FOREIGN KEY FK_9AEE11B9A76ED395');
        $this->addSql('ALTER TABLE front_orm_physical_profile DROP FOREIGN KEY FK_33DB55E8A76ED395');
        $this->addSql('ALTER TABLE front_orm_review DROP FOREIGN KEY FK_62AC02ACA76ED395');
        $this->addSql('ALTER TABLE front_orm_tracking DROP FOREIGN KEY FK_43DF2844A76ED395');
        $this->addSql('ALTER TABLE front_orm_wishlist DROP FOREIGN KEY FK_77426069A76ED395');
        $this->addSql('ALTER TABLE front_orm_wishlist_liked DROP FOREIGN KEY FK_964BE09AA76ED395');
        $this->addSql('ALTER TABLE front_orm_wishlist_liked DROP FOREIGN KEY FK_964BE09AFB8E54CD');
        $this->addSql('DROP TABLE front_orm_fromback_brand');
        $this->addSql('DROP TABLE front_orm_cart');
        $this->addSql('DROP TABLE front_orm_fromback_category');
        $this->addSql('DROP TABLE front_orm_newsletter');
        $this->addSql('DROP TABLE front_orm_physical_profile');
        $this->addSql('DROP TABLE front_orm_review');
        $this->addSql('DROP TABLE front_orm_tracking');
        $this->addSql('DROP TABLE front_orm_user');
        $this->addSql('DROP TABLE front_orm_wishlist');
        $this->addSql('DROP TABLE front_orm_wishlist_liked');
    }
}
