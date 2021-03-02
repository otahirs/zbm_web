---
title: Polaris
process:
    twig: true
    markdown: false
---
{% set p = page.find('/auth/polaris') %}
{% for rok, year in p.header.polaris %}
    <section>
    <h2>{{rok}}</h2>
    <div class="pure-g">
        {% for cislo, pdf in year %}
            <div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-4 pure-u-lg-1-5 pure-u-xl-1-6"> 
                <div class="polaris--outerDiv">
                    <div class="polaris--innerDiv">
                        <a href="{{base_url_absolute}}/databaze/polaris/{{rok}}/{{pdf}}" target="_blank">
                            <img class="pure-img" src="{{base_url_absolute}}/databaze/polaris/{{rok}}/{{pdf}}.jpg">
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