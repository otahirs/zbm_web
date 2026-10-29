---
title: 'Archiv postupů'
process:
    twig: true
    markdown: false
# Odkazy na systémy s postupy - zobrazují se zde i v záložce "Postupy" na stránkách skupin.
# Pro změnu stačí upravit tento seznam (admin: stránka Archiv postupů -> Expert).
routes_links:
    -
        name: Livelox
        link: 'https://www.livelox.com/?tab=allEvents&timePeriod=pastMonth&country=CZE&q=sk%20brno%20%C5%BEabov%C5%99esky&sorting=time'
        note: 'klubové tréninky a závody od roku 2024'
routes_note: 'Připravujeme přechod na OResults (Aftershow), odkaz doplníme.'
content:
    items:
        '@page.descendants': /data/events
    filter:
        routable: true
    order:
        by: header.start
        dir: desc
---

{% include 'partials/routes_links.html.twig' with {'links_page': page} %}
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
