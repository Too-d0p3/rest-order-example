<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250605234155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__order_products AS SELECT product_id, order_id, name, price, quantity FROM order_products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_products
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE order_products (id BLOB NOT NULL --(DC2Type:uuid)
            , order_id BLOB NOT NULL --(DC2Type:uuid)
            , product_id VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INTEGER NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_5242B8EB8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO order_products (product_id, order_id, name, price, quantity) SELECT product_id, order_id, name, price, quantity FROM __temp__order_products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__order_products
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5242B8EB8D9F6D38 ON order_products (order_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__order_products AS SELECT order_id, product_id, name, price, quantity FROM order_products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE order_products
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE order_products (product_id VARCHAR(100) NOT NULL, order_id BLOB NOT NULL --(DC2Type:uuid)
            , name VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INTEGER NOT NULL, PRIMARY KEY(product_id), CONSTRAINT FK_5242B8EB8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO order_products (order_id, product_id, name, price, quantity) SELECT order_id, product_id, name, price, quantity FROM __temp__order_products
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__order_products
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5242B8EB8D9F6D38 ON order_products (order_id)
        SQL);
    }
}
