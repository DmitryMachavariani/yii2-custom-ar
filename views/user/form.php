<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Users;
use app\models\Settings;

/**
 * @var $this         \yii\web\View
 * @var $model        \app\models\Users
 * @var $profile      \app\models\Profile
 * @var $settingsModel\app\models\Settings
 */
$this->title = 'Управление пользователем';
?>

<h2><?=$this->title?></h2>
<?php $form = ActiveForm::begin() ?>

<?= $form->errorSummary($model) ?>
<div class="container-clear margin-bottom">
    <div class="container-clear">
        <div class="raw">
            <div class="col-md-6">
                <?= $form->field($model, 'username') ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'phone') ?>
                <?= $form->field($model, 'status')->dropDownList(
                    Users::STATUSES
                ) ?>

            </div>
            <div class="col-md-6">
                <?= $form->field($profile, 'last_name') ?>
                <?= $form->field($profile, 'first_name') ?>
                <?= $form->field($profile, 'middle_name') ?>
                <?= $form->field($profile, 'job') ?>
            </div>
        </div>
    </div>
    <div class="container-clear">
        <div class="raw">
            <div class="col-md-6">
                <?= $form->field($profile, 'photo')->fileInput() ?>
                <img src="<?= $profile->getAvatar(200, 200) ?>"
                     class="image" width="200" alt="User Avatar"/>
            </div>
            <div class="col-md-6">
                <h3>Уведомления</h3>
                <?= $form->field($settingsModel, 'key')->hiddenInput(
                    [
                        'name' => $settingsModel->formName() . '[key][2]',
                        'value' => Settings::USE_EMAIL
                    ]
                )->label(false) ?>
                <?= $form->field($settingsModel, 'value')->checkbox(
                    [
                        'name' => $settingsModel->formName() . '[value][2]',
                        'label' => Settings::getLabel(Settings::USE_EMAIL),
                        'checked' => (bool)Settings::getValue(
                            $model->id, Settings::USE_EMAIL
                        )
                    ]
                ) ?>
                <?= $form->field($settingsModel, 'key')->hiddenInput(
                    [
                        'name' => $settingsModel->formName()
                            . '[key][0]',
                        'value' => Settings::USE_TELEGRAM
                    ]
                )->label(false) ?>
                <?= $form->field($settingsModel, 'value')->checkbox(
                    [
                        'name' => $settingsModel->formName()
                            . '[value][0]',
                        'label' => Settings::getLabel(
                            Settings::USE_TELEGRAM
                        ),
                        'checked' => (bool)Settings::getValue(
                            $model->id, Settings::USE_TELEGRAM
                        )
                    ]
                ) ?>
                <?= $form->field($settingsModel, 'key')->hiddenInput(
                    [
                        'name' => $settingsModel->formName()
                            . '[key][1]',
                        'value' => Settings::TELEGRAM_ID
                    ]
                )->label(false) ?>
                <?= $form->field($settingsModel, 'value')->textInput(
                    [
                        'name' => $settingsModel->formName()
                            . '[value][1]',
                        'value' => Settings::getValue(
                            $model->id, Settings::TELEGRAM_ID
                        )
                    ]
                )->label(Settings::getLabel(Settings::TELEGRAM_ID)) ?>
            </div>
        </div>
    </div>
</div>
<?= Html::submitButton(
    $model->isNewRecord ? 'Создать' : 'Сохранить',
    ['class' => 'btn btn-success']
) ?>

<?php ActiveForm::end() ?>
