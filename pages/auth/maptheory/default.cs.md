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
<h2>Nahrát mapovou teorii</h2>
<div class="row">
        <div class="col-md-6">
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
        <div class="col-md-6">
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
                        <a href="/data/maptheory/{{group_name}}/{{item}}" target="_blank">
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
window.addEventListener('DOMContentLoaded', function () {
    const notyf = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        },
        duration: 3500,
    });

    $("#mapTheoryForm").submit(function(e){
        e.preventDefault(); 
        e.stopPropagation();
        var submitButton = document.getElementById("sendForm");
        var responseDiv = document.getElementById("response");
        responseDiv.innerHTML = '<br><p><i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i>  Soubor mapové teorie se zpracovává. </p>';
          $.ajax({
                url: "/php/mapt/savemapt",
                type: "POST",
                data: new FormData(document.getElementById("mapTheoryForm")),
                processData: false,
                contentType: false,
                success: function (){
                    responseDiv.innerHTML = "";
                    notyf.success("Úpěšně uloženo!");
                    setTimeout(() => window.location.replace(location.href), 1000);
                },
                error: function (xhr, desc, err){
                    if(xhr.responseText) {
                        notyf.error({message:xhr.responseText, duration: 9000, ripple: false});
                    }
                    else {
                        notyf.error("Neočekávaná chyba");
                    }
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                    responseDiv.innerHTML = "";
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
                    notyf.success("Soubor mapové teorie úspěšně smazán.");     
                },
                error: function (xhr, desc, err){
                    notyf.error("Neočekávaná chyba");
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                }
            });
        }
        

    })
});
</script>

