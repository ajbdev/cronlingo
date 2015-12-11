# CRON Lingo

CRON Lingo takes a string about the recurrence of time and turns it into CRON tab syntax.

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
