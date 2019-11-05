---
title: Polaris
process:
    twig: true
    markdown: false
---

{% set p = page.find('/data/polaris') %}
{% for rok, year in p.header.polaris %}
    <section>
    <h2>{{rok}}</h2>
    <div class="row">
        {% for cislo, pdf in year %}
            <div class="col-sm-6 col-md-3 col-lg-2"> 
                <div class="polaris--outerDiv">
                    <div class="polaris--innerDiv">
                        <a href="/data/polaris/{{rok}}/{{pdf}}" target="_blank">
                            <img src="/data/polaris/{{rok}}/{{pdf}}.jpg">
                            <div class="polaris--title"> 
                                {{pdf[13:2]}}
                            </div>
                        </a> 
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
    </section>
    <hr>
{% endfor %}
