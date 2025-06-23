# External Calendars

Calendar extension for integration of external calendars with [iCalendar-format](https://en.wikipedia.org/wiki/ICalendar).

This module supports importing and synchronization of iCalendar calendar feeds.

Depending on the export service you use for your ICAL import, you may notice different results due to differences in the used iCal format. 
In case you experience any unexpected results, please let us know on [github](https://github.com/humhub-contrib/humhub-modules-external-calendar/issues).

**Be careful!**
Some calendars are not intended for permanent synchronization. These do not send a "last_modified" timestamp. 
Please do not select the option `auto sync hourly` for these. It could slow down your system.

This module was forked from [David Born](https://github.com/staxDB/humhub-modules-external-calendar).
