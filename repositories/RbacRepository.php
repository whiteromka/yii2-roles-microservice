<?php

namespace app\repositories;

use app\models\AuthItem;
use Exception;
use Yii;

class RbacRepository
{
    /** Получить все названия ролей из БД */
    public function getRoles(): array
    {
        return AuthItem::find()->asArray()->select(['name'])->where(['type' => AuthItem::TYPE_ROLE])->all();
    }

    /** Получить все названия разрешений из БД */
    public function getPermissions(): array
    {
        return AuthItem::find()->asArray()->select(['name'])->where(['type' => AuthItem::TYPE_PERMISSION])->all();
    }

    /** Получить все роли и разрешения рекурсивно для $userExternalId */
    public function getRolesAndPermissionsByUserId(int $userExternalId): array
    {
        $sql = <<<SQL
            WITH RECURSIVE permission_hierarchy AS (
                SELECT
                    ai.name,
                    ai.type
                FROM auth_item ai
                    INNER JOIN auth_assignment aa ON aa.item_name = ai.name
                    INNER JOIN "user" u ON u.id = aa.user_id
                WHERE u.external_id = :userExternalId
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
            $data = Yii::$app->db->createCommand($sql, [':userExternalId' => $userExternalId])->queryAll();
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
}
