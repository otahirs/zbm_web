---
title: Upravit
date: '2018-10-24'
never_cache_twig: true
process:
    twig: true
    markdown: false
access:
    site:
        edit-event: true
---

{% if uri.query('event') %}
    {% set event = page.find('/data/events/' ~ uri.query('event')) %}
    {% if event.header is null %} {% set error = "header" %} {% endif %}
{% elseif uri.query('new') %}
    {% set event = []|merge({'template': uri.query('new')})  %}
    {% if event.template not in ['zavod', 'soustredeni', 'trenink'] %} {% set error = "new" %} {% endif %}
{% else %}
    {% set error = "parram" %}
{% endif %}

{% if error %}
    <div class="notices red">
        {% if error == "header" %} 
        <p> CHYBA: Událost nenalezena </p>
        {% elseif error == "new" %}
        <p> CHYBA: Pokus o vytvoření neznámého typu události</p>
        {% else %}
        <p> CHYBA: Není zadán žádný požadavek</p>
        {% endif %}
    </div>
{% else %}

<form id="editEvent" method="post" action="">
        <input name="POST_type" type="hidden" value="editEvent">
        <input name="id" type="hidden" value="{{ event.header.id }}">
        <input name="template" id="template" type="hidden" value="{{ event.template }}">
        {{ event.header.id }}
        <ul class="tabs">
            <li data-tab="info" class="tab-link current">Základní informace</li>
            {% if event.template == "zavod" or event.template == "trenink" %} 
                <li data-tab="zt" class="tab-link">
                    {% if event.template == "zavod" %}
                        Závod
                    {% else %}
                        Trénink
                    {% endif %}
                </li> 
            {% endif %}
            {% if event.template == "soustredeni" %}
                <li data-tab="soustredeni" class="tab-link">Soustředění</li> 
            {% endif %}
            {% if event.template == "soustredeni" or event.template == "trenink" %}
                <li data-tab="routes" class="tab-link">Postupy</li>
                <li data-tab="results" class="tab-link">Výsledky</li>
            {% endif %}
        </ul>
        <div id="info" class="tab-content current">
            <div class="row">
            <div class="col-6">
                <div class="row">
                    <div class="col-12">
                        <label for="name">Název</label>
                        <input id="name" name="title" type="text" value="{{ event.header.title }}" required>
                    </div>
                    <div class="col-6">
                        <label for="date1">Od</label>
                        <input id="date1" name="start" type="text" value="{{ event.header.start }}" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" required title="yyyy-mm-dd">
                    </div>
                    <div class="col-6">
                        <label for="date2">Do</label>
                        <input id="date2" name="end" type="text" value="{{ event.header.end }}" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" title="yyyy-mm-dd">
                    </div>
                    <div class="col-6">
                        <label for="place">Místo</label>
                        <input id="place" name="place" type="text" value="{{ event.header.place }}">
                    </div>
                    <div class="col-6">
                        <label for="GPS">GPS</label>
                        <input id="GPS" name="GPS" type="text" value="{{ event.header.gps }}">
                    </div>
                    <div class="col-6">
                        <label for="meetTime">Sraz / čas</label>
                        <input id="meetTime" name="meetTime" type="text" value="{{ event.header.meetTime }}">
                    </div>
                    <div class="col-6">
                        <label for="meetPlace">Sraz / místo</label>
                        <input name="meetPlace" type="text" value="{{ event.header.meetPlace }}">
                    </div>
                    <div class="col-12">
                        <label for="transport">Doprava</label>
                        <textarea id="transport" name="transport" type="text" rows="1">{{ event.header.transport }}</textarea>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <br>
                <fieldset>
                    <legend>Skupiny:</legend>
                    <input name="zabicky" type="hidden" value="0">
                    <input id="zabicky" type="checkbox" name="zabicky" value="1" {% if "zabicky" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="zabicky"> žabičky </label> <br>
                    <input name="pulci1" type="hidden" value="0">
                    <input id="pulci1" type="checkbox" name="pulci1" value="1" {% if "pulci1" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="pulci1"> pulci 1 </label> <br>
                    <input name="pulci2" type="hidden" value="0">
                    <input id="pulci2" type="checkbox" name="pulci2" value="1" {% if "pulci2" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="pulci2"> pulci 2 </label> <br>
                    <input name="zaci1" type="hidden" value="0">
                    <input id="zaci1" type="checkbox" name="zaci1" value="1" {% if "zaci1" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="zaci1"> žáci 1 </label> <br>
                    <input name="zaci2" type="hidden" value="0">
                    <input id="zaci2" type="checkbox" name="zaci2" value="1" {% if "zaci2" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="zaci2"> žáci 2 </label> <br>
                    <input name="dorost" type="hidden" value="0">
                    <input id="dorost" type="checkbox" name="dorost" value="1" {% if "dorost" in event.header.taxonomy.skupina %} checked {% endif %}>
                        <label for="dorost"> dorost+ </label>
                </fieldset>

                <label for="leader">Vedoucí</label>
                <input id="leader" name="leader" type="text" value="{{ event.header.leader }}">
            </div> 
            <div class="col-12">
                <label for="note">Poznámka</label>
                <textarea id="note" name="note" rows="1">{{ event.header.note }}</textarea>
            </div>
        </div>
        {% if event.header.start != event.header.end %}
            <hr>
            <div class="row">
                <div class="col-6">
                    <label for="accomodation">Ubytování</label>
                    <textarea id="accomodation" name="accomodation" type="text" rows="1">{{ event.header.accomodation }}</textarea>
                </div>
                <div class="col-6">
                    <label for="food">Strava</label>
                    <textarea id="food" name="food" type="text" rows="1">{{ event.header.food }}</textarea>
                </div> 
            </div> <!-- row -->
        {% endif %}
        </div>
   
        


    <div id="zt" class="tab-content">
        {% if event.template == "zavod" %}
            <label for="link">Odkaz na ORIS / stránky závodu</label>
            <input id="link" name="link" type="text" value="{{ event.header.link }}">
            <hr>
        {% endif %}
    
        {% if event.template == "zavod" or event.template == "trenink" %}
            <div class="row">
                <div class="col-6">
                    <label for="startTime">Start</label>
                    <input id="startTime" name="startTime" type="text" value="{{ event.header.startTime }}">
                
                    <label for="eventTypeDescription">Tratě</label>
                    <textarea id="eventTypeDescription" name="eventTypeDescription" type="text" rows="1">{{ event.header.eventTypeDescription }}</textarea>
                </div>
                <div class="col-6">
                    <label for="map">Mapa</label>
                    <input id="map" name="map" type="text" value="{{ event.header.map }}">
                
                    <label for="terrain">Terén</label>
                    <textarea id="terrain" name="terrain" type="text" rows="3">{{ event.header.terrain }}</textarea>
                </div>
            </div> <!-- row -->
        {% endif %}
    </div>

    {% if event.template == "soustredeni" %}
        <div id="soustredeni" class="tab-content">
            <label for="signups">Přihlášky</label>
            <input id="signups" name="signups" type="text" value="{{ event.header.singups }}">
            
            <label for="price">Cena</label>
            <textarea id="price" name="price">{{ event.header.price }}</textarea>
        
            <label for="return">Návrat</label>
            <textarea id="return" name="return">{{ event.header.return }}</textarea>
        
            <label for="program">Náplň / program</label>
            <textarea id="program" name="program">{{ event.header.program }}</textarea>
        
            <label for="thingsToTake">S sebou</label>
            <textarea id="thingsToTake" name="thingsToTake">{{ event.header.thingsToTake }}</textarea>
        </div><!-- id="soustredeni" -->
    {% endif %}
    <div id="routes" class="tab-content">
        {% for route in event.header.routes %}
        <fieldset>
        <div class="row"> 
            <div class="col-sm-6">
                <input name="routeName[]" type="text" placeholder="Popis" value="{{ route.name }}">
            </div>
            <div class="col-sm-6">
                <input name="routeLink[]" type="text" placeholder="Odkaz" value="{{ route.link }}">
            </div>
        </div>
        </fieldset>
        {% endfor %}
        <button id="addRoute" type="button">přdat další</button>
    </div>
    <div id="results" class="tab-content">
        {% for results in event.header.results %}
        <fieldset>
        <div class="row"> 
            <div class="col-sm-6">
                <input name="resultsName[]" type="text" placeholder="Popis" value="{{ results.name }}">
            </div>
            <div class="col-sm-6">
                <input name="resultsLink[]" type="text" placeholder="Odkaz" value="{{ results.link }}">
            </div>
        </div>
        </fieldset>
        {% endfor %}
        <button id="addResults" type="button">přdat další</button>
    </div>        
    <hr>
    <div class="row row justify-content-between">
        <div class="col-auto">
            <button id="saveEvent" type="submit" class="special">Uložit</button>
        </div>
        <div class="col"  id="formResponse" style="line-height: 1em;">
        </div>
        <div class="col-auto">
            <button id="deleteEvent" type="button"><i class="fa fa-trash-o" aria-hidden="true"></i></button> 
        </div>
    </div>
    <br>
    
</form>

<style>
.CodeMirror, .CodeMirror-scroll {
	min-height: 100px;
}
</style>

<script>
window.addEventListener('DOMContentLoaded', function () {
    var simplemde = new SimpleMDE({ 
        element: document.getElementById("note"), //misto textarea nacte markdown editor
        spellChecker: false,
        status: false,
        hideIcons: ["side-by-side", "fullscreen"],
    });

    /**** prevent submit on enter ***/
        $(document).on("keypress", "input", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

    /**** autoresize textareas ***/
        $.fn.extend({
            autoresize: function () {
            $(this).on("change keyup keydown paste cut", function () {
            $(this).height(0).height(this.scrollHeight);
            }).change();
            }
        });
        $("textarea").autoresize();
        // trigger resize on load
        $("textarea").each(function(){
            $(this).height( this.scrollHeight);
        });

    /*** delete <br> tags from textareas ***/ 
        String.prototype.replaceAll = function (find, replace) {
            var result = this;
            do {
                var split = result.split(find);
                result = split.join(replace);
            } while (split.length > 1);
            return result;
        };
        var newline = String.fromCharCode(13, 10);
        $("textarea").each(function() {
            this.value = this.value.replaceAll('<br>', '');
        });

    /*** tabs ***/
    $('ul.tabs li').click(function(){
            var tab_id = $(this).attr('data-tab');

            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');

            $(this).addClass('current');
            $("#"+tab_id).addClass('current');
            //resize textarea
            $("textarea").each(function(){
            $(this).height( this.scrollHeight);
        });
        })

    /*** addRoute ***/
    $("#addRoute").click(function(){
        $(this).before('<fieldset><div class="row">' + 
                        '<div class="col-sm-6">' +
                        '    <input name="routeName[]" type="text" placeholder="Popis">' +
                        '</div>' +
                        '<div class="col-sm-6">' +
                        '    <input name="routeLink[]" type="text" placeholder="Odkaz">' +
                        '</div>' +
                       '</div></fieldset>');
    })
    /*** addResults ***/
    $("#addResults").click(function(){
        $(this).before('<fieldset><div class="row">' + 
                        '<div class="col-sm-6">' +
                        '    <input name="resultsName[]" type="text" placeholder="Popis">' +
                        '</div>' +
                        '<div class="col-sm-6">' +
                        '    <input name="resultsLink[]" type="text" placeholder="Odkaz">' +
                        '</div>' +
                       '</div></fieldset>');
    })
        
    /* submit */
    var save_btn = document.getElementById("saveEvent"),
        delete_btn = document.getElementById("deleteEvent"),
        form = document.getElementById("editEvent"),
        date1 = document.getElementById("date1"),
        date2 = document.getElementById("date2"),
        id = document.querySelector("[name='id']"),
        formResponse = document.getElementById("formResponse");

    save_btn.onclick = function(e){
        e.preventDefault();
        //check if form is valid
        if(form.checkValidity()){

            var formData = new FormData(form);
            formData.append('note', simplemde.value() );
            $.ajax({
                url: "/php/editevent",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response){   
                    formResponse.innerHTML = "<br>Úspěšně uloženo.";
                    formResponse.style.color = "green";
                    setTimeout(function(){ 
                        formResponse.innerHTML = ""; 
                    }, 3000);
                    if (id.value == "") {
                        var json = $.parseJSON(response);
                        if (json.id) {
                            $("[name='id']").val(json.id);
                        }
                    }
                    
                    //window.location.replace(location.href);
                },
                error: function (xhr, desc, err){

                    if(xhr.responseText){
                        formResponse.innerHTML = xhr.responseText;
                    }
                    else{
                        formResponse.innerHTML = "<br>Chyba, zkontrolujte console log";
                    }
                    formResponse.style.color = "red";
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                }
            });

        }
        else{
            form.reportValidity();
            if($("#name").val().trim() == ""){
                formResponse.innerHTML ='<br>Musí být vyplněn název události';
                formResponse.style.color = "red";
            }
            else if(!date1.checkValidity() || !date2.checkValidity()){
                formResponse.innerHTML ='<br>Datum musí být ve formátu "yyyy-mm-dd"';
                formResponse.style.color = "red";
            }
        }
    }
    delete_btn.onclick = function(e){
        e.preventDefault();
        if (confirm("Smazat událost?") == true) {
            var formData = new FormData(form);
            formData.append('delete', true );
            $.ajax({
                url: "/php/editevent",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (){   
                    formResponse.innerHTML = "<br>Událost byla smazána, pokud jste si to rozmysleli, klikněte na tlačítko uložit";
                    formResponse.style.color = "red";
                    //window.location.replace(location.href);
                },
                error: function (xhr, desc, err){

                    if(xhr.responseText){
                        formResponse.innerHTML = xhr.responseText;
                    }
                    else{
                        formResponse.innerHTML = "<br>Chyba, zkontrolujte console log";
                    }
                    formResponse.style.color = "red";
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                }
            });
        }
    }
        
    })
    </script>
{% endif %}
    