---
title: 'Upravit Novinky'
process:
    twig: true
    markdown: false
access:
    site:
        login: true
never_cache_twig: true
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

<div class="row no-gutters" style="height: 100%;"> {# cela stranka | je pouzit css framework purecss.io grids #}
  
    <div id="novinky" class="col-md-8"> <!-- plan + novinky vlevo -->
    <div class="inner">
        <header id="header">
            <h1>Novinky</h1>
            <span id="addNewsButton" class="button special">Přidat&nbsp;<i class="fa fa-plus-square-o" aria-hidden="true"></i></span>
        </header>
        <section>
    

      {% set news_collection = page.collection('news') %}

      {% for p in news_collection if ( p.header.date|date('Y-m-d') >= strtotime("today -30 day")|date('Y-m-d') ) %}
          <article id="{{ p.header.id }}" data-author="{{p.header.user}}">
            <h4 class="news--header row justify-content-between">
                  <span class="news--header_left col edit-news" style="cursor: pointer;"> <span class="newsTitle">{{ p.header.title }}</span>&nbsp;<i class="fa fa-pencil-square-o" aria-hidden="true"></i> </span> 
                  <span class="news--header_right col-auto newsDate">{{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y') }}</span>
            </h4>
            <div class="row newsPictures">
                {% for img in p.header.pictures %}
                  {# small screens shows double image size #}
                  {% set img_mobile = img.ratio %}
                  {% if img.ratio * 2 <= 12  %}
                    {% set img_mobile = img.ratio * 2 %}
                  {% endif %}
                  <div class="newsIMG col-md-{{img.ratio}} col-sm-{{img_mobile}}" data-name="{{img.name}}" data-ratio="{{ img.ratio }}">
                    <picture>
                      {# časem WebP #}
                      <img src="/data/news/{{p.header.date|slice(0,4)}}/{{p.header.id}}/img/{{img.name}}_preview.jpg" alt="Zde by měl být obrázek">
                    </picture>
                  </div>
                {% endfor %}
            </div>
            <section class="newsText">
              {{p.content}}
            </section>
          </article>
          <hr>
      {% endfor %}
    
    <script>
    $(".newsPictures").each(function(){
            if(this.children.length == 0){
              this.remove();
            }
        });
    </script>
    </section>
    </div>
   </div> <!-- plan + novinky -->


    <div id="right_box" class="col-md-4">
    <div>
    <br>
      <h4>
        <i class="fa fa-arrow-left" aria-hidden="true"></i> <i class="fa fa-plus-square-o" aria-hidden="true"></i> přidej novinku <br> 
       <i class="fa fa-arrow-left" aria-hidden="true"></i> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> uprav novinku <br>
        ---<br>
        <i class="fa fa-arrow-down" aria-hidden="true"></i> uprav událost
      </h4>
    
      <div class="soon__timeline"></div>
      {% set soon_collection = page.collection() %}
      {# further filter the collection #}
        {% for p in soon_collection %}
          {% if  not (  p.header.start|date('Y-m-d') <= strtotime("today +10 day")|date('Y-m-d') and p.header.end|date('Y-m-d') >= strtotime("today")|date('Y-m-d') ) %}
              {% set soon_collection = soon_collection.remove() %}
          {% endif %}
        {% endfor %}
         
      {% set events = collectionToEventsByDate(soon_collection) %}
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
                
                <section class="soon__event editBliziSeButton" style="cursor: pointer;">
                    <a href="/auth/events/edit?event={{p.header.id}}">
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
                  </article>
                </section>
                
            {% endfor %}
        {% endfor %}
    </div>
    </div> <!-- blizi se -->

</div> <!-- uzavira celou stranku , row -->


{#######################################
######## Pridani a edit novinek ########
########################################
je pouzit jeden modal jak pro pridani tak upravu Novinky
pomoci js se dynamicky meni obsah modalu, podle toho, ktere tlacitko ho vyvolalo
formular se odesila na server dvema zpusoby
1. pokud jsou nahravany nove obrazky pres dropzone.js, jsou k nim pridana
ostatni data z formulare a odeslana dropzone.js prikazem "myDropzone.processQueue()"
2. pokud je formular odesilan bez novych obrazku je odeslan klasicky 
#######################################}


{#######  HTML ########}
<div id="NewsModal" class="news--modal">
  <div id="NewsModalScroll">
    <div id="NewsModalContent" class="news--modal-content">
      <h2 id="News--header">Přidat novinku</h2>
      <form id="News--form" enctype="multipart/form-data" method="post" action="/php/news">
        <input id="News--POST-type" name="POST_type" type="hidden" value="addNews">  {# identifikace POST pozadavku pro PHP zpracovani #}
        <input id="News--id" name="id" type="hidden" value="">  {# id novinky, pokud se upravuje #}
        <input id="News--author" name="author" type="hidden" value=""> 
        <input id="News--date" name="date" type="hidden" value="">
        <input type="text" id="News--title" name="title"  placeholder="Nadpis" value="">
        <div id="News--pictures"> {# zde se budou dynamicky pridavat nastaveni sirky pro obrazky #}
        </div>
        <textarea id="News--content" name="content"  placeholder="Za sedmero horami..." ></textarea>
        <div class="dropzone" id="NewsDropzone">
        </div> {# dropzone pro upload obrazku #}
        
        <button type="button" class="special" id="News--submit-all">Uložit</button>
        <button type="button" id="News--close">Zrušit</button>
        <span id="News--deleteButtonSpan"></span>
      </form>
    </div> <!-- modal content -->
  </div>
</div> <!-- modal -->

{####### News modal Javascript ########}
<script>
window.addEventListener('DOMContentLoaded', function () {

/* inicializace prekladace z HTML zpet na markdown */
const News_turndownService = new TurndownService({
  headingStyle: 'atx',
  emDelimiter: '*',
});
/* inicializace text editoru */
var News_simplemde = new SimpleMDE({ element: document.getElementById("News--content"),
                                spellChecker: false,
                                status: false});

const notyf = new Notyf({
    position: {
        x: 'right',
        y: 'top',
    },
    duration: 3500,
});

/* vars*/
  var News_deleteButtonSpan = document.getElementById("News--deleteButtonSpan"),
      News_header = document.getElementById("News--header"),
      News_POST_type = document.getElementById("News--POST-type"),
      News_id = document.getElementById("News--id"),
      News_date = document.getElementById("News--date"),
      News_author = document.getElementById("News--author"),
      News_title = document.getElementById("News--title"),
      News_pictures = document.getElementById("News--pictures"),
      News_modal = document.getElementById('NewsModal'),
      News_ModalContent = document.getElementById('NewsModalContent'),
      News_ModalScroll = document.getElementById('NewsModalScroll');

// pokud se klikne na zrusit, zavre se modal
    document.getElementById("News--close").onclick = function(e) {
        News_modal.style.display = "none";
        News_title.value = ""; //vymaz nazvu
        News_pictures.innerHTML = "";//vymaze vsechny obrazky z modal
        News_simplemde.value(""); //vymaz textoveho editoru
        News_deleteButtonSpan.innerHTML = ""; //vymaze delete tlacitko
    }
      
       // pokud se klikne mimo modal, zavre se 
  /*  window.onclick = function(event) {
        if (event.target == News_modal) {
            News_modal.style.display = "none";
        }
    }*/

/**** Pridani Novinky ****/
// kdyz se zmackne tlaticko "+", otevre se modal, pobiha prepis informaci, pokud byl predtim otevren modal pro edit novinky
document.getElementById("addNewsButton").onclick = function() {       
    News_POST_type.value = "addNews"; //inicializace POST pozadavku pro PHP zpracovani
    News_header.innerHTML = "Přidat novinku";  //inicializace - Nadpis
    News_ModalContent.style.marginTop = window.pageYOffset + "px";
    News_modal.style.display = "block"; //zobrazi modal
    News_simplemde.codemirror.refresh(); //inicializuje textovy iditor
}

function deleteImageToggle(){
    var delete_img = this.parentElement.querySelector(".News--img-delete-input");
    if(delete_img.value == "true"){
        this.parentElement.style.backgroundColor = "white";
        delete_img.value = "false";
    }
    else {
        this.parentElement.style.backgroundColor = "#ff2d2d";
        delete_img.value = "true";
    }
}
    
function createImageOptionsDiv(formName, displayName){
    var img_index = News_pictures.lastElementChild ? Number(News_pictures.lastElementChild.getAttribute("data-index")) + 1 : 0;
    var select = document.createElement('div'); 
        select.setAttribute("data-index", img_index);
        select.innerHTML = '<input type="hidden" class="News--img-delete-input News--img-settings" name="img['+ img_index +'][img_delete]" value="false">' +
                           '<div class="News--img-delete"><i class="fa fa-trash-o" aria-hidden="true"></i></div>' +
                           '<input class="News--img-settings" name="img['+ img_index +'][img_name]" type="hidden" value="'+ formName + '">' +
                           '<select class="News--img-settings" name="img['+ img_index +'][img_ratio]" id="' + formName + '">' +
                              '<option value="12">1</option>' +
                              '<option value="6" selected>1/2</option>' +
                              '<option value="3">1/4</option>' +
                            '</select>';
        select.innerHTML += '<label class="News--img-label" for="' + formName + '" title="' + displayName + '">' + displayName + '</label>'; 
    News_pictures.appendChild(select); //vlozi do modalu
    select.querySelector(".News--img-delete").addEventListener( "click", deleteImageToggle);
}



/**** Edit Novinky ****/
$(".edit-news").click(function(){
    News_POST_type.value = "updateNews"; //nacte do skryteho "form input" typ POST pozadavku pro PHP zpracovani
    News_header.innerHTML = "Upravit novinku"; //inicializace - Nadpis

    var novinka = $(this).closest("article")[0]; //nacte tag arcitle obalujici novinku, ktery je nejbize tlacitku (cestuje nahoru po DOM)
    News_author.value = novinka.dataset.author;
    News_id.value = novinka.id; //nacte do skryteho "form input" ID novinky, kvuli PHP zpracovani
    News_date.value = novinka.querySelector(".newsDate").innerHTML; //nacte do skryteho "form input" datum novinky, kvuli PHP zpracovani
    News_title.value = novinka.querySelector(".newsTitle").innerHTML.trim() ; //nacte nazev
    News_simplemde.value( News_turndownService.turndown(novinka.querySelector("section").innerHTML.trim() ) ); //nacte text novinky, prevede HTML zpet na markdown a vlozi do text editoru

    /* pro kazdy ubrazek v novince vytvori "select", kde se da vybrat kolik max stranky bude obrazek zabirat */
    $(novinka).find(".newsIMG").each(function(img_index) { //foreach cyklus pro obrazky v novince
        var formName = this.getAttribute("data-name"),
            displayName = formName.slice(14);
        createImageOptionsDiv(formName, displayName);
        document.getElementById(formName).value = this.getAttribute("data-ratio"); //v modulu vybere v "select" hodnutu, ktera byla nastavena v novince
     });

     /* prida tlacitko pro smazani novinky*/
       News_deleteButtonSpan.innerHTML = '<button type="button" id="deleteNewsButton"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
     
    
    News_ModalContent.style.marginTop = window.pageYOffset + "px";
    News_modal.style.display = "block"; // zobrazi modal
    News_simplemde.codemirror.refresh(); //inicializace textovy editor
});

  function showLoader(){
    var newsDropzone = document.getElementById('NewsDropzone');
    newsDropzone.style.border = "none";
    newsDropzone.style.backgroundColor = "white";
    newsDropzone.innerHTML = '<div class="loader">Odesílám</div>';
  }

  function showError(xhr, desc, err){
    notyf.error("Neočekávaná chyba");
    console.log(err);
    console.log(desc);
    console.log(xhr);
  }

  function appendForm(formData){
    formData.append("POST_type", News_POST_type.value );
    formData.append("title", News_title.value );
    formData.append("id", News_id.value );
    formData.append("date", News_date.value );
    formData.append("author", News_author.value );
    formData.append("content", News_simplemde.value() );
    
    var img_arr = $(".News--img-settings");
    for ( index = 0; index < img_arr.length; index++ ) {
      formData.append(img_arr[index].getAttribute("name"), $(img_arr[index]).val());
    }
  }
/*****************************/
/*** POST odeslání novinky ***/
/*****************************/

// pro odeslání obrázků použit dropzone.js
var myDropzone = new Dropzone("div#NewsDropzone", {
    url: "/php/news",   //kam posila
    autoProcessQueue: false, //zakaze defaultni zpracovani
    uploadMultiple: true,  // nahravani vice souboru
    parallelUploads: 10,
    maxFiles: 10,
    maxFilesize: 20, //v MB
    acceptedFiles: "image/jpeg, image/png, image/gif",
    addRemoveLinks: true, //lze odstranit nahrany soubor
    renameFile: function (file) {
        return new Date().getTime() + '_' + file.name;
    },
    init: function() {
        var myDropzone = this;
        /**************************/
        /** Zpracovani formulare **/
        /**************************/
        document.getElementById("News--submit-all").onclick = function (e) {

            if( News_title.value == ''){ 
              News_title.focus();
              notyf.error('Chybí název novinky.');
            }
            else{
              // pokud v dropzone nejsou soubory, odesle se formular
              if (myDropzone.getQueuedFiles().length <= 0) { 

                  var formData = new FormData();
                    appendForm(formData);
                    showLoader();
                    $.ajax({
                        url: (News_POST_type.value == "updateNews" ? "/php/news/update" :  "/php/news/add"),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function ()
                        {  
                          notyf.success("Úspěšně uloženo!")
                          News_modal.style.display = "none";
                          window.location.replace(location.href);
                        },
                        error: function (xhr, desc, err){
                          showError(xhr, desc, err);
                        }
                    });
              }
              // pokud jsou, odeslou se obrazky
              else {
                  myDropzone.options.url = News_POST_type.value == "updateNews" ? "/php/news/update" :  "/php/news/add";
                  myDropzone.processQueue();
              }
            }
        };

       //k odeslanym obrazkum se pridaji i zbyle data
        myDropzone.on("sendingmultiple", function(data, xhr, formData) {
            appendForm(formData);
        });

        myDropzone.on("successmultiple", function() {
            notyf.success("Úspěšně uloženo!")
            News_modal.style.display = "none";
            window.location.replace(location.href);
        });

        myDropzone.on('error', function(file, errorMessage, xhr) {
          if(errorMessage){
            notyf.error(errorMessage);
          }
          else if(xhr.responseText){
            notyf.error(xhr.responseText);
          }
          else{
            showError(xhr, errorMessage, file); 
          }
        });
        myDropzone.on('addedfile', function(file) {        
            setTimeout(function(){  // needed to wait until "accepted" atributes is created
                if (file.accepted) createImageOptionsDiv(file.upload.filename, file.name);
            }, 100);
        });

        myDropzone.on("removedfile", function(file) {
            if (file.accepted) {
                var rmdiv = document.getElementById(file.upload.filename).parentElement;
                rmdiv.parentElement.removeChild(rmdiv);
            }
        });

        myDropzone.on("totaluploadprogress", function(progress, totalBytes, totalBytesSent) {
            if (totalBytes == 0) { //fix totaluploadprogress event fire when file is removed from dropzone
                return;
            }
            if (progress == 100) {
                showLoader();
                return;
            }
            var drop = document.getElementById("NewsDropzone");
            drop.innerHTML = ''
            drop.style.border = "none";
            drop.style.backgroundColor = "#e65646";
            drop.style.height = "0.5em";
            drop.style.transition = "all 0.5s";
            drop.style.margin = "1em 0";

            drop.style.width = progress + "%";
        });
      
    } // } init function

}) // }) dropzone

/**** Delete Novinky ****/
document.getElementById("News--deleteButtonSpan").onclick = function(e) {
    if( e.target.id = "deleteNewsButton"){
      if (confirm("Smazat novinku?") == true) {
        var deleteNewsForm = new FormData();
          deleteNewsForm.append("id", News_id.value );
          showLoader();
          
          $.ajax({
              url: "/php/news/delete",
              type: "POST",
              data: deleteNewsForm,
              processData: false,
              contentType: false,
              success: function (){ 
                notyf.success("Novinka smazána!")
                News_modal.style.display = "none";
                window.location.replace(location.href);
              },
              error: function (xhr, desc, err){
                showError(xhr, desc, err);
              }
          });

      }

    }
}

}, false); // laod
</script>