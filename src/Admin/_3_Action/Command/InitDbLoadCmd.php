<?php

declare(strict_types=1);

namespace App\Admin\_3_Action\Command;

use App\Admin\_2_Export\InitDbLoadInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

class InitDbLoadCmd implements InitDbLoadInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws DBALException
     */
    public function initDbLoad(): void
    {
        $this->ex('SET FOREIGN_KEY_CHECKS=0');

        $this->ex('TRUNCATE TABLE cst_customer');
        $this->ex('TRUNCATE TABLE auth_user');
        $this->ex('TRUNCATE TABLE ord_fixed_address');
        $this->ex('TRUNCATE TABLE ord_order_header');
        $this->ex('TRUNCATE TABLE ord_order_line');
        $this->ex('TRUNCATE TABLE ord_order_sscc');

        $this->ex('SET FOREIGN_KEY_CHECKS=1');

        $this->ex("INSERT INTO cst_customer (id, name) VALUES(1, 'System owner')");
        $this->ex("INSERT INTO cst_customer (id, name) VALUES(2, 'Acme Company')");

        $this->ex("INSERT INTO auth_user (id, login, customer_id, roles) VALUES(NULL, 'admin', 1, JSON_ARRAY(\"ROLE_ADMIN\", \"ROLE_USER\"))");
        $this->ex("INSERT INTO auth_user (id, login, customer_id, roles) VALUES(NULL, 'user-1', 2, JSON_ARRAY(\"ROLE_USER\"))");

        $this->ex("
            INSERT INTO ord_fixed_address (id, customer_id, external_id, name_company_or_person, address, city, zip_code) 
            VALUES(1, 2, 'HQ', 'Acme Company', 'ul. Garbary 125', 'Poznań', '61-719')");

        $this->ex("
            INSERT INTO ord_order_header (id, customer_id, number, status, quantity_total, loading_date, loading_name_company_or_person, loading_address, loading_city, loading_zip_code, loading_contact_person, loading_contact_phone, loading_contact_email, loading_fixed_address_external_id, delivery_name_company_or_person, delivery_address, delivery_city, delivery_zip_code, delivery_contact_person, delivery_contact_phone, delivery_contact_email) 
            VALUES (1, 2, '12', 'NEW', 4, '2024-5-29', 'Acme Company', 'ul. Garbary 125', 'Poznań', '61-719', 'Contact Person', '+44-000-000-000', 'person@email.com', 'HQ', 'Receiver Company', 'ul. Wschodnia', 'Poznań', '61-001', 'Person2', '+48-111-111-111', NULL)");

        $this->ex("
            INSERT INTO ord_order_line (id, order_id, customer_id, quantity, length, width, height, weight_one_pallet, weight_total, goods_description) 
            VALUES (1, 1, 2, 3, 120, 80, 100, 20000, 60000, 'computers')");
        $this->ex("
            INSERT INTO ord_order_line (id, order_id, customer_id, quantity, length, width, height, weight_one_pallet, weight_total, goods_description) 
            VALUES (2, 1, 2, 1, 140, 80, 100, 7500, 7500, 'printers')");

        $this->ex("
            INSERT INTO ord_order_sscc (id, order_id, customer_id, code) 
            VALUES (1, 1, 2, 34567)");
        $this->ex("
            INSERT INTO ord_order_sscc (id, order_id, customer_id, code) 
            VALUES (2, 1, 2, 34570)");
        $this->ex("
            INSERT INTO ord_order_sscc (id, order_id, customer_id, code) 
            VALUES (3, 1, 2, 34582)");
        $this->ex("
            INSERT INTO ord_order_sscc (id, order_id, customer_id, code) 
            VALUES (4, 1, 2, 34593)");
    }

    /**
     * @throws DBALException
     */
    private function ex(string $q): void
    {
        $this->connection->executeStatement($q);
    }
}
