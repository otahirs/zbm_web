---
title: Novinky
process:
    twig: true
    markdown: false
content:
    items:
        @page.descendants:
            '/data/events'
    filter: 
        routable: true
    order:
        by: header.start
        dir: asc
news:
    items:
        @page.descendants:
            '/data/news'
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
            <h1>Novinky</h1>
            <a href="#soon" id="hip" style="text-align:right;border-bottom:none;"><h1>hip<i class="fa fa-level-down" aria-hidden="true"></i></h1></a>
        </header>
        <section>
        {% set news_collection = page.collection('news') %}

        <!--
        <article>
              <h3 class="news--header row justify-content-between">
                  <span class="news--header_left col"> Plán na tento týden </span> 
              </h3>
              
              <section class="newsText">
                Aktuální plán na tento týden naleznete po rozkliknutí své tréninkové skupiny v levém menu.
              </section>
            </article>
        <hr>
        -->

        {% for p in news_collection if  ( p.header.date|date('Y-m-d') >= strtotime("today -30 day")|date('Y-m-d') ) %}
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
                    <div class="newsText--summary" style="cursor:pointer" title="Kliknutím zobrazíte celou novinku">{{summary}}</div>
                    <div class="newsText--content" style="display:none;">{{p.content}}</div>
                  {% else %}
                    <div class="newsText--content">{{p.content}}</div>
                  {% endif %}
                <div class="newsText--footer row justify-content-between no-gutters">
                  <div class="author col-auto"><em>{{p.header.user}}</em></div>
                  <a class="comment-count col-auto" href="{{p.url ~ "#commento"}}"> <i class="fa fa-comments-o" aria-hidden="true"></i></a>
                </div> 
              </section>
            </article>
            <hr>

        {% endfor %}
        </section>
      </div>      
      <script>
        window.addEventListener('DOMContentLoaded', () => {
            $('.newsText--summary').on('click', function(){
              this.nextElementSibling.style.display='block';
              this.style.display='none';
            });
        });  
      </script>
  </div> <!--  novinky -->


  <div id="soon" class="col-md-4">
      
      {% set soon_collection = page.collection() %}

      {# further filter the collection #}
      {% for p in soon_collection %}
        {% if  not (  p.header.start|date('Y-m-d') <= strtotime("today +10 day")|date('Y-m-d') and p.header.end|date('Y-m-d') >= strtotime("today")|date('Y-m-d') ) %}
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

      <div id="soon--timeline"></div>
      {% for i in 0..10 %}
          {% set currdate = strtotime("today +" ~ i ~ " day")|date('Y-m-d') %}

          {% if currdate in events|keys or currdate in entries|keys or currdate == "now"|date('Y-m-d') %}
            <h4 class="soon--date">
              <span class="soon--dot {% if currdate == "now"|date('Y-m-d') %} soon--dot-now {% endif %}"></span> &nbsp;      
              <span class="soon--countdown">
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
              <span class="soon--day"> 
                {{currdate|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccc d.M.') }}
              </span>
            </h4>
          {% endif %}
          
          {# ongoing events that started before today #}
          {% for p in attribute(events, currdate) if currdate in events|keys %}      
              <a href="{{p.url}}">
              <section class="event" title="Klikni pro více informací">
                <h3 class="soon-title">
                  {{ p.header.title ~' '~ p.header.event.location }} 
                  <br>
                  <em style="font-weight:normal;font-size: 1rem;">
                    {% set group = p.header.taxonomy.skupina %}
                    {% if group|length > 0 and group|length < 6 %}
                    {% if "zabicky" in group %} žabičky {% endif %} 
                    {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                    {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                    {% if "dorost" in group %} dorost+ {% endif %}
                    {% endif %}
                  </em>
                </h3>
                
                <article class="soon-content" data-id="{{p.header.id}}" data-template="{{p.header.template}}">
                  {{p.content|markdown}}
                </article>
              </section>
              </a>
          {% endfor %}

          {% if currdate in entries|keys %}
            <section class="soon-deadline">
              <div class="title"><i class="fa fa-bell-o" aria-hidden="true"></i> Končící přihlášky <br></div>

              {% for event in attribute(entries, currdate) %}
                <div class="entry{% if loop.last %} last{% endif %}">
                  {{event.name}} <br>{{event.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM')}}
                </div>
              {% endfor %}
            </section>
          {% endif %}
      {% endfor %}

       


  <a href="#top" id="hop" style="text-align:right;border-bottom:none;"><h1>hop<i class="fa fa-level-up" aria-hidden="true"></i></h1></a>
  </div> <!-- blizi se -->

</div> <!-- uzavira celou stranku , pure-g -->
