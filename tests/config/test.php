<?php

return [
    'modules' => ['calendar', 'external-calendar'],
    'fixtures' => [
        'default',
        'external-calendar' => 'humhub\modules\external_calendar\tests\codeception\fixtures\ExternalCalendarEntryFixture'
    ]
];
