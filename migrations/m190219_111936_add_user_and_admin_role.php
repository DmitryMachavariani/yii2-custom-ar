<?php

use yii\db\Migration;

/**
 * Class m190219_111936_add_user_and_admin_role
 */
class m190219_111936_add_user_and_admin_role extends Migration
{
    /**
     * @return bool|void
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        $itemUser = new \app\models\AuthItem();
        $itemUser->name = 'user';
        $itemUser->type = 1;
        $itemUser->description = 'Простой пользователь';
        $itemUser->save(false);

        $itemAdmin = new \app\models\AuthItem();
        $itemAdmin->name = 'admin';
        $itemAdmin->type = 1;
        $itemAdmin->description = 'Администратор';
        $itemAdmin->save(false);

        $username = 'admin';
        $password = '123456';
        $passwordHash = Yii::$app->security->generatePasswordHash($password);

        $user = new \app\models\Users();
        $user->username = $username;
        $user->password = $passwordHash;
        $user->status = \app\models\Users::STATUS_ADMIN;
        $user->save(false);

        $role = Yii::$app->authManager->getRole('admin');
        if ($role) {
            Yii::$app->authManager->assign($role, $user->id);
        }
    }

    public function safeDown()
    {
        \app\models\AuthItem::deleteAll(['name' => 'user']);
        \app\models\AuthItem::deleteAll(['name' => 'admin']);

        \app\models\Users::deleteAll(['username' => 'admin']);
    }
}
