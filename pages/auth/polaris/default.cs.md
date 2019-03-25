---
title: Polaris
process:
    twig: true
    markdown: false
access:
    site:
        polaris: true
---

<form id="polarisForm" class="pure-form pure-form-aligned" enctype="multipart/form-data" method="post">
<div class="pure-g">
        <div class="pure-u-1">
            <h2>Nahrát nový Polaris</h2>

        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <div class="pure-control-group">
                <label for="PDF">PDF</label> 
                <input id="PDF" type="file" name="PDF" accept="application/pdf" required oninvalid="this.setCustomValidity('Nahrejte Polaris ve formátu PDF.')"
                oninput="setCustomValidity('')" >
            </div>
            <div class="pure-control-group">
                <label for="year">Rok</label>  
                <input id="year" list="yearList" name="year" style="width: 6em;" required pattern="[0-9]{4}" oninvalid="this.setCustomValidity('Vyplňte rok ve formátu YYYY')"
                oninput="setCustomValidity('')" autocomplete="off">
                <datalist id="yearList">
                    <option value="{{ "now"|date("Y") }}">
                </datalist>  
            </div>  
            <div class="pure-control-group">
                <label for="cislo">Číslo</label>
                <input id="cislo" list="cisloList" name="cislo" style="width: 4.5em" required  pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Vyplňte číslo časopisu ve formátu XX')"
                oninput="setCustomValidity('')" autocomplete="off">
                <datalist id="cisloList">
                    <option value="01">
                    <option value="02">
                    <option value="03">
                    <option value="04">
                    <option value="05">
                    <option value="06">
                    <option value="07">
                    <option value="08">
                    <option value="09">
                </datalist>
            </div> 
        </div>
        <div class="pure-u-1 pure-u-md-1-2">
            <button type="submit" id="sendPolaris">Odeslat</button>
            <div id="response"></div>
        </div>
        <div class="pure-u-1">
            <br>
            <p><em>Po odeslání trvá zpracování Polarisu několik desítek sekund, neopouštějte stránku.</em></p>
        </div>
</div>
</form>
        <hr>

<script>
    $("#polarisForm").submit(function(e){
        e.preventDefault(); 
        e.stopPropagation();
        var submitButton = document.getElementById("sendPolaris");
        var responseDiv = document.getElementById("response");
        submitButton.style.display = "none";
        responseDiv.innerHTML = '<br><i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i> Náhrávám se polaris a vytváří se náhled.';
        responseDiv.style.color = "black";
          var polarisForm = new FormData(document.getElementById("polarisForm"));
          $.ajax({
              url: "/php/polaris",
              type: "POST",
              data: polarisForm,
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
</script>


{% set p = page.find('/databaze/polaris') %}
{% for rok, year in p.header.polaris %}
    <section>
    <h2>{{rok}}</h2>
    <div class="pure-g">
        {% for cislo, pdf in year %}
            <div class="pure-u-1 pure-u-sm-1-2 pure-u-md-1-4 pure-u-lg-1-5 pure-u-xl-1-6"> 
                <div class="polaris--outerDiv">
                    <div class="polaris--innerDiv">
                        <a href="{{base_url_absolute}}/databaze/polaris/{{rok}}/{{pdf}}" target="_blank">
                            <img class="pure-img" src="{{base_url_absolute}}/databaze/polaris/{{rok}}/{{pdf}}.jpg">
                            <div class="polaris--title"> 
                                {{pdf[13:2]}}
                            </div>
                        </a> 
                    </div>
                    <div class="polaris--delete" data-year="{{rok}}" data-cislo="{{pdf[13:2]}}" data-pdf="{{pdf}}"> 
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
        {% endfor %}
    </div>
    </section>
    <hr>
{% endfor %}


<script>
    $(".polaris--delete").click( function(e){
        e.stopPropagation();
        if (confirm("Odstranit Polaris?") == true) {
            var deleteDiv = this.parentElement.parentElement;
            var deletePolarisForm = new FormData();
            deletePolarisForm.append("year", this.getAttribute("data-year") );
            deletePolarisForm.append("cislo", this.getAttribute("data-cislo") );
            deletePolarisForm.append("pdf", this.getAttribute("data-pdf") );
            $.ajax({
                url: "/php/deletepolaris",
                type: "POST",
                data: deletePolarisForm,
                processData: false,
                contentType: false,
                success: function (){
                    $(deleteDiv).fadeOut(1000);      
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





















