<?php

namespace humhub\modules\external_calendar;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\modules\external_calendar\models\ExternalCalendarEntry;
use humhub\modules\external_calendar\permissions\ManageCalendar;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
use yii\helpers\Url;

class Module extends ContentContainerModule
{
    public $exportFileName = 'base.ics';
    public $exportFileMime = 'text/calendar';
    public $autoSaveExpansions = false;

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::class,
            User::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentClasses(): array
    {
        return [
            ExternalCalendar::class,
            ExternalCalendarEntry::class,
        ];
    }

    public function enable()
    {
        // check if calendar module is enabled
        if (!Yii::$app->hasModule('calendar') && !isset(Yii::$app->modules['calendar'])) {
            Yii::$app->getView()->warn(Yii::t('ExternalCalendarModule.base', 'Module cannot be activated. Please install and activate the calendar module first.'));
            return false;
        }

        return parent::enable();
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        /** @var ExternalCalendar $calendar */
        foreach (ExternalCalendar::find()->all() as $calendar) {
            $calendar->hardDelete();
        }
        parent::disable();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Yii::t('ExternalCalendarModule.base', 'External Calendar');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('ExternalCalendarModule.base', 'Extends the Calendar-Module to show external calendars with iCal');
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('ExternalCalendarModule.base', 'External Calendar');
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return Yii::t('ExternalCalendarModule.base', 'Manage external calendar here.');
        } elseif ($container instanceof User) {
            return Yii::t('ExternalCalendarModule.base', 'Manage external calendar for your profile.');
        }
    }

    /**
     * @inheritdoc
     */
    public function disableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::disableContentContainer($container);
        /** @var ExternalCalendar $calendar */
        foreach (ExternalCalendar::find()->contentContainer($container)->each() as $calendar) {
            $calendar->hardDelete();
        }
    }

    public function getConfigUrl()
    {
        return Url::to(['/external_calendar/config/index']);
    }

    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        if ($container->permissionManager->can(ManageCalendar::class)) {
            return $container->createUrl('/external_calendar/calendar/index');
        } else {
            return;
        }
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [
                new permissions\ManageCalendar(),
                new permissions\ManageEntry(),
            ];
        }
        return [];
    }

}
