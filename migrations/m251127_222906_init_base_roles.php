<?php

use yii\db\Migration;

class m251127_222906_init_base_roles extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        // СОЗДАЕМ РАЗРЕШЕНИЯ

        // Разрешения для вопросов
        $viewQuestions = $auth->createPermission('viewQuestions');
        $viewQuestions->description = 'Просмотр вопросов';
        $auth->add($viewQuestions);

        $createQuestion = $auth->createPermission('createQuestion');
        $createQuestion->description = 'Создание вопросов';
        $auth->add($createQuestion);

        $updateOwnQuestion = $auth->createPermission('updateOwnQuestion');
        $updateOwnQuestion->description = 'Редактирование своих вопросов';
        $auth->add($updateOwnQuestion);

        $updateAnyQuestion = $auth->createPermission('updateAnyQuestion');
        $updateAnyQuestion->description = 'Редактирование любых вопросов';
        $auth->add($updateAnyQuestion);

        $deleteOwnQuestion = $auth->createPermission('deleteOwnQuestion');
        $deleteOwnQuestion->description = 'Удаление своих вопросов';
        $auth->add($deleteOwnQuestion);

        $deleteAnyQuestion = $auth->createPermission('deleteAnyQuestion');
        $deleteAnyQuestion->description = 'Удаление любых вопросов';
        $auth->add($deleteAnyQuestion);

        // Разрешения для ответов
        $viewAnswers = $auth->createPermission('viewAnswers');
        $viewAnswers->description = 'Просмотр ответов';
        $auth->add($viewAnswers);

        $createAnswer = $auth->createPermission('createAnswer');
        $createAnswer->description = 'Создание ответов';
        $auth->add($createAnswer);

        $updateOwnAnswer = $auth->createPermission('updateOwnAnswer');
        $updateOwnAnswer->description = 'Редактирование своих ответов';
        $auth->add($updateOwnAnswer);

        $updateAnyAnswer = $auth->createPermission('updateAnyAnswer');
        $updateAnyAnswer->description = 'Редактирование любых ответов';
        $auth->add($updateAnyAnswer);

        $deleteOwnAnswer = $auth->createPermission('deleteOwnAnswer');
        $deleteOwnAnswer->description = 'Удаление своих ответов';
        $auth->add($deleteOwnAnswer);

        $deleteAnyAnswer = $auth->createPermission('deleteAnyAnswer');
        $deleteAnyAnswer->description = 'Удаление любых ответов';
        $auth->add($deleteAnyAnswer);

        // Разрешения для категорий
        $viewCategories = $auth->createPermission('viewCategories');
        $viewCategories->description = 'Просмотр категорий';
        $auth->add($viewCategories);

        $createCategory = $auth->createPermission('createCategory');
        $createCategory->description = 'Создание категорий';
        $auth->add($createCategory);

        $updateCategory = $auth->createPermission('updateCategory');
        $updateCategory->description = 'Редактирование категорий';
        $auth->add($updateCategory);

        $deleteCategory = $auth->createPermission('deleteCategory');
        $deleteCategory->description = 'Удаление категорий';
        $auth->add($deleteCategory);

        // Разрешения для тегов
        $viewTags = $auth->createPermission('viewTags');
        $viewTags->description = 'Просмотр тегов';
        $auth->add($viewTags);

        $createTag = $auth->createPermission('createTag');
        $createTag->description = 'Создание тегов';
        $auth->add($createTag);

        $updateTag = $auth->createPermission('updateTag');
        $updateTag->description = 'Редактирование тегов';
        $auth->add($updateTag);

        $deleteTag = $auth->createPermission('deleteTag');
        $deleteTag->description = 'Удаление тегов';
        $auth->add($deleteTag);

        // СОЗДАЕМ РОЛИ

        // Роль: Пользователь
        $user = $auth->createRole('user');
        $user->description = 'Обычный пользователь';
        $auth->add($user);

        // Роль: Админ
        $admin = $auth->createRole('admin');
        $admin->description = 'Администратор';
        $auth->add($admin);

        // НАСТРАИВАЕМ ИЕРАРХИЮ

        // Пользователь наследует базовые разрешения на просмотр
        $auth->addChild($user, $viewQuestions);
        $auth->addChild($user, $viewAnswers);
        $auth->addChild($user, $viewCategories);
        $auth->addChild($user, $viewTags);

        // Пользователь может CRUD свои вопросы
        $auth->addChild($user, $createQuestion);
        $auth->addChild($user, $updateOwnQuestion);
        $auth->addChild($user, $deleteOwnQuestion);

        // Пользователь может CRUD свои ответы
        $auth->addChild($user, $createAnswer);
        $auth->addChild($user, $updateOwnAnswer);
        $auth->addChild($user, $deleteOwnAnswer);

        // Админ наследует все права пользователя
        $auth->addChild($admin, $user);

        // Админ может CRUD любые вопросы
        $auth->addChild($admin, $updateAnyQuestion);
        $auth->addChild($admin, $deleteAnyQuestion);

        // Админ может CRUD любые ответы
        $auth->addChild($admin, $updateAnyAnswer);
        $auth->addChild($admin, $deleteAnyAnswer);

        // Админ может CRUD категории
        $auth->addChild($admin, $createCategory);
        $auth->addChild($admin, $updateCategory);
        $auth->addChild($admin, $deleteCategory);

        // Админ может CRUD теги
        $auth->addChild($admin, $createTag);
        $auth->addChild($admin, $updateTag);
        $auth->addChild($admin, $deleteTag);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}
