---
title: 'Mapová teorie'
date: '2018-10-08'
process:
    twig: true
    markdown: false
access:
    site:
        maptheory: true
---

<form id="mapTheoryForm" enctype="multipart/form-data" method="post">
<div class="pure-g">
        <div class="pure-u-1">
            <h2>Nahrát mapovou teorii</h2>
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div>
                <input type="file" name="PDF" accept="application/pdf" required oninvalid="this.setCustomValidity('Nahrejte soubor ve formátu PDF.')" oninput="setCustomValidity('')" >
            </div>
            <br>
            <div>
                <input type="text" name="date" placeholder="Datum" list="dateList" style="width: 7.45em;" pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" required title="formát yyyy-mm-dd" oninvalid=" this.setCustomValidity('Vyplňte datum ve formátu yyyy-mm-dd')" oninput="setCustomValidity('')" autocomplete="off">
                <datalist id="dateList">
                    <option value="{{ "now"|date("Y-m-d") }}">
                </datalist>  
            </div>  
            <br>
            <div>
                <select name="group" style="width: 7.45em;" required oninvalid=" this.setCustomValidity('Vyberte skupinu')" oninput="setCustomValidity('')">
                    <option value="" disabled selected>- skupina -</option>
                    <option value="pulci2">Pulci 2</option>
                    <option value="zaci1">Žáci 1</option>
                    <option value="zaci2">Žáci 2</option>
                    <option value="dorost">Dorost +</option>
                </select> 
            </div> 
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <button type="submit" id="sendForm">Odeslat</button>
            <div id="response"></div>
        </div>
</div>
</form>
        <hr>

{% set p = page.find('/data/maptheory') %}
{% for group_name, group in p.header.maptheory %}
    <section>
    <h4>{{group_name}}</h4>
        <ul>
        {% for item in group %}  
                    <li>            
                        <a href="{{base_url_absolute}}/data/maptheory/{{item}}" target="_blank">
                            {{item}}
                        </a> &nbsp;
                    <span class="maptheory--delete" data-group="{{group_name}}" data-name="{{item}}" style="cursor:pointer;"> 
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </span>
                    </li>
        {% endfor %}
        </ul>
    </section>
    <hr>
{% endfor %}


<script>
    $("#mapTheoryForm").submit(function(e){
        e.preventDefault(); 
        e.stopPropagation();
        var submitButton = document.getElementById("sendForm");
        var responseDiv = document.getElementById("response");
        submitButton.style.display = "none";
        responseDiv.innerHTML = '<br><i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i> Náhrávám se soubor mapové teorie.';
        responseDiv.style.color = "black";
          var mapTheoryForm = new FormData(document.getElementById("mapTheoryForm"));
          $.ajax({
              url: "/php/mapt/savemapt",
              type: "POST",
              data: mapTheoryForm,
              processData: false,
              contentType: false,
              success: function (){
                  responseDiv.innerHTML = "<br>Úspěšně uloženo";
                  responseDiv.style.color = "green";
                  setTimeout(function(){ 
                     responseDiv.innerHTML = ""; 
                     }, 3000);  window.location.replace(location.href);
              },
              error: function (xhr, desc, err){
                submitButton.style.display = "block";
                responseDiv.innerHTML = "<br>CHYBA!!<br>" + xhr.responseText;
                responseDiv.style.color = "red";
                console.log(err);
                console.log(desc);
                console.log(xhr);
              }
          });
      });


    $(".maptheory--delete").click( function(e){
        e.stopPropagation();
        if (confirm("Odstranit soubor mapové teorie?") == true) {
            var deleteLi = this.parentElement;
            var deleteMapThForm = new FormData();
            deleteMapThForm.append("group", this.getAttribute("data-group") );
            deleteMapThForm.append("name", this.getAttribute("data-name") );
            $.ajax({
                url: "/php/mapt/deletemapt",
                type: "POST",
                data: deleteMapThForm,
                processData: false,
                contentType: false,
                success: function (){
                    $(deleteLi).fadeOut(1000);      
                },
                error: function (xhr, desc, err){
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                }
            });
        }
        

    })
</script>

