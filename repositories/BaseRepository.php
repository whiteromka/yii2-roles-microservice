<?php

namespace app\repositories;

use yii\db\ActiveRecord;

abstract class BaseRepository
{
    /**
     * @return class-string<ActiveRecord>
     */
    abstract protected function modelClass(): string;

    public function findOne(int $id): ?ActiveRecord
    {
        $class = $this->modelClass();
        return $class::findOne($id);
    }

    public function findByName(string $name): ?ActiveRecord
    {
        $class = $this->modelClass();
        return $class::findOne(['name' => $name]);
    }
}
