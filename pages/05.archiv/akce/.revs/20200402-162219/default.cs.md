---
title: Archiv událostí
content:
    items:
        @page.descendants:
            '/data/events'
    filter: 
        routable: true
    order:
        by: header.start
        dir: asc
---
{% set collection = page.collection() %}
<div id="program" >
<div class="row">
  <div class="col">
    <input type="text" style="display:inline" class="search" placeholder="Hledat.." />&nbsp;
    <a class="button" id="reset_btn"><i class="fa fa-refresh" aria-hidden="true"></i></a>&nbsp;
    <a class="button special" id="filter_btn">zobrazit filtr</a>
  </div>
</div>
<br>
<div id="filter_program" class="row" style="display: none">
  <div class="col-sm-6 col-md-3">
    <fieldset>
    <label>Typ události</label>
    <input class="filter-all" type="radio" value="all" name="type" id="type-all" checked />
    <label for="type-all" style="display:none;">Vše</label>
    <input class="filter" type="radio" value="trenink" name="type" id="type-T" />
    <label for="type-T">Trénink</label>
    <input class="filter" type="radio" value="zavod" name="type" id="type-Z" />
    <label for="type-Z">Závod</label>
    <input class="filter" type="radio" value="soustredeni" name="type" id="type-S" />
    <label for="type-S">Soustředění</label>
    </fieldset>
  </div>
  <div class="col-md-6">
    <fieldset>
    <label>Skupina</label>
	<div class="row">
		<div class="col-md-6">
			<input class="filter-all" type="radio" value="all" name="skupina" id="skupina-all" checked />
			<label for="skupina-all" style="display:none;">Vše</label>
			<input class="filter" type="radio" value="zabicky" name="skupina" id="skupina-zabicky" />
			<label for="skupina-zabicky">Žabičky</label>
			<br>
			<input class="filter" type="radio" value="pulci1" name="skupina" id="skupina-pulci1" />
			<label for="skupina-pulci1">Pulci 1</label>
			<br>
			<input class="filter" type="radio" value="pulci2" name="skupina" id="skupina-pulci2" />
			<label for="skupina-pulci2">Pulci 2</label>
			<br>
		</div>
		<div class="col-md-6">
			<input class="filter" type="radio" value="dorost" name="skupina" id="skupina-dorost" />
			<label for="skupina-dorost">Dorost+</label>
      <br>
			<input class="filter" type="radio" value="zaci1" name="skupina" id="skupina-zaci1" />
			<label for="skupina-zaci1">Žáci 1</label>
			<br>
			<input class="filter" type="radio" value="zaci2" name="skupina" id="skupina-zaci2" />
			<label for="skupina-zaci2">Žáci 2</label>
		</div>	
	<div>
	</fieldset>
  </div>
  <div class="col-sm-6 col-md-3" >
    <fieldset>
    <label>Filtr data</label>
    <button data-toggle="datepicker" type="button" style="height: 2.75em;font-size: 1em;line-height: 2.9em;color:inherit !important; box-shadow:none;"><i class="fa fa-calendar" aria-hidden="true"></i>&nbsp;&nbsp;vše</button>
    <br>
    <input id="include-older" type="checkbox" checked/>
    <label for="include-older">zobrazit již uplynulé</label>
    </fieldset>
  </div>
 </div>
{#tabulka#}
  <table>
  <tbody class="list">
    {# eventy starsi nez DNES#}
    
  {% for p in collection %}
  
      <tr>
          <td class="datum edit" title="Upravit událost">
            <a href="/data/events/{{ p.header.id[:4] }}/{{ p.header.id }}" target="_blank">
              {# HELP formaty casu http://userguide.icu-project.org/formatparse/datetime #}
              {# pokud neni stejny mesic - format 6. cerven - 2. cervenec #}
              {% if p.header.start|date('m') != p.header.end|date('m') %}
                {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
              {# pokud neni stejny den - format 6.-8. cerven #}
              {% elseif p.header.start != p.header.end %}
                {{p.header.start|date("j.") ~ ' — '~ p.header.end|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
              {% else %}
              {# pokud stejny den - format 6. cerven #}
                {{p.header.start|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM') }}
              {% endif %}
              {# pokud minule roky, zobrazi rok #}
              {% if p.header.end|date("Y") != "now"|date("Y") %}
                {{ p.header.end|date("Y") }}
              {% endif %}
            </a>
          </td>
          <td class="nazev edit" title="Upravit událost">
            <a href="/data/events/{{ p.header.id[:4] }}/{{ p.header.id }}" target="_blank">
              <b>{{ p.title }}</b>
            </a>
          </td>
          <td class="misto edit" title="Upravit událost">
            <a href="/data/events/{{ p.header.id[:4] }}/{{ p.header.id }}" target="_blank">
              {{p.header.place}}
            </a>
          </td>
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
          <td class="start" style="display: none !important;">{{ p.header.start | date('U') }}</td>
          <td class="end" style="display: none !important;"> 
              {{ p.header.end | date('U') }}
          </td>
      </tr>
  {% endfor %}
  {# oddelovaci cara #}
    <tr class="program--now" title="Dnes - {{"today"|localizeddate('medium', 'none', 'cs','Europe/Prague', 'd. MMMM Y')}}">
          <td class="datum"></td>
          <td class="nazev"></td>
          <td class="misto"></td>
          <td class="skupina" style="display: none !important;"></td>
          <td class="type" style="display: none !important;"></td>
          <td class="startMonth" style="display: none !important;">{{ "now"| date('m/Y') }}</td>     
          <td class="endMonth" style="display: none !important;">{{ "now"| date('m/Y') }}</td>
          <td class="start" style="display: none !important;">{{ "now"|date("U") }}</td>
          <td class="end" style="display: none !important;">{{ "now"|date("U") }}</td>
      </tr>

    </tbody>
   </table>
   <ul class="pagination"></ul>
</div>

<table class="no-result">
<tr><td><em>Vyhledávání neodpovídá žádná událost.</em></td></tr>
</table>


<script>
 window.addEventListener('DOMContentLoaded', function () {
   
  // links hover background
  $(".edit").hover( 
    function () { $(this).parent().find("td").addClass('backgroundAccent') },     
    function () { $(this).parent().find("td").removeClass('backgroundAccent') }
  );

  // show/hide filter
  var filter_div = document.getElementById('filter_program');
  $('#filter_btn').click( function(){
    if (filter_div.style.display === "none") {
      filter_div.style.display = "flex";
      this.innerHTML = "schovat filtr";
    } else {
      filter_div.style.display = "none";
      this.innerHTML = "zobrazit filtr";
    }
  });

  // datepicker
  var $datepicker = $('[data-toggle="datepicker"]'),
    bnt_text = $datepicker.html();
    now = Math.floor(Date.now() / 1000);
  $datepicker.datepicker({
      language: 'cs-CZ',
      format: 'mm/yyyy',
      trigger: $datepicker
    });

	var options = {
    valueNames: [ 'datum', 'nazev', 'misto', 'skupina', 'type', 'startMonth', 'endMonth', 'start', 'end' ],
    page: 9,
    pagination: true
	};

  // list.js
  var userList = new List('program', options);
  
  function showCurrent(item) {
    if (item.values().start >= now || item.values().end > (now - 5*3600*24)) {
      return true;
    }
    return false;
  } 

  function resetList(){
  	//userList.search();
    userList.sort('start', { order: "asc" });
  	//userList.filter(showCurrent); 
  	$(".filter-all").prop('checked', true);
  	$('.filter').prop('checked', false);
    $('.search').val('');
    $datepicker.html(bnt_text);
    $("#include-older").prop("checked", true); //false

  	updateList();
  	//console.log('Reset Successfully!');
  };

  function updateList(){
    var values_skupina = $("input[name=skupina]:checked").val();
  	var values_type = $("input[name=type]:checked").val();
    var value_datepicker = $datepicker.datepicker('getDate', true);
    var include_old = $("#include-older").prop("checked");
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

      if($datepicker.html() != bnt_text) {
        dateFilter = item.values().startMonth.indexOf(value_datepicker) >= 0 || item.values().endMonth.indexOf(value_datepicker) >= 0;
      } else if(include_old) {
        dateFilter = true;
      } else {
        dateFilter = showCurrent(item);
      }
      
      if (item.elm.className == "program--now" && dateFilter) {
        return true;
      }
  		return typeFilter && skupinaFilter && dateFilter;
  	});
  	userList.update();
  };
  
  
  //updateList();
    $("input[name=skupina]").change(updateList);
    $('input[name=type]').change(updateList);
    $("#include-older").change(updateList);
    $datepicker.on('pick.datepicker', updateList);

/* pokud neni zaznam zobrazi hlasku*/
  	userList.on('updated', function (list) {
        if (list.matchingItems.length > 0) {
          $('.no-result').hide()
        } else {
          $('.no-result').show()
        } 
    });

    
  
  resetList();
	$("#reset_btn").click(resetList);
	

}, false); // onload  
</script>
