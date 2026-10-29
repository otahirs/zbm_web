---
title: 'Archiv postupů'
process:
    twig: true
    markdown: false
content:
    items:
        '@page.descendants': /data/events
    filter:
        routable: true
    order:
        by: header.start
        dir: desc
---

<p>
    Postupy z klubových tréninků a závodů od roku 2024 najdete na
    <a href="https://www.livelox.com/?tab=allEvents&timePeriod=pastMonth&country=CZE&q=sk%20brno%20%C5%BEabov%C5%99esky&sorting=time" target="_blank">Liveloxu</a>.
    Připravujeme přechod na OResults (Aftershow), odkaz doplníme.
</p>
<p>Starší postupy uložené u jednotlivých akcí:</p>

{% for p in page.collection() if p.header.routes|length %}
<h4>{{ p.title }} | {{ p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM Y') }}</h4>
<ul>
    {% for r in p.header.routes %}
    <li><a href="{{ r.link }}" target="_blank">{{ r.name }}</a></li>
    {% endfor %}
</ul>
{% else %}
<p><em>Žádné uložené postupy.</em></p>
{% endfor %}
