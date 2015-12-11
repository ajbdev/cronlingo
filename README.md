# CRON Lingo

CronLingo turns human readable strings about time and turns them into valid crontabs. 

Examples:

```php
echo CronLingo::fromExpression('Every day at midnight');
// "0 0 * * *"

echo CronLingo::fromExpression('Every 15 minutes at midnight on the weekend');
// "*/15 0 * * 0,6"

echo CronLingo::fromExpression('Every other minute in August at noon on the weekday');
// "*/2 12 * 8 1,2,3,4,5"

echo CronLingo::fromExpression('The 1st day in April at midnight');
// "0 0 1 4 *"

echo CronLingo::fromExpression('Every day on the weekday at 2:25pm');
// "25 14 * * 1,2,3,4,5"
```
## Syntax

CronLingo identifies the parts of a string with specific time-related keywords such as "on, to, at" and uses this to deduce the time meaning and convert it into part of a cron tab. It's not particular about the order of these statements.
Here's a brief list of things that it will pick up and parse into a crontab:
- Periods (daily, weekly, monthly)
- Exact times (9:30 PM, 8a, 3p)
- Meridiems (AM/PM/A/P)
- Intervals (1st, second, other, ninth, etc)
- Specific fields (second, minute, hour, day, month)
- Day of week (sunday, monday, etc)
- 12 o'clocks (noon, midnight)
- Lists (e.g., 5 to 12 minutes)
- Month names (january, february, etc)
- Weekend / weekday


## What's a cron tab?
A cron tab is an expression that defines a recurring period of time.

It looks something like this:
```
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
```

From [Wikipedia](https://en.wikipedia.org/wiki/Cron):

The software utility Cron is a time-based job scheduler in Unix-like computer operating systems. People who set up and maintain software environments use cron to schedule jobs (commands or shell scripts) to run periodically at fixed times, dates, or intervals. It typically automates system maintenance or administration—though its general-purpose nature makes it useful for things like connecting to the Internet and downloading email at regular intervals.


