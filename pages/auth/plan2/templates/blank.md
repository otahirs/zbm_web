---
title: 'Týdenní plán - šablony'
template: default
access:
    site:
        plan: true
process:
    markdown: false
cache_enable: false
defaultTemplate: Letní
templates:
    Letní:
        monday:
            -
                group:
                    - dorost
                name: Posilovna
                time: '14:30'
                place: 'Bohunice, kampus, FSpS'
        tuesday:
            -
                group:
                    - zaci1
                    - zaci2
                name: 'Běžecké posilování'
                time: '16:00 – 17:45'
                place: 'Stadion pod Palackého vrchem'
        wednesday:
            -
                group:
                    - dorost
                name: Posilovna
                time: '14:30'
                place: 'Bohunice, kampus, FSpS'
        thursday:
            -
                group:
                    - pulci1
                    - pulci2
                    - zaci1
                name: 'Běžecký trénink (P1 + P2 + Z1 prvním rokem)'
                time: '16:00 – 17:45'
                place: 'hala Rosnička (sraz) a okolí'
            -
                group:
                    - zaci1
                    - zaci2
                name: 'Běžecký trénink (Z1 druhým rokem + Z2)'
                time: '16:00 – 18:00'
                place: 'hala Rosnička (sraz) a okolí'
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
                name: ''
                time: ''
                place: ''
        sunday:
            -
                name: ''
                time: ''
                place: ''
    Zimní: {  }
---

<div class="notices red" id="error" style="display:none">Při odesílání požadavku došlo k chybě. Pokud problém přetrvává, popište ho prosím na <i>web@zabiny.club</i><br> Ota</div>
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
    </div>
    <div class="col-auto">
        <button id="edit-plan__submit" type="button" class="button special">Uložit</button>
    </div>
</div>
<br>
{% set curr_template = uri.query('template') ?? page.header.defaultTemplate %}
<label for="choose-template">Vybrat šablonu:</label>
<select name="template" id="choose-template" autocomplete="off" style="display:inline">
    {% for template_name, _ in page.header.templates %} 
        <option value="{{template_name}}" {% if curr_template == template_name %} selected {% endif %}>{{template_name}}</option>
    {% endfor %}
</select>
<button class="set-default-template" type="button" title='Výchozí šablona vyplňuje plán "příští týden" při posunu týdnů.' {% if curr_template == page.header.defaultTemplate %} disabled>Tato šablona je výchozí {% else %}>Nastavit jako výchozí {% endif %}</button> 
<br><br>
{# inicializace poli urcujiciho den v tydnu #}
{% set collection = page.collection({'items': {'@page.descendants': '/data/events'}, 'filter': {'routable': 'true'},'order': {'by': 'default', 'dir': 'asc'}}) %}

<form autocomplete="off" id="program"> 
    {% set start_day = date("monday this week") %}
    {% set template = attribute(page.header.templates, uri.query('template')) ?? attribute(page.header.templates, page.header.defaultTemplate) %}
    
    <table class="edit-plan">
        {# inicializace promene urcujici jestli uz je v tabulce zapsany den v tydnu #}
        
        {% for day_num, day in ['monday','tuesday','wednesday','thursday','friday','saturday', 'sunday'] %}
            {% set datum_dne_v_tydnu = start_day|date_modify(" +" ~ (loop.index0)  ~ " days")|date("Y-m-d") %}
            {% set events = template[day] %}

            {% for event in events %}
                {% set n = loop.index0 %}
                <tr {% if day_num % 2 == 0 %} class="edit-plan__odd-day"  {% else %} class="edit-plan__even-day" {% endif %} data-day={{day}}>
                    {% if loop.first and allday_event_count == 0 %}
                        <td class="day"  rowspan="{{loop.length + allday_event_count}}">{{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccc') }}</td>
                    {% endif %}
                    <td class="group">
                        <select class="multi" multiple  name="template[{{curr_template}}][{{day}}][{{n}}][group][]">
                            <option value="zabicky" {% if "zabicky" in event.group %} selected {% endif %}>Žabičky</option>
                            <option value="pulci1" {% if "pulci1" in event.group %} selected {% endif %}>Pulci 1</option>
                            <option value="pulci2" {% if "pulci2" in event.group %} selected {% endif %}>Pulci 2</option>
                            <option value="zaci1" {% if "zaci1" in event.group %} selected {% endif %}>Žáci 1</option>
                            <option value="zaci2" {% if "zaci2" in event.group %} selected {% endif %}>Žáci 2</option>
                            <option value="dorost" {% if "dorost" in event.group %} selected {% endif %}>Dorost+</option>
                        </select>
                    </td>
                    <td class="name"> <input type="text" size="25" value="{{event.name}}" name="template[{{curr_template}}][{{day}}][{{n}}][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="9" value="{{event.time}}" name="template[{{curr_template}}][{{day}}][{{n}}][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" value="{{event.place}}" name="template[{{curr_template}}][{{day}}][{{n}}][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>                         
                </tr>
            {% else %}
                <tr {% if day_num % 2 == 0 %} class="edit-plan__odd-day"  {% else %} class="edit-plan__even-day" {% endif %} data-day={{day}}>
                    {% if allday_event_count == 0 %}
                        <td class="day"  rowspan="1">{{ datum_dne_v_tydnu|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccc') }}</td>
                    {% endif %}
                    <td class="group">
                            <select class="multi" multiple  name="template[{{curr_template}}][{{day}}][0][group][]">
                                <option value="zabicky">Žabičky</option>
                                <option value="pulci1">Pulci 1</option>
                                <option value="pulci2">Pulci 2</option>
                                <option value="zaci1">Žáci 1</option>
                                <option value="zaci2">Žáci 2</option>
                                <option value="dorost">Dorost+</option>
                            </select>
                        </td>
                    <td class="name"> <input type="text" size="25" name="template[{{curr_template}}][{{day}}][0][name]" placeholder="název"></td>     
                    <td class="time"> <input type="text" size="9" name="template[{{curr_template}}][{{day}}][0][time]" placeholder="čas"> </td>
                    <td class="place"> <input type="text" size="40" name="template[{{curr_template}}][{{day}}][0][place]" placeholder="místo"> </td>  
                    <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td>          
                </tr>
            {% endfor %}
            
        {% endfor %}
    </table>
</form>
<hr>

<div class="row justify-content-between">
<div class="col">
    <label for="create-template" style="display: inline-block;">Vytvořit novou šablonu:</label><br>
    <input type="text" id="create-template" class="create-new-template__input" placeholder="název nové šablony" style="display:inline"></input>
    <button type="button" class="create-new-template__button">Vytvořit</button> 
</div>

<div class="col-auto">
    <label for="create-template">Odstranit tuto šablonu:</label>
    <button class="delete-template" id="delete-template" type="button" {% if curr_template == page.header.defaultTemplate %} disabled>Nelze odstranit výchozí šablonu {% else %} >Odstranit{% endif %}</button> 
</div>
</div>



<script>
    window.addEventListener('DOMContentLoaded', function(){
        
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
            const formNamePrefix = `template[{{curr_template}}][${otherTr.dataset.day}][${new Date().getTime()}]`;
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
                <td class="time"> <input type="text" size="9" name="${formNamePrefix}[time]" placeholder="čas"> </td>
                <td class="place"> <input type="text" size="40" name="${formNamePrefix}[place]" placeholder="místo"> </td>
                <td class="delete"><i class="fa fa-trash-o" aria-hidden="true"></i></td> `;
            
            tr.querySelector(".delete").addEventListener("click", deleteRow);
  
            const dayTrList = document.querySelectorAll(`[data-day="${otherTr.dataset.day}"]`);
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
            const rows = document.querySelectorAll("tr");
            let currDay = 0;
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
                let dayName = document.createElement("td");
                dayName.className = "day";
                dayName.innerHTML = CZdays[currDay];
                dayName.rowSpan = document.querySelectorAll(`[data-day="${day}"]:not([style*="display: none"]`).length;
                dayName.addEventListener("click", appendRowToDay);
                tr.prepend(dayName);
                currDay += 1;
            });

        }

        // cleanup table
        function removeEmptyRows() {
            const rows = document.querySelectorAll("tr");
            rows.forEach(row => {
                let day = row.dataset.day;
                let rowsInDay = document.querySelectorAll(`[data-day="${day}"]`).length;
                if ( rowsInDay > 1 &&
                    !row.querySelector(".group").firstElementChild.value &&
                    !row.querySelector(".name")?.firstElementChild.value  &&
                    !row.querySelector(".time")?.firstElementChild.value  &&
                    !row.querySelector(".place")?.firstElementChild.value) 
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
            let day = row.dataset.day;
            
            if(document.querySelectorAll(`[data-day="${day}"]`).length === 1) {
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

        function showError() {
            document.getElementById("error").style.display = "block";
        }
        
        // submit form
        const submitButton = document.getElementById("edit-plan__submit");
        
        submitButton.addEventListener("click", (e) => {
            e.preventDefault();
            var planForm = new FormData(document.getElementById("program"));
            $.ajax({
             url: "/php/plan/savetemplate",
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
                  showError();
             }
            });
        })

        document.querySelector(".create-new-template__button").addEventListener("click", (e) => {
            if (!confirm("Neuložené změny v plánu budou ztraceny, chcete pokračovat?")) {
                return
            }
            e.preventDefault();
            let templateName = document.querySelector(".create-new-template__input").value;
            var newTemplateFormData = new FormData();
            newTemplateFormData.append("templateName", templateName);
            $.ajax({
                url: "/php/plan/createtemplate",
                type: "POST",
                data: newTemplateFormData,
                processData: false,
                contentType: false,
                success: function (response){ 
                    templateName = JSON.parse(response);
                    location.href = `{{page.url}}?template=${templateName}`;
                },
                error: function (xhr, desc, err){
                    showError();
                }
            });
        })

        document.querySelector(".delete-template").addEventListener("click", (e) => {
            if (!confirm("Neuložené změny v plánu budou ztraceny, chcete pokračovat?")) {
                return
            }
            e.preventDefault();
            var deleteTemplateFormData = new FormData();
            deleteTemplateFormData.append("deletedTemplate", "{{curr_template}}");
            $.ajax({
                url: "/php/plan/deletetemplate",
                type: "POST",
                data: deleteTemplateFormData,
                processData: false,
                contentType: false,
                success: function (response){ 
                    location.href = `{{page.url}}`;
                },
                error: function (xhr, desc, err){
                    showError();
                }
            });
        })

        document.querySelector(".set-default-template").addEventListener("click", (e) => {
            if (!confirm("Neuložené změny v plánu budou ztraceny, chcete pokračovat?")) {
                return
            } 
            e.preventDefault();
            var setDefaultTemplateFormData = new FormData();
            setDefaultTemplateFormData.append("defaultTemplate", "{{curr_template}}");
            $.ajax({
                url: "/php/plan/setdefaulttemplate",
                type: "POST",
                data: setDefaultTemplateFormData,
                processData: false,
                contentType: false,
                success: function (response){ 
                    location.href = `{{page.url}}`;
                },
                error: function (xhr, desc, err){
                    showError();
                }
            });
        })


        document.querySelector("#choose-template").addEventListener("change", (select) => {
            if (!confirm("Neuložené změny v plánu budou ztraceny, chcete pokračovat?")) {
                return
            }
            location.href = `{{page.url}}?template=${select.target.value}`;
        })
        
    })

    
    

</script>
            