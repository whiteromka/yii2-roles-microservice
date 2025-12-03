<?php

namespace app\controllers\api;

use app\dto\api\ApiResponseDto;
use app\repositories\UserRepository;
use Exception;
use Yii;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use app\services\RbacService;

class RbacController extends ApiController
{
    private RbacService $rbacService;
    private UserRepository $userRepository;

    public function __construct(
        $id,
        $module,
        RbacService $rbacService,
        UserRepository $serviceRepository,
        $config = []
    ) {
        $this->rbacService = $rbacService;
        $this->userRepository = $serviceRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * Получить все роли и разрешения пользователя
     * GET api/rbac/user-permissions/1
     *
     * @param int $userId // тут будет внешний ID
     * @return array
     */
    public function actionUserPermissions(int $userId): array
    {
        try {
            $user = $this->userRepository->findByExternalId($userId);
            if (!$user) {
                return $this->error("Пользователь с ID {$userId} не найден", 404);
            }

            $data = $this->rbacService->getRolesAndPermissionsByUserId($user->id);
            return ApiResponseDto::success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }

    /**
     * Получить все роли и разрешения в системе
     * GET api/rbac/all-user-permissions
     *
     * @return array
     */
    public function actionAllUserPermissions(): array
    {
        try {
            $data = $this->rbacService->getAllRolesAndPermissions();
            return ApiResponseDto::success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }

    /**
     * Добавить роли и разрешения
     * POST api/rbac/add-roles-and-permissions
     * {
     *   "user_id": 1, // Это внешний ID
     *   "roles": ['admin', '...'],
     *   "permissions": ['viewQuestions', '...']
     * }
     * @return array
     */
    public function actionAddRolesAndPermissions(): array
    {
        try {
            $postData = json_decode(Yii::$app->request->getRawBody(), true);
            $user = $this->userRepository->findByExternalId($postData['user_id'] ?? 0);
            if (!$user) {
                return $this->error("Пользователь с ID {$postData['user_id']} не найден", 404);
            }
            $postData['user_id'] = $user->id;
            $data = $this->rbacService->addRolesAndPermissions($postData);
            return ApiResponseDto::success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }
}
