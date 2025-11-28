<?php

namespace app\services;

use app\repositories\RbacRepository;
use app\models\AuthItem;
use yii\base\Component;
use Yii;

class RbacService extends Component
{
    private RbacRepository $rbacRepository;
    public const CACHE_PREFIX_KEY = 'roles_and_permissions_';

    public function __construct(
        RbacRepository $rbacRepository = null,
        $config = []
    ) {
        $this->rbacRepository = $rbacRepository ?: Yii::createObject(RbacRepository::class);
        parent::__construct($config);
    }

    private function getCacheDuration(): int
    {
        return YII_ENV === 'dev' ? 10 : 86400;
    }

    /** Получить все роли и разрешения  */
    public function getRolesAndPermissionsByUserId(int $userId, bool $useCache = true): array
    {
        if (!$useCache) {
            $result = $this->rbacRepository->getRolesAndPermissionsByUserId($userId);
            return $this->sort($result);
        }

        $cacheKey = self::CACHE_PREFIX_KEY . $userId;
        return Yii::$app->cache->getOrSet($cacheKey, function() use ($userId) {
            $result = $this->rbacRepository->getRolesAndPermissionsByUserId($userId);
            return $this->sort($result);
        }, $this->getCacheDuration());
    }

    /** Отсортировать роли и разрешения */
    private function sort(array $rolesAndPermissions = []): array
    {
        $result = [
            'roles' => [],
            'permissions' => []
        ];
        if (empty($rolesAndPermissions)) {
            return $result;
        }

        foreach($rolesAndPermissions as $authItem) {
            if (!empty($authItem['type']) && $authItem['type'] === AuthItem::TYPE_ROLE) {
                $result['roles'][] = $authItem['name'];
            } else {
                $result['permissions'][] = $authItem['name'];
            }
        }
        return $result;
    }
}
