<?php
/* @var $this \humhub\modules\ui\view\components\View */

use humhub\modules\external_calendar\models\CalendarExport;
use humhub\widgets\Link;
use humhub\widgets\ModalButton;
use yii\data\ActiveDataProvider;
use humhub\widgets\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

$dataProvider = new ActiveDataProvider([
    'query' => CalendarExport::find()->where(['user_id' => Yii::$app->user->id]),
    'sort' => false
]);

?>
<div class="modal-body">
    <div class="alert alert-danger">
        <?= Yii::t('ExternalCalendarModule.base', 'As part of recent updates, the "External Calendar" module has been revised, and the calendar export functionality has been migrated to the "Calendar" module. While the legacy export method will remain temporarily available during the transition phase, it will be deprecated soon. We recommend switching to the new export feature provided by the "Calendar" module as soon as possible.') ?>
    </div>
    <div class="external-calendar-overview">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['style' => 'padding-top:0'],
        'summary' => false,

        'columns' => [

            [
                'attribute' => 'name',
                'label' => Yii::t('ExternalCalendarModule.base', 'Name'),
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'format' => 'raw',
                'label' =>  Yii::t('ExternalCalendarModule.base', 'Url'),
                'value' => function($model) {
                    /* @var $model CalendarExport */
                    $id = 'external_calendar_url_'.$model->id;
                    $copyLink = Link::withAction(Yii::t('ExternalCalendarModule.base', 'Copy to clipboard'), 'copyToClipboard', null, '#'.$id)->icon('fa-clipboard')->style('color:#777');
                    $copyLink = '<p class="help-block pull-right">'.$copyLink.'</p>';
                    return Html::textarea(null, $model->getExportUrl(), ['id' => $id, 'disabled' => true, 'class' => 'form-control', 'rows' => 3]).$copyLink;
                }
            ],
            [
                'class' => 'humhub\libs\ActionColumn',
                'options' => ['style' => 'min-width:100px;'],
                'modelIdAttribute' => 'token',
                'actions' => function ($model, $key) {
                    return [
                        Yii::t('ExternalCalendarModule.base', 'Download') => ['export', 'linkOptions' => ['data-pjax-prevent' => 1]],
                        Yii::t('ExternalCalendarModule.base', 'Delete') => ['edit', 'linkOptions' => [
                            'data-action-click' => 'ui.modal.post',
                            'data-action-url' => Url::to(['/external_calendar/export/delete', 'id' => $key]),
                            'data-action-confirm' => true
                        ]],
                    ];
                }
            ],

        ]
    ]); ?>
    </div>
</div>

