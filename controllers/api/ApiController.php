<?php

namespace app\controllers\api;

use yii\web\UnauthorizedHttpException;
use yii\filters\ContentNegotiator;
use app\dto\api\ApiResponseDto;
use yii\rest\Controller;
use yii\web\Response;
use Yii;

/**
 * Базовый API Controller
 */
abstract class ApiController extends Controller
{
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

    public function beforeAction($action)
    {
        try {
            $this->checkApiToken();
        } catch (UnauthorizedHttpException $e) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->end(json_encode($this->error($e->getMessage(), 401)));
        }
        return parent::beforeAction($action);
    }

    /**
     * Проверить токен (по заголовку)
     * @throws UnauthorizedHttpException
     */
    protected function checkApiToken(): void
    {
        $token = Yii::$app->request->headers->get('X-Api-Token');

        if (!$token || $token !== Yii::$app->params['xApiToken']) {
            throw new UnauthorizedHttpException('Неверный апи токен!');
        }
    }

    /** Успешный ответ */
    protected function success(array $data): array
    {
        return ApiResponseDto::success($data);
    }

    /** Ответ ошибкой */
    protected function error(string $errorMessage, int $code = 400, bool $isLog = false): array
    {
        if ($isLog) {
            Yii::error($errorMessage);
        }
        Yii::$app->response->statusCode = $code;
        return ApiResponseDto::error('Ошибка: ' . $errorMessage);
    }
}
