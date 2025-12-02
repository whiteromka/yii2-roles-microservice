<?php

namespace app\controllers\api;

use app\dto\api\ApiResponseDto;
use app\models\User;
use app\repositories\UserRepository;
use Exception;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;

class UserController extends ApiController
{
    private UserRepository $userRepository;

    public function __construct($id, $module, UserRepository $serviceRepository, $config = [])
    {
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
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Получить пользователя
     * GET api/user/view/1
     */
    public function actionView(int $id): array
    {
        $user = $this->userRepository->findByExternalId($id);
        if (!$user) {
            return $this->error("Сервис ID={$id} не найден", 404);
        }

        return ApiResponseDto::success($user->toArray());
    }

    /**
     * Создать нового пользователя
     * POST api/rbac/create-user
     * {
     *   "name": "Test",
     *   "last_name": "Testov",
     *   "external_id": 12,
     *   "service_id": 1,              // это свойство необязательно при наличии service_name
     *   "service_name": "it-question" // это свойство необязательно при наличии service_id
     *   "status": 1,                  // это свойство необязательно
     *   "email": "test@testov.ru"     // это свойство необязательно
     * }
     *
     * @return array
     */
    public function actionCreate(): array
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
}
