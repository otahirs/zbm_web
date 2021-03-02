---
content:
    items:
        '@page.descendants': /data/events/
process:
    markdown: false
---

{# icalendar start #}
BEGIN:VCALENDAR
X-WR-CALNAME: ZBM - vše
VERSION:2.0
PRODID:test/zabiny.club//cs-CZ
{# icalendar events #}
{% for event in page.collection if event.header.id %}
BEGIN:VEVENT
UID:{{event.header.id}}
DTSTAMP:{{event.date|date("Ymd") ~ "T" ~ event.date|date("His") ~ "Z"}}
DTSTART:{{event.header.start|date("Ymd")}}
DTEND:{{event.header.end|date("Ymd")}}
SUMMARY:{{event.header.title}}
LOCATION:{{event.header.place}}
URL:{{event.url(true)}}
END:VEVENT
{% endfor %}
{# icalendar end #}
END:VCALENDAR