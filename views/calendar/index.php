<?php

use humhub\components\View;
use humhub\helpers\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\modules\space\models\Space;
use humhub\modules\calendar\widgets\ContainerConfigMenu;
use humhub\modules\ui\icon\widgets\Icon;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\bootstrap\Link;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $model ExternalCalendar */

$publicText = Yii::t('ExternalCalendarModule.view', 'Public');
$privateText = Yii::t('ExternalCalendarModule.view', 'Private');

$helpText = ($contentContainer instanceof Space)
    ? Yii::t('ExternalCalendarModule.base', 'This view lists all calenders configured for this space')
    : Yii::t('ExternalCalendarModule.base', 'This view lists all calenders configured in your profile');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('CalendarModule.config', '<strong>Calendar</strong> module configuration') ?>
    </div>

    <?= ContainerConfigMenu::widget() ?>

    <div class="panel-body">
        <div class="clearfix">
            <?= Button::success(Yii::t('ExternalCalendarModule.view', 'Add Calendar'))
                ->icon('plus')
                ->link($contentContainer->createUrl('edit'))
                ->right()
                ->sm() ?>
            <h4><?= Yii::t('ExternalCalendarModule.base', 'External Calendars Overview') ?></h4>
            <div class="text-body-secondary"><?= $helpText ?></div>
        </div>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => '',
            'showHeader' => false,
            'columns' => [
                'title' => [
                    'format' => 'raw',
                    'value' => function ($data) use ($contentContainer) {
                        /* @var $data ExternalCalendar */
                        return Link::to(Html::encode($data->title), $contentContainer
                            ->createUrl('view', ['id' => $data->id]));
                    },
                ],
                'visibility' => [
                    'header' => '',
                    'attribute' => 'public',
                    'options' => ['style' => 'width:40px'],
                    'format' => 'raw',
                    'value' => function ($data) use ($privateText, $publicText) {
                        /* @var $data ExternalCalendar */
                        return $data->content->visibility
                            ? Icon::get('globe')->tooltip($publicText)
                            : Icon::get('lock')->tooltip($privateText);
                    },
                ],
                [
                    'header' => '',
                    'class' => ActionColumn::class,
                    'options' => ['style' => 'width:40px'],
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) use ($contentContainer) {
                            $viewUrl = $contentContainer->createUrl('view', ['id' => $model->id]);
                            return Button::primary()
                                ->icon('eye')
                                ->link($viewUrl)
                                ->tooltip(Yii::t('ExternalCalendarModule.base', 'View'))
                                ->sm()
                                ->right();
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>
