---
title: 'Editorské menu'
date: '2018-07-09'
process:
    twig: true
    markdown: false
never_cache_twig: true
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
        <a href="/auth/plan">
            <h2>Plán na tento týden</h2>
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
        <a href="/auth/nahrat-program">
            <h2>Importovat program</h2>
        </a>
    </div>
</div>
<hr>
<a href="/user_profile">můj účet</a>
<hr>
    <div>
        <p>Chyby na opravu zde:
            <ul>
                <li>nadchazejici eventy bez zadane skupiny</li>
                <li>bez zadaneho srazu</li>
                <li>(zavody bez linku na Oris)</li>
                <li>??</li>                
            </ul>
            
        </p>
    </div>
