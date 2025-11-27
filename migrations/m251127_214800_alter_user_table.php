<?php

use yii\db\Migration;

class m251127_214800_alter_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'service_id', $this->integer()->notNull()->after('id'));
        $this->addForeignKey(
            'fk_user_service_id',
            '{{%user}}',
            'service_id',
            '{{%service}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->createIndex('idx_user_service_id', '{{%user}}', 'service_id');

        // Удаляем старую колонку service_name
        $this->dropIndex('idx_users_service_name', '{{%user}}');
        $this->dropColumn('{{%user}}', 'service_name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Восстанавливаем service_name (нужно определить логику отката)
        $this->addColumn('{{%user}}', 'service_name', $this->string(100)->notNull()->after('external_id'));

        // Удаляем внешний ключ и индекс
        $this->dropForeignKey('fk_user_service_id', '{{%user}}');
        $this->dropIndex('idx_user_service_id', '{{%user}}');

        // Удаляем колонку service_id
        $this->dropColumn('{{%user}}', 'service_id');

        // Восстанавливаем старый индекс
        $this->createIndex('idx_users_service_name', '{{%user}}', 'service_name');
    }
}
