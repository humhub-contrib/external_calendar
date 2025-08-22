<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\external_calendar\models\forms\ConfigForm;
use humhub\widgets\bootstrap\Button;
use humhub\widgets\form\ActiveForm;

/* @var $model ConfigForm */
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h1><?= Yii::t('ExternalCalendarModule.view', 'Calendar Extension Configuration') ?></h1>
    </div>
    <div class="panel-body">
        <?php $form = ActiveForm::begin() ?>

            <?= $form->field($model, 'autopost_calendar')->checkbox() ?>
            <?= $form->field($model, 'autopost_entries')->checkbox() ?>
            <?= $form->field($model, 'legacy_mode')->checkbox() ?>

            <hr>

            <?= Button::save()->submit() ?>
            <?= Button::light(Yii::t('ExternalCalendarModule.view', 'Back to modules'))
                ->link(['/admin/module'])
                ->right()
                ->loader(false) ?>

        <?php ActiveForm::end() ?>
    </div>
</div>
