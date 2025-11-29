<?php

namespace app\services;

use app\repositories\RbacRepository;
use yii\base\Component;
use yii\caching\TagDependency;
use Yii;

class RbacService extends Component
{
    public const CACHE_PREFIX_KEY = 'roles_and_permissions_';
    public const CACHE_TAG_PREFIX = 'user_rbac_';

    private RbacRepository $rbacRepository;

    public function __construct(
        ?RbacRepository $rbacRepository = null,
        $config = []
    ) {
        $this->rbacRepository = $rbacRepository ?: Yii::createObject(RbacRepository::class);
        parent::__construct($config);
    }

    private function getCacheDuration(): int
    {
        return YII_ENV === 'dev' ? 10 : 86400; // 1 день
    }

    /** Получить все роли и разрешения  */
    public function getRolesAndPermissionsByUserId(int $userId): array
    {
        return Yii::$app->cache->getOrSet(
            self::CACHE_PREFIX_KEY . $userId,
            function() use ($userId) {
                return $this->rbacRepository->getRolesAndPermissionsByUserId($userId);
            },
            $this->getCacheDuration(),
            new TagDependency(['tags' => self::CACHE_TAG_PREFIX . $userId])
        );
    }

    /** Сбросить кэш для пользователя $userId */
    public function invalidateUserCache(int $userId): void
    {
        TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_PREFIX . $userId);
    }
}
