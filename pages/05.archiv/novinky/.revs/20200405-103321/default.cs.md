---
title: 'Archiv novinek'
process:
    twig: true
    markdown: false
news:
    items:
        '@page.descendants': /data/news
    filter:
        routable: true
    order:
        by: header.id
        dir: desc
---

  <div id="novinky" > <!-- plan + novinky vlevo -->

        {% set news_collection = page.collection('news') %}

        {% for p in news_collection %}
            <article id="{{ p.header.id }}">
              <h3 class="news--header row justify-content-between">
                  <span class="news--header_left col"> {{ p.header.title }} </span> 
                  <span class="news--header_right col-auto"> {{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }}</span>
              </h3>
              {% if p.header.pictures|length > 0 %}
              <div class="row newsPictures">
                  {% for img in p.header.pictures %}
                    <div class="newsIMG col-2">
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
                  {% set summary = p.content|safe_truncate_html(20) %}
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
      <script>
        window.addEventListener('DOMContentLoaded', () => {
            $('.newsText--summary').on('click', function(){
              this.nextElementSibling.style.display='block';
              this.style.display='none';
            });
        });  
      </script>
  </div> <!--  novinky -->

