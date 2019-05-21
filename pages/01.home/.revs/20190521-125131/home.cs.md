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

<div class="pure-g"> {# cela stranka | je pouzit css framework purecss.io grids #}
  
    <div id="novinky" class="pure-u-1 pure-u-sm-16-24"> <!-- plan + novinky vlevo -->
    <div class="inner">

      {% set news_collection = page.collection().ofType('novinka').order('p.header.id','asc') %}

      {% for p in news_collection.order('date','desc') %}

        {% if  ( p.header.date|date('Y-m-d') >= strtotime("today -30 day")|date('Y-m-d') ) %}
          <article id="{{ p.header.id }}">
            <h4 class="news--header">
                <span class="newsDate">{{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }} </span> &nbsp; &nbsp; <span class="newsTitle"> {{ p.header.title }} </span>
            </h4>
            <div class="pure-g newsPictures">
                {% for img in p.header.pictures %}
                  {# small screens shows double image size #}
                  {% set PC_img_ratio = img.ratio|slice(2,2) %}
                  {% if PC_img_ratio % 2 == 0  %}
                  {% set Mobile_img_ratio = PC_img_ratio // 2 %}
                  {% else %}
                  {% set Mobile_img_ratio = PC_img_ratio %}
                  {% endif %}
                  <div class="newsIMG pure-u-1-{{Mobile_img_ratio}} pure-u-sm-1-{{PC_img_ratio}}" data-name="{{img.name}}" data-ratio="{{ img.ratio }}">
                    <a href="{{base_url_absolute}}/databaze/{{p.header.date|slice(0,4)}}/novinky/novinka_{{p.header.id}}/img/{{img.name}}" target="_blank" title="Zobrazit originální obrázek">
                      <picture>
                        {# časem WebP #}
                        <img class="pure-img" src="user/pages/databaze/{{p.header.date|slice(0,4)}}/novinky/novinka_{{p.header.id}}/img/preview_{{img.name}}" alt="Zde by měl být obrázek">
                      </picture>
                    </a>
                  </div>
                {% endfor %}
            </div>
            <section class="newsText">
              {{p.content}}
            </section>
          </article>
          <hr width="62.11%">
        {% endif %}
      {% endfor %}
  </div>
   </div> <!--  novinky -->


    <div id="blizi-se" class="pure-u-1 pure-u-sm-8-24">
      <div class="timeline"></div>
      {% set soon_collection = page.collection().ofOneOfTheseTypes(['zavod', 'trenink', 'soustredeni', 'tabor']).order('p.header.start','asc') %}
      {% set currdate = strtotime("today")|date('Y-m-d') %}
      {% if soon_collection.first().header.start > currdate %}
            <h6><span class="dot"></span> &nbsp;
              {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper ~ ' | '~ currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd.M.')|upper }}
            </h6>
      {% endif %}
        
      {% for p in soon_collection %}
        {% if  (  p.header.start|date('Y-m-d') <= strtotime("today +10 day")|date('Y-m-d') and p.header.end|date('Y-m-d') >= strtotime("today")|date('Y-m-d') ) %}
          {% if p.header.start != currdate %}
            {% set currdate = p.header.start %}
            <h6><span class="dot"></span> &nbsp;
              {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper ~ ' | '~ currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd.M.')|upper }}
            </h6>
          {% endif %}
          <section>
            <b>
                {{ p.header.title ~' '~ p.header.event.location }} 
              <br>{% if p.header.eventTypeDescription is not empty %}<em style="font-weight:normal;">{{p.header.eventTypeDescription}}</em>{% endif %}
            </b>
            <article data-id="{{p.header.id}}" data-template="{{p.header.template}}">
              {{p.content}}
            </article>
          </section>

        {% endif %}
      {% endfor %}

    </div> <!-- blizi se -->

</div> <!-- uzavira celou stranku , pure-g -->
