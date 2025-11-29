<?php

namespace app\controllers\api;

use app\dto\api\ApiResponseDto;
use app\models\User;
use Exception;
use Yii;
use yii\filters\VerbFilter;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use app\services\RbacService;

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
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create-user' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * GET Получить все роли и разрешения пользователя
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

    /**
     * POST Создать нового пользователя
     *
     * @return array
     */
    public function actionCreateUser(): array
    {
        try {
            $rawBody = Yii::$app->getRequest()->getRawBody();
            $data = json_decode($rawBody, true);

            $user = new User();
            if ($user->load($data, '') && $user->save()) {
                return ApiResponseDto::success($user->toArray());
            } else {
                return $this->error($user->getError());
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }

    private function error(string $errorMessage, int $code = 400, bool $isLog = false): array
    {
        if ($isLog) {
            Yii::error($errorMessage);
        }
        $this->response->statusCode = $code;
        return ApiResponseDto::error('Ошибка сервера: ' . $errorMessage);
    }
}
