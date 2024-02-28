<?php

if (!Yii::$app->getModule('calendar')) {
    return 'You must first install and enable the Calendar module.';
}

return null;
