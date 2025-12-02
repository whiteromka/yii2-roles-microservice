<?php

namespace app\services;

use app\repositories\RbacRepository;
use app\repositories\UserRepository;
use yii\base\Component;
use yii\caching\TagDependency;
use Yii;

class RbacService extends Component
{
    public const CACHE_PREFIX_KEY = 'roles_and_permissions_';
    public const CACHE_TAG_PREFIX = 'user_rbac_';

    private RbacRepository $rbacRepository;
    private UserRepository $userRepository;

    public function __construct(
        RbacRepository $rbacRepository,
        UserRepository $userRepository,
        $config = []
    ) {
        parent::__construct($config);
        $this->rbacRepository = $rbacRepository;
        $this->userRepository = $userRepository;
    }

    private function getCacheDuration(): int
    {
        return YII_ENV === 'dev' ? 10 : 86400; // 1 день
    }

    /** Получить все роли и разрешения для пользователя $userId */
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

    /** ToDo дописать как будет обновление ролей и разрешений пользователя */
    /** Сбросить кэш для пользователя $userId */
    public static function invalidateUserCache(int $userId): void
    {
        TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_PREFIX . $userId);
    }

    /** Получить все роли и разрешения */
    public function getAllRolesAndPermissions(): array
    {
        return [
            'roles' => $this->rbacRepository->getRoles(),
            'permissions' => $this->rbacRepository->getPermissions()
        ];
    }

    /**
    * @var array $data
    * {
    *   "user_id": 1,
    *   "roles": ['admin', '...'],
    *   "permission": ['viewQuestions', '...']
    * }
    */
    public function addRolesAndPermissions(array $data): array
    {
        $user = $this->userRepository->findByExternalId($data['user_id']);
        $roles = $data['roles'] ?? [];
        $permissions = $data['permissions'] ?? [];
        $newItems = array_merge($roles, $permissions);

        $existingData = $this->getRolesAndPermissionsByUserId($user->id);
        $existingItems = array_merge($existingData['roles'], $existingData['permission']);

        $data = [
            'userId' => $user->id,
            'newItems' => $newItems,
            'existingItems' => $existingItems
        ];
        return $this->rbacRepository->addRolesAndPermissions($data);
    }
}
