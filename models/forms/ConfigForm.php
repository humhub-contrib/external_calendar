<?php

namespace humhub\modules\external_calendar\models\forms;

use humhub\modules\external_calendar\models\CalendarExport;
use Yii;
use yii\base\Model;

/**
 * ConfigForm uses for AdminController to set the configs for all external calendars
 *
 * @author David Born ([staxDB](https://github.com/staxDB))
 */
class ConfigForm extends Model
{
    /**
     * @var bool determines whether external calendars should be posted to stream
     */
    public $autopost_calendar = true;

    /**
     * @var bool determines whether external calendar entries should be posted to stream
     */
    public $autopost_entries = true;

    /**
     * @var bool
     *
     * As part of recent updates, the "External Calendar" module has been revised,
     * and the calendar export functionality has been migrated to the "Calendar" module.
     * While the legacy export method will remain temporarily available during the transition phase,
     * it will be deprecated soon.
     */
    public $legacy_mode = false;

    /**
     * @inheritdocs
     */
    public function init()
    {
        $settings = Yii::$app->getModule('external_calendar')->settings;
        $this->autopost_calendar = $settings->get('autopost_calendar', $this->autopost_calendar);
        $this->autopost_entries = $settings->get('autopost_entries', $this->autopost_entries);
        $this->legacy_mode = $settings->get('legacy_mode',  CalendarExport::find()->where(['user_id' => Yii::$app->user->id])->exists());
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self();
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return [
            [['autopost_calendar', 'autopost_entries'], 'required'],
            [['autopost_calendar', 'autopost_entries', 'legacy_mode'], 'boolean'],
        ];
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return [
            'autopost_calendar' => Yii::t('ExternalCalendarModule.view', 'Post new calendar on stream'),
            'autopost_entries' => Yii::t('ExternalCalendarModule.view', 'Post new entries on stream'),
            'legacy_mode' => Yii::t('ExternalCalendarModule.view', 'Legacy Mode'),
        ];
    }


    public function attributeHints()
    {
        return [
            'legacy_mode' => Yii::t('ExternalCalendarModule.view', '(soon to be deprecated)'),
        ];
    }

    /**
     * Saves the given form settings.
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $settings = Yii::$app->getModule('external_calendar')->settings;
        $settings->set('autopost_calendar', $this->autopost_calendar);
        $settings->set('autopost_entries', $this->autopost_entries);
        $settings->set('legacy_mode', $this->legacy_mode);

        return true;

    }

}
