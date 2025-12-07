<?php

namespace app\services;

use app\models\forms\RolesAndPermissionsForm;
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

    /**
     * Получить все роли и разрешения для пользователя $userId
     *
     * @param int $userId // Внутренний ID
     * @return array []
     */
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

    /**
     * Сбросить кэш для пользователя $userId
     *
     * @param int $userId // Внутренний ID
     */
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

    /** Добавить еще ролей и разрешений */
    public function addRolesAndPermissions(RolesAndPermissionsForm $form): void
    {
        $existingData = $this->getRolesAndPermissionsByUserId($form->user_id);
        $newItems = array_merge($form->roles, $form->permissions);
        $existingItems = array_merge($existingData['roles'], $existingData['permissions']);
        $rolesAndPermissions = array_diff($newItems, $existingItems);
        if (empty($rolesAndPermissions)) {
            return;
        }

        $data = [
            'userId' => $form->user_id,
            'rolesAndPermissions' => $rolesAndPermissions
        ];
        $this->rbacRepository->addRolesAndPermissions($data);
    }
}
