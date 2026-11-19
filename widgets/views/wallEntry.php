<?php

use humhub\components\View;
use humhub\modules\external_calendar\assets\Assets;
use humhub\modules\external_calendar\helpers\CalendarUtils;
use humhub\modules\external_calendar\models\ExternalCalendarEntry;
use humhub\widgets\Icon;

/* @var View $this */
/* @var ExternalCalendarEntry $calendarEntry */

$color = $calendarEntry->calendar->color ?: 'var(--bs-info)';
$description = CalendarUtils::renderDescription($calendarEntry->description);

Assets::register($this);
?>
<div class="fs-6 fw-medium text-body-emphasis mb-2">
    <?= Icon::get('clock')->color($color)->size(Icon::SIZE_LG)->fixedWith() ?> <?= $calendarEntry->getFormattedTime() ?>
</div>
<?php if (!empty($calendarEntry->location)) : ?>
    <p>
        <?= Icon::get('map-pin')->color($color)->size(Icon::SIZE_LG)->fixedWith() ?>
        <?= $calendarEntry->getLocation(true) ?>
    </p>
<?php endif ?>

<?php if ($description !== '') : ?>
    <div data-ui-show-more data-read-more-text="<?= Yii::t('ExternalCalendarModule.view', 'Read full description...') ?>">
        <?= $description ?>
    </div>
<?php endif ?>
