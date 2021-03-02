---
title: 'Editorské menu'
date: '2018-07-09'
process:
    twig: true
    markdown: false
never_cache_twig: true
access:
    site:
        login: true
---
<style>
    .row > div > a {
        display: block;
        border: solid black 1px;
        min-height: 100px;    
        padding: 20px;
    }
</style>
<div class="row no-gutters">
    <div class="col-sm-6 col-md-4">
        <a href="/auth/novinky">
            <h2>Novinky</h2>
        </a>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="/auth/plan2">
            <h2>Týdenní plán</h2>
        </a>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="/auth/events">
            <h2>Události</h2>
        </a>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="/auth/polaris">
            <h2>Polaris</h2>
        </a>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="/auth/maptheory">
            <h2>Mapová teorie</h2>
        </a>
    </div>
    <div class="col-sm-6 col-md-4">
        <a href="/user_profile">
            <h2>Můj účet</h2>
        </a>
    </div>
</div>
<hr>
<h2>Changelog</h2>
<ul>
    <li>2020-06-16 <br> Přihlášení na stránky nyní nepřesměrovává. Tedy pokud půjdete např na zabiny.club/auth/plan2, ale nebudete přihlášení, tak po následném přihlášení nebudete přesměrování na rozcestník, ale můžete pokračovat přímo na původní stránce.</li>
    <li>2020-06-11 <br> Plán na tento týden přepracován. Možnost filtrovat plán dle skupin. Rozšířené možnosti šablon.</li>
    <li>2020-04-30 <br>Závody zrušené v členské sekci jsou nyní na stránkách smazány. Záloha je uložena pro případnou obnovu. <br>Zálohovány jsou nyní i události smazané přes editaci.</li>
    <li>U <a href="https://zabiny.club/auth/events">seznamu událostí</a> lze nyní ve filtru zaškrtnout "zobrazit již uplynulé" události. U akcí z let minulých se zobrzí i příslušný rok.</li>
    <li>Nyní lze filtrovat  <a href="https://zabiny.club/auth/events">události</a> se zachováním aktuálního data. Filtr již nezpůsobí zobrazení všech událostí od počátku věků.</li>
</ul>
