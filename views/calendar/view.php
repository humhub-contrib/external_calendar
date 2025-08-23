<?php

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\calendar\widgets\ContainerConfigMenu;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\external_calendar\assets\Assets;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\widgets\bootstrap\Badge;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\modal\ModalButton;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model ExternalCalendar */
/* @var $contentContainer ContentContainerActiveRecord */

Assets::register($this);
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
            <h4><?= Yii::t('ExternalCalendarModule.base', 'Calendar  {title}', [
                'title' => Html::encode($model->title),
            ]) ?></h4>
            <div class="text-body-secondary">
                <?= Yii::t('ExternalCalendarModule.base', 'In this view you can review and manually synchronize the calendar {title}.', ['title' => Html::encode($this->title) ]) ?>
            </div>
        </div>

        <div class="btn-group-sm">
            <?= Button::primary(Yii::t('ExternalCalendarModule.base', 'Edit'))
                ->link($contentContainer->createUrl('edit', ['id' => $model->id]))
                ->icon('pencil-square-o') ?>
            <?= Button::danger(Yii::t('ExternalCalendarModule.base', 'Delete'))
                ->icon('trash-o')
                ->action('external_calendar.removeCalendar', $contentContainer->createUrl('delete', ['id' => $model->id]))
                ->id('modal_delete_task_' . $model->id)
                ->confirm(
                    Yii::t('ExternalCalendarModule.base', '<strong>Confirm</strong> deleting'),
                    Yii::t('ExternalCalendarModule.base', 'Are you sure you want to delete this item?'),
                    Yii::t('ExternalCalendarModule.base', 'Delete'),
                    Yii::t('ExternalCalendarModule.base', 'Cancel'),
                ) ?>
            <?= ModalButton::primary(Yii::t('ExternalCalendarModule.view', 'Sync Calendar'))
                ->load($contentContainer->createUrl('sync', ['id' => $model->id]))
                ->icon('refresh')
                ->right() ?>
        </div>
        <br>
        <div>
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'title',
                    'url:url',
                    'public:boolean',
                    [
                        'label' => Yii::t('ExternalCalendarModule.view', 'Synchronization Mode'),
                        'value' => $model->getSyncMode(),
                    ],
                    [
                        'label' => Yii::t('ExternalCalendarModule.view', 'Event Mode'),
                        'value' => $model->getEventMode(),
                    ],
                    'time_zone',
                    [
                        'attribute' => 'color',
                        'format' => 'raw',
                        'value' => Badge::none($model->color)
                            ->cssBgColor($model->color)
                            ->cssTextColor('transparent')
                            ->pill()
                    ],
                    'version',
                    'cal_name',
                    'cal_scale'
                ],
            ]) ?>
        </div>
        <br>
        <div class="clearfix">
            <?= Button::back($contentContainer->createUrl('index'), Yii::t('ExternalCalendarModule.base', 'Back to overview')) ?>
        </div>
    </div>
</div>
