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
    items: '@root.descendants'
    order:
        by: date
        dir: asc
---

<div class="row no-gutters"> {# cela stranka | je pouzit css framework purecss.io grids #}
  
    <div id="novinky" class="col-md-8"> <!-- plan + novinky vlevo -->
    <div class="inner">
        <header id="header">
            <h1>Novinky &nbsp;&nbsp;&nbsp;<span id="addNewsButton" class="button small special">Přidat&nbsp;<i class="fa fa-plus-square-o" aria-hidden="true"></i></span>  </h1>
        </header>
        <section>
    

      {% set news_collection = page.collection().ofType('novinka').order('p.header.id','asc') %}

      {% for p in news_collection.order('date','desc') %}

        {% if  ( p.header.date|date('Y-m-d') >= strtotime("today -30 day")|date('Y-m-d') ) %}
          <article id="{{ p.header.id }}">
            <h4 class="news--header">
                <span class="newsDate">{{p.header.date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. M. Y')|upper }} </span> &nbsp; &nbsp; <span class="newsTitle"> {{ p.header.title }} </span>
            {#  {% if (authorize(['site.novinky'])) %} #} 
          			&nbsp;
                {# otevrit modal #}
                <span class="edit-news" style="cursor: pointer;"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
        	{#	  {% endif %}#} 
            </h4>
            <div class="row no-gutters newsPictures">
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
            <section class="newsText">
              {{p.content}}
            </section>
          </article>
          <hr width="62.11%">
        {% endif %}
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


    <div id="soon" class="col-md-4">
    <br>
      <h4>Kliknutím upravíte náhled události</h4>
    
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

          
          <section class="editBliziSeButton" style="cursor: pointer; background-color:white">
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
         
        {% endif %}
      {% endfor %}

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
      <div id="News--responseText" style="color:red"></div>
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

/* vars*/
  var News_deleteButtonSpan = document.getElementById("News--deleteButtonSpan"),
      News_header = document.getElementById("News--header"),
      News_POST_type = document.getElementById("News--POST-type"),
      News_id = document.getElementById("News--id"),
      News_date = document.getElementById("News--date"),
      News_title = document.getElementById("News--title"),
      News_pictures = document.getElementById("News--pictures"),
      News_modal = document.getElementById('NewsModal'),
      News_ModalContent = document.getElementById('NewsModalContent'),
      News_responseText = document.getElementById('News--responseText'),
      News_ModalScroll = document.getElementById('NewsModalScroll');

// pokud se klikne na zrusit, zavre se modal
    document.getElementById("News--close").onclick = function(e) {
        News_modal.style.display = "none";
        News_title.value = ""; //vymaz nazvu
        News_pictures.innerHTML = "";//vymaze vsechny obrazky z modal
        News_simplemde.value(""); //vymaz textoveho editoru
        News_deleteButtonSpan.innerHTML = ""; //vymaze delete tlacitko
        News_responseText.innerHTML = "";
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
        select.innerHTML += '<label class="News--img-label" for="' + formName + '">' + displayName + '</label>'; 
    News_pictures.appendChild(select); //vlozi do modalu
    select.querySelector(".News--img-delete").addEventListener( "click", deleteImageToggle);
}



/**** Edit Novinky ****/
$(".edit-news").click(function(){
    News_POST_type.value = "updateNews"; //nacte do skryteho "form input" typ POST pozadavku pro PHP zpracovani
    News_header.innerHTML = "Upravit novinku"; //inicializace - Nadpis

    var novinka = $(this).closest("article")[0]; //nacte tag arcitle obalujici novinku, ktery je nejbize tlacitku (cestuje nahoru po DOM)
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
    News_ModalContent.innerHTML = '<div class="ajaxError">' +
                                  '<div class="ajaxErrorText" >Něco se pokazilo..</div><hr><br>' +
                                  '<button class="ajaxErrorButton"  type="button" onclick="window.location.replace(location.href)"><i class="fafa-refresh" aria-hidden="true"></i>&nbsp;Obnovit stránku</button><br><br>' +
                                  '<div class="ajaxErrorNote">Zkontrolujte <i>console.log</i> nebo kontaktujte správce stránek.</div>' +
                                  '</div>';
    console.log(err);
    console.log(desc);
    console.log(xhr);
    console.log(xhr.responseText);
  }

  function appendForm(formData){
    formData.append("POST_type", News_POST_type.value );
    formData.append("title", News_title.value );
    formData.append("id", News_id.value );
    formData.append("date", News_date.value );
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
              alert('Musí být vyplněn název novinky.');
            }
            else{
              // pokud v dropzone nejsou soubory, odesle se formular
              if (myDropzone.getQueuedFiles().length <= 0) { 

                  var formData = new FormData();
                    appendForm(formData);
                    showLoader();
                    $.ajax({
                        url: "/php/news",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function ()
                        {  window.location.replace(location.href);
                        },
                        error: function (xhr, desc, err){
                          showError(xhr, desc, err);
                        }
                    });
              }
              // pokud jsou, odeslou se obrazky
              else {
                  myDropzone.processQueue();
              }
            }
        };

       //k odeslanym obrazkum se pridaji i zbyle data
        myDropzone.on("sendingmultiple", function(data, xhr, formData) {
            appendForm(formData);
        });

        myDropzone.on("successmultiple", function() {
            window.location.replace(location.href);
        });

        myDropzone.on('error', function(file, errorMessage, xhr) {
          if(errorMessage){
            News_responseText.innerHTML = "<br>" . errorMessage;
          }
          else if(xhr.responseText){
            News_responseText.innerHTML = "<br>" . xhr.responseText;
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
          deleteNewsForm.append("POST_type", "deleteNews" );
          deleteNewsForm.append("id", News_id.value );
          showLoader();
          
          $.ajax({
              url: "/php/news",
              type: "POST",
              data: deleteNewsForm,
              processData: false,
              contentType: false,
              success: function (){ 
                window.location.replace(location.href);
              },
              error: function (xhr, desc, err){
                showError(xhr, desc, err);
                 }
          });

      }

    }
}


{#######################################
############ Edit blizise ##############
########################################}

    /* inicializace prekladace z HTML zpet na markdown "Turndown"*/
    const editBliziSe_turndownService = new TurndownService({
      headingStyle: 'atx', //mění defaultni zobrazení nadpisu na ten pouzivany v gravu
      emDelimiter: '*',
    
    });

  $(".editBliziSeButton").click(function(){
      var soonEvent = this;
      if(soonEvent.classList.contains("active")) return;
      soonEvent.classList.add("active");
      var content = this.querySelector("article") //nacte tag obsahujici text blizi se
      var content_text = content.innerHTML.trim(); //ulozi stary text a odstihne ze zacatku a konce bile znaky
      content.innerHTML = '<form method="post" action="/php/blizise">' +  //nahradi text blizi se formularem na upravu
                            '<input name="POST_type" type="hidden" value="editBliziSe">' +
                            '<input name="id" type="hidden" value="'+ content.getAttribute("data-id") +'">' +
                            '<input name="template" type="hidden" value="'+ content.getAttribute("data-template") +'">' +
                            '<textarea name="content"></textarea>' +
                            '<button class="saveBlizise special fit" type="submit" style="margin-top: 1em">Uložit</button>' +
                            '<div class="row">' +
                            '<div class="col-8">' +
                            '<button class="editBliziSeCancel fit small" type="button">Zrušit</button>' +
                            '</div>' +
                            '<div class="col-4">' +
                            '<button class="regenerateBliziSe fit small" type="button" title="Znovu vygenerovat obsah"><i class="fa fa-refresh" aria-hidden="true"></i></button>' +
                            '</div>' +
                            '</div>' +
                            '</form>';

      var editBliziSe_simplemde = new SimpleMDE({ element: content.querySelector("textarea"), //misto textarea nacte markdown editor
                                   spellChecker: false,
                                   status: false});
      editBliziSe_simplemde.value( editBliziSe_turndownService.turndown(content_text) ); //nahraje do editoru drive ulozeny text, ktery zkonvertuje z html tagu z5 na markdown pomoci .js knihovny "turndown"

      $(".editBliziSeCancel").click(function(e){ //tlacitko pro zruseni
        e.stopPropagation(); //zastavi propagaci click eventu aby se neodesilal formular pres tlacitko submit
        content.innerHTML = content_text; //vrati drive ulozeny text
        soonEvent.classList.remove("active");
      })

      $(".saveBlizise").click(function(e){
          e.preventDefault(); //zabrani defaultnimu odeslani formulare
          e.stopPropagation();
          var bliziseForm = new FormData($(this).closest("form")[0]);
          bliziseForm.append("content", editBliziSe_simplemde.value() );
          $.ajax({
              url: "/php/blizise",
              type: "POST",
              data: bliziseForm,
              processData: false,
              contentType: false,
              success: function ()
              { 
                soonEvent.classList.remove("active"); 
                window.location.replace(location.href);
              },
              error: function (xhr, desc, err){
                console.log(err);
                console.log(desc);
                console.log(xhr);
              }
          });
      });

      $(".regenerateBliziSe").click(function(e){ //tlacitko pro zruseni
        var bliziseForm = new FormData($(this).closest("form")[0]);
        bliziseForm.append("regenerate", true );
        $.ajax({
              url: "/php/blizise",
              type: "POST",
              data: bliziseForm,
              processData: false,
              contentType: false,
              success: function ()
              {  soonEvent.classList.remove("active");
                window.location.replace(location.href);  
              },
              error: function (xhr, desc, err){
                console.log(err);
                console.log(desc);
                console.log(xhr);
              }
          });
      })
  });
  }, false); // laod
  </script>