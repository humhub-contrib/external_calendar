<?php

namespace humhub\modules\external_calendar;

use humhub\helpers\ControllerHelper;
use humhub\modules\calendar\helpers\dav\enum\EventProperty;
use humhub\modules\calendar\widgets\CalendarControls;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\external_calendar\integration\calendar\CalendarExtension;
use humhub\modules\external_calendar\jobs\SyncHourly;
use humhub\modules\external_calendar\jobs\SyncDaily;
use humhub\modules\external_calendar\models\ExternalCalendarEntry;
use humhub\modules\external_calendar\models\forms\ConfigForm;
use humhub\modules\external_calendar\permissions\ManageEntry;
use humhub\modules\external_calendar\widgets\DownloadIcsLink;
use humhub\modules\external_calendar\widgets\ExportButton;
use Yii;
use yii\base\WidgetEvent;
use yii\base\BaseObject;
use humhub\modules\calendar\interfaces\event\legacy\CalendarEventIFWrapper;
use humhub\modules\calendar\helpers\dav\event\GetObjectEvent;
use humhub\modules\calendar\helpers\dav\event\UpdateObjectEvent;
use humhub\modules\calendar\helpers\dav\event\DeleteObjectEvent;

class Events extends BaseObject
{
    /**
     * @inheritdoc
     */
    public static function onBeforeRequest()
    {
        try {
            static::registerAutoloader();
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Register composer autoloader when Reader not found
     */
    public static function registerAutoloader()
    {
        if (class_exists(\ICal\ICal::class)) {
            return;
        }

        require Yii::getAlias('@external_calendar/vendor/autoload.php');
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\event\CalendarItemTypesEvent
     * @return mixed
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public static function onGetCalendarItemTypes($event)
    {
        try {
            $contentContainer = $event->contentContainer;

            if (!$contentContainer || $contentContainer->moduleManager->isEnabled('external_calendar')) {
                CalendarExtension::addItemTypes($event);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * @param $event WidgetEvent
     */
    public static function onContainerConfigMenuInit($event)
    {
        try {
            /* @var $container ContentContainerActiveRecord */
            $container = $event->sender->contentContainer;
            if ($container && $container->moduleManager->isEnabled('external_calendar')) {
                $event->sender->addItem([
                    'label' => Yii::t('ExternalCalendarModule.base', 'External Calendars'),
                    'id' => 'tab-calendar-external',
                    'url' => $container->createUrl('/external_calendar/calendar/index'),
                    'visible' => $container->can(ManageEntry::class),
                    'isActive' => ControllerHelper::isActivePath('external_calendar'),
                ]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onFindCalendarItems($event)
    {
        try {
            /* @var ContentContainerActiveRecord $contentContainer */
            $contentContainer = $event->contentContainer;

            if (!$contentContainer || $contentContainer->moduleManager->isEnabled('external_calendar')) {
                CalendarExtension::addItems($event);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * @param $event WidgetEvent
     */
    public static function onCalendarControlsInit($event)
    {
        if (ConfigForm::instantiate()->legacy_mode) {
            try {
                /* @var $controls CalendarControls */
                $controls = $event->sender;
                $controls->addWidget(ExportButton::class, ['container' => $controls->container], ['sortOrder' => 50]);
            } catch (\Throwable $e) {
                Yii::error($e);
            }
        }
    }

    /**
     * Defines what to do if hourly cron runs.
     *
     * @param $event
     * @return void
     */
    public static function onCronHourlyRun($event)
    {
        try {
            Yii::$app->queue->push(new SyncHourly());
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Defines what to do if daily cron runs.
     *
     * @param $event
     * @return void
     */
    public static function onCronDailyRun($event)
    {
        try {
            Yii::$app->queue->push(new SyncDaily());
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    /**
     * Callback to validate module database records.
     *
     * @param Event $event
     * @throws \Exception
     * @throws \Throwable
     */
    public static function onIntegrityCheck($event)
    {
        try {
            $integrityController = $event->sender;
            $integrityController->showTestHeadline("External Calendar Module - Entries (" . ExternalCalendarEntry::find()->count() . " entries)");
            foreach (ExternalCalendarEntry::find()->joinWith('calendar')->each() as $entry) {
                if ($entry->calendar === null) {
                    if ($integrityController->showFix("Deleting external calendar entry id " . $entry->id . " without existing calendar!")) {
                        $entry->hardDelete();
                    }
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onWallEntryLinks($event)
    {
        try {
            if ($event->sender->object instanceof ExternalCalendarEntry) {
                $event->sender->addWidget(DownloadIcsLink::class, ['calendarEntry' => $event->sender->object]);
            }
        } catch (\Throwable $e) {
            Yii::error($e);
        }
    }

    public static function onCaldavGetObject(GetObjectEvent $event)
    {
        $object = ExternalCalendarEntry::find()
            ->readable()
            ->where(['uid' => $event->objectId])
            ->one();

        $event->object = new CalendarEventIFWrapper(['options' => $object->getFullCalendarArray()]);
    }

    public static function onCaldavUpdateObject(UpdateObjectEvent $event)
    {
        /** @var ExternalCalendarEntry $object */
        $object = ExternalCalendarEntry::find()
            ->readable()
            ->where(['uid' => $event->object->getUid()])
            ->one();

        if (!$object) {
            return;
        }

        if (!empty($title = $event->properties->get(EventProperty::TITLE))) {
            $object->title = $title;
        }

        if (!empty($description = $event->properties->get(EventProperty::DESCRIPTION))) {
            $object->description = $description;
        }

        if (!empty($location = $event->properties->get(EventProperty::LOCATION))) {
            $object->location = $location;
        }

        $object->save();
    }

    public static function onCaldavDeleteObject(DeleteObjectEvent $event)
    {
        /** @var ExternalCalendarEntry $object */
        $object = ExternalCalendarEntry::find()
            ->readable()
            ->where(['uid' => $event->object->getUid()])
            ->one();

        if (!$object) {
            return;
        }

        $object->content->delete();
    }
}
