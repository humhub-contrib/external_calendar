<?php

use humhub\helpers\Html;
use humhub\modules\external_calendar\helpers\CalendarUtils;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\modules\ui\icon\widgets\Icon;

/* @var $calendar ExternalCalendar */

$color = $calendar->color ?: 'var(--info)';
$description = CalendarUtils::renderDescription($calendar->description);
?>
<div class="ps-2" style="border-left: 3px solid <?= Html::encode($color) ?>">
    <div class="clearfix">
        <a href="<?= $calendar->getUrl() ?>" class="float-start me-2">
            <?= Icon::get('calendar')->style('font-size:35px')->class('text-light') ?>
        </a>
        <h4>
            <a href="<?= $calendar->getUrl() ?>">
                <?= Yii::t('ExternalCalendarModule.view', 'External Calendar: ') ?>
                <b><?= Html::encode($calendar->title) ?></b>
            </a>
        </h4>
        <h5>
            <?= Yii::t('ExternalCalendarModule.view', 'A new Calendar has been added.') ?>
        </h5>
    </div>
    <?php if ($description !== '') : ?>
        <div data-ui-show-more data-read-more-text="<?= Yii::t('ExternalCalendarModule.view', 'Read full description...') ?>">
            <?= $description ?>
        </div>
    <?php endif ?>
</div>
