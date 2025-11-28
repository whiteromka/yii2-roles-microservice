<?php

namespace app\controllers\api;

use Exception;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use app\services\RbacService;
use app\dto\api\RbacResponseDto;

/**
 * RBAC API Controller
 */
class RbacController extends Controller
{
    private RbacService $rbacService;

    public function __construct(
        $id,
        $module,
        $config = [],
        RbacService $rbacService = null
    ) {
        $this->rbacService = $rbacService ?: Yii::createObject(RbacService::class);
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
     *
     * @param int $userId
     * @return array
     */
    public function actionUserPermissions(int $userId): array
    {
        try {
            $data = $this->rbacService->getRolesAndPermissionsByUserId($userId);
            return RbacResponseDto::success($data);
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $this->response->statusCode = 500;
            return RbacResponseDto::error('Ошибка сервера: ' . $e);
        }
    }

    /**
     * Проверить конкретное разрешение у пользователя
     *
     * @param int $userId
     * @param string $permission
     * @return array
     */
    public function actionCheckPermission(int $userId, string $permission): array
    {
        return []; // ToDo дописать
    }
}
