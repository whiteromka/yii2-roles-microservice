<?php

use yii\db\Migration;

class m251129_143947_init_base_user extends Migration
{
    public function safeUp()
    {
        $transaction = $this->db->beginTransaction();
        try {
            $this->insert('{{%service}}', [
                'id' => 1,
                'name' => 'it-question',
                'host' => 'localhost:8080',
                'descr' => 'Основной сервис вопросов и ответов',
            ]);
            // Сброс последовательности на максимальный ID
            $sql = <<<SQL
                SELECT setval('service_id_seq', (SELECT MAX(id) FROM "service"));
            SQL;
            $this->execute($sql);

            $this->insert('{{%user}}', [
                'id' => 1,
                'external_id' => 10,
                'service_id' => 1,
                'name' => 'Test',
                'last_name' => 'T',
                'email' => 'some@yandex.ru',
                'status' => 1,
            ]);
            $sql = <<<SQL
                SELECT setval('user_id_seq', (SELECT MAX(id) FROM "user"));
            SQL;
            $this->execute($sql);

            $this->insert('{{%auth_assignment}}', [
                'item_name' => 'admin',
                'user_id' => 1,
            ]);
            $transaction->commit();
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function safeDown()
    {
        $this->delete('{{%auth_assignment}}', ['user_id' => 1]);

        $this->delete('{{%user}}', ['external_id' => 10]);

        $this->delete('{{%service}}', ['name' => 'it-question']);
        return true;
    }
}
