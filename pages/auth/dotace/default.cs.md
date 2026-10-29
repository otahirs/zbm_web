---
title: 'Podklady pro dotace'
process:
    twig: true
    markdown: false
access:
    site:
        dotace: true
cache_enable: false
never_cache_twig: true
cache_control: 'no-store, private'
dotace_variants:
    brno:
        label: 'Rozpis Brno (PDF)'
        line: 'Soustředění bude podpořeno z dotace Magistrátu města Brna – vrcholový sport.'
        logo: logo-brno.png
    kraj:
        label: 'Rozpis Kraj (PDF)'
        line: 'Soustředění bude podpořeno z dotace Jihomoravského kraje - Podpora sportu'
        logo: logo-jmk.jpg
dotace_defaults:
    meetPlace: 'Kr. Pole nádraží'
    transport: auty
    accomodation: 'na postelích'
    food: polopenze
    return: 'poslední den akce odpoledne'
    thingsToTake: 'sportovní oblečení, boty, buzolu, čip, hodinky'
---

{% set year = "now"|date("Y") %}
{% set qfrom = uri.query('from') %}
{% set qto = uri.query('to') %}
{% set dfrom = year ~ '-01-01' %}
{% set dto = year ~ '-12-31' %}
{% set from = (not (qfrom is iterable or qfrom is null) and qfrom matches '/^\\d{4}-\\d{2}-\\d{2}$/') ? qfrom : dfrom %}
{% set to = (not (qto is iterable or qto is null) and qto matches '/^\\d{4}-\\d{2}-\\d{2}$/') ? qto : dto %}
{% set camps = phpDotaceCamps(from, to) %}

<form method="get" action="/auth/dotace">
    <h2>Období</h2>
    <label>Od <input type="date" name="from" value="{{ from|e('html_attr') }}" required></label>
    <label>Do <input type="date" name="to" value="{{ to|e('html_attr') }}" required></label>
    <button type="submit">Zobrazit soustředění</button>
</form>
<hr>

{% if camps|length == 0 %}
    <p>V tomto období nejsou žádná soustředění.</p>
{% else %}
<form method="post" action="/php/dotace/generate">
    <input type="hidden" name="from" value="{{ from|e('html_attr') }}">
    <input type="hidden" name="to" value="{{ to|e('html_attr') }}">

    <h2>Dokumenty</h2>
    {% for key, variant in page.header.dotace_variants %}
        <div><label><input type="checkbox" name="docs[]" value="{{ key|e('html_attr') }}" checked> {{ variant.label|e }}</label></div>
    {% endfor %}
    <div><label><input type="checkbox" name="docs[]" value="seznam" checked> Seznam účastníků (ODS)</label></div>

    <h2>Soustředění</h2>
    <table>
        <thead>
            <tr><th></th><th>Soustředění</th><th>Termín</th><th>Název v dokumentu</th></tr>
        </thead>
        <tbody>
        {% for camp in camps %}
            <tr>
                <td><input type="checkbox" name="camps[]" value="{{ camp.id|e('html_attr') }}" checked></td>
                <td>{{ camp.title|e }}</td>
                <td>{{ camp.start|e }}{% if camp.end %} – {{ camp.end|e }}{% endif %}</td>
                <td><input type="text" name="titles[{{ camp.id|e('html_attr') }}]" value="{{ camp.defaultTitle|e('html_attr') }}" size="50">{% if not camp.fromMembers %}<br><small>bez seznamu (akce není z members)</small>{% endif %}</td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    <br>
    <button type="submit">Vygenerovat</button>
</form>
{% endif %}
