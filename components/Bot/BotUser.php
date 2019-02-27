<?php

namespace app\components\Bot;

use app\models\Actions;
use app\models\AuthItem;
use app\models\Dependencies;
use yii\helpers\ArrayHelper;

/**
 * Class BotUser.
 *
 * @package app\components\Bot
 */
class BotUser
{
    const ACTION_START_CONTACT = 'contractStart';

    const ACTION_START_BUILDING = 'startBuilding';
    const ACTION_END_BUILDING = 'endBuilding';

    const ACTION_START_AR_AS = 'arAsStart';
    const ACTION_END_AR_AS = 'arAsEnd';
    const ACTION_START_IOS = 'iosStart';
    const ACTION_END_IOS = 'iosEnd';

    const ACTION_BUILD_FINISH_COMIMSSION = 'buildfinishcommissionStart';
    const ACTION_BUILD_FINISH_CLIENT = 'buildfinishclientStart';

    const ACTION_START_FOUNDATION = 'foundationStart';
    const ACTION_END_FOUNDATION = 'foundationEnd';
    const ACTION_START_SHELL= 'shellStart'; // коробка
    const ACTION_END_SHELL= 'shellEnd'; // коробка
    const ACTION_START_WINDOWS = 'windowsStart';
    const ACTION_END_WINDOWS = 'windowsEnd';
    const ACTION_START_DECORATION = 'decorationStart';
    const ACTION_END_DECORATION = 'decorationEnd';
    const ACTION_END_OBJECT = 'objectEnd';

    const ACTION_REPORT_PROBLEM = 'reportProblem';
    const ACTION_PROJECT_DOC = 'projectDoc';

    /**
     * @param null|string $key
     *
     * @param bool        $restrict
     *
     * @return array|mixed
     */
    public static function getActions($key = null, $restrict = false)
    {
        $data = [];
        $models = AuthItem::find()
            ->joinWith(['userTypeActions.action'])
            ->asArray()
            ->all();

        foreach ($models as $model) {
            $actions = [];
            foreach ($model['userTypeActions'] as $action) {
                $actions[] = $action['action']['name'];
            }
            $data[$model['name']] = $actions;
        }

        if (isset($data[$key])) {
            return $data[$key];
        }
        if (!$restrict) {
            return $data;
        }

        return [];
    }

    /**
     * @param null $key
     * @param boolean $returnAsString
     *
     * @return array|mixed
     */
    public static function getActionDescription($key = null, $returnAsString = false)
    {
        $dependencies = Actions::find()->asArray()->all();
        $data = ArrayHelper::map($dependencies, 'name', 'description');

        if (isset($data[$key])) {
            return $data[$key];
        }
        if ($returnAsString) {
            return false;
        }

        return $data;
    }

    /**
     * @param bool $returnAsString
     *
     * @return array
     */
    public static function getDependentActions($returnAsString = false)
    {
        $data = [];
        $dependencies = Dependencies::find()
            ->joinWith(['a act', 'd dep'])
            ->asArray()
            ->all();

        foreach ($dependencies as $dependency) {
            $action = $dependency['a']['name'];
            $depend = $dependency['d']['name'];

            $data[$action] = [$depend];
        }

        return $data;
    }

    /**
     * @param $action
     *
     * @return bool
     */
    public static function isDependentAction($action)
    {
        \Yii::$app->bot->log(Dependencies::find()
            ->orWhere([
                'action' => $action,
            ])
            ->orWhere(['dependence' => $action])
            ->createCommand()->rawSql, 'action');
        return Dependencies::find()
            ->orWhere([
                'action' => $action,
            ])
            ->orWhere(['dependence' => $action])
            ->count() > 0;
    }

    /**
     * @param $action
     *
     * @return bool|int|string
     */
    public static function getParentAction($action)
    {
        $allActions = self::getDependentActions();

        foreach ($allActions as $parentAction => $actions) {
            if (in_array($action, $actions)) {
                return $parentAction;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function getInfiniteActions()
    {
        return [
            self::ACTION_REPORT_PROBLEM,
            self::ACTION_PROJECT_DOC,
        ];
    }

    /**
     * @param $action
     *
     * @return bool
     */
    public static function isInfiniteAction($action)
    {
        return in_array($action, self::getInfiniteActions());
    }
}
