---
title: Novinky
process:
    twig: true
    markdown: false
content:
    items:
        '@page.descendants': /data/events
    filter:
        routable: true
    order:
        by: header.start
        dir: asc
news:
    items:
        '@page.descendants': /data/news
    filter:
        routable: true
    order:
        by: header.id
        dir: desc
---

<div class="row no-gutters" style="height:100%"> {# cela stranka | je pouzit css framework purecss.io grids #}
 
  <div id="novinky" class="col-md-8"> <!-- plan + novinky vlevo -->
      <div class="inner">
        <header id="header">
            <h1>Novinky
              <a href="#right_box" id="hip" style="text-align:right;border-bottom:none;"><span style="float: right;"><i class="fa fa-level-down" aria-hidden="true"></i></span></a>
            </h1>
        </header>
        <section>
        {% set news_collection = page.collection('news') %}
        {% for p in news_collection if  ( p.header.date|date('Y-m-d') >= strtotime("today -45 day")|date('Y-m-d') ) %}
            <article id="{{ p.header.id }}">
              <h3 class="news--header row justify-content-between">
                  <span class="news--header_left col"> {{ p.header.title }} </span> 
                  <span class="news--header_right col-auto"> {{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }}</span>
              </h3>
              {% if p.header.pictures|length > 0 %}
              <div class="row newsPictures">
                  {% for img in p.header.pictures %}
                    {# small screens shows double image size #}
                    {% set img_mobile = img.ratio %}
                    {% if img.ratio * 2 <= 12  %}
                      {% set img_mobile = img.ratio * 2 %}
                    {% endif %}
                    <div class="newsIMG col-md-{{img.ratio}} col-{{img_mobile}}" data-name="{{img.name}}" data-ratio="{{ img.ratio }}">
                      <a href="/data/news/{{p.header.date|slice(0,4)}}/{{p.header.id}}/img/{{img.name}}" target="_blank" title="Zobrazit originální obrázek">
                        <picture>
                          {# časem WebP #}
                          <img src="/data/news/{{p.header.date|slice(0,4)}}/{{p.header.id}}/img/{{img.name}}_preview.jpg" alt="Zde by měl být obrázek">
                        </picture>
                      </a>
                    </div>
                  {% endfor %}
              </div>
              {% endif %}
              <section class="newsText">
                  {% set summary = p.content|safe_truncate_html(70) %}
                  {% if p.content != summary %}
                    <div class="newsText--summary">{{summary}} <span class="newsText--show-all" style="cursor:pointer" title="Kliknutím zobrazíte celou novinku">zobrazit vše</span></div>
                    <div class="newsText--content" style="display:none;">{{p.content}}</div>
                  {% else %}
                    <div class="newsText--content">{{p.content}}</div>
                  {% endif %}
                <div class="newsText--footer row justify-content-between no-gutters">
                  <div class="author col-auto"><em>{{p.header.user}}</em></div>
                  <!-- <a class="comment-count col-auto" href="{{p.url ~ "#commento"}}"> <i class="fa fa-comments-o" aria-hidden="true"></i></a> -->
                </div> 
              </section>
            </article>
            <hr>

        {% endfor %}
        <div style="margin-top: -1em;"></div>
        {% for p in news_collection if  ( p.header.date|date('Y-m-d') >= strtotime("today -135 day")|date('Y-m-d') and p.header.date|date('Y-m-d') < strtotime("today -45 day")|date('Y-m-d') ) %}
            <article id="{{ p.header.id }}">
              <h3 class="news--header row justify-content-between">
                  <span class="news--header_left col"> <a href="{{p.url}}" style="border-bottom: none;">{{ p.header.title }}</a> </span> 
                  <span class="news--header_right col-auto"> {{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }}</span>
              </h3>
            </article>
            <hr style="margin: 1em 0;">
        {% endfor %}
        </section>
      </div>      
      <script>
        window.addEventListener('DOMContentLoaded', () => {
            $('.newsText--show-all').on('click', function(){
              this.parentElement.nextElementSibling.style.display='block';
              this.parentElement.style.display='none';
            });
        });  
      </script>
  </div> <!--  novinky -->

  <div id="right_box" class="col-md-4">
    <div>
      <h4>Odkazy</h4>
        <a class="home__link" href="https://www.orientacnisporty.cz/">Český&nbsp;svaz&nbsp;orientačních&nbsp;sportů</a>
        <a class="home__link" href="https://oris.orientacnisporty.cz/">ORIS</a> </br>
        <a class="home__link" href="http://www.lpu.cz/beda/">Jak&nbsp;to&nbsp;vidí&nbsp;Béďa</a> 
        <a class="home__link" href="https://reprezentace.orientacnibeh.cz/">Reprezentace&nbsp;OB</a> </br>
        <a class="home__link" href="https://tsm.zabiny.club">TSM&nbsp;Jižní&nbsp;Morava</a>
        <a class="home__link" href="https://mapy.orientacnisporty.cz/cs/clubs/zbm">Vydané&nbsp;mapy</a>
    </div>
    <div>    
      <h4>Tréninkové skupiny</h4>
      <div id="home__groups" style="display:flex;">
          <div style="flex:1;">
            <a href="/skupiny/dorost" class="home__groups-text">Dorost+</a> <br>
            <a href="/skupiny/hobby" class="home__groups-text">Hobby</a>
          </div>
          <div style="flex:1;">
            <a href="/skupiny/pulci1" class="home__groups-text">Pulci&nbsp;1</a> <br>
            <a href="/skupiny/pulci2" class="home__groups-text">Pulci&nbsp;2</a>
          </div>
          <div style="flex:1;">
            <a href="/skupiny/zaci1" class="home__groups-text">Žáci&nbsp;1</a> <br>
            <a href="/skupiny/zaci2" class="home__groups-text">Žáci&nbsp;2</a>
          </div>
          <div style="flex:1;">
            <a href="/skupiny/zabicky" class="home__groups-text">Žabičky</a>
          </div>
        </div>
    </div>
    <div>
      <h4>Události</h4>
      <div class="soon__history">
        <div class="history__timeline"></div>
        {% set history_collection = page.collection() %}
        {% for p in history_collection.order('header.end', 'desc') if not ( p.header.end|date('Y-m-d') < strtotime("today")|date('Y-m-d') ) %}
            {% set history_collection = history_collection.remove() %}
        {% endfor %}
        {% for p in history_collection.slice(0,3).order('header.end') %}
          <section class="history__event">
            <a href="{{p.url}}" title="Klikni pro všechny informace"  class="history__link-to-event">
              <h4 class="history__title">
                {{ p.header.title ~' '~ p.header.event.location }} 
                <em style="font-weight:normal;font-size: 1rem;">
                  {% set group = p.header.taxonomy.skupina %}
                  {% if group|length > 0 and group|length < 6 %}
                  {% if "zabicky" in group %} žabičky {% endif %} 
                  {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                  {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                  {% if "dorost" in group %} dorost+ {% endif %}
                  {% if "hobby" in group %} hobby {% endif %}
                  {% endif %}
                </em>
              </h4>
            </a>
            <article class="soon__content" data-orisid="{{p.header.orisid}}" data-history="true">
              {% if p.header.hasStartist %}
                <a class="external-link" href='https://oris.orientacnisporty.cz/Startovka?id={{p.header.orisid}}' target="_blank">startovky</a><br>
              {% endif %}
              {% if p.header.hasResults %}
                <a class="external-link" href='https://oris.orientacnisporty.cz/Vysledky?id={{p.header.orisid}}' target="_blank">výsledky</a>
              {% endif %}
            </article>
          </section>
        {% endfor %}
      </div>
        
        {% set soon_collection = page.collection() %}
  
        {# further filter the collection #}
        {% for p in soon_collection %}
          {% if  not (  p.header.start|date('Y-m-d') <= strtotime("today +14 day")|date('Y-m-d') and p.header.end|date('Y-m-d') >= strtotime("today")|date('Y-m-d') ) %}
              {% set soon_collection = soon_collection.remove() %}
          {% endif %}
        {% endfor %}
         
        {% set events = collectionToEventsByDate(soon_collection) %}
        
        {# try to get entries from members.zbm.eob #}
        {% try %}
            {% set entries = "https://members.eob.cz/zbm/api_racelist.php"|getJsonZbmEntries %}
        {% catch %}
          <div class="notices red">
            <p>Chyba připojení na přihláškov systém, nejsou zobrazeny blížící se přihlášky.</p>
          </div>
        {% endcatch %}
  
        <div class="soon__timeline"></div>
        {% for i in 0..14 %}
            {% set currdate = strtotime("today +" ~ i ~ " day")|date('Y-m-d') %}
  
            {% if currdate in events|keys or currdate in entries|keys or currdate == "now"|date('Y-m-d') %}
              <h4 class="soon__date">
                <span class="soon__dot {% if currdate == "now"|date('Y-m-d') %} soon__dot--now {% endif %}"></span> &nbsp;      
                <span class="soon__countdown">
                  {% if i == 0 %}
                    dnes
                  {% elseif i == 1 %}
                    zítra
                  {% elseif 1 < i and i < 5 %}
                    za {{i}} dny
                  {% else %}
                    za {{i}} dní
                  {% endif %}
                </span>
                <span class="soon__day"> 
                  {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccc d.M.') }}
                </span>
              </h4>
            {% endif %}
            
            {# ongoing events that started before today #}
            {% for p in attribute(events, currdate) if currdate in events|keys %}      
                
                <section class="soon__event">
                  <a href="{{p.url}}" title="Klikni pro všechny informace"  class="soon__link-to-event">
                    <h4 class="soon__title">
                      {{ p.header.title ~' '~ p.header.event.location }} 
                      <br>
                      <em style="font-weight:normal;font-size: 1rem;">
                        {% set group = p.header.taxonomy.skupina %}
                        {% if group|length > 0 and group|length < 6 %}
                        {% if "zabicky" in group %} žabičky {% endif %} 
                        {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                        {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                        {% if "dorost" in group %} dorost+ {% endif %}
                        {% if "hobby" in group %} hobby {% endif %}
                        {% endif %}
                      </em>
                    </h4>
                  </a>
                  <article class="soon__content" data-id="{{p.header.id}}" data-template="{{p.header.template}}" data-orisid="{{p.header.orisid}}">
                    {{p.content|markdown}}
                    {% if p.header.hasStartist %}
                      <a class="external-link" href='https://oris.orientacnisporty.cz/Startovka?id={{p.header.orisid}}' target="_blank">startovky</a><br>
                    {% endif %}
                    {% if p.header.hasResults %}
                      <a class="external-link" href='https://oris.orientacnisporty.cz/Vysledky?id={{p.header.orisid}}' target="_blank">výsledky</a>
                    {% endif %}
                  </article>
                </section>
                
            {% endfor %}
  
            {% if currdate in entries|keys %}
              <section class="soon__deadline">
                {% for event in attribute(entries, currdate) %}
                  <div class="entry">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>&nbsp; {{event.name}}
                  </div>
                {% endfor %}
              </section>
            {% endif %}
        {% endfor %}
  
    <a href="#novinky" id="hop" style="text-align:right;border-bottom:none;"><h1><i class="fa fa-level-up" aria-hidden="true"></i></h1></a>
    </div> <!-- blizi se -->

  </div>
  

</div> <!-- uzavira celou stranku , pure-g -->
