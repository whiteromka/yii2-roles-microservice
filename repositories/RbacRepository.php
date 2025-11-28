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
            return $data ?: [];
        } catch (Exception $e) {
            Yii::error('Ошибка при получении ролей и разрешений пользователя: ' . $e->getMessage());
            return [];
        }
    }
}
