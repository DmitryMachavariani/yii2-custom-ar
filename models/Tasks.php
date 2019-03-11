<?php

namespace app\models;

use app\components\FileHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\UploadedFile;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property int $project_id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $priority
 * @property int $assigned_to
 * @property int $created_by
 * @property double $estimate
 * @property int $notify
 * @property string $date_created
 * @property string $date_updated
 * @property string              $planned_start_date
 * @property string              $planned_end_date
 * @property string              $real_start_date
 * @property string              $real_end_date
 *
 * @property Projects $project
 * @property Users $assigned
 * @property Users $created
 * @property History[] $history
 * @property History $lastHistory
 * @property Files[] $attachments
 * @property Trackers[] $trackers
 */
class Tasks extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_FEEDBACK = 2;
    const STATUS_FINISHED = 3;
    const STATUS_CLOSED = 4;

    const STATUSES = [
        self::STATUS_NEW => 'Новая',
        self::STATUS_IN_PROGRESS => 'В работе',
        self::STATUS_FEEDBACK => 'Обратная связь',
        self::STATUS_FINISHED => 'Завершена',
        self::STATUS_CLOSED => 'Закрыта'
    ];

    const PRIORITY_LOW = 0;
    const PRIORITY_NORMAL = 1;
    const PRIORITY_HARD = 2;

    const PRIORITIES = [
        self::PRIORITY_NORMAL => 'Нормальный',
        self::PRIORITY_LOW => 'Низкий',
        self::PRIORITY_HARD => 'Высокий'
    ];

    /**
     * @var UploadedFile[]
     */
    public $files;

    /**
     * @var float
     */
    public $tracked;

    public static function tableName()
    {
        return 'tasks';
    }

    public function rules()
    {
        return [
            [['title', 'assigned_to', 'estimate', 'project_id'], 'required'],
            [['project_id', 'status', 'priority', 'assigned_to', 'assigned_to', 'notify'], 'integer'],
            [['description'], 'string'],
            [['estimate'], 'number'],
            [['date_created', 'date_updated', 'files', 'planned_start_date', 'planned_end_date', 'real_start_date', 'real_end_date',], 'safe'],
            [['title'], 'string', 'max' => 255],

            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Projects::class, 'targetAttribute' => ['project_id' => 'id']],
            [['assigned_to'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['assigned_to' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['created_by' => 'id']],
//            [['trackers'], 'exist', 'skipOnError' => true, 'targetClass' => Trackers::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Проект',
            'title' => 'Название',
            'description' => 'Описание',
            'status' => 'Статус',
            'priority' => 'Приоритет',
            'assigned_to' => 'Назначена',
            'created_by' => 'Создана',
            'estimate' => 'Оценка',
            'tracked' => 'Трудозатраты',
            'notify' => 'Уведомить',
            'date_created' => 'Дата создания',
            'date_updated' => 'Дата обновления',
            'planned_start_date' => 'План. дата начала',
            'planned_end_date' => 'План. дата окончания',
            'real_start_date' => 'Факт. дата начала',
            'real_end_date' => 'Факт. дата окончания',
            'files' => 'Файлы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Projects::class, ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUser()
    {
        return $this->hasMany(ProjectUser::class, ['project_id' => 'id'])->via('project');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('projectUser');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistory()
    {
        return $this->hasMany(History::class, ['model_id' => 'id'])
            ->andOnCondition(['model_name' => 'Tasks']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(Files::class, ['model_id' => 'id'])
            ->andOnCondition(['model_name' => 'Task']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssigned()
    {
        return $this->hasOne(Users::class, ['id' => 'assigned_to']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreated()
    {
        return $this->hasOne(Users::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTrackers()
    {
        return $this->hasMany(Trackers::class, ['task_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TasksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TasksQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return self::STATUSES;
    }

    /**
     * @param int $status
     *
     * @return mixed|string
     */
    public static function getStatus(int $status)
    {
        $statuses = self::getStatuses();

        return $statuses[$status] ?? '';
    }

    /**
     * @return array
     */
    public static function getPriorities()
    {
        return self::PRIORITIES;
    }

    /**
     * @param int $priority
     *
     * @return mixed|string
     */
    public static function getPriority(int $priority)
    {
        $priorities = self::getPriorities();

        return $priorities[$priority] ?? '';
    }

    /**
     * @param int $priority
     *
     * @return mixed|string
     */
    public static function getPriorityColored(int $priority)
    {
        $priorities = self::getPriorities();
        $currentPriority = $priorities[$priority];

        switch ($priority) {
            case self::PRIORITY_LOW:
                $class = 'fa fa-circle-o text-aqua';
                break;

            case self::PRIORITY_NORMAL:
                $class = 'fa';
                break;

            case self::PRIORITY_HARD:
                $class = 'fa fa-circle-o text-red';
                break;

            default:
                $class = '';
        }

        $priority = Html::tag('i', ' ' . $currentPriority, ['class' => $class]);

        return $priority;
    }

    /***
     * @param int $status
     *
     * @return bool
     */
    public function setStatus(int $status): bool
    {
        $this->status = $status;
        return $this->save(false);
    }

    /***
     * @param int $userId
     *
     * @return bool
     */
    public function setAssigned(int $userId): bool
    {
        $this->assigned_to = $userId;
        return $this->save(false);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->date_created = date('Y-m-d H:i:s');
            $this->created_by = Yii::$app->user->id;
        }

        $changedAttributes = [];
        $skippedAttributes = ['file', 'date_updated'];
        foreach ($this->oldAttributes as $key => $value) {
            if (in_array($key, $skippedAttributes)) {
                continue;
            }

            if ($this->oldAttributes[$key] != $this->attributes[$key]) {
                $changedAttributes[] = 'Изменён параметр "' . $this->getAttributeLabel($key) . '" на ' . $this->attributes[$key];
            }
        }

        if (!empty($changedAttributes)) {
            History::create(History::TYPE_CHANGE_ATTRIBUTES, History::MODEL_TASKS, $this->id, implode("\n", $changedAttributes));
        }

        $this->date_updated = date('Y-m-d H:i:s');
        if (empty($this->planned_start_date)) {
            $this->planned_start_date = $this->date_created;
        }
        if (empty($this->planned_end_date)) {
            $this->planned_end_date = date('Y-m-d', strtotime($this->planned_start_date . ' + 1 days'));
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->status == self::STATUS_CLOSED) {
            $this->real_end_date = date('Y-m-d');
        }

        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        $column = ArrayHelper::getColumn($this->trackers, 'time');
        $this->tracked = array_sum($column);

        $this->oldAttributes = $this->attributes;

        parent::afterFind();
    }

    /**
     * @return bool
     */
    public function prepareFile()
    {
        if (!$this->files) {
            return false;
        }
        foreach ($this->files as $uploadedFile) {
            $filesModel = Files::createForTask();
            $filesModel->name = $uploadedFile->getBaseName() . ($uploadedFile->getExtension() ? '.' . $uploadedFile->getExtension() : '');
            $filesModel->model_id = $this->id;
            $filesModel->status = 1;
            $filesModel->date_created = $filesModel->date_updated = date('Y-m-d H:i:s');
            $filesModel->save();

            $folder = mb_strtolower(Yii::getAlias('@uploads') . '/' . $filesModel->model_name . '/' . $this->id . '/');
            $fileHelper = new FileHelper($uploadedFile->tempName, $folder);
            $fileHelper->setBaseFolder(Yii::getAlias('@uploads'))->saveAs($folder . '/' . $filesModel->name);
        }

        History::create(History::TYPE_ADD_FILE, History::MODEL_TASKS, $this->id);
        return true;
    }

    /**
     * @return array
     */
    public function getUsersToNotify()
    {
        return array_diff(array_unique([
            $this->assigned_to,
            $this->created_by,
            $this->notify
        ]), ([@Yii::$app->user->id ?? 0]));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastHistory()
    {
        return $this->hasOne(History::class, ['model_id' => 'id'])
            ->andOnCondition(['model_name' => 'Tasks'])
            ->orderBy([History::tableName() . '.date' => SORT_DESC]);
    }
}
