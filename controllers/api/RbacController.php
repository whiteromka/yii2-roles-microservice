<?php

namespace app\controllers\api;

use app\dto\api\ApiResponseDto;
use Exception;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use app\services\RbacService;

class RbacController extends ApiController
{
    private RbacService $rbacService;

    public function __construct(
        $id,
        $module,
        RbacService $rbacService,
        $config = []
    ) {
        $this->rbacService = $rbacService;
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
     * @param int $userId
     * @return array
     */
    public function actionUserPermissions(int $userId): array
    {
        try {
            $data = $this->rbacService->getRolesAndPermissionsByUserId($userId);
            return ApiResponseDto::success($data);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }
}
