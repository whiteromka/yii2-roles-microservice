<?php

use yii\db\Migration;

class m251127_191836_init_rbac_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // Создаем разрешения (permissions)
        $viewAdminPanel = $auth->createPermission('viewAdminPanel');
        $viewAdminPanel->description = 'Просмотр админ панели';
        $auth->add($viewAdminPanel);

        $manageUsers = $auth->createPermission('manageUsers');
        $manageUsers->description = 'Управление пользователями';
        $auth->add($manageUsers);

        $manageRoles = $auth->createPermission('manageRoles');
        $manageRoles->description = 'Управление ролями и разрешениями';
        $auth->add($manageRoles);

        $manageContent = $auth->createPermission('manageContent');
        $manageContent->description = 'Управление контентом';
        $auth->add($manageContent);

        $viewAuditLogs = $auth->createPermission('viewAuditLogs');
        $viewAuditLogs->description = 'Просмотр логов аудита';
        $auth->add($viewAuditLogs);

        // Создаем роли (roles)
        $user = $auth->createRole('user');
        $user->description = 'Обычный пользователь';
        $auth->add($user);

        $moderator = $auth->createRole('moderator');
        $moderator->description = 'Модератор';
        $auth->add($moderator);
        $auth->addChild($moderator, $viewAdminPanel);
        $auth->addChild($moderator, $manageContent);
        $auth->addChild($moderator, $viewAuditLogs);

        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);
        $auth->addChild($admin, $moderator);
        $auth->addChild($admin, $manageUsers);
        $auth->addChild($admin, $manageRoles);

        $superAdmin = $auth->createRole('superAdmin');
        $superAdmin->description = 'Супер администратор';
        $auth->add($superAdmin);
        $auth->addChild($superAdmin, $admin);

        // Назначаем роль superAdmin для первого пользователя (ID=1)
        if ($this->db->schema->getTableSchema('{{%user}}') !== null) {
            $userExists = $this->db->createCommand('SELECT 1 FROM {{%user}} WHERE id = 1')->queryScalar();
            if ($userExists) {
                $auth->assign($superAdmin, 1);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
