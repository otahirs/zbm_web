---
title: 'Týdenní plán'
template: default
access:
    site:
        plan: true
process:
    markdown: false
plan:
    thisWeek:
        monday:
            -
                group:
                    - dorost
                name: 'Atletické posilko'
                time: '16:45'
                place: 'Nám. Svornosti, https://mapy.cz/s/cubasamuca'
            -
                group:
                    - zaci1
                    - zaci2
                name: Tělocvična
                time: '16:30 – 18:00'
                place: 'hala Rosnička'
        tuesday:
            -
                group:
                    - zabicky
                    - pulci1
                    - pulci2
                    - zaci1
                    - zaci2
                name: 'Mapový trénink'
                time: '16:00 – 18:00'
                place: 'okolí Brna'
        wednesday:
            -
                group:
                    - dorost
                name: 'Atletické posilko'
                time: '16:45'
                place: 'Nám. Svornosti, https://mapy.cz/s/cubasamuca'
        thursday:
            -
                group:
                    - pulci1
                    - pulci2
                    - zaci1
                    - zaci2
                name: 'Běžecký trénink'
                time: '16:00 – 18:00'
                place: 'Stadion pod Palackého vrchem'
            -
                group:
                    - dorost
                name: Dráha
                time: '17:00'
                place: 'Areál VUT, pPv'
        friday:
            -
                name: ''
                time: ''
                place: ''
        saturday:
            -
                group:
                    - zaci2
                name: 'Běh dle plánu'
                time: ''
                place: Samostatně
        sunday:
            -
                name: ''
                time: ''
                place: ''
    nextWeek:
        monday:
            -
                group:
                    - dorost
                name: 'Atletické posilko'
                time: '16:45'
                place: 'Nám. Svornosti, https://mapy.cz/s/cubasamuca'
            -
                group:
                    - zaci1
                    - zaci2
                name: Tělocvična
                time: '16:30 – 18:00'
                place: 'hala Rosnička'
        tuesday:
            -
                group:
                    - zabicky
                    - pulci1
                    - pulci2
                    - zaci1
                    - zaci2
                name: 'Mapový trénink'
                time: '16:00 – 18:00'
                place: 'okolí Brna'
        wednesday:
            -
                group:
                    - dorost
                name: 'Atletické posilko'
                time: '16:45'
                place: 'Nám. Svornosti, https://mapy.cz/s/cubasamuca'
        thursday:
            -
                group:
                    - pulci1
                    - pulci2
                    - zaci1
                    - zaci2
                name: 'Běžecký trénink'
                time: '16:00 – 18:00'
                place: 'Stadion pod Palackého vrchem'
            -
                group:
                    - dorost
                name: Dráha
                time: '17:00'
                place: 'Areál VUT, pPv'
        friday:
            -
                name: ''
                time: ''
                place: ''
        saturday:
            -
                group:
                    - zaci2
                name: 'Běh dle plánu'
                time: ''
                place: Samostatně
        sunday:
            -
                name: ''
                time: ''
                place: ''
---

<div class="row justify-content-between"> 
    <div class="col">
        <input type="checkbox" value="all"  id="filter-all" checked />
        <label for="filter-all">Vše</label>
        <input class="filter" type="checkbox" value="zabicky" id="filter-zabicky" />
        <label for="filter-zabicky">Žabičky</label>
        <input class="filter" type="checkbox" value="pulci1" id="filter-pulci1" />
        <label for="filter-pulci1">Pulci 1</label>
        <input class="filter" type="checkbox" value="pulci2" id="filter-pulci2" />
        <label for="filter-pulci2">Pulci 2</label>
        <input class="filter" type="checkbox" value="zaci1" id="filter-zaci1" />
        <label for="filter-zaci1">Žáci 1</label>
        <input class="filter" type="checkbox" value="zaci2" id="filter-zaci2" />
        <label for="filter-zaci2">Žáci 2</label>
        <input class="filter" type="checkbox" value="dorost" id="filter-dorost" />
        <label for="filter-dorost">Dorost+</label>
        <input class="filter" type="checkbox" value="hobby" id="filter-hobby" />
        <label for="filter-hobby">Hobby</label>
    </div>
    <div class="col-auto">
        <button class="edit-plan__submit special" type="button">Uložit</button>
    </div>
</div>
<br>
{# inicializace poli urcujiciho den v tydnu #}
{% set collection = page.collection({'items': {'@page.descendants': '/data/events'}, 'filter': {'routable': 'true'},'order': {'by': 'default', 'dir': 'asc'}}) %}

<form autocomplete="off" id="program"> 
{% set plan = page.header.plan %}
{% for week, weekAr in {"thisWeek": plan.thisWeek, "nextWeek" : plan.nextWeek} %}
    {% if loop.first %}
        {% set start_day = date("monday this week") %}
        <h4>Aktuální týden {{ start_day|date("j.m.") ~ " – " ~ start_day|date_modify("+6 day")|date("j.m.") }}</h4>
    {% else %}
        {% set start_day = date("monday next week")%}
        <h4>Příští týden {{ start_day|date("j.m.") ~ " – " ~ start_day|date_modify("+6 day")|date("j.m.") }}</h4>
    {% endif %}
    
    <table class="edit-plan">
        {# inicializace promene urcujici jestli uz je v tabulce zapsany den v tydnu #}
        
        {% for day_num, day in ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'] %}
              
            {% set datum_dne_v_tydnu = start_day|date_modify(" +" ~ day_num ~ " days")|date("Y-m-d") %}
            {% set day_collection = collection.copy() %}
            {% for p in day_collection  if not (  p.header.start <= datum_dne_v_tydnu and p.header.end >= datum_dne_v_tydnu ) %}
                {% set day_collection = day_collection.remove() %}
            {% endfor %}

            {% for p in day_collection %}
                <tr class="{% if day_num % 2 == 0 %} edit-plan__odd-day  {% else %}edit-plan__even-day {% endif %}" data-week={{week}} data-day={{day}}>
                    <td class="group">
                         {% if p.header.start == datum_dne_v_tydnu %}
                        <select class="multi" multiple name="events[{{p.header.id}}][]">
                            {% set group = p.header.taxonomy.skupina %}
                            <option value="zabicky" {% if "zabicky" in group %} selected {% endif %}>Žabičky</option>
                            <option value="pulci1" {% if "pulci1" in group %} selected {% endif %}>Pulci 1</option>
                            <option value="pulci2" {% if "pulci2" in group %} selected {% endif %}>Pulci 2</option>
                            <option value="zaci1" {% if "zaci1" in group %} selected {% endif %}>Žáci 1</option>
                            <option value="zaci2" {% if "zaci2" in group %} selected {% endif %}>Žáci 2</option>
                            <option value="dorost" {% if "dorost" in group %} selected {% endif %}>Dorost+</option>
                            <option value="hobby" {% if "hobby" in group %} selected {% endif %}>Hobby</option>
                        </select>  
                        {% endif %}
                    </td>
                    <td class="link event" colspan="4"> 
                        <a href="{{ p.url }}" target="_blank">{{ p.title }}</a> &nbsp;&nbsp;&nbsp;
                        <a href="/auth/events/edit?event={{ p.header.id }}" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </td>
                </tr>
            {% endfor %}
            
            {% set events = weekAr[day] %}
            {% for timestamp, event in events %}
                <tr {% if day_num % 2 == 0 %} class="edit-plan__odd-day"  {% else %} class="edit-plan__even-day" {% endif %} data-week={{week}} data-day={{day}}>
                    <td class="group">
                        <select class="multi" multiple  name="plan[{{week}}][{{day}}][{{timestamp}}][group][]">
                            <option value="zabicky" {% if "zabicky" in event.group %} selected {% endif %}>Žabičky</option>
                            <option value="pulci1" {% if "pulci1" in event.group %} selected {% endif %}>Pulci 1</option>
                            <option value="pulci2" {% if "pulci2" in event.group %} selected {% endif %}>Pulci 2</option>
                            <option value="zaci1" {% if "zaci1" in event.group %} selected {% endif %}>Žáci 1</option>
                            <option value="zaci2" {% if "zaci2" in event.group %} selected {% endif %}>Žáci 2</option>
                            <option value="dorost" {% if "dorost" in event.group %} selected {% endif %}>Dorost+</option>
                            <option value="hobby" {% if "hobby" in event.group %} selected {% endif %}>Hobby</option>
                        </select>
                    </td>
                    <td class="name"> <input type="text" size="25" value="{{event.name}}" name="plan[{{week}}][{{day}}][{{timestamp}}][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="9" value="{{event.time}}" name="plan[{{week}}][{{day}}][{{timestamp}}][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" value="{{event.place}}" name="plan[{{week}}][{{day}}][{{timestamp}}][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>                         
                </tr>
            {% endfor %}

            {% if events|length + day_collection|length == 0 %}
                {% set timestamp = 'now'|date('U') %}
                <tr {% if day_num % 2 == 0 %} class="edit-plan__odd-day"  {% else %} class="edit-plan__even-day" {% endif %} data-week={{week}} data-day={{day}}>
                    <td class="group">
                        <select class="multi" multiple  name="plan[{{week}}][{{day}}][{{timestamp}}][group][]">
                            <option value="zabicky">Žabičky</option>
                            <option value="pulci1">Pulci 1</option>
                            <option value="pulci2">Pulci 2</option>
                            <option value="zaci1">Žáci 1</option>
                            <option value="zaci2">Žáci 2</option>
                            <option value="dorost">Dorost+</option>
                            <option value="hobby">Hobby</option>
                        </select>
                    </td>
                    <td class="name"> <input type="text" size="25" name="plan[{{week}}][{{day}}][{{timestamp}}][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="9" name="plan[{{week}}][{{day}}][{{timestamp}}][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" name="plan[{{week}}][{{day}}][{{timestamp}}][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>          
                </tr>
            {% endif %}
            
        {% endfor %}
    </table>
{% endfor %}
</form>
<button type="button" id="plan--load-template">Načíst ze šablony <i class="fa fa-upload" aria-hidden="true"></i></button> 

<a href="/auth/plan2/templates" class="button">Spravovat šablony <i class="fa fa-th" aria-hidden="true"></i></a>

<div style="min-height: 8em;">&nbsp; <!-- prevents group selection dialog overflow --> </div>

<div id="NewsModal" class="news--modal">
    <div id="NewsModalContent" class="news--modal-content">
      <h2 id="News--header">Načíst plán ze šablony</h2>
      <form id="News--form" method="post" action="/php/plan/loadfromtemplate">
        <div>
            <p>Pozor! Data ze šablony nahradí všechny aktivity v daném týdnu pro všechny tréninkové skupiny. Veškeré změny budou ztraceny. Události zobrazované v kalendáři zůstávají zachovány.</p>

            <label for="week">nahradit</label>
            <select name="week" id="week">
                <option value="thisWeek">tento týden</option>
                <option value="nextWeek" selected>příští týden</option>
            </select>
            <label for="template">šablonou</label>
            <select name="template" id="template">
                {% set template_page = page.find('/auth/plan2/templates').header%}
                {% for template_name, _ in template_page.templates %} 
                    <option value="{{template_name}}" {% if template_page.defaultTemplate == template_name %} selected {% endif %}>{{template_name}}</option>
                {% endfor %}
            </select>
        </div>
        <hr>
        <button type="button" class="special edit-plan-modal__submit-button">Nahradit</button>
        <button type="button" class="edit-plan-modal__close-button">Zrušit</button>
      </form>
    </div> <!-- modal content -->
</div> <!-- modal -->

<script>
    window.addEventListener('DOMContentLoaded', function(){

        const notyf = new Notyf({
            position: {
                x: 'right',
                y: 'top',
            },
            duration: 3500,
        });
        
        // init multiselects for group selection
        $('.multi').multiselect();

        resetMultiselects = () => {
            removeAllMultiselects();
            $('.multi').multiselect();
        }

        // append row to day
        function createNewRowForDay(otherTr){
            let tr = document.createElement("tr");
            tr.className  = otherTr.className;
            Object.assign(tr.dataset, otherTr.dataset);
            const formNamePrefix = `plan[${otherTr.dataset.week}][${otherTr.dataset.day}][${new Date().getTime()}]`;
            tr.innerHTML = `
                <td class="group">
                        <select class="multi" multiple="" name="${formNamePrefix}[group][]">
                            <option value="zabicky">Žabičky</option>
                            <option value="pulci1">Pulci 1</option>
                            <option value="pulci2">Pulci 2</option>
                            <option value="zaci1">Žáci 1</option>
                            <option value="zaci2">Žáci 2</option>
                            <option value="dorost">Dorost+</option>
                            <option value="hobby">Hobby</option>
                        </select>
                <td class="name"> <input type="text" size="25" name="${formNamePrefix}[name]" placeholder="název"> </td>     
                <td class="time"> <input type="text" size="9" name="${formNamePrefix}[time]" placeholder="čas"> </td>
                <td class="place"> <input type="text" size="40" name="${formNamePrefix}[place]" placeholder="místo"> </td>
                <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td> `;
            
            tr.querySelector(".delete").addEventListener("click", deleteRow);
  
            const dayTrList = document.querySelectorAll(`[data-week="${otherTr.dataset.week}"][data-day="${otherTr.dataset.day}"]`);
            let appendAfterTr = dayTrList.item(dayTrList.length - 1);

            appendAfterTr.parentNode.insertBefore(tr, appendAfterTr.nextSibling);
            $(tr).find("select").multiselect();
            return tr;
        }

        function appendRowToDay(clickEvent) {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            createNewRowForDay(clickEvent.target.parentNode);
            clickEvent.target.rowSpan = clickEvent.target.rowSpan + 1;
        }

        document.querySelectorAll(".day").forEach((day) => {
            day.addEventListener("click", appendRowToDay)
        })

        // rebuilt first coll of table with rowspan
        function rebuiltDayNames() {
            document.querySelectorAll(".day").forEach(e => e.parentNode.removeChild(e));
            const CZdays = ["pondělí", "úterý", "středa", "čtvrtek", "pátek", "sobota", "neděle"];
            const days = ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'];
            const weeks = ["thisWeek", "nextWeek"];
            const rows = document.querySelectorAll("tr");
            let currDay = 0;
            let currWeek = 0;
            rows.forEach(function(tr, i) {
                if (tr.dataset.day != days[currDay]) {
                    return;
                }
                if (tr.style.display === "none") {
                    if(rows[i+1]?.dataset.day == days[currDay]) {
                        return;
                    } else {
                        tr = createNewRowForDay(tr);
                    }
                    
                }
                let day = days[currDay];
                let week = weeks[currWeek];
                let dayName = document.createElement("td");
                dayName.className = "day";
                dayName.innerHTML = CZdays[currDay];
                dayName.rowSpan = document.querySelectorAll(`[data-week="${week}"][data-day="${day}"]:not([style*="display: none"]`).length;
                dayName.addEventListener("click", appendRowToDay);
                tr.prepend(dayName);
                currDay += 1;
                if (currDay == 7) {
                    currWeek = 1;
                    currDay = 0;
                }
            });

        }

        // cleanup table
        function removeEmptyRows() {
            const rows = document.querySelectorAll("tr");
            rows.forEach(row => {
                let week = row.dataset.week;
                let day = row.dataset.day;
                let rowsInDay = document.querySelectorAll(`[data-week="${week}"][data-day="${day}"]`).length;
                if ( rowsInDay > 1 &&
                    !row.querySelector(".group")?.firstElementChild?.value &&
                    !row.querySelector(".name")?.firstElementChild.value  &&
                    !row.querySelector(".time")?.firstElementChild.value  &&
                    !row.querySelector(".place")?.firstElementChild.value &&
                    !row.querySelector(".event")) 
                {
                    row.parentNode?.removeChild(row);
                }
            })
            resetMultiselects();
            rebuiltDayNames();
        }
        removeEmptyRows();

        // delete row 
        function deleteRow() {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            let row = this.parentNode;
            let week = row.dataset.week;
            let day = row.dataset.day;
            
            if(document.querySelectorAll(`[data-week="${week}"][data-day="${day}"]`).length === 1) {
                createNewRowForDay(row);
            }
            row.parentNode.removeChild(row);
            resetMultiselects();
            rebuiltDayNames();            
        }

        document.querySelectorAll(".delete").forEach((td) => {
            td.addEventListener("click", deleteRow);
        })

        // filters
        let filters = document.querySelectorAll(".filter");
        let filter_all = document.querySelector("#filter-all");        

        function filter() {
            const rows = document.querySelectorAll("tr");
            filter_all.checked = false;
                rows.forEach((row) => {
                    const selected = row.querySelectorAll('option:checked');
                    const values = Array.from(selected).map(el => el.value);
                    row.style.display = "none";
                    filters.forEach((filter) => {
                        if(filter.checked && values.indexOf(filter.value) >= 0)
                            row.style.display = "table-row";
                    })
                })
                removeEmptyRows();
        }

        let initFilterRun = false;
        filters.forEach((f) => {
            f.addEventListener("change", filter);
            if (f.checked) {
                initFilterRun = true;
            }
        })
        if (initFilterRun) {
            filter();
        }

        
        filter_all.addEventListener("change", () => {
            const rows = document.querySelectorAll("tr");
            filters.forEach((f)=> {
                f.checked = false;
            })
            rows.forEach((r) => {
                r.style.display = filter_all.checked ? "table-row" : "none";
            })
            removeEmptyRows();
        })

        // submit form
        const submitButton = document.querySelector(".edit-plan__submit");
        submitButton.addEventListener("click", (e) => {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            e.preventDefault();
            $.ajax({
                url: "/php/plan/saveplan",
                type: "POST",
                data: new FormData(document.getElementById("program")),
                processData: false,
                contentType: false,
                success: function (){ 
                    notyf.success("Plán uložen!");
                },
                error: function (xhr, desc, err){
                    notyf.error("Neočekávaná chyba");
                    console.log(xhr);
                    console.log(desc);
                    console.log(err);
                }
            });
        })

        // modal
        // modal
        var modal = document.querySelector(".news--modal");
        var modal_content =  document.querySelector(".news--modal-content");
        document.getElementById("plan--load-template").onclick = function(e) {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            modal_content.style.marginTop = window.pageYOffset + "px";
            modal.style.display = "block";
        }
        document.querySelector(".edit-plan-modal__close-button").onclick = function(e) {
            modal.style.display = "none";
        }
        document.querySelector(".edit-plan-modal__submit-button").addEventListener("click", (e) => {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            e.preventDefault();
            $.ajax({
                url: "/php/plan/loadfromtemplate",
                type: "POST",
                data: new FormData(e.target.closest("form")),
                processData: false,
                contentType: false,
                success: function (){ 
                    notyf.success("Šablona načtena!<br>Stránka se obnoví.");
                    modal.style.display = "none";
                    setTimeout(() => window.location.replace(location.href), 1000);
                },
                error: function (xhr, desc, err){
                    notyf.error("Neočekávaná chyba");
                    console.log(xhr);
                    console.log(desc);
                    console.log(err);
                }
            });
        })
        
    })

    
    

</script>