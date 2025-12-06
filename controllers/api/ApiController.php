<?php

namespace app\controllers\api;

use app\dto\api\ApiResponseDto;
use yii\rest\Controller;
use Yii;

/**
 * Базовый API Controller
 */
abstract class ApiController extends Controller
{
    protected function success(array $data): array
    {
        return ApiResponseDto::success($data);
    }

    protected function error(string $errorMessage, int $code = 400, bool $isLog = false): array
    {
        if ($isLog) {
            Yii::error($errorMessage);
        }
        Yii::$app->response->statusCode = $code;
        return ApiResponseDto::error('Ошибка сервера: ' . $errorMessage);
    }
}
