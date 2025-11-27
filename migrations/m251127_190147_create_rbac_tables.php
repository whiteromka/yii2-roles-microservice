<?php

use yii\db\Migration;

class m251127_190147_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Таблица для правил
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addPrimaryKey('pk_auth_rule_name', '{{%auth_rule}}', 'name');

        // Таблица для ролей и разрешений
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->addPrimaryKey('pk_auth_item_name', '{{%auth_item}}', 'name');
        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', 'type');
        $this->addForeignKey(
            'fk_auth_item_rule_name',
            '{{%auth_item}}',
            'rule_name',
            '{{%auth_rule}}',
            'name',
            'SET NULL',
            'CASCADE'
        );

        // Таблица для иерархии
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ]);

        $this->addPrimaryKey('pk_auth_item_child', '{{%auth_item_child}}', ['parent', 'child']);
        $this->addForeignKey(
            'fk_auth_item_child_parent',
            '{{%auth_item_child}}',
            'parent',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_item_child_child',
            '{{%auth_item_child}}',
            'child',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Таблица для назначений
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer()->notNull(),
            'created_at' => $this->integer(),
        ]);

        $this->addPrimaryKey('pk_auth_assignment', '{{%auth_assignment}}', ['item_name', 'user_id']);
        $this->addForeignKey(
            'fk_auth_assignment_item_name',
            '{{%auth_assignment}}',
            'item_name',
            '{{%auth_item}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_assignment_user_id',
            '{{%auth_assignment}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_auth_assignment_user_id', '{{%auth_assignment}}');
        $this->dropForeignKey('fk_auth_assignment_item_name', '{{%auth_assignment}}');
        $this->dropTable('{{%auth_assignment}}');

        $this->dropForeignKey('fk_auth_item_child_child', '{{%auth_item_child}}');
        $this->dropForeignKey('fk_auth_item_child_parent', '{{%auth_item_child}}');
        $this->dropTable('{{%auth_item_child}}');

        $this->dropForeignKey('fk_auth_item_rule_name', '{{%auth_item}}');
        $this->dropTable('{{%auth_item}}');

        $this->dropTable('{{%auth_rule}}');
    }
}
