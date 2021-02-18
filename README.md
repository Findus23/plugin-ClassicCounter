# Matomo ClassicCounter Plugin

## Description

No website is complete without a nostalgic view counter at the bottom!

This plugin allows you to add an image showing the total amount of visits per `idSite`.

**Important: Installing this plugin makes the output of the `VisitsSummary.get` API endpoint visible for everyone!**

```html
<img src="https://yourmatomoinstance.example/index.php?module=ClassicCounter&action=svg&idSite=1&period=day">
```
### Optional parameters:

- `&mode=` one of `["nb_visits", "nb_actions", "nb_visits_converted", "bounce_count", "sum_visit_length", "max_actions", "bounce_rate", "nb_actions_per_visit", "avg_time_on_site"]` (responses from the `VisitsSummary.get` API)
- `&backgroundColor=`: A hex color without the `#` (e.g. `f00` or `fe1234`)
- `&foregroundColor=`
- `&lightColor=`
- `&historicValue=`: A number that is added to the value from Matomo before being displayed. Useful if you want to add data from a counter before starting to use Matomo.

<img src="https://github.com/Findus23/plugin-ClassicCounter/blob/4.x-dev/screenshots/4909.png?raw=true" height="80">
