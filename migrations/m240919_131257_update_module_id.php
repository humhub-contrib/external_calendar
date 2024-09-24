<?php

use humhub\components\Migration;
use humhub\models\ModuleEnabled;
use humhub\models\Setting;
use humhub\modules\activity\models\Activity;
use humhub\modules\content\models\ContentContainerSetting;

/**
 * Class m240919_131257_update_module_id
 */
class m240919_131257_update_module_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $oldId = 'external_calendar';
        $newId = 'external-calendar';

        $moduleEnabled = ModuleEnabled::findOne(['module_id' => $oldId]);
        if ($moduleEnabled) {
            $moduleEnabled->module_id = $newId;
            $moduleEnabled->save();

            Activity::updateAll(['module' => $newId], ['module' => $oldId]);
            Setting::updateAll(['module_id' => $newId], ['module_id' => $oldId]);
            ContentContainerSetting::updateAll(['module_id' => $newId], ['module_id' => $oldId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240919_131257_update_module_id cannot be reverted.\n";

        return false;
    }
}
