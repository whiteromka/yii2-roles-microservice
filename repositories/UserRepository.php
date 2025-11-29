<?php

namespace app\repositories;

use app\models\User;

class UserRepository
{
    private ServiceRepository $serviceRepository;

    public function __construct()
    {
        $this->serviceRepository = new ServiceRepository();
    }

    /** Создать пользователя */
    public function createUser(array $data): User|bool
    {
        if (!empty($data['service_name'])) {
            return $this->createUserWithServiceName($data);
        }

        if (!empty($data['service_id'])) {
            return $this->createUserWithServiceId($data);
        }

        return false;
    }

    /** Создать пользователя с service_name */
    public function createUserWithServiceName(array $data): User|bool
    {
        $service = $this->serviceRepository->findByName($data['service_name']);
        if (!empty($service)) {
            $user = new User([
                'external_id' => $data['id'],
                'service_id' => $service->id,
                'name' => $data['username'],
                'lastname' => $data['lastname'],
                'email' => $data['email'],
                'status' => $data['status'] ?? 1
            ]);
            $user->save();
            return $user;
        } else {
            return false;
        }
    }

    /** Создать пользователя с service_id */
    public function createUserWithServiceId(array $data): User
    {
        $user = new User([
            'external_id' => $data['id'],
            'service_id' => $data['service_id'],
            'name' => $data['username'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'status' => $data['status'] ?? 1
        ]);
        $user->save();
        return $user;
    }
}
