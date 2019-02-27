<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\models\Users;
use app\models\Settings;

/**
 * @var $this \yii\web\View
 * @var $model \app\models\Users
 * @var $settingsModel\app\models\Settings
 */
?>

<?php $form = ActiveForm::begin() ?>

<?= $form->errorSummary($model) ?>
<div class="container margin-bottom">
    <div class="container">
        <div class="raw">
            <div class="col-md-5">
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'phone') ?>
                <?= $form->field($model, 'status')->dropDownList(Users::STATUSES) ?>
                <?= $form->field($model->profile, 'job') ?>
            </div>
            <div class="col-md-5">
                <?= $form->field($model->profile, 'last_name') ?>
                <?= $form->field($model->profile, 'first_name') ?>
                <?= $form->field($model->profile, 'middle_name') ?>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="raw">
        <div class="col-md-5">
            <?= $form->field($model->profile, 'photo')->fileInput() ?>
            <img src="<?= $model->profile->getAvatar(200, 200)?>" class="image" width="200" alt="User Avatar"/>
        </div>
        <div class="col-md-5">
            <h3>Уведомления</h3>
            <?= $form->field($settingsModel, 'key')->hiddenInput(['name' => $settingsModel->formName() . '[key][2]', 'value' => Settings::USE_EMAIL])->label(false) ?>
            <?= $form->field($settingsModel, 'value')->checkbox([
                'name' => $settingsModel->formName() . '[value][2]',
                'label' => Settings::getLabel(Settings::USE_EMAIL),
                'checked' => (bool)Settings::getValue($model->id, Settings::USE_EMAIL)
            ]) ?>
            <div class="raw">
                <div class="col-md-6">
                    <?= $form->field($settingsModel, 'key')->hiddenInput(['name' => $settingsModel->formName() . '[key][0]', 'value' => Settings::USE_TELEGRAM])->label(false) ?>
                    <?= $form->field($settingsModel, 'value')->checkbox([
                        'name' => $settingsModel->formName() . '[value][0]',
                        'label' => Settings::getLabel(Settings::USE_TELEGRAM),
                        'checked' => (bool)Settings::getValue($model->id, Settings::USE_TELEGRAM)
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($settingsModel, 'key')->hiddenInput(['name' => $settingsModel->formName() . '[key][1]', 'value' => Settings::TELEGRAM_ID])->label(false) ?>
                    <?= $form->field($settingsModel, 'value')->textInput([
                        'name' => $settingsModel->formName() . '[value][1]',
                        'value' => Settings::getValue($model->id, Settings::TELEGRAM_ID)
                    ])->label(Settings::getLabel(Settings::TELEGRAM_ID)) ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
<?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end() ?>
