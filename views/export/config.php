<?php

use humhub\modules\external_calendar\assets\Assets;
use humhub\widgets\ModalDialog;

use humhub\widgets\Tabs;

/* @var $this \humhub\modules\ui\view\components\View */


Assets::register($this);

?>

<?php ModalDialog::begin(['header' => Yii::t('ExternalCalendarModule.base', '<strong>Calendar</strong> export OLD'), 'size' => 'large']) ?>

    <?= Tabs::widget([
        'viewPath' => '@external_calendar/views/export',
        'items' => [
            ['label' => Yii::t('ExternalCalendarModule.base', 'My exports'), 'view' => 'tab-overview', 'active' => true]
        ]
    ]); ?>

<?php ModalDialog::end() ?>
