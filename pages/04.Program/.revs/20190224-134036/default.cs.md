---
title: Program
content:
    items: '@root.descendants'
    order:
        by: header.start
        dir: asc
    pagination: false
---

{% set collection = page.collection().ofOneOfTheseTypes(['zavod', 'trenink', 'soustredeni', 'tabor']) %}
<div id="filtr_program">
{#filtr skupin#}
<div class="pure-g">
  <div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8" style="display: none">
    <input class="filter-all" type="radio" value="all" name="skupina" id="skupina-all" checked />
    <label for="skupina-all" class="pure-radio">Vše</label>
  </div>
  <div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="zabicky" name="skupina" id="skupina-zabicky" />
<label for="skupina-zabicky" class="pure-radio">Žabičky</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="pulci1" name="skupina" id="skupina-pulci1" />
<label for="skupina-pulci1" class="pure-radio">Pulci 1</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="pulci2" name="skupina" id="skupina-pulci2" />
<label for="skupina-pulci2" class="pure-radio">Pulci 2</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="zaci1" name="skupina" id="skupina-zaci1" />
<label for="skupina-zaci1" class="pure-radio">Žáci 1</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="zaci2" name="skupina" id="skupina-zaci2" />
<label for="skupina-zaci2" class="pure-radio">Žáci 2</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="dorost" name="skupina" id="skupina-dorost" />
<label for="skupina-dorost" class="pure-radio">Dorost+</label>
</div>
</div>
{#filtr typu#}
<div class="pure-g">
  <div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8" style="display: none">
    <input class="filter-all" type="radio" value="all" name="type" id="type-all" checked />
    <label for="type-all" class="pure-radio">Vše</label>
  </div>
  <div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="trenink" name="type" id="type-T" />
<label for="type-T" class="pure-radio">Trénink</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="zavod" name="type" id="type-Z" />
<label for="type-Z" class="pure-radio">Závod</label>
</div>
<div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
<input class="filter" type="radio" value="soustredeni" name="type" id="type-S" />
<label for="type-S" class="pure-radio">Soustředění</label>
</div>
</div>
<hr>
 {#hledani a reset#}
<div class="pure-g">
  <div class="pure-u-1-2">
    <input type="text" class="search" placeholder="Hledat.." />
    <button id="reset_btn" style="height:2.75em"><i class="fa fa-refresh" aria-hidden="true"></i></button>
  </div>
  <div class="pure-u-1-2">
    <input data-toggle="datepicker" type="button"  value='filtr data' style="height: 2.75em;font-size: 1em;line-height: 2.9em;">
  </div>
</div>
<br>
{#tabulka#}

  <table class="program">
  <tbody class="list">
    {# eventy starsi nez DNES#}
    <tr style="background-color: red; color: red">
          <td class="datum"></td>
          <td class="den"></td>
          <td class="nazev"></td>
          <td class="misto"></td>
          <td class="skupina"  style="display: none !important;"></td>
          <td class="type" style="display: none !important;"></td>
          <td class="startMonth" style="display: none !important;">{{ "now"| date('m/Y') }}</td>     
          <td class="endMonth" style="display: none !important;">{{ "now"| date('m/Y') }}</td>
          <td class="start" style="display: none !important;"> {{ "now"|date("U") }}</td>
          <td class="end" style="display: none !important;"> {{ "now"|date("U") }}</td>
      </tr>
  {% for p in collection %}
  
      <tr >
          <td class="datum">
            {# HELP formaty casu http://userguide.icu-project.org/formatparse/datetime #}
            {# HELP |localizeddate http://twig-extensions.readthedocs.io/en/latest/intl.html#}
            {# pokud neni stejny mesic - format 6. cerven - 2. cervenec #}
            {% if p.header.start|localizeddate('medium', 'none','cs','Europe/Prague', 'M') != p.header.end|localizeddate('medium', 'none','cs','Europe/Prague', 'M')%}
              {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
            {# pokud neni stejny den - format 6.-8. cerven #}
            {% elseif p.header.start|localizeddate('medium', 'none') != p.header.end|localizeddate('medium', 'none')%}
              {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd.') ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
            {% else %}
            {# pokud stejny den - format 6. cerven #}
              {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
            {% endif %}
          </td>
          <td class="den">
            {# pokud neni stejny den - format PO - PA #}
            {% if p.header.start|localizeddate('medium', 'none','cs','Europe/Prague', 'dM') != p.header.end|localizeddate('medium', 'none','cs','Europe/Prague', 'dM')%}
              {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
            {% else %}
            {# pokud stejny den - format PO #}
              {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'cccccc')|upper }}
            {% endif %}
          </td>
          <td class="nazev"><a href="{{ p.url }}"><b>{{ p.title }}</b></a></td>
          <td class="misto">{{p.header.place}}</td>
          <td class="skupina" style="display: none !important;"> 
            {% set all = true %}
            {% for s in p.header.taxonomy.skupina %} 
                {% set all = false %}
                {{ s ~ ' ' }} 
            {% endfor %}
            {% if all == true %}
                zabicky pulci1 pulci2 zaci1 zaci2 dorost
            {% endif %}
          </td>
          <td class="type" style="display: none !important;"> 
              {{ p.header.template }}
          </td>
          <td class="startMonth" style="display: none !important;"> 
              {{ p.header.start | date('m/Y') }}
          </td>     
          <td class="endMonth" style="display: none !important;"> 
              {{ p.header.end| date('m/Y') }}
          </td>
          <td class="start" style="display: none !important;"> 
              {{ p.header.start | date('U') }}
          </td>
          <td class="end" style="display: none !important;"> 
              {{ p.header.end | date('U') }}
          </td>
      </tr>
  {% endfor %}
  {# oddelovaci cara #}
  

    </tbody>
   </table>
   
   <ul class="pagination"></ul>
</div>


<script>
var $datepicker = $('[data-toggle="datepicker"]'),
    bnt_text = $datepicker.val();
    now = Math.floor(Date.now() / 1000);
$datepicker.datepicker({
    language: 'cs-CZ',
    format: 'mm/yyyy',
    trigger: $datepicker
  });

$(document).ready(function() {
	var options = {
    valueNames: [ 'datum', 'den', 'nazev', 'misto', 'skupina', 'type', 'startMonth', 'endMonth', 'start', 'end' ],
    page: 20,
    pagination: true
	};

  var userList = new List('filtr_program', options);
  
  function resetList(){
  	userList.search();
    userList.sort('start', { order: "asc" });
  	userList.filter(function (item) {
      if (trim(item.values().start) >= now || trim(item.values().end) > (now - 5*3600*24)) {
        return true;
      } else {
        return false;
      }
    }); 
  	//userList.update();
  	$(".filter-all").prop('checked', true);
  	$('.filter').prop('checked', false);
    $('.search').val('');
    $datepicker.val(bnt_text);
  	//console.log('Reset Successfully!');
  };

  function updateList(){
    var values_skupina = $("input[name=skupina]:checked").val();
  	var values_type = $("input[name=type]:checked").val();
  	//console.log(values_skupina, values_type);

  	userList.filter(function (item) {
  		var skupinaFilter = false;
      var typeFilter = false;
      var dateFilter = false;

  		if(values_skupina == "all")
  		{
  			skupinaFilter = true;
  		} else {
  			skupinaFilter = item.values().skupina.indexOf(values_skupina) >= 0;
      }
      
  		if(values_type == "all")
  		{
  			typeFilter = true;
  		} else {
  			typeFilter = item.values().type.indexOf(values_type) >= 0;
      }

      if($datepicker.val() == bnt_text)
      {
        dateFilter = true;
      } else {
        dateFilter = item.values().startMonth.indexOf($datepicker.val()) >= 0 || item.values().endMonth.indexOf($datepicker.val()) >= 0;
      }
      
  		return typeFilter && skupinaFilter && dateFilter;
  	});
  	userList.update();
  };
  
  $(function(){
    //updateList();
    $("input[name=skupina]").change(updateList);
    $('input[name=type]').change(updateList);
    $datepicker.on('change', function () {
        updateList();
    });

/* pokud neni zaznam zobrazi hlasku,dodelat
  	userList.on('updated', function (list) {
  		if (list.matchingItems.length > 0) {
  			$('.no-result').hide()
  		} else {
  			$('.no-result').show()
  		}
  	 });
     */
    });
    
    resetList();
    $("#reset_btn").click(resetList);

});
</script>
