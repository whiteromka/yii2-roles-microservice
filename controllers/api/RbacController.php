<?php

namespace app\controllers\api;

use app\models\forms\RolesAndPermissionsForm;
use app\models\forms\UserForm;
use app\repositories\UserRepository;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use app\services\RbacService;
use Exception;
use Yii;

class RbacController extends ApiController
{
    private RbacService $rbacService;
    private UserRepository $userRepository;

    public function __construct(
        $id,
        $module,
        RbacService $rbacService,
        UserRepository $userRepository,
        $config = []
    ) {
        $this->rbacService = $rbacService;
        $this->userRepository = $userRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * Получить все роли и разрешения пользователя
     * GET api/rbac/user-permissions/1/1
     *
     * @param int $externalId
     * @param int $serviceId
     * @return array
     */
    public function actionUserPermissions(int $externalId, int $serviceId): array
    {
        $form = new UserForm([
            'external_id' => $externalId,
            'service_id'  => $serviceId,
        ]);
        try {
            if ($form->validate()) {
                $data = $this->rbacService->getRolesAndPermissionsByUserId($form->user_id);
                return $this->success($data);
            } else {
                return $this->error($form->getError());
            }
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
        $form = new RolesAndPermissionsForm();
        try {
            if ($form->load(Yii::$app->request->getBodyParams(), '') && $form->validate()) {
                $this->rbacService->addRolesAndPermissions($form);
                return $this->success($form->toArray());
            } else {
                return $this->error($form->getErrorsAsString());
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }
}
