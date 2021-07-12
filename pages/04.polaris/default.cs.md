---
title: Polaris
process:
    twig: true
    markdown: false
---
<p>kontakt: <em>polaris@zabiny.club</em></p>
<hr>
{% set p = page.find('/data/polaris') %}
{% for rok, year in p.header.polaris %}
    <section>
    <h2>{{rok}}</h2>
    <div class="row">
        {% for cislo, pdf in year %}
            <div class="col-sm-6 col-md-3 col-lg-2"> 
                <div class="polaris">
                    <a href="/data/polaris/{{rok}}/{{pdf}}" target="_blank">
                        <img src="/data/polaris/{{rok}}/{{pdf}}.jpg">
                    </a>
                </div>
            </div>
        {% endfor %}
    </div>
    </section>
    <hr>
{% endfor %}
