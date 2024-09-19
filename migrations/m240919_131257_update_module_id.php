<?php

use humhub\components\Migration;
use humhub\models\ModuleEnabled;

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
        $moduleEnabled = ModuleEnabled::findOne(['module_id' => 'external_calendar']);
        if ($moduleEnabled) {
            $moduleEnabled->module_id = 'external-calendar';
            $moduleEnabled->save();
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
