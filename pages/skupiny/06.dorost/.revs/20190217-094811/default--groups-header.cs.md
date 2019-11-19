---
title: Dorost+
process:
    twig: true
    markdown: false
skupina: dorost
---

{% set collection = taxonomy.findTaxonomy({'skupina':page.header.skupina})  %}  {# .order({'by': page.header.start, 'dir': 'asc'}) musí se otestovat #}
<div class="pure-g"> 
    <div class="pure-u-1">
      Do skupiny dorost+ patří všichni starší 15 let. Tady už je to tvrdá dřina, náročné tréninky a při závodě samé kopca a hustníky. Odměnou nám jsou silné zážitky skvělý kolektiv, a kamarádi celý život. Jo, a o prázdnínách samozřejmě do Švédska, Norska..
      <hr>
    </div>
    <div class="pure-u-1 pure-u-sm-1-2" id="plan">
        <h2>Plán na tento týden</h2>
        {# ziska array s pravidelnymi treninky #}
        {% set pravidelne_treninky_site = page.find('/auth/plan') %}
        {% set pravidelne_treninky =  attribute(attribute(pravidelne_treninky_site, "header"), "plan") %}
        <table class="plan">
            {# inicializace pole urcujiciho den v tydnu #}
            {% set ENGweek = ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'] %}
            {% set CZweek = [ 'PO', 'UT', 'ST', 'CT', 'PA', 'SO', 'NE'] %}
            {# inicializace promene urcujici jestli uz je v tabulce zapsany den v tydnu #}
            {% set zapsany_den = 'NULL' %}

            {# cyklus pro 7 dni prochazejici pole "CZweek", aktualni promenou zastupuje "den" #}
            {% for day_num,den in CZweek %}
                {# "datum_dne_v_tydnu" drzi datum dnu v aktualnim tydnu#}
                {% set datum_dne_v_tydnu = strtotime([strtotime("last Sunday")|date('Y-m-d'), " +", loop.index, " day"]|join)|date('Y-m-d') %} {#kvuli chybe ve 'strtotime' nelze zadat jen 'last Sunday + day'#}
                {#cyklus prochazejici vsechny soubory v databazi#}
                {% for p in collection if p.header.template in ['zavod', 'trenink', 'soustredeni', 'tabor'] %}
                    {# pokud je mezi start a end datum_dne_v_tydnu, zobrazi se v tabulce#}
                    {%  if (  p.header.start <= datum_dne_v_tydnu and p.header.end >= datum_dne_v_tydnu ) %}
                      <tr {% if day_num % 2 == 0 %} class="plan--lichyDen"  {% else %} class="plan--sudyDen" {% endif %}>
                        <td class="den">
                            {# zobrazi nazev dne (napr. "PO") jen pokud jiz neexistuje zaznam dne v tabulce #}
                            {% if zapsany_den != den %}    {# podminka, jestli uz se zapisoval #}
                              {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
                              {% set zapsany_den = den %}   {# ulozime, ze uz se zapisoval #}
                            {% endif %}
                        </td>
                        <td class="nazev"> <a href="{{ p.url }}">{{ p.title }}</a></td>         {# vypise nazev s odkazem na event #}
                        <td class="sraz">
                          {# pokud se datum dne rovná datu zacatku eventu, zobrazi sraz #}
                          {% if p.header.start == datum_dne_v_tydnu %}
                            {% if p.header.meetTime is defined  %}
                              {{p.header.meetTime }}
                            {% endif %}
                          {% endif %}
                        </td>    
                        <td class="misto"> 
                            {# pokud se datum dne rovná datu zacatku eventu, zobrazi misto srazu #}
                            {% if p.header.start == datum_dne_v_tydnu %}
                            {% if p.header.meetPlace is defined  %}
                              {{p.header.meetPlace }}
                            {% endif %}
                          {% endif %}
                        </td>                            
                      </tr>
                    {% endif %}
                {% endfor %}
                {% for event in attribute(pravidelne_treninky, ENGweek[day_num]) if page.header.skupina in event.group %}
                  <tr {% if day_num % 2 == 0 %} class="plan--lichyDen"  {% else %} class="plan--sudyDen" {% endif %}>
                        <td class="den">
                            {# zobrazi nazev dne (napr. "PO") jen pokud jiz neexistuje zaznam dne v tabulce #}
                            {% if zapsany_den != den %}    {# podminka, jestli uz se zapisoval #}
                              {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
                              {% set zapsany_den = den %}   {# ulozime, ze uz se zapisoval #}
                            {% endif %}
                        </td>
                        <td class="nazev"> {{event.name}} </td>         {# vypise nazev s odkazem na event #}
                        <td class="sraz"> {{event.meetup}} </td>
                        <td class="misto"> {{event.place}} </td>                            {# vypise misto konani #}
                  </tr>
                {% endfor %}
                  {# pokud neni v den nic, je volno #}
                {% if zapsany_den != den %}  {# jestli se nezapisoval den do tabulky, neni tam zadny zaznam => zapiseme ze je volno #}
                  <tr {% if day_num % 2 == 0 %} class="plan--lichyDen"  {% else %} class="plan--sudyDen" {% endif %}>
                    <td class="den"> {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}  </td>
                    <td class="nazev"> volno </td>
                    <td class="misto"> </td>
                    <td class="sraz"> </td>
                  </tr>
                {% endif %}
            {% endfor %}
        </table>
        <br>
        <h2>Soustředění</h2>
            <table>
            {% for p in collection if p.header.end >= strtotime("today")|date('Y-m-d') and p.header.template == "soustredeni" %}
                    <tr>
                      <td class="datum">
                            {# HELP formaty casu http://userguide.icu-project.org/formatparse/datetime #}
                            {# HELP |localizeddate http://twig-extensions.readthedocs.io/en/latest/intl.html#}
                            {# pokud neni stejny mesic - format 6. cerven - 2. cervenec #}
                            {% if p.header.start|localizeddate('medium', 'none','cs','Europe/Prague', 'M') != p.header.end|localizeddate('medium', 'none','cs','Europe/Prague', 'M')%}
                            {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc d. MMMM') ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc d. MMMM') }}
                            {# pokud neni stejny den - format 6.-8. cerven #}
                            {% elseif p.header.start|localizeddate('medium', 'none') != p.header.end|localizeddate('medium', 'none')%}
                            {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc d.') ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc d. MMMM') }}
                            {% else %}
                            {# pokud stejny den - format 6. cerven #}
                            {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc d. MMMM') }}
                            {% endif %}
                        </td>
                        <td class="nazev"><a href="{{ p.url }}">{{ p.title }}</a></td>
                        <td class="misto">{{p.header.place}}</td>
                    </tr>
            {% else %}
                <tr>
                    <td><em>Momentálně nejsou naplánována žádná soustředění.</em></td>
                </tr>
            {% endfor %}
            </table>
    </div>
</div> <!-- pure-g -->