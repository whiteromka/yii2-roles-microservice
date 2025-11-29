<?php

namespace app\dto\api;

/**
 * DTO для ответа по API
 */
class ApiResponseDto
{
    public bool $success = true;
    public array $data = [];
    public ?string $error = null;

    public static function success(array $data): array
    {
        $dto = new self();
        $dto->data = $data;
        return $dto->toArray();
    }

    public static function error(string $error): array
    {
        $dto = new self();
        $dto->success = false;
        $dto->error = $error;
        return $dto->toArray();
    }

    private function toArray()
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'error' => $this->error,
        ];
    }
}
