<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2026 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\external_calendar\jobs;

use humhub\modules\external_calendar\Events;
use humhub\modules\queue\ActiveJob;
use humhub\modules\external_calendar\models\ExternalCalendar;
use Yii;

class SingleSync extends ActiveJob
{
    public $id;

    public function run()
    {
        Events::registerAutoloader();

        if ($calendar = ExternalCalendar::find()->where(['id' => $this->id])->one()) {
            $calendar->sync();
        }
    }
}
