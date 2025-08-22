<?php

namespace humhub\modules\external_calendar\widgets;

use humhub\modules\content\widgets\stream\WallStreamModuleEntryWidget;
use humhub\modules\external_calendar\models\ExternalCalendarEntry;

/**
 * @inheritdoc
 */
class WallEntry extends WallStreamModuleEntryWidget
{
    /**
     * @inheritdoc
     */
    public $editRoute = '/external_calendar/entry/update';

    /**
     * @inheritdoc
     */
    public $editMode = self::EDIT_MODE_MODAL;

    /**
     * @var ExternalCalendarEntry
     */
    public $model;

    public function getControlsMenuEntries()
    {
        $this->renderOptions->disableControlsEntrySwitchVisibility();
        return parent::getControlsMenuEntries();
    }

    /**
     * @return string returns the content type specific part of this wall entry (e.g. post content)
     */
    protected function renderContent()
    {
        return $this->render('wallEntry', [
            'calendarEntry' => $this->model,
        ]);
    }

    /**
     * @return string a non encoded plain text title (no html allowed) used in the header of the widget
     */
    protected function getTitle()
    {
        return $this->model->title;
    }
}
