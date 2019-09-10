---
title: Novinky
process:
    twig: true
    markdown: false
content:
    items: '@root.descendants'
    order:
        by: date
        dir: asc
---

<div class="row no-gutters" style="height:100%"> {# cela stranka | je pouzit css framework purecss.io grids #}
  
  <div id="novinky" class="col-md-8"> <!-- plan + novinky vlevo -->
      <div class="inner">
        <header id="header">
            <h1>Novinky</h1>
        </header>
        <section>
        {% set news_collection = page.collection().ofType('novinka').order('p.header.id','asc') %}

        {% for p in news_collection.order('date','desc') %}

          {% if  ( p.header.date|date('Y-m-d') >= strtotime("today -30 day")|date('Y-m-d') ) %}
            <article id="{{ p.header.id }}">
              <h4 class="news--header">
                  <span class="newsDate">{{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }} </span> &nbsp; &nbsp; <span class="newsTitle"> {{ p.header.title }} </span>
              </h4>
              {% if p.header.pictures|length > 0 %}
              <div class="row newsPictures">
                  {% for img in p.header.pictures %}
                    {# small screens shows double image size #}
                    {% set img_mobile = img.ratio %}
                    {% if img.ratio * 2 <= 12  %}
                      {% set img_mobile = img.ratio * 2 %}
                    {% endif %}
                    <div class="newsIMG col-md-{{img.ratio}} col-sm-{{img_mobile}}" data-name="{{img.name}}" data-ratio="{{ img.ratio }}">
                      <a href="{{base_url_absolute}}/data/news/{{p.header.date|slice(0,4)}}/{{p.header.id}}/img/{{img.name}}" target="_blank" title="Zobrazit originální obrázek">
                        <picture>
                          {# časem WebP #}
                          <img src="{{base_url_absolute}}/data/news/{{p.header.date|slice(0,4)}}/{{p.header.id}}/img/preview_{{img.name}}" alt="Zde by měl být obrázek">
                        </picture>
                      </a>
                    </div>
                  {% endfor %}
              </div>
              {% endif %}
              <section class="newsText">
                {{p.content}}
              </section>
            </article>
            <hr width="62.11%">
          {% endif %}

        {% endfor %}
        </section>
      </div>      
  </div> <!--  novinky -->


  <div id="soon" class="col-md-4">
      <div id="soon--timeline"></div>
      {% set soon_collection = page.collection().ofOneOfTheseTypes(['zavod', 'trenink', 'soustredeni', 'tabor']).order('header.start','asc') %}
      {% set currdate = strtotime("today")|date('Y-m-d') %}
              
      {% for p in soon_collection %}
        {% if  (  p.header.start|date('Y-m-d') <= strtotime("today +10 day")|date('Y-m-d') and p.header.end|date('Y-m-d') >= strtotime("today")|date('Y-m-d') ) %}
        
          {% if first is not defined %}
              <h6 class="soon--date soon--date-now"><span class="soon--dot soon--dot-now"></span> &nbsp;
              {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper ~ ' | '~ currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd.M.')|upper }}
              </h6>
            {% set first = 1 %}
          {% endif %}

          {% if p.header.start > currdate %}
            {% set currdate = p.header.start %}
            <h6 class="soon--date"><span class="soon--dot"></span> &nbsp;
              {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper ~ ' | '~ currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd.M.')|upper }}
            </h6>
          {% endif %}

          <a href="{{p.url}}">
          <section>
            <h4 class="soon-title">
                {{ p.header.title ~' '~ p.header.event.location }} 
              <br>
              <em style="font-weight:normal;">
                {% set group = p.header.taxonomy.skupina %}
                {% if group|length > 0 and group|length < 6 %}
                {% if "zabicky" in group %} žabičky {% endif %} 
                {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                {% if "dorost" in group %} dorost+ {% endif %}
                {% endif %}
              </em>
            </h4>
            <article class="soon-content" data-id="{{p.header.id}}" data-template="{{p.header.template}}">
              {{p.content}}
            </article>
          </section>
          </a>

        {% endif %}
      {% endfor %}

  </div> <!-- blizi se -->

</div> <!-- uzavira celou stranku , pure-g -->
