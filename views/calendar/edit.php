<?php

use humhub\helpers\Html;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\modules\calendar\widgets\ContainerConfigMenu;
use humhub\widgets\bootstrap\Button;

/* @var $model ExternalCalendar */
/* @var $contentContainer ContentContainer */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-heading">
            <?= Yii::t('CalendarModule.config', '<strong>Calendar</strong> module configuration') ?>
        </div>
    </div>

    <?= ContainerConfigMenu::widget() ?>

    <div class="panel-body">
        <div class="clearfix">
            <?= Button::back($contentContainer->createUrl('index'), Yii::t('ExternalCalendarModule.base', 'Back to overview'))->sm() ?>
            <h4><?= $model->isNewRecord
                ? Yii::t('ExternalCalendarModule.view', 'Add external Calendar')
                : Yii::t('ExternalCalendarModule.base', 'Edit Calendar  {title}', [
                    'title' => Html::encode($model->title),
                ]) ?></h4>
        </div>

        <?= $this->render('_form', [
            'model' => $model,
            'contentContainer' => $contentContainer,
        ]) ?>
    </div>
</div>
