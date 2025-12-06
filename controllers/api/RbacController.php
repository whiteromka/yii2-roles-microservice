<?php

namespace app\controllers\api;

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
     * GET api/rbac/user-permissions/1/1
     *
     * @param int $externalId // тут будет внешний ID
     * @param int $serviceId
     * @return array
     */
    public function actionUserPermissions(int $externalId, int $serviceId): array
    {
        try {
            $user = $this->userRepository->findByExternalIdAndService($externalId, $serviceId);
            if (!$user) {
                return $this->error("Пользователь с ID {$externalId} не найден", 404);
            }

            $data = $this->rbacService->getRolesAndPermissionsByUserId($user->id);
            return $this->success($data);
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
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }

    /**
     * Добавить роли и разрешения
     * POST api/rbac/add-roles-and-permissions
     * {
     *   "external_id": 1,
     *   "service_id": 1,
     *   "roles": ['admin', '...'],
     *   "permissions": ['viewQuestions', '...']
     * }
     * @return array
     */
    public function actionAddRolesAndPermissions(): array
    {
        try {
            $postData = json_decode(Yii::$app->request->getRawBody(), true);

            $user = $this->userRepository->findByExternalIdAndService($postData['external_id'], $postData['service_id']);
            if (!$user) {
                return $this->error("Пользователь с ID {$postData['external_id']} не найден", 404);
            }

            $postData['external_id'] = $user->id;
            $data = $this->rbacService->addRolesAndPermissions($postData);
            return $this->success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }
}
