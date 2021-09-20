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
<span id="back-btn" class="button small"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i> zpět na seznam událostí</span>
<script>
    document.querySelector('#back-btn').onclick = () => {
        if((document.referrer).endsWith('/auth/events')  && "{{uri.query('event')}}" !== "" ) {
            // if no new event was created, go back in history to keep search results on events page
            history.back();
        }
        else {
            location.href = "{{base_url}}/auth/events";
        }
    }
</script>

{% if uri.query('event') %}
    {% if uri.query('event') != "new" %}
        {% set year = uri.query('event')[:4] %}
        {% set event = page.find('/data/events/' ~ year ~ "/" ~ uri.query('event')|lower) %}
        {% if event.header is null %} 
            {% set error = "header" %} 
        {% else %}
            <button onClick="javascript:window.open('{{event.url}}', '_blank');" type="button" class="small">náhled</button>
        {% endif %}
    {% else %}
        <button id="preview-btn" title="odkaz na náhled se zpřístupní po prvním uložení události" style="pointer-events: auto;" disabled type="button" class="small">náhled</button>
    {% endif %}
{% else %}
    {% set error = "parram" %}
{% endif %}

{% if error %}
    <div class="notices red">
        {% if error == "header" %} 
        <p> CHYBA: Událost nenalezena </p>
        {% else %}
        <p> CHYBA: Není zadán žádný požadavek</p>
        {% endif %}
    </div>
{% else %}

<br><br>
<form id="editEvent" method="post" action="" autocomplete="off">
        <input name="POST_type" type="hidden" value="editEvent">
        <input name="id" type="hidden" value="{{ event.header.id }}">
        <ul class="tabs">
            <li data-tab="info" class="tab-link current">Základní informace</li>
            
            <li data-tab="zt" class="tab-link" {% if not (event.template == "zavod" or event.template == "trenink") %} style="display:none;" {% endif %} >
                {% if event.template == "zavod" %}
                    Závod
                {% else %}
                    Trénink
                {% endif %}
            </li> 
            <li data-tab="soustredeni" class="tab-link" {% if not (event.template == "soustredeni") %} style="display:none;" {% endif %}>
                Soustředění
            </li> 
            <li data-tab="routes" class="tab-link" {% if not (event.template == "soustredeni" or event.template == "trenink") %} style="display:none;" {% endif %}>
                Postupy
            </li>
            <li data-tab="results" class="tab-link" {% if not (event.template == "soustredeni" or event.template == "trenink") %} style="display:none;" {% endif %}>
                Výsledky
            </li>
            
        </ul>
        <div id="info" class="tab-content current">
            <label for="event-type">Kategorie / Typ</label>
            <select name="type" id="event-type">
                <optgroup label="Závody">
                    <option value="Z" {% if event.header.type == "Z" %} selected {% endif %}>Závod</option>
                    <option value="BZL" {% if event.header.type == "BZL" %} selected {% endif %}>BZL</option>
                    <option value="BBP" {% if event.header.type == "BBP" %} selected {% endif %}>Bežecký závod (BBP)</option>
                </optgroup>
                <optgroup label="Tréninky">
                    <option value="M" {% if event.header.type == "M" %} selected {% endif %}>Mapový trénink</option>
                    <option value="T" {% if event.header.type == "T" %} selected {% endif %}>Trénink</option>
                </optgroup>
                <optgroup label="Soustředění">
                    <option value="S" {% if event.header.type == "S" %} selected {% endif %}>Soustředění</option>
                    <option value="TABOR" {% if event.header.type == "TABOR" %} selected {% endif %}>Tábor</option>
                </optgroup>
                <optgroup label="Jiné">
                    <option value="L" {% if event.header.type == "L" %} selected {% endif %}>Liga škol</option>
                    <option value="J" {% if event.header.type not in ["Z", "M", "T", "S", "BZL", "BBP", "TABOR", "L"] %} selected {% endif %}>Nezařazeno</option>
                </optgroup>
            </select>
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
                        <input id="place" name="place" type="text" value="{{ event.header.place }}" required>
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
                    <div class="row">
                        <div class="col-6">
                            <input id="dorost" type="checkbox" name="dorost" class="group-selector" {% if "dorost" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="dorost"> dorost+ </label> <br>
                            <input id="zaci2" type="checkbox" name="zaci2" class="group-selector" {% if "zaci2" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="zaci2"> žáci 2 </label> <br>
                            <input id="zaci1" type="checkbox" name="zaci1" class="group-selector" {% if "zaci1" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="zaci1"> žáci 1 </label> <br>
                            <input id="hobby" type="checkbox" name="hobby" class="group-selector" {% if "hobby" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="hobby"> hobby </label> <br>
                        </div>
                        <div class="col-6">
                            <input id="pulci2" type="checkbox" name="pulci2" class="group-selector" {% if "pulci2" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="pulci2"> pulci 2 </label> <br>
                            <input id="pulci1" type="checkbox" name="pulci1" class="group-selector" {% if "pulci1" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="pulci1"> pulci 1 </label> <br>
                            <input id="zabicky" type="checkbox" name="zabicky" class="group-selector" {% if "zabicky" in event.header.taxonomy.skupina %} checked {% endif %}>
                                <label for="zabicky"> žabičky </label> <br>    
                            <button id="groups-select-all" class="small" style="margin-top: 1.6em;" type="button">vše</button> 
                            <button id="groups-select-none" class="small" type="button">nic</button>
                        </div>
                    </div>                
                </fieldset>
                <label for="leader">Vedoucí</label>
                <input id="leader" name="leader" type="text" value="{{ event.header.leader }}">
            </div> 
            <div class="col-12">
                <label for="note">Poznámka</label>
                <textarea id="note" name="note" rows="1">{{ event.header.note }}</textarea>
            </div>
        </div>
            <div id="foodAndAccommodation" {% if event.header.start == event.header.end %} style="display:none" {% endif %}>
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
            </div>
        </div>
   
        


    <div id="zt" class="tab-content">
        <div id="zt-link" {% if not (event.template == "zavod") %} style="display:none;" {% endif %} >
            <label for="link">Odkaz na ORIS / stránky závodu</label>
            <input id="link" name="link" type="text" value="{{ event.header.link }}">
            <hr>
        </div>
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
    </div>

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

    const notyf = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        },
        duration: 3500,
    });
    
    // markdown editors
    var text_editors = {};

    function initMarkdownEditor(id) {
        var smde = new SimpleMDE({ 
            element: document.getElementById(id), //misto textarea nacte markdown editor
            spellChecker: false,
            status: false,
            hideIcons: ["side-by-side", "fullscreen"],
        });
        text_editors[id] = smde;
    }

    initMarkdownEditor("note");
    initMarkdownEditor("transport");

    {% if event.template == "soustredeni" %}      
        initMarkdownEditor("program");
        initMarkdownEditor("price");
        initMarkdownEditor("thingsToTake");
    {% endif %}
    
    /**** all & none group select buttons ****/
    $("#groups-select-all").click( () => { $('.group-selector').prop('checked', true); } )
    $("#groups-select-none").click( () => { $('.group-selector').prop('checked', false); } )

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
        // trigger resize on load
        $("textarea").autoresize();

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
        //refresh simplemde editors
        for (var name in text_editors)
            text_editors[name].codemirror.refresh();
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

    // change displayed tabs on event type change
    var zt = $('[data-tab="zt"]');
    var link = $('#zt-link');
    var soustredeni = $('[data-tab="soustredeni"]');
    var routes = $('[data-tab="routes"]');
    var results = $('[data-tab="results"]');
    $("#event-type").change(function(){
        zt.hide()
        link.hide();
        soustredeni.hide();
        routes.hide();
        results.hide();
        switch(this.value){
            case  "Z":
            case "BZL":
            case "BBP":
                zt.html('Závod');
                zt.show();
                link.show();
                break;
            case "M":
            case "T":
                zt.html('Trénink');
                zt.show();
                routes.show();
                results.show();
                break;
            case "S":
            case "TABOR":
                soustredeni.show();
                routes.show();
                results.show();
                break;
        }
    })
    // show food and accommodation if dates differ
    var date1 = document.querySelector("#date1");
    var date2 = document.querySelector("#date2");
    var foodAndAccommodation = document.querySelector("#foodAndAccommodation");
    function toggleFoodAndAccommodation() {
        if(date1.checkValidity() && date2.checkValidity() && date1.value < date2.value){
            foodAndAccommodation.style.display = "inherit";
            $("textarea").autoresize();
        }
        else {
            foodAndAccommodation.style.display = "none";
        }
    }
    date1.addEventListener('change', toggleFoodAndAccommodation);
    date2.addEventListener('change', toggleFoodAndAccommodation);
    /* submit */
    var save_btn = document.getElementById("saveEvent"),
        delete_btn = document.getElementById("deleteEvent"),
        form = document.getElementById("editEvent"),
        title = document.getElementById("name"),
        date1 = document.getElementById("date1"),
        date2 = document.getElementById("date2"),
        place = document.getElementById("place"),
        id = document.querySelector("[name='id']");

    function ajaxError(xhr, desc, err) {
        notyf.error('Neočekávaná chyba');
        console.log(err);
        console.log(desc);
        console.log(xhr);
    }

    save_btn.onclick = function(e){
        e.preventDefault();
        //check if form is valid
        if(form.checkValidity()){
            if(date2.value && date1.value > date2.value) {
                notyf.error('Datum "Do" je později než datum "Od".');
                return;
            }

            var formData = new FormData(form);
            for (var name in text_editors) {
                formData.append(name, text_editors[name].value() );
            }
            $.ajax({
                url: "/php/editevent",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response){   
                    notyf.success('Úspěšně uloženo!');
                    if (id.value == "") {
                        var json = $.parseJSON(response.replace(/(<([^>]+)>)/gi, "")); // strip <p>...</p> tags from json response
                        if (json.id) {
                            $("[name='id']").val(json.id);
                            history.replaceState({}, '', "{{page.url}}?event=" + json.id);
                            let p = document.querySelector('#preview-btn');
                            p.onclick = () => window.open(`{{base_url}}/data/events/${json.id.substr(0, 4)}/${json.id}`, '_blank');
                            p.disabled = false;
                        }
                    }
                },
                error: ajaxError
            });

        }
        else{
            if(!title.checkValidity()){
                notyf.error('Musí být vyplněn "Název" události');
            }
            else if(!date1.checkValidity() || !date2.checkValidity()){
                notyf.error('Datum musí být ve formátu "yyyy-mm-dd"');
            }
            else if(!place.checkValidity()) {
                notyf.error('Musí být vyplněno "Místo"');
            }
            form.reportValidity();
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
                    notyf.success({
                        message: 'Událost byla smazána, pokud jste si to rozmysleli, klikněte na tlačítko uložit.',
                        duration: 9000
                    });
                },
                error: ajaxError
            });
        }
    }

    })
    </script>
{% endif %}
    