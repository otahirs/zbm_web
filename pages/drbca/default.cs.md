---
title: Drbča
date: '2019-09-11'
process:
    markdown: false
---

{% try %}
    <div id="mylist">
    <input value="MČR" id="MČR" type="checkbox" name="level">
    <label for="MČR">MČR</label>
    <input value="ČPŠ" id="ČPŠ" type="checkbox" name="level">
    <label for="ČPŠ">Štafety</label>
    <input value="ŽA" id="ŽA" type="checkbox" name="level">
    <label for="ŽA">A</label>
    <input value="ŽB" id="ŽB" type="checkbox" name="level">
    <label for="ŽB">B</label>
    <input value="JMO" id="JMO" type="checkbox" name="type">
    <label for="JMO">JMO</label>
    <input class="search" placeholder="Hledat.." />
    <hr>
    <div class="list">
    {% set url = 'https://oris.orientacnisporty.cz/API/?format=json&method=getEventList&sport=1&datefrom=' ~ "now"|date("Y-m-d") ~ "&dateto=" ~ "now +1 year"|date("Y-12-31") %}
    {% set oris = url|getJson %}
    {% for race in oris.Data %}
        <div class="row">
        <span class="col-4 col-xs-3 col-md-2">{{ race.Date|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM Y') }}</span>
        <span class="col-auto name"><a href="/drbca/zavod?id={{race.ID}}">{{race.Name}}</a></span>
        <span class="col-auto level">{{ race.Level.ShortName }}</span>
        <span class="col-auto region">{{ race.Region }}</span>
        </div>
    {% endfor %}
    </div>
    </div> <!-- list -->
    <script>
    window.addEventListener('DOMContentLoaded', function () {

        var options = {
        valueNames: [ 'name', 'region', 'region', 'level']
        };

        var userList = new List('mylist', options);

        function updateList(){
            var filter_levels = [];
            $("input[name=level]:checked").each(function() {
                filter_levels.push($(this).val());
            });
            var JMO = document.getElementById("JMO").checked ? true : false;

            userList.filter( (item) => {
                if (filter_levels.length === 0 && !JMO) {
                    return true;
                }
                else if ( filter_levels.includes(item.values().level) ) {
                    return true;
                } 
                else if (JMO && item.values().region.indexOf("JM") >= 0) {
                    return true
                }
                return false;
            });
        }
        
        $("input:checkbox").change( () => {
            updateList();
        })
        updateList();
    });
    </script>
{% catch %}
    <div class="notices red">
        <p> Chyba přiojení na ORIS </p>
    </div>
{% endcatch %}

