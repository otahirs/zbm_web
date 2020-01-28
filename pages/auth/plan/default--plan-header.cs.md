---
process:
    twig: true
    markdown: false
access:
    site:
        plan: true
planTemplate: winter
plan:
    monday:
        1:
            name: 'kopce (světlo a buzola s sebou)'
            place: 'Hala Rosnička, Horákova 7'
            meetup: '16:30'
            group:
                    - dorost
    tuesday:
        1:
            name: 'běžecké posilování'
            place: 'BiGy, Barvičova 85'
            meetup: '16:00'
            group:
                    - zaci1
                    - zaci2
        2:
            name: 'běžecké posilování'
            place: 'SPŠ Purkyňova, Purkyňova 2832/97'
            meetup: '17:30'
            group:
                    - dorost
    wednesday:
        1:
            name: 'výprava za OB'
            place: 'ZŠ Novoměstská, Novoměstská 1887/21'
            meetup: '16:00'
            group:
                    - zabicky
        2:
            name: 'hry + mapa'
            place: 'ZŠ Novoměstská, Novoměstská 1887/21'
            meetup: '16:00'
            group:
                    - pulci1
                    - pulci2
        3:
            name: 'mapa + teorie'
            place: 'ZŠ Novoměstská, Novoměstská 1887/21'
            meetup: '16:00'
            group:
                    - zaci1
        4:
            name: 'běžecký trénink + teorie'
            place: 'ZŠ Novoměstská, Novoměstská 1887/21'
            meetup: '16:30'
            group:
                    - zaci2
        5:
            name: 'tempové intervaly a teorie'
            place: 'ZŠ Novoměstská, Novoměstská 1887/21'
            meetup: '16:30'
            group:
                    - dorost
    thursday:
        1:
            name: 'Bazén - plavání'
            place: 'Bazén Kraví hora'
            meetup: '16:45'
            group:
                    - zaci2
    friday: null
    saturday: null
    sunday: null
---

<div id="planEdit">
        {% set plan_collection = page.collection({'items':'@root.descendants','order': {'by': 'default', 'dir': 'asc'}}).ofOneOfTheseTypes(['zavod', 'trenink', 'soustredeni', 'tabor']) %}
        
        {# ziska array s pravidelnymi treninky #}
        {% set pravidelne_treninky = page.header.plan %}
        
        <table class="plan">
            {# inicializace pole urcujiciho den v tydnu #}
            {% set ENGweek = ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'] %}
            {% set CZweek = [ 'PO', 'UT', 'ST', 'CT', 'PA', 'SO', 'NE'] %}
            {# inicializace promene urcujici jestli uz je v tabulce zapsany den v tydnu #}
            {% set zapsany_den = 'NULL' %}

            {# cyklus pro 7 dni prochazejici pole "CZweek", aktualni promenou zastupuje "den" #}
            {% for day_num,den in CZweek %}
                {# "datum_dne_v_tydnu" drzi datum dnu v aktualnim tydnu #}
                {% set datum_dne_v_tydnu = strtotime([strtotime("last Sunday")|date('Y-m-d'), " +", loop.index, " day"]|join)|date('Y-m-d') %} {#kvuli chybe ve 'strtotime' nelze zadat jen 'last Sunday + day'#}
                {#cyklus prochazejici vsechny soubory v databazi#}
                {% for p in plan_collection %}
                    {# pokud je mezi start a end datum_dne_v_tydnu, zobrazi se v tabulce#}
                    {%  if (  p.header.start <= datum_dne_v_tydnu and p.header.end >= datum_dne_v_tydnu ) %}
                      <tr {% if day_num % 2 == 0 %} class="plan--lichyDen event--program"  {% else %} class="plan--sudyDen event--program" {% endif %}
                        data-path="{{ "/auth/events/edit?event=" ~ p.header.id[0:4] ~ "/" ~ p.header.id }}">
                        <td class="den">
                            {# zobrazi nazev dne (napr. "PO") jen pokud jiz neexistuje zaznam dne v tabulce #}
                            {% if zapsany_den != den %}    {# podminka, jestli uz se zapisoval #}
                              {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
                              {% set zapsany_den = den %}   {# ulozime, ze uz se zapisoval #}
                            {% endif %}
                        </td>
                        <td class="skupina"> {# vypise vsechny skupiny v eventu #}
                            {% set group = p.header.taxonomy.skupina %}
                            {% if group|length == 6 %} všichni 
                            {% else %}
                            {% if "zabicky" in group %} žabičky {% endif %} 
                            {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                            {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                            {% if "dorost" in group %} dorost+ {% endif %}
                            {% endif %}
                        </td>
                        <td class="nazev">{{ p.title }}</td>         {# vypise nazev s odkazem na event #}
                        <td class="sraz">
                          {# pokud se datum dne rovná datu zacatku eventu, zobrazi sraz #}
                          {% if p.header.start == datum_dne_v_tydnu %}
                            {% if p.header.meetTime is defined  %}
                              {{p.header.meetTime }}
                            {% endif %}
                          {% endif %}
                        </td>    {# vypise zacatek eventu aka sraz #}
                        <td class="misto"> {# vypise misto konani #}
                          {% if p.header.start == datum_dne_v_tydnu %}
                            {% if p.header.meetPlace is defined  %}
                              {{p.header.meetPlace }}
                            {% endif %}
                          {% endif %}
                        </td>
                        <td class="zobrazit"  style="cursor: pointer;padding: 0">
                            <a href="{{p.url}}" target="_blank">
                              <div>
                                <i class="fa fa-eye zobrazit" aria-hidden="true" style="color:#222; padding-right: 8px; padding-left: 8px;"></i>
                              </div>
                            </a>
                        </td>                            
                      </tr>
                    {% endif %}
                {% endfor %}
                {% for event in attribute(pravidelne_treninky, ENGweek[day_num]) %}
                  <tr {% if day_num % 2 == 0 %} class="plan--lichyDen event--basic"  {% else %} class="plan--sudyDen event--basic" {% endif %}
                      data-day="{{day_num}}" 
                      data-group="{{event.group|join(' ') }}"
                      data-name="{{event.name}}"
                      data-meetup="{{event.meetup}}"
                      data-place="{{event.place}}">
                        <td class="den">
                            {# zobrazi nazev dne (napr. "PO") jen pokud jiz neexistuje zaznam dne v tabulce #}
                            {% if zapsany_den != den %}    {# podminka, jestli uz se zapisoval #}
                              {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
                              {% set zapsany_den = den %}   {# ulozime, ze uz se zapisoval #}
                            {% endif %}
                        </td>
                        <td class="skupina"> {# vypise vsechny skupiny v eventu #}
                            {% set group = event.group %}
                            {% if group|length == 6 %} všichni 
                            {% else %}
                            {% if "zabicky" in group %} žabičky {% endif %} 
                            {% if "pulci1" in group and "pulci2" in group %} pulci {% elseif "pulci1" in group %} pulci1 {% elseif "pulci2" in group %} pulci2 {% endif %} 
                            {% if "zaci1" in group and "zaci2" in group %} žáci {% elseif "zaci1" in group %} žáci1 {% elseif "zaci2" in group %} žáci2 {% endif %} 
                            {% if "dorost" in group %} dorost+ {% endif %}
                            {% endif %}
                        </td>
                        <td class="nazev"> {{event.name}} </td>         {# vypise nazev s odkazem na event #}
                        <td class="sraz"> {{event.meetup}} </td>
                        <td class="misto"> {{event.place}} </td>                            {# vypise misto konani #}
                        <td></td>
                  </tr>
                {% endfor %}
                {# prida prazdny radek #}
                <tr {% if day_num % 2 == 0 %} class="plan--lichyDen event--add"  {% else %} class="plan--sudyDen event--add" {% endif %} data-day="{{day_num}}" data-group="" data-name="" data-meetup="" data-place="">
                  <td class="den"> 
                    {% if zapsany_den != den %}    {# podminka, jestli uz se zapisoval dan #}
                      {{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
                    {% else %}
                      &nbsp;
                    {% endif %}
                  </td>
                  <td class="skupina"></td>
                  <td class="nazev"></td>
                  <td class="sraz"> </td>
                  <td class="misto"> </td>
                  <td></td>
                </tr>
            {% endfor %}
         </table>
</div> <!-- plan -->
<button type="button" class="special" id="plan--save">Uložit změny</button>

<button type="button" id="plan--save-template">Načíst šablonu</button>
<select id="plan--select-template">
  <option value="summer">Letní</option>
  <option value="winter">Zimní</option>
  <option value="None">Prázdná</option>
</select>


<div id="plan--modal">
  <div id="plan--modal-content">
    <div class="row">
      <div class="col-6">
          <input type="checkbox" id="plan--checkbox-zabicky">
            <label for="plan--checkbox-zabicky">žabičky</label><br>
          <input type="checkbox" id="plan--checkbox-pulci1">
            <label for="plan--checkbox-pulci1">pulci 1</label><br>
          <input type="checkbox" id="plan--checkbox-pulci2">
            <label for="plan--checkbox-pulci2">pulci 2</label><br>
          <input type="checkbox" id="plan--checkbox-zaci1">
            <label for="plan--checkbox-zaci1">žáci 1</label><br>
          <input type="checkbox" id="plan--checkbox-zaci2">
            <label for="plan--checkbox-zaci2">žáci 2</label><br>
          <input type="checkbox" id="plan--checkbox-dorost">
            <label for="plan--checkbox-dorost">dorost+</label>
      </div>
      <div class="col-6">
        <input type="text" id="plan--name" placeholder="název">
        <input type="text" id="plan--meetup" placeholder="čas">
        <input type="text" id="plan--place" placeholder="místo">
          <br>
          <button type="button" class="special fit" id="plan--modal-save">Změnit</button>
          <button type="button" class="fit small" id="plan--modal-delete">Smazat</button>
        </div>
      </div>
    <div>  <!-- row -->
  </div> <!-- modal content -->
</div> <!-- modal -->







<script>

window.addEventListener('DOMContentLoaded', function() {
  /************************************/
  /* Eventy ze šablony - event--basic */
  /************************************/
  var modal = document.getElementById("plan--modal"),
      event_program = $(".event--program"),
      modal_content = document.getElementById("plan--modal-content"),
      zabicky = document.getElementById("plan--checkbox-zabicky"),
      pulci1 = document.getElementById("plan--checkbox-pulci1"),
      pulci2 = document.getElementById("plan--checkbox-pulci2"),
      zaci1 = document.getElementById("plan--checkbox-zaci1"),
      zaci2 = document.getElementById("plan--checkbox-zaci2"),
      dorost = document.getElementById("plan--checkbox-dorost"),
      name = document.getElementById("plan--name"),
      meetup = document.getElementById("plan--meetup"),
      place = document.getElementById("plan--place"),
      save_btn = document.getElementById("plan--save"),
      modal_sava_btn = document.getElementById("plan--modal-save"),
      modal_delete_btn = document.getElementById("plan--modal-delete");
      
    // otevření modalu a načtení do něj dat z tabulku
      // basic - ukládaný záznam, add - řádek pro přidání
      $(".event--basic, .event--add").click(open_modal);
        
      // nacteni dat z tabulky
      function open_modal(){
        clicked_row = this;
        // skupiny
          var groups = clicked_row.getAttribute("data-group");
          // pokud obsahuje nějaké znaky
          if(groups.length > 1){
            groups.split(" ").forEach(function(group_name) {
              document.getElementById("plan--checkbox-"+ group_name).checked = true;
            })
          }
        // nazev
          name.value = clicked_row.getAttribute("data-name");
        // cas
          meetup.value = clicked_row.getAttribute("data-meetup");
        // misto
          place.value = clicked_row.getAttribute("data-place");
          
          modal_content.style.marginTop = window.pageYOffset + "px";
          bodyScrollLock.disableBodyScroll(modal);
          modal.style.display = "block";
      }

      function reset_modal(){
        zabicky.checked = pulci1.checked = pulci2.checked = zaci1.checked = zaci2.checked = dorost.checked = false;
        name.value = meetup.value = place.value = "";
        modal.style.display = "none";
        bodyScrollLock.enableBodyScroll(modal);
      }

      function remove_if_empty(){
        /* pokud je všechno prazdne a neni posledni radek, odstrani se */
        if($(clicked_row).hasClass("event--basic") && name.value == "" && meetup.value == "" && place.value == "" ){
          // presune nazev dne
            var day_name = $(clicked_row).children(".den").html();
            if(day_name.trim().length > 1){
              $(clicked_row).next().children(".den").html(day_name);
            }
          // ostranit
          $(clicked_row).remove();
        }
      }

        // fce vrací upravená data do tabulky, odstrani radek pokud je prazdny, popř. po vyplnění dodá další prázdný
          function save_modal(event) {
            // presun dat 
              // skupiny
                var data_str = "",
                    show_str = "";
                if(zabicky.checked && pulci1.checked && pulci2.checked && zaci1.checked && zaci2.checked && dorost.checked){
                  data_str = "zabicky pulci1 pulci2 zaci1 zaci2 dorost";
                  show_str = "všichni";
                }
                else{
                  if(zabicky.checked){ data_str = "zabicky"; show_str = "žabičky";}
                  if(pulci1.checked && pulci2.checked){ data_str += " pulci1 pulci2"; show_str += " pulci";}
                    else if(pulci1.checked){ data_str += " pulci1"; show_str += " pulci1";}
                      else if(pulci2.checked){ data_str += " pulci2"; show_str += " pulci2";}
                  if(zaci1.checked && zaci2.checked){ data_str += " zaci1 zaci2"; show_str += " žáci";}
                    else if(zaci1.checked){ data_str += " zaci1"; show_str += " žáci1";}
                      else if(zaci2.checked){ data_str += " zaci2"; show_str += " žáci2";}
                  if(dorost.checked){ data_str += " dorost"; show_str += " dorost+";}     
                }
                clicked_row.setAttribute("data-group", data_str.trim());
                $(clicked_row).children(".skupina").html(show_str.trim()); 
              // nazev
                clicked_row.setAttribute("data-name", name.value);
                $(clicked_row).children(".nazev").html(name.value);
              // cas
                clicked_row.setAttribute("data-meetup", meetup.value);
                $(clicked_row).children(".sraz").html(meetup.value);
              // misto
                clicked_row.setAttribute("data-place", place.value);
                $(clicked_row).children(".misto").html(place.value);
            /* pokud je upravovan posledni radek dne, prida dalsi prazdny */
                if($(clicked_row).hasClass("event--add") && (name.value != "" || meetup.value != "" || place.value != "") ){
                  var new_row = document.createElement("tr");
                  var day = clicked_row.getAttribute("data-day"),
                      classes = clicked_row.className;
                  new_row.className = classes;
                  new_row.setAttribute("data-day", day);
                  new_row.setAttribute("data-group", "");
                  new_row.setAttribute("data-name", "");
                  new_row.setAttribute("data-meetup", "");
                  new_row.setAttribute("data-place", "");
                  $(new_row).addClass("event--add");
                  new_row.innerHTML = '<td class="den">&nbsp;</td>'+  
                                      '<td class="skupina"></td>'+
                                      '<td class="nazev"></td>'+
                                      '<td class="sraz"></td>'+
                                      '<td class="misto"></td>'+
                                      '<td></td>';
                  new_row.addEventListener('click', open_modal);
                  $(clicked_row).removeClass("event--add");
                  $(clicked_row).addClass("event--basic");
                  $(clicked_row).after(new_row);
                }
            
            remove_if_empty();
            reset_modal();
          } 
      // vymazat řádek
          modal_delete_btn.addEventListener('click',function(){
            reset_modal();
            remove_if_empty();
          })

      // Zavřit modal beze zmen
          // Ecs
          window.addEventListener('keyup',function(e){
            if (e.keyCode === 27) { 
              reset_modal();
            }
          })
          // click mimo modal
          window.addEventListener('click', function(e){
            if (e.target == modal) {
              reset_modal();
            }
          })

      // pri kliknuti na ulozit nebo enter navrátí data z modalu do tabulky
          // Enter
          window.addEventListener('keyup',function(e){
            if (e.keyCode === 13) { //13 = enter
              save_modal();
            }
          })
          
          // Save
          modal_sava_btn.addEventListener('click', save_modal)

      // uložit vše
      save_btn.addEventListener('click', function(){
        var planForm = new FormData();
        // všechny řádky tabulky označené jako event--basic pošle na zpracování
        $(".event--basic").each(function(index){
          var day = this.getAttribute("data-day");
          var groups = this.getAttribute("data-group");
          if(groups.length > 1){
            groups.split(" ").forEach(function(group_name) {
              planForm.append("data["+ day +"]["+ index +"][group][]", group_name );
            })
          }
          planForm.append("data["+ day +"]["+ index +"][name]", this.getAttribute("data-name") );
          planForm.append("data["+ day +"]["+ index +"][meetup]", this.getAttribute("data-meetup") );
          planForm.append("data["+ day +"]["+ index +"][place]", this.getAttribute("data-place") );
        })
        planForm.append("filePath", "{{'./' ~ page.relativePagePath() ~ '/' ~ page.name }}" );
        planForm.append("template", "{{ page.header.planTemplate }}" );

        $.ajax({
             url: "/php/plan",
             type: "POST",
             data: planForm,
             processData: false,
             contentType: false,
             success: function (){ 
               window.location.replace(location.href);
             },
             error: function (xhr, desc, err){

             }
        });
      })

  /******************/
  /* Načíst šablonu */
  /******************/
        var template_btn = document.getElementById("plan--save-template"),
            select = document.getElementById('plan--select-template');
        // zobrazí současně použitou šablonu jako výchozí
          var option;

          for (var i=0; i<select.options.length; i++) {
            option = select.options[i];

            if (option.value == "{{page.header.planTemplate}}") {
              option.setAttribute('selected', true);
            } 
          }
        // uložit výběr šablony
          template_btn.addEventListener('click', function(){
            var templateForm = new FormData();
            templateForm.append("template", select.options[select.selectedIndex].value);
            templateForm.append("filePath", "{{'./' ~ page.relativePagePath() ~ '/' ~ page.name }}" );
            $.ajax({
                url: "/php/plan/loadtemplate",
                type: "POST",
                data: templateForm,
                processData: false,
                contentType: false,
                success: function (){ 
                  window.location.replace(location.href);
                },
                error: function (xhr, desc, err){

                }
            });
          })
  /*****************************************************/
  /* Otevřít úpravu eventu z programu - event--program */
  /*****************************************************/
     /* $(".event--program").click(function() {
          if(!$(this).hasClass( "zobrazit" )){
            console.log(this);
            var redirectWindow = window.open(this.getAttribute("data-path"), '_blank');
            redirectWindow.location;     
          }
      }); */
    $(".event--program").click(function(event) {
      if(!$(event.target).hasClass('zobrazit')) {
        var redirectWindow = window.open(this.getAttribute("data-path"), '_blank');
            redirectWindow.location;  
            console.log(event.target);
      }
    });
});
</script>
































