<?php

use yii\db\Migration;

class m251126_165945_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'external_id' => $this->string(500)->notNull()->unique()->comment('Внешний ID пользователя'),
            'service_name' => $this->string(100)->notNull()->comment('Имя внешнего сервиса пользователя'),
            'name' => $this->string(100)->null(),
            'last_name' => $this->string(100)->null(),
            'email' => $this->string(255)->null(),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);

        $this->createIndex('idx_users_service_name', 'user', 'service_name');
    }

    public function safeDown()
    {
        $this->dropTable('user');
    }
}
