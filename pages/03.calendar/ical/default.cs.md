---
title: 'Export a synchronizace kalendáře'
---
<a href="/calendar"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> zpět na kalendář</a>

Všechny žabiňácké události zobrazené v kalendáři, případně pouze události vybrané tréninkové skupiny si nyní můžete nechat zobrazovat i ve vašem oblíbeném internetovém kalendáři. Stačí si do aplikace přidat vybraný žabiňácký kalendář pomocí odkazu níže. Zobrazí se vám nejen aktuální události, ale žabiňácký kalendář se bude i dále průběžně synchronizovat. Například pro přidání do kalendáře google vložte odkaz do <a href="https://calendar.google.com/calendar/r/settings/addbyurl" target="_blank">nastavení google kalendářů zde</a>.


| typ kalendáře | odkaz |
| ---|---|
|   všechny události| `{{base_url_absolute}}/calendar/ical/all`       |
|   žabičky         | `{{base_url_absolute}}/calendar/ical/zabicky`   |
|   pulci 1         | `{{base_url_absolute}}/calendar/ical/pulci1`    |
|   pulci 2         | `{{base_url_absolute}}/calendar/ical/pulci2`    |
|   žáci 1          | `{{base_url_absolute}}/calendar/ical/zaci1`     |
|   žáci 2          | `{{base_url_absolute}}/calendar/ical/zaci2`     |
|   dorost+         | `{{base_url_absolute}}/calendar/ical/dorost`    |

---
Události jsou ze systému exportovány ve formě <a href="https://cs.wikipedia.org/wiki/ICalendar" target="_blank">iCalendar</a> souboru, jedná se o otevřený standard, který podporuje většina aplikací pro správu kalendářů. Pomocí odkazu lze stáhnout i samotný _.ics_ soubor kalendáře, který lze také importovat do různých aplikací. Většinou je však vhodnější přidat kalendář pomocí jeho URL adresy, pak se sám průběžně aktualizuje.