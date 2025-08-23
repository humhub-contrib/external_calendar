<?php

use humhub\modules\external_calendar\models\ExternalCalendarEntry;
use humhub\widgets\modal\Modal;
use humhub\widgets\modal\ModalButton;

/* @var $model ExternalCalendarEntry */
/* @var $editUrl string */
?>
<?php $form = Modal::beginFormDialog([
    'header' => Yii::t('ExternalCalendarModule.view', 'Update {modelClass}: ', [
            'modelClass' => Yii::t('ExternalCalendarModule.base', 'External Calendar Entry')
        ]) . $model->title,
    'form' => ['enableClientValidation' => false],
    'footer' => ModalButton::cancel() . ModalButton::save()->submit($editUrl),
]) ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'location')->textarea(['maxlength' => true]) ?>
<?php Modal::endFormDialog() ?>
