humhub.module('external-calendar', function (module, require, $) {
    var client = require('client');

    var removeCalendar = function(event) {
        client.post(event);
    }

    module.export({
        removeCalendar: removeCalendar
    });
});
