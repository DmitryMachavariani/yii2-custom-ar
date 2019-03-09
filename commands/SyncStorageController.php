<?php

namespace app\commands;

use app\components\FileHelper;
use app\models\Files;
use app\models\Tasks;
use yii\console\Controller;

/**
 * Class SyncStorageController.
 *
 * @package app\commands
 */
class SyncStorageController extends Controller
{
    public function actionIndex()
    {
        $tasks = Tasks::find()->all();

        foreach ($tasks as $task) {
            /** @var Files[] $files */
            $files = $task->attachments;
            foreach ($files as $file) {
                $filePath = $file->getFullPath();
                if (!file_exists($filePath)) {
                    $file->delete();
                    continue;
                }
                if (\Yii::$app->storage->save($filePath)) {
                    @unlink($filePath);
                }
            }
        }
    }
}
