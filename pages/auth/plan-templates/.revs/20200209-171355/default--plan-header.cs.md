---
title: 'Týdenní program'
date: '2018-09-29'
process:
    twig: true
    markdown: false
access:
    site:
        plan: true
currentSeason: winter
summer:
    monday: null
    tuesday:
        1:
            name: 'dráha'
            place: 'G Matyáše Lercha '
            meetup: '16:15'
            group:
        2:
            name: 'dráha'
            place: 'ZŠ Horní'
            meetup: '17:00'
            group:
                    - dorost
    wednesday: null
    thursday:
        1:
            name: 'dráha'
            place: 'G Matyáše Lercha '
            meetup: '16:00'
            group:
                    - dorost
        2:
            name: 'tělocvična - volejbal'
            place: 'ZŠ Kotlářská'
            meetup: '19:30'
            group:
                    - dorost
    friday:
        1:
            name: 'tělocvična - florbal, basketbal'
            place: 'ZŠ Kotlářská'
            meetup: '18:00'
            group:
                    - dorost
    saturday: null
    sunday: null
winter:
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
            meetup: '16:00'
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
            name: 'tělocvična'
            place: 'ZŠ Merhautova'
            meetup: '16:30'
            group:
                    - pulci1
                    - pulci2
                    - zaci1
        2:
            name: 'běžecký trénink (žáci 1 druhým rokem)'
            place: 'Hala Rosnička, Horákova 7'
            meetup: '16:30'
            group:
                    - zaci1
        3:
            name: 'kompenzační cvičení'
            place: 'bude upřesněno'
            meetup: '17:30'
            group:
                    - dorost
        4:
            name: 'běžecký trénink'
            place: 'Hala Rosnička, Horákova 7'
            meetup: '16:30'
            group:
                    - zaci2
    friday: null
    saturday: null
    sunday: null
---

<form id="form-weekly-plan" method="post" action="" >
    <input name="POST_type" type="hidden" value="weeklyPlan">
    <input name="filePath" type="hidden" value="{{'./' ~ page.relativePagePath() ~ '/' ~ page.name}}"> 
    <input id="POST_season" name="season" type="hidden" value="{{page.header.currentSeason}}"> 
    <div id="control">
        <b>Vybrat šablonu:</b>
        <input type="radio" name="season" value="summer" id="choose-summer" {% if page.header.currentSeason == "summer" %} checked {% endif %}> 
            <label for="choose-summer">Letní</label>   
        <input type="radio" name="season" value="winter" id="choose-winter" {% if page.header.currentSeason == "winter" %} checked {% endif %}> 
            <label for="choose-winter">Zimní</label>  

        <button id="save-weekly-plan" type="submit">Uložit</button>
        <span id="form-response"></span>
    </div>
    <hr>
        {# tables for each season #}
            {% set listOfSeasons = ["summer", "winter"] %}
            {% for season in listOfSeasons %}
                <table id="table-{{season}}" {% if page.header.currentSeason != season %} style="display: none;" {% endif %}>
                {% set CZweek = ["Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota","Neděle"] %}
                {% set numberOfEvents = 0 %}
                {% for day in attribute(attribute(page, "header"), season) %}
                    {% set day_index = loop.index -1 %}
                    <tr> <td class="weeklyHeader" colspan="5">{{CZweek[day_index]}}</td> </tr>
                    {% for id,event in day %}
                        <tr>
                            <td class="group"> 
                                <input type="checkbox" id="zab-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="zabicky" {% if "zabicky" in event.group  %} checked {% endif %}>
                                    <label for="zab-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> žabičky </label>
                                <br>
                                <input type="checkbox" id="pu1-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="pulci1"   {% if "pulci1" in event.group %}   checked {% endif %}>
                                    <label for="pu1-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> pulci 1 </label>
                                <br>
                                <input type="checkbox" id="pu2-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="pulci2"   {% if "pulci2" in event.group %}   checked {% endif %}>
                                    <label for="pu2-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> pulci 2</label>
                                <br>
                                <input type="checkbox" id="za1-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="zaci1"    {% if "zaci1" in event.group %}    checked {% endif %}>
                                    <label for="za1-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> žáci 1 </label>
                                <br>
                                <input type="checkbox" id="za2-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="zaci2"    {% if "zaci2" in event.group %}    checked {% endif %}>
                                    <label for="za2-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> žáci 2 </label>
                                <br>
                                <input type="checkbox" id="dor-{{season}}-{{day_index}}-{{id}}" name="{{season}}[{{day_index}}][{{id}}][group][]" value="dorost"  {% if "dorost" in event.group %}  checked {% endif %}>
                                    <label for="dor-{{season}}-{{day_index}}-{{id}}" class="weekly--checkbox"> dorost+ </label>
                            </td>
                            <td class="name"> <input type="text" name="{{season}}[{{day_index}}][{{id}}][name]" value="{{event.name}}" placeholder="název"> </td>
                            <td class="meetup"> <input type="text" name="{{season}}[{{day_index}}][{{id}}][meetup]" value="{{event.meetup}}" placeholder="hh:mm"> </td>
                            <td class="place"> <input type="text" name="{{season}}[{{day_index}}][{{id}}][place]" value="{{event.place}}" placeholder="místo"> </td>
                            <td class="delete"> <span class="remove-row" style="cursor: pointer;"> <i class="fa fa-trash" aria-hidden="true"></i></span> </td>
                        </tr>
                        {% set numberOfEvents = loop.index %}
                    {% else %}  
                        {% set numberOfEvents = 0 %}
                    {% endfor %}
                    <tr> <td colspan="5" data-numberOfEvents="{{numberOfEvents}}" data-dayIndex="{{day_index}}" class="addAfter {{season}}" style="cursor: pointer;"><i class="fa fa-plus" aria-hidden="true"></i></td></tr>
                {% endfor %}
                </table>

                <script>
                    {# insert new blank row before clicked row "+" #}
                    $('.addAfter.{{season}}').click(function(){ // season is a twig variable
                        var clickedRow =  this.parentElement;
                        var numberOfEvent = this.getAttribute("data-numberOfEvents");  // numberOfEvent destinguish rows for POST request handling
                        numberOfEvent = parseInt(numberOfEvent)+1;
                        var dayIndex = this.getAttribute("data-dayIndex");
                        var newRow = document.createElement('tr');
                        newRow.innerHTML =  '<td class="group">' +  //season - day in week   - event id
                                                '<input id="zab-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="zabicky" type="checkbox">' +
                                                    '<label for="zab-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> žabičky </label><br>' +
                                                '<input id="pu1-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="pulci1" type="checkbox">' + 
                                                    '<label for="pu1-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> pulci  1</label><br>' +
                                                '<input id="pu2-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="pulci2" type="checkbox">' + 
                                                    '<label for="pu2-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> pulci  2</label><br>' +
                                                '<input id="za1-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="zaci1" type="checkbox">' +
                                                    '<label for="za1-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> žáci 1 </label><br>' +
                                                '<input id="za2-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="zaci2" type="checkbox">' +
                                                    '<label for="za2-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> žáci 2 </label><br>' +
                                                '<input id="dor-{{season}}-' + dayIndex + '-' + numberOfEvent + '" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][group][]" value="dorost" type="checkbox">' +
                                                    '<label for="dor-{{season}}-' + dayIndex + '-' + numberOfEvent + '" class="weekly--checkbox"> dorost+ </label>' +
                                            '</td>' +
                                            '<td class="name"> <input type="text" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][name]" placeholder="název" type="text"> </td>' +
                                            '<td class="meetup"> <input type="text" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][meetup]" placeholder="hh:mm" type="text"> </td>' +
                                            '<td class="place"> <input type="text" name="{{season}}[' + dayIndex + '][' + numberOfEvent + '][place]" placeholder="místo" type="text"> </td>' +
                                            '<td class="delete"> <span id="del-'+ dayIndex + '-' + numberOfEvent + '" class="remove-row" style="cursor: pointer;"> <i class="fa fa-trash" aria-hidden="true"></i></span> </td>'
                        clickedRow.parentElement.insertBefore(newRow,clickedRow);
                        this.setAttribute("data-numberOfEvents", numberOfEvent );
                        // delete row
                        document.getElementById('del-'+ dayIndex + '-' + numberOfEvent).onclick = function(){        
                            if(confirm("Smazat akci?")){
                                var deleteRow =  this.parentElement.parentElement;
                                deleteRow.parentNode.removeChild(deleteRow);
                            }
                        }
                    });
                </script>
            {% endfor %}
 </form>
{# sctript with the same code for all tables #}
<script>
window.addEventListener('DOMContentLoaded', function() {
    $(".remove-row").click(function(){
            if(confirm("Smazat akci?")){
            var clickedRow =  this.parentElement.parentElement;
            clickedRow.parentNode.removeChild(clickedRow);
            }
    })
    {# change displayed table and save it to forminput #}
    var tableSummer = document.getElementById("table-summer");
    var tableWinter = document.getElementById("table-winter");
    var POSTseason = document.getElementById("POST_season");
    document.getElementById("choose-summer").onclick = function () {
        tableSummer.style.display = '';
        tableWinter.style.display = 'none';
        POSTseason.value = "summer";
    }
    document.getElementById("choose-winter").onclick = function () {
        tableWinter.style.display = '';
        tableSummer.style.display = 'none';
        POSTseason.value = "winter";
    }
    {# POST changes to server #}
    document.getElementById("save-weekly-plan").onclick = function(e){
        e.preventDefault();
        var formData = new FormData(document.getElementById("form-weekly-plan"));
        var formResponse = document.getElementById("form-response");
        $.ajax({
            url: "/php/plan/savetemplates",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (){   
                formResponse.innerHTML = "<br>Úspěšně uloženo";
                formResponse.style.color = "green";
                setTimeout(function(){ 
                    formResponse.innerHTML = ""; 
                }, 3000);
            },
            error: function (xhr, desc, err){
                formResponse.innerHTML = "<br>Chyba, zkontrolujte console log";
                formResponse.style.color = "red";
                console.log(err);
                console.log(desc);
                console.log(xhr);
                }
            });
    }
});
</script>
















