<?php

namespace app\controllers\api;

use app\models\Service;
use app\repositories\ServiceRepository;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use Yii;

class ServiceController extends ApiController
{
    private ServiceRepository $serviceRepository;

    public function __construct($id, $module, ServiceRepository $serviceRepository, $config = [])
    {
        $this->serviceRepository = $serviceRepository;
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
                    'view' => ['GET'],
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Получить сервис
     * GET api/service/view/1
     */
    public function actionView(int $id): array
    {
        $service = $this->serviceRepository->findOne($id);

        if (!$service) {
            return $this->error("Сервис с ID {$id} не найден", 404);
        }

        return $this->success($service->toArray());
    }

    /**
     * Создать сервис
     * POST api/service/create
     * {
     *   "name": "My Service",
     *   "host": "https://example.com",
     *   "descr": "Optional"
     * }
     */
    public function actionCreate(): array
    {
        $data = json_decode(Yii::$app->request->getRawBody(), true);

        if (!is_array($data)) {
            return $this->error("Некорректный JSON");
        }

        $service = new Service();
        if ($service->load($data, '') && $service->save()) {
            return $this->success($service->toArray());
        }
        return $this->error($service->getError());
    }
}
