<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2019 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\external_calendar\widgets;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\components\Widget;
use humhub\widgets\modal\ModalButton;
use Yii;

class ExportButton extends Widget
{
    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    public function run()
    {
        if (Yii::$app->user->isGuest) {
            return '';
        }

        return ModalButton::light()
            ->icon('download')
            ->load(Yii::$app->user->getIdentity()->createUrl('/external_calendar/export/edit'))
            ->tooltip(Yii::t('ExternalCalendarModule.base', 'Export'));
    }

}
