---
title: 'Upravit eventy'
date: '2018-10-24'
access:
    site:
        edit-event: true
content:
    items: '@root.descendants'
    order:
        by: header.start
        dir: asc
    pagination: false
---

{% set collection = page.collection().ofOneOfTheseTypes(['zavod', 'trenink', 'soustredeni', 'tabor']) %}
<div id="filtr_program">

  {#hledani a reset#}
<div class="pure-g">
  <div class="pure-u-1-2 pure-u-sm-1-4 pure-u-md-1-8">
    <input type="text" class="search" placeholder="Hledat.." />
  </div>
  <div class="pure-u-1-4 pure-u-md-5-24 pure-u-lg-4-24">
  </div>
  <div class="pure-u-1-24">
    <button id="reset_btn"><i class="fa fa-refresh" aria-hidden="true"></i></button>
  </div>
</div>

<hr>
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
<label for="skupina-pulci2" class="pure-radio">Pulci 1</label>
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


{#tabulka#}

  <table class="program">
  <tbody class="list">
    {# eventy starsi nez DNES#}
  {% for p in collection %}
      <tr class="editEvents--event" data-path="{{ base_url  ~ "/auth/upravit-eventy/edit?event=/data/" ~ p.header.id[1:4] ~ "/" ~ p.header.template ~ "/" ~ p.header.id ~ "/" ~ p.name }}" style="cursor: pointer;">
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
          <td class="nazev">{{ p.title }}</td>
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
      </tr>
  {% endfor %}
    </tbody>
   </table>
   
   <ul class="pagination"></ul>
</div>



<script>

$(document).ready(function() {
	var options = {
    valueNames: [ 'datum', 'den', 'nazev', 'misto', 'skupina', 'type' ],
    page: 20,
    pagination: true
	};

  var userList = new List('filtr_program', options);

  function resetList(){
  	userList.search();
  	userList.filter();
  	userList.update();
  	$(".filter-all").prop('checked', true);
  	$('.filter').prop('checked', false);
  	$('.search').val('');
  	//console.log('Reset Successfully!');
  };

  function updateList(){
    var values_skupina = $("input[name=skupina]:checked").val();
  	var values_type = $("input[name=type]:checked").val();
  	//console.log(values_skupina, values_type);

  	userList.filter(function (item) {
  		var skupinaFilter = false;
  		var typeFilter = false;

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
  		return typeFilter && skupinaFilter;
  	});
  	userList.update();
  	//console.log('Filtered: ' + values_skupina);
  };
  
  $(function(){
    //updateList();
    $("input[name=skupina]").change(updateList);
  	$('input[name=type]').change(updateList);

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
    
    $("#reset_btn").click(resetList);
});
$(".editEvents--event").click(function() {
    var redirectWindow = window.open(this.getAttribute("data-path"), '_blank');
    redirectWindow.location;     
});
</script>
