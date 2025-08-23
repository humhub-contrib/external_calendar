<?php

use humhub\components\View;
use humhub\modules\external_calendar\assets\Assets;
use humhub\widgets\bootstrap\Tabs;
use humhub\widgets\modal\Modal;

/* @var $this View */

Assets::register($this);
?>
<?php Modal::beginDialog([
    'header' => Yii::t('ExternalCalendarModule.base', '<strong>Calendar</strong> export OLD'),
    'size' => Modal::SIZE_LARGE,
]) ?>

    <?= Tabs::widget([
        'viewPath' => '@external_calendar/views/export',
        'items' => [
            [
                'label' => Yii::t('ExternalCalendarModule.base', 'My exports'),
                'view' => 'tab-overview',
                'active' => true,
            ]
        ]
    ]) ?>

<?php Modal::endDialog() ?>
