<?php

use humhub\helpers\Html;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\external_calendar\assets\Assets;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;
use humhub\widgets\form\ContentVisibilitySelect;

/* @var $model ExternalCalendar */
/* @var $contentContainer ContentContainer */

if (!isset($model->color) && isset($contentContainer->color)) {
    $model->color = $contentContainer->color;
} elseif (!isset($model->color)) {
    $model->color = '#d1d1d1';
}

Assets::register($this);
?>
<div class="calendar-extension-calendar-form">
    <?php $form = ActiveForm::begin(['id'=>'add-new-calendar', 'enableClientValidation' => true]) ?>

    <?= $form->errorSummary($model) ?>

    <?= Html::activeLabel($model, 'color') ?>
    <div id="event-color-field" class="input-group input-color-group space-color-chooser-edit">
        <?= $form->field($model, 'color')->colorInput() ?>
        <?= $form->field($model, 'title')->textInput(['placeholder' => Yii::t('ExternalCalendarModule.view', 'Title')]) ?>
    </div>

    <?= $form->field($model, 'url')->textarea(['rows' => 6, 'placeholder' => Yii::t('ExternalCalendarModule.view', 'e.g. https://calendar.google.com/calendar/ical/...')]) ?>

    <?= $form->field($model, 'public')->widget(ContentVisibilitySelect::class) ?>
    <?= $form->field($model, 'sync_mode')->dropDownList($model->getSyncModeItems()) ?>
    <?= $form->field($model, 'event_mode')->dropDownList($model->getEventModeItems()) ?>

    <?= Button::save()->submit() ?>

    <?php ActiveForm::end() ?>
</div>
