---
title: Závod
date: '2019-09-11'
process:
    markdown: false
never_cache_twig: true
---
<style>
    .col-3{
        text-align: right;
        font-weight: bold;
        margin-bottom: 1em;
    }
</style>
{% try %}
   
    {% set url = "https://oris.orientacnisporty.cz/API/?format=json&method=getEvent&id=" ~ uri.query('id') %}
    {% set oris = url|getJson %}
    {{dump(oris)}}
    {% if uri.query('id') == null or oris.Status != "OK" %}
    <div class="notices red">
        <p> Chybné ID závodu </p>
    </div>
    {% endif %}
    {% catch %}
    <div class="notices red">
        <p> Chyba přiojení na ORIS </p>
    </div>
    {% endcatch %}
    {% set oris = oris.Data %}
    <div class="row">
        <div class="col-3">Datum</div>
        <div class="col-9">{{oris.Date|date("d.m.Y")}}</div>

        <div class="col-3">Název</div>
        <div class="col-9">{{oris.Name}}</div>

        <div class="col-3">Místo</div>
        <div class="col-9">{{oris.Place}}</div>

        <div class="col-3">Pořádající oddíl</div>
        <div class="col-9">{{oris.Org1.Abbr}}{{oris.Org2.Abbr ? "+ " ~ oris.Org2.Abbr : "" }}</div>

        <div class="col-3">Žebříček</div>
        <div class="col-9">{{oris.Level.NameCZ}}</div>

        <div class="col-3">Ranking</div>
        <div class="col-9">{{oris.Ranking ? 'Ano' : 'Ne'}}</div>

        <div class="col-3">Odkaz</div>
        <div class="col-9">https://oris.orientacnisporty.cz/Zavod?id={{uri.query('id')}}</div>

        <div class="col-3">1. datum přihlášek</div>
        <div class="col-9">{{ (oris.EntryDate1 ~ "-1 day")|date("d.m.Y") }}</div>

        <div class="col-3">2. datum přihlášek</div>
        <div class="col-9">{{ (oris.EntryDate2 ~ "-1 day")|date("d.m.Y") }}</div>

        <div class="col-3">3. datum přihlášek</div>
        <div class="col-9">{{ (oris.EntryDate3 ~ "-1 day")|date("d.m.Y") }}</div>

        <div class="col-12"><hr></div>

        <div class="col-3">Seznam kategorií</div>
        <div class="col-9" style="line-break: anywhere;">
            {% for class in oris.Classes %}{{class.Name}};{% endfor %}
        </div>
        <div class="col-12"><hr></div>
        <div class="col-3">Účet</div>
        <div class="col-9">{{oris.EntryBankAccount|replace({'xxxx':'1113'}) }}</div>
    </div>
    <br><br>
    


