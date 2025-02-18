<?php

namespace humhub\modules\external_calendar\models;

use Colors\RandomColor;
use humhub\libs\Html;
use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\components\ContentActiveRecord;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\external_calendar\jobs\UpdateCalendarVisibility;
use humhub\modules\external_calendar\models\forms\ConfigForm;
use humhub\modules\external_calendar\permissions\ManageCalendar;
use humhub\modules\search\interfaces\Searchable;
use ICal\ICal;
use Yii;
use yii\base\InvalidValueException;

/**
 * This is the model class for table "external_calendar".
 *
 * @property int $id
 * @property string $title
 * @property string $url
 * @property string $time_zone The timeZone these entries was saved, note the dates itself are always saved in app timeZone
 * @property string $color
 * @property string $version    The ical-version, the calendar is stored
 * @property string $cal_name    The original calendar-name
 * @property string $cal_scale    The original calendar scale format, e.g. Gregorian
 * @property int $sync_mode    Set if the Content should be autosynced
 * @property int $event_mode    Set how old and new Events should be handled
 * @property ExternalCalendarEntry[] $entries
 * @property string $description
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class ExternalCalendar extends ContentActiveRecord implements Searchable
{
    public const ITEM_TYPE_KEY = 'external_calendar';
    /**
     * Sync Modes
     */
    public const SYNC_MODE_NONE = 0;
    public const SYNC_MODE_HOURLY = 1;
    public const SYNC_MODE_DAILY = 2;
    /**
     * Event Modes
     */
    public const EVENT_MODE_CURRENT_MONTH = 0;
    public const EVENT_MODE_ALL = 1;
    /**
     * @var array all given sync modes as array
     */
    public static $syncModes = [
        self::SYNC_MODE_NONE,
        self::SYNC_MODE_HOURLY,
        self::SYNC_MODE_DAILY,
    ];
    /**
     * @var array all given sync modes as array
     */
    public static $eventModes = [
        self::EVENT_MODE_CURRENT_MONTH,
        self::EVENT_MODE_ALL,
    ];
    /**
     * @inheritdoc
     */
    public $moduleId = 'external_calendar';
    /**
     * @inheritdoc
     */
    public $wallEntryClass = 'humhub\modules\external_calendar\widgets\WallEntryCalendar';
    /**
     * @inheritdoc
     */
    public $managePermission = ManageCalendar::class;
    /**
     * @var bool
     */
    public $allowFiles = false;
    /**
     * @var int form field
     */
    public $public;
    /**
     * @inheritdoc
     */
    public $streamChannel = null;
    /**
     * @inheritdoc
     */
    public $silentContentCreation = true;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'external_calendar';
    }

    /**
     *  init by settings
     */
    public function init()
    {

        parent::init();

        if (!$this->color) {
            $this->color = RandomColor::one(['luminosity' => 'light']);
        }

        if ($this->event_mode === null) {
            $this->event_mode = static::EVENT_MODE_ALL;
        }
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->public === null) {
            $this->public = $this->content->visibility;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $result = [
            [['title'], 'string', 'max' => 100],
            [['url'], 'string', 'max' => 255],
            [['title', 'url'], 'required'],
            [['time_zone'], 'string', 'max' => 60],
            [['color'], 'string', 'max' => 7],
            [['url'], 'validateURL'],
            [['public'], 'integer', 'min' => 0, 'max' => 1],
            [['sync_mode'], 'in', 'range' => self::$syncModes],
            [['event_mode'], 'in', 'range' => self::$eventModes],
        ];

        if (!$this->allowFiles) {
            $result[] = [['url'], 'url', 'defaultScheme' => 'https', 'message' => Yii::t('ExternalCalendarModule.view', "No valid ical url! Try an url with http / https.")];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('ExternalCalendarModule.base', "External Calendar");
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getIcon()
    {
        return 'fa-calendar';
    }

    /**
     * Validator for the url field.
     *
     * @param string $attribute attribute name
     * @param array $params parameters
     */
    public function validateURL($attribute, $params)
    {
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            $this->addError($attribute, Yii::t('ExternalCalendarModule.view', "Bad URL"));
            return false;
        }

        try {
            new ICal($this->url, [
                'defaultTimeZone' => Yii::$app->timeZone,
            ]);
        } catch (\Exception $e) {
            $this->addError($attribute, Yii::t('ExternalCalendarModule.view', "Error while fetching ical"));
            Yii::error($e);
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('ExternalCalendarModule.view', 'ID'),
            'title' => Yii::t('ExternalCalendarModule.view', 'Title'),
            'url' => Yii::t('ExternalCalendarModule.view', 'Url'),
            'public' => Yii::t('ExternalCalendarModule.view', 'Public'),
            'time_zone' => Yii::t('ExternalCalendarModule.view', 'Timezone'),
            'color' => Yii::t('ExternalCalendarModule.view', 'Color'),
            'version' => Yii::t('ExternalCalendarModule.view', 'iCal Version'),
            'cal_name' => Yii::t('ExternalCalendarModule.view', 'Original Calendar Name'),
            'cal_scale' => Yii::t('ExternalCalendarModule.view', 'Original Calendar Scale'),
            'sync_mode' => Yii::t('ExternalCalendarModule.view', 'Automatic synchronization'),
            'event_mode' => Yii::t('ExternalCalendarModule.view', 'Synchronization interval'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getSearchAttributes()
    {
        return [
            'title' => $this->title,
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        foreach (ExternalCalendarEntry::find()->where(['calendar_id' => $this->id])->each() as $item) {
            $item->delete();
        }

        return parent::beforeDelete();
    }

    /**
     * @param $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        $this->setSettings();
        $this->content->visibility = $this->public;
        return parent::beforeSave($insert);
    }

    private function setSettings()
    {
        $settings = ConfigForm::instantiate();

        if ($settings->autopost_calendar) {
            // set back to autopost true
            $this->streamChannel = 'default';
            $this->silentContentCreation = false;
        }
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        // Do not touch the order of this check, old attributes are cleared in afterSave!
        $visibilityChanged = $this->content->visibility != $this->content->getOldAttribute('visibility');

        parent::afterSave($insert, $changedAttributes);

        if ($visibilityChanged) {
            Yii::$app->queue->push(new UpdateCalendarVisibility(['calendarId' => $this->id]));
        }
    }

    public function afterMove(ContentContainerActiveRecord $container = null)
    {
        // TODO: Check if users are also in the new space...
        foreach ($this->entries as $entry) {
            $entry->move($container);
        }
    }

    public function getUrl()
    {
        return $this->content->container->createUrl('//external_calendar/calendar/view', ['id' => $this->id]);
    }


    public static function getSyncModeItems()
    {
        return [
            self::SYNC_MODE_NONE => Yii::t('ExternalCalendarModule.view', 'No auto synchronization'),
            self::SYNC_MODE_HOURLY => Yii::t('ExternalCalendarModule.view', 'Hourly'),
            self::SYNC_MODE_DAILY => Yii::t('ExternalCalendarModule.view', 'Daily'),
        ];
    }

    public function getSyncMode()
    {
        switch ($this->sync_mode) {
            case (self::SYNC_MODE_NONE):
                return Yii::t('ExternalCalendarModule.view', 'No auto synchronization');
                break;
            case (self::SYNC_MODE_HOURLY):
                return Yii::t('ExternalCalendarModule.view', 'Hourly');
                break;
            case (self::SYNC_MODE_DAILY):
                return Yii::t('ExternalCalendarModule.view', 'Daily');
                break;
            default:
                return;
        }
    }

    public static function getEventModeItems()
    {
        return [
            self::EVENT_MODE_CURRENT_MONTH => Yii::t('ExternalCalendarModule.view', 'Only sync events from current month'),
            self::EVENT_MODE_ALL => Yii::t('ExternalCalendarModule.view', 'Synchronize all events'),
        ];
    }

    public function getEventMode()
    {
        switch ($this->event_mode) {
            case (self::EVENT_MODE_CURRENT_MONTH):
                return Yii::t('ExternalCalendarModule.view', 'Only synchronize events from current month');
                break;
            case (self::EVENT_MODE_ALL):
                return Yii::t('ExternalCalendarModule.view', 'Synchronize all events');
                break;
            default:
                return;
        }
    }

    /**
     * @return ExternalCalendarEntry[]
     */
    public function getRecurringEventRoots()
    {
        return $this->getEntries(false)
            ->readable()
            ->andWhere('external_calendar_entry.rrule IS NOT NULL')->all();
    }

    public function getEntries(bool $includeRecurrences = true): ActiveQueryContent
    {
        $query = $this->hasMany(ExternalCalendarEntry::class, ['calendar_id' => 'id']);

        if (!$includeRecurrences) {
            $query->andWhere('external_calendar_entry.parent_event_id IS NULL');
        }

        return $query;
    }

    public function addAttributes(ICal $ical)
    {
        // add info to CalendarModel
        $this->time_zone = $ical->calendarTimeZone();
        $this->cal_name = $ical->calendarName();
        if (isset($ical->cal['VCALENDAR']['VERSION'])) {
            $this->version = $ical->cal['VCALENDAR']['VERSION'];
        }
        if (isset($ical->cal['VCALENDAR']['CALSCALE'])) {
            $this->cal_scale = $ical->cal['VCALENDAR']['CALSCALE'];
        }
    }

    public function getItemTypeKey()
    {
        return (static::ITEM_TYPE_KEY . '_' . $this->id);
    }

    public function getFullCalendarArray()
    {
        return [
            'title' => Html::encode($this->title),
            'color' => Html::encode($this->color),
            'icon' => 'fa-calendar-o',
            'format' => 'Y-m-d H:i:s',
        ];
    }

    /**
     * Syncronizes this external calendar
     *
     * @return static
     * @throws \yii\base\Exception
     * @throws InvalidValueException
     */
    public function sync($rangeStart = null, $rangeEnd = null)
    {
        ICalSync::sync($this, $rangeStart, $rangeEnd);
        return $this;
    }
}
