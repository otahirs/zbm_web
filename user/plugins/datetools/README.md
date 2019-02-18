# Date tools plugin for Grav CMS

*This plugin works with Grav v1.1.3 and above.*

This [Grav](http://getgrav.org) plugin provides date tools to use inside of Twig for filtering pages. With the release of Grav 0.9.13 `startDate` and `endDate` were introduced to collection parsing. You can use the following `datetools` to set various dates for retrieving [collections.](http://learn.getgrav.org/content/collections) 

## Configuration

Set up your preferred date formats in datetools.yaml

The default is set to the American date format of 01/01/2015 12:00am.

```
dateFormat: 
    default: "m/d/Y g:ia"
    long: "l, F j, g:ia"
    medium: "F j, g:ia"
    short: "m/d/y"
```

## Example Twig Use

```
{% set events = page
    .collection({'items':{'@taxonomy.type':'event'}})
    .dateRange(datetools.startOfWeek, datetools.endOfMonth)
    .order('date', 'asc') %}

<ul>
{% for event in events %}
    <li><a href="{{ event.url }}">{{ event.title }}</a> {{ event.date|date('m/d/Y g:ia') }}</li>
{% endfor %}
</ul>
```

## Common Dates and Times

```
datetools.today
datetools.yesterday
datetools.tomorrow
datetools.startOfWeek
datetools.endOfWeek
datetools.startOfMonth
datetools.endOfMonth
datetools.startOfYear
datetools.endOfYear
```

## Relative Parser Method

The following date parser is based on the [Carbon](https://github.com/briannesbitt/Carbon) api extension for DateTime in PHP 5.3+. Read the [documentation](https://github.com/briannesbitt/Carbon#carbon) for the Carbon project for more info.

```
datetools.parseDate('now')
datetools.parseDate('next wednesday')
datetools.parseDate('last friday')
datetools.parseDate('first day of January 2015')
```