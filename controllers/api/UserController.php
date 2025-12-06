<?php

namespace app\controllers\api;

use api\models\forms\UserCreateForm;
use app\repositories\UserRepository;
use app\services\UserService;
use Exception;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;

class UserController extends ApiController
{
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(
        $id,
        $module,
        UserRepository $serviceRepository,
        UserService $userService,
        $config = []
    )
    {
        $this->userRepository = $serviceRepository;
        $this->userService = $userService;
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
     * GET api/user/view/1/2
     *
     * @param int $externalId
     * @param int $serviceId
     * @return array
     */
    public function actionView(int $externalId, int $serviceId): array
    {
        $user = $this->userRepository->findByExternalIdAndService($externalId, $serviceId);
        if (!$user) {
            return $this->error("Пользователь с ID {$externalId} не найден", 404);
        }

        return $this->success($user->toArray());
    }

    /**
     * Создать нового пользователя
     * POST api/user/create-user
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
     * @throws InvalidConfigException
     */
    public function actionCreate(): array
    {
        $form = new UserCreateForm();
        $form->load(Yii::$app->request->getBodyParams(), '');
        if (!$form->validate()) {
            return $this->error($form->getError());
        }

        try {
            $user = $this->userService->create($form);
            return $this->success($user->toArray());
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500, true);
        }
    }
}
