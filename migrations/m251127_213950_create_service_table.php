<?php

use yii\db\Migration;

class m251127_213950_create_service_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->unique()->comment('Название сервиса'),
            'host' => $this->string(255)->notNull()->comment('Хост сервиса'),
            'descr' => $this->text()->null()->comment('Описание сервиса'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultValue(null),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%service}}');
    }
}
