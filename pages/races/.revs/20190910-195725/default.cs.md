---
title: 'Soutěže pořádané naším oddílem'
date: '2019-06-19'
process:
    markdown: false
---

<div class="row">
    <div class="col-2"><a href="{{base_url}}/races/zhustastr.html">Zhusta Cup</a></div>
    <div class="col-3"><a href="http://bzl.zbmob.cz/">Brněnská zimní liga</a></div>
    <div class="col-3"><a href="{{base_url}}/races/supersprint.html">Mistrovství Brna ve sprintu</a></div>
    <div class="col-4"><a href="http://zbm.eob.cz/op/op.htm">Oddílové přebory od roku 1981</a></div>
</div>
<hr>

<h2>Závody v rámci ČSOS</h2>

{% try %}
    {% set oris = "https://oris.orientacnisporty.cz/API/?format=json&method=getEventList&club=205&datefrom=1970-01-01"|getJson %}
    {% set year = 0 %}
    {% for race in oris.Data|reverse %}
        {% if race.Date[0:4] != year %}
            {% set year = race.Date[0:4] %}
            {% if not loop.first %} </div><br><br> {% endif %}
            <h3>{{ race.Date[0:4] }}</h3>
            <div class="row">
        {% endif %}
        <span class="col-4 col-xs-3 col-md-2">{{ race.Date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}</span>
        <span class="col-8 col-xs-9 col-md-10"><a href="https://oris.orientacnisporty.cz/Zavod?id={{race.ID}}">{{race.Name}}</a></span>
    {% endfor %}
    </div>
{% catch %}
    <div class="notices red">
        <p> Chyba přiojení na ORIS </p>
    </div>
{% endcatch %}



<br><br>
<h3>2012</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">6. a 7. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://mcrdruzstva.eob.cz/">Mistrovství ČR štafet, klubů a oblastních výběrů</a></span>
    <span class="col-4 col-xs-3 col-md-2">23. června</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z120623/">Mistrovství Jihomoravského kraje na klasické trati</a></span>
</div>

<br><br>
<h3>2011</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">15. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z111015/">13. závod Jihomoravské ligy</a></span>
    <span class="col-4 col-xs-3 col-md-2">16. a 17. dubna</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/mcr11/">M ČR v nočním OB</a></span>
</div>
<br><br>
<h3>2010</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">22. a 23. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z1005/">Český pohár štafet</a></span>
    <span class="col-4 col-xs-3 col-md-2">22. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z1005/">hummel sprint cup, žebříček A - sprint, WRE</a></span>
</div>
<br><br>
<h3>2009</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">29. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z090529/">M Brna ve sprintu, M JmK ve sprintu</a></span>
    <span class="col-4 col-xs-3 col-md-2">30. květen</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z080530/">Veřejný závod štafet</a></span>
    <span class="col-4 col-xs-3 col-md-2">19. duben</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/mcr08/">M ČR na dlouhé trati v OB</a></span>
</div>
<br><br>
<h3>2008</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">13. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z071013/">10. závod Jihomoravské ligy</a></span>
    <span class="col-4 col-xs-3 col-md-2">12. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z070512/">Mistrovství Jihomoravského kraje na klasické trati</a></span>
    <span class="col-4 col-xs-3 col-md-2">11. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z070511/">Mistrovství Jihomoravského kraje ve štafetách</a></span>
</div>
<br><br>
<h3>2006</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">14. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z061014181/">12. závod Jihomoravské ligy</a></span>
    <span class="col-4 col-xs-3 col-md-2">17. a 18. června</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/mcr06/">M ČR na krátké trati v OB</a></span>
    <span class="col-4 col-xs-3 col-md-2">26. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z060526918/">Mistrovství Jihomoravského kraje ve štafetách</a></span>
</div>
<br><br>
<h3>2005</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">15. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z051015199/">6. podzimní oblastní žebříček</a></span>
    <span class="col-4 col-xs-3 col-md-2">1. a 2. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/mcr05/">M ČR na krátké a klasické trati v MTBO, Český pohár MTBO (12. a 13. kolo)</a></span>
    <span class="col-4 col-xs-3 col-md-2">14. a 15. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z0505/">Český pohár štafet a Česká liga klubů</a></span>
    <span class="col-4 col-xs-3 col-md-2">14. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z0505/">Continental cup, HI-TEC sprint cup a žebříček A</a></span>
</div>
<br><br>
<h3>2004</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">5. a 6. června</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z0406/vysledky.htm">Continental cup, Silva cup a žebříček A</a></span>
</div>
<br><br>
<h3>2003</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">6. a 7. září</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z0309/vysledky.htm">Český pohár štafet a Česká liga klubů</a></span>
    <span class="col-4 col-xs-3 col-md-2">6. září</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z0309/vysledky.htm">žebříček A, Silva cup a Continental cup</a></span>
</div>
<br><br>
<h3>2002</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">21. dubna</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z020421041/">5. JOŽ žactva, dorostu a dospělých</a></span>
</div>
<br><br>
<h3>2001</h3>
<div class="row">
    <span class="col-4 col-xs-3 col-md-2">13. října</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z011013189/vysledky.htm">4. podzimní oblastní žebříček</a></span>
    <span class="col-4 col-xs-3 col-md-2">19. května</span>
    <span class="col-8 col-xs-9 col-md-10"><a href="http://zbm.eob.cz/zavody/z010519064/vysledky.htm">7. oblastní žebříček</a></span>
</div>
<br><br>




