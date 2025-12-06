<?php

namespace app\services;

use api\models\forms\UserCreateForm;
use app\models\User;
use app\repositories\ServiceRepository;
use app\repositories\UserRepository;
use Exception;

class UserService
{
    public function __construct(
        readonly private UserRepository $userRepository,
        readonly private ServiceRepository $serviceRepository
    ) {}

    /**
     * @throws Exception
     */
    public function create(UserCreateForm $form): User
    {
        $serviceId = $form->service_id;
        if (!$serviceId && $form->service_name) {
            $service = $this->serviceRepository->findByName($form->service_name);
            if (!$service) {
                throw new Exception('Сервис не найден');
            }
            $form->service_id = $service->id;
        }

        if ($this->userRepository->findByExternalIdAndService($form->external_id, $form->service_id)) {
            throw new Exception('Пользователь уже существует');
        }

        return $this->userRepository->create($form);
    }
}
