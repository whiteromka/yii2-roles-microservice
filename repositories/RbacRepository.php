<?php

namespace app\repositories;

use app\models\AuthItem;
use app\services\RbacService;
use Exception;
use Yii;

class RbacRepository
{
    /** Получить все названия ролей из БД */
    public function getRoles(): array
    {
        return AuthItem::find()->select(['name'])->where(['type' => AuthItem::TYPE_ROLE])->column();
    }

    /** Получить все названия разрешений из БД */
    public function getPermissions(): array
    {
        return AuthItem::find()->select(['name'])->where(['type' => AuthItem::TYPE_PERMISSION])->column();
    }

    /** Получить все роли и разрешения рекурсивно для $userId */
    public function getRolesAndPermissionsByUserId(int $userId): array
    {
        $sql = <<<SQL
            WITH RECURSIVE permission_hierarchy AS (
                SELECT
                    ai.name,
                    ai.type
                FROM auth_item ai
                    INNER JOIN auth_assignment aa ON aa.item_name = ai.name
                WHERE aa.user_id = :userId
                UNION ALL
                SELECT
                    child.name,
                    child.type
                FROM permission_hierarchy parent
                    INNER JOIN auth_item_child aic ON parent.name = aic.parent
                    INNER JOIN auth_item child ON aic.child = child.name
            )
            SELECT DISTINCT name, type
            FROM permission_hierarchy
            ORDER BY name;
        SQL;

        try {
            $data = Yii::$app->db->createCommand($sql, [':userId' => $userId])->queryAll();
            return $this->sort($data);
        } catch (Exception $e) {
            Yii::error('Ошибка при получении ролей и разрешений пользователя: ' . $e->getMessage());
            return [];
        }
    }

    /** Отсортировать роли и разрешения по разным массивам */
    private function sort(array $rolesAndPermissions = []): array
    {
        $result = [
            'roles' => [],
            'permissions' => []
        ];

        foreach ($rolesAndPermissions as $item) {
            $key = $item['type'] === AuthItem::TYPE_ROLE ? 'roles' : 'permissions';
            $result[$key][] = $item['name'];
        }

        return $result;
    }

    /**
     * Добавить роли и разрешения к пользователю
     * @var array $data
     * {
     *   "userId": 1,
     *   "rolesAndPermissions": ['...'],
     * }
     */
    public function addRolesAndPermissions(array $data): void
    {
        $rows = [];
        $timestamp = time();
        foreach ($data['rolesAndPermissions'] as $item) {
            $rows[] = [
                $item,
                $data['userId'],
                $timestamp,
            ];
        }

        try {
            Yii::$app->db->createCommand()->batchInsert(
                'auth_assignment',
                ['item_name', 'user_id', 'created_at'],
                $rows
            )->execute();
            RbacService::invalidateUserCache($data['userId']);
        } catch (Exception $e) {
            Yii::error('Ошибка при обновлении RBAC: ' . $e->getMessage());
        }
    }
}
