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
            1:
                name: Posilovna
                place: 'Bohunice, kampus, FSpS'
                meetup: '14:30'
                group:
                    - dorost
        tuesday:
            1:
                name: 'Běžecké posilování'
                place: 'Stadion pod Palackého vrchem'
                meetup: '16:00 – 17:45'
                group:
                    - zaci1
                    - zaci2
        wednesday:
            1:
                name: Posilovna
                place: 'Bohunice, kampus, FSpS'
                meetup: '14:30'
                group:
                    - dorost
        thursday:
            1:
                name: 'Běžecký trénink'
                place: 'hala Rosnička (sraz) a okolí'
                meetup: '16:00 – 17:45 (P1 + P2 + Z1 prvním rokem)'
                group:
                    - pulci1
                    - pulci2
                    - zaci1
            2:
                name: 'Běžecký trénink'
                place: 'hala Rosnička (sraz) a okolí'
                meetup: '16:00 – 18:00 (Z1 druhým rokem + Z2)'
                group:
                    - zaci1
                    - zaci2
            3:
                name: Dráha
                place: 'Areál VUT, pPv'
                meetup: '17:00'
                group:
                    - dorost
        friday: null
        saturday: null
        sunday: null
    nextWeek:
        monday:
            1591643170881:
                name: ''
                time: ''
                place: ''
            1591643173148:
                name: ''
                time: ''
                place: ''
        tuesday:
            1591643170892:
                name: ''
                time: ''
                place: ''
            1591643173156:
                name: ''
                time: ''
                place: ''
        wednesday:
            1591643170897:
                name: ''
                time: ''
                place: ''
            1591643173161:
                name: ''
                time: ''
                place: ''
        thursday:
            1591643170902:
                name: ''
                time: ''
                place: ''
            1591643173165:
                name: ''
                time: ''
                place: ''
        friday:
            1591643170906:
                name: ''
                time: ''
                place: ''
            1591643173169:
                name: ''
                time: ''
                place: ''
        saturday:
            1591643170911:
                name: ''
                time: ''
                place: ''
            1591643173174:
                name: ''
                time: ''
                place: ''
        sunday:
            1591643170916:
                name: ''
                time: ''
                place: ''
            1591643173181:
                name: ''
                time: ''
                place: ''
---
<style>
    .plan input {
        width: 100%;
        border-radius: 0px;
        background-color: inherit;
    }
    table.plan td {
        padding: 0px;
    }
    .multiselect-wrapper, .multiselect-input-div, .group {
        width: 130px;
        background-color: inherit;
    }
    table.plan .day, table.plan .delete {
        padding: 0 1em;
        border: solid 1px rgba(210, 215, 217, 0.75);
    }
    .multiselect-count {
        background-color: #2b2b2b;
        color: white;
    }
.multiselect-wrapper ul li.active, .multiselect-wrapper ul li:hover {
    background-color: #e65a51;
    color: #fff;
}
li.active:last-child {

    padding: inherit !important;
    margin: inherit;

}
.multiselect-wrapper .multiselect-list .multiselect-checkbox {
    margin-right: -132px;
}
table.plan .event {
    height: 2.75em;
    padding: 0 0.9em;
    border: solid 1px rgba(210, 215, 217, 0.75);
}
table.plan .event.group {
    cursor: default;
}
.multiselect-list.active li {
    padding: 0px;
}

</style>
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
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button id="plan--submit" type="button" class="button special">Uložit</button>
<br><br>
{# inicializace poli urcujiciho den v tydnu #}
{% set collection = page.collection({'items': {'@page.descendants': '/data/events'}, 'filter': {'routable': 'true'},'order': {'by': 'default', 'dir': 'asc'}}) %}

<form autocomplete="off" id="program"> 
{% for week, weekAr in page.header.plan %}
    {% if loop.first %}
        {% set start_day = date("monday this week") %}
        <h4>Aktuální týden {{ start_day|date("j.m.") ~ " – " ~ start_day|date_modify("+6 day")|date("j.m.") }}</h4>
    {% else %}
        {% set start_day = date("monday next week")%}
        <h4>Příští týden {{ start_day|date("j.m.") ~ " – " ~ start_day|date_modify("+6 day")|date("j.m.") }}</h4>
    {% endif %}
    
    <table class="plan">
        {# inicializace promene urcujici jestli uz je v tabulce zapsany den v tydnu #}
        
        {% for day_num, day in ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'] %}
              
            {% set datum_dne_v_tydnu = start_day|date_modify(" +" ~ day_num ~ " days")|date("Y-m-d") %}
            {% set day_collection = collection.copy() %}
            {% for p in day_collection  if not (  p.header.start <= datum_dne_v_tydnu and p.header.end >= datum_dne_v_tydnu ) %}
                {% set day_collection = day_collection.remove() %}
            {% endfor %}

            {% for p in day_collection %}
                <tr class="{% if day_num % 2 == 0 %} plan--lichyDen  {% else %} plan--sudyDen {% endif %}" data-week={{week}} data-day={{day}}>
                    <td class="group">
                        <select class="multi" multiple disabled name="events[{{p.header.id}}]{{week}} {{day}}][group]">
                            {% set group = p.header.taxonomy.skupina %}
                            <option value="zabicky" {% if "zabicky" in group %} selected {% endif %}>Žabičky</option>
                            <option value="pulci1" {% if "pulci1" in group %} selected {% endif %}>Pulci 1</option>
                            <option value="pulci2" {% if "pulci2" in group %} selected {% endif %}>Pulci 2</option>
                            <option value="zaci1" {% if "zaci1" in group %} selected {% endif %}>Žáci 1</option>
                            <option value="zaci2" {% if "zaci2" in group %} selected {% endif %}>Žáci 2</option>
                            <option value="dorost" {% if "dorost" in group %} selected {% endif %}>Dorost+</option>
                        </select>  
                    </td>
                    <td class="link event" colspan="4"> 
                        <a href="{{ p.url }}" target="_blank">{{ p.title }}</a> &nbsp;&nbsp;&nbsp;
                        <a href="/auth/events/edit?event={{ p.header.id[:4] }}/{{ p.header.id|lower }}" target="_blank"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    </td>
                </tr>
            {% endfor %}
            
            {% set events = weekAr[day] %}
            {% for timestamp, event in events %}
                <tr {% if day_num % 2 == 0 %} class="plan--lichyDen"  {% else %} class="plan--sudyDen" {% endif %} data-week={{week}} data-day={{day}}>
                    <td class="group">
                        <select class="multi" multiple  name="plan[{{week}}][{{day}}][{{timestamp}}][group][]">
                            <option value="zabicky" {% if "zabicky" in event.group %} selected {% endif %}>Žabičky</option>
                            <option value="pulci1" {% if "pulci1" in event.group %} selected {% endif %}>Pulci 1</option>
                            <option value="pulci2" {% if "pulci2" in event.group %} selected {% endif %}>Pulci 2</option>
                            <option value="zaci1" {% if "zaci1" in event.group %} selected {% endif %}>Žáci 1</option>
                            <option value="zaci2" {% if "zaci2" in event.group %} selected {% endif %}>Žáci 2</option>
                            <option value="dorost" {% if "dorost" in event.group %} selected {% endif %}>Dorost+</option>
                        </select>
                    </td>
                    <td class="name"> <input type="text" size="25" value="{{event.name}}" name="plan[{{week}}][{{day}}][{{timestamp}}][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="5" value="{{event.time}}" name="plan[{{week}}][{{day}}][{{timestamp}}][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" value="{{event.place}}" name="plan[{{week}}][{{day}}][{{timestamp}}][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>                         
                </tr>
            {% endfor %}

            {% if events|length + day_collection|length == 0 %}
                {% set timestamp = 'now'|date('U') %}
                <tr {% if day_num % 2 == 0 %} class="plan--lichyDen"  {% else %} class="plan--sudyDen" {% endif %} data-week={{week}} data-day={{day}}>
                    <td class="group">
                            <select class="multi" multiple  name="plan[{{week}}][{{day}}][{{timestamp}}][group][]">
                                <option value="zabicky">Žabičky</option>
                                <option value="pulci1">Pulci 1</option>
                                <option value="pulci2">Pulci 2</option>
                                <option value="zaci1">Žáci 1</option>
                                <option value="zaci2">Žáci 2</option>
                                <option value="dorost">Dorost+</option>
                            </select>
                        </td>
                    <td class="name"> <input type="text" size="25" name="plan[{{week}}][{{day}}][{{timestamp}}][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="5" name="plan[{{week}}][{{day}}][{{timestamp}}][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" name="plan[{{week}}][{{day}}][{{timestamp}}][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>          
                </tr>
            {% endif %}
            
        {% endfor %}
    </table>
{% endfor %}
</form>
<button type="button" id="plan--load-template">Načíst ze šablony <i class="fa fa-window-maximize" aria-hidden="true"></i></button> 

<button type="button" onclick="window.open('./plan2/templates','_blank')">Spravovat šablony <i class="fa fa-external-link" aria-hidden="true"></i></button>



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
        <button type="button" class="special" id="News--submit-all">Nahradit</button>
        <button type="button" id="News--close">Zrušit</button>
      </form>
      <div id="News--responseText" style="color:red"></div>
    </div> <!-- modal content -->
</div> <!-- modal -->

<script>
    document.addEventListener('DOMContentLoaded', function(){
        
        // init multiselects for group selection
        $('.multi').multiselect();

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
                        </select>
                <td class="name"> <input type="text" size="25" name="${formNamePrefix}[name]" placeholder="název"> </td>     
                <td class="time"> <input type="text" size="5" name="${formNamePrefix}[time]" placeholder="čas"> </td>
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
                    !row.querySelector(".group").firstElementChild.value &&
                    !row.querySelector(".name")?.firstElementChild.value  &&
                    !row.querySelector(".time")?.firstElementChild.value  &&
                    !row.querySelector(".place")?.firstElementChild.value) 
                {
                    row.parentNode?.removeChild(row);
                }
            })
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
        const submitButton = document.getElementById("plan--submit");
        submitButton.addEventListener("click", (e) => {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            e.preventDefault();
            var planForm = new FormData(document.getElementById("program"));
            $.ajax({
             url: "/php/plan/saveplan",
             type: "POST",
             data: planForm,
             processData: false,
             contentType: false,
             success: function (){ 
                const initialText = submitButton.innerHTML;
                submitButton.innerHTML = 'Uloženo <i class="fa fa-check" aria-hidden="true"></i>';
                submitButton.style.backgroundColor = "green";
                setTimeout( () => {
                    submitButton.innerHTML = initialText;
                    submitButton.style.backgroundColor = "";
                }, 2000);
             },
             error: function (xhr, desc, err){

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
        document.getElementById("News--close").onclick = function(e) {
            modal.style.display = "none";
        }
        document.getElementById("News--submit-all").addEventListener("click", (e) => {
            if (document.querySelector(".multiselect-list.active") !== null) return;
            e.preventDefault();
            var formData = new FormData(e.target.closest("form"));
            $.ajax({
             url: "/php/plan/loadfromtemplate",
             type: "POST",
             data: formData,
             processData: false,
             contentType: false,
             success: function (){ 
               window.location.replace(location.href);
             },
             error: function (xhr, desc, err){

             }
            });
        })
        
    })

    
    

</script>