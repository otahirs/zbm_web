---
title: Polaris
process:
    twig: true
    markdown: false
access:
    site:
        polaris: true
---

<form id="polarisForm" enctype="multipart/form-data" method="post">
<h2>Nahrát nový Polaris</h2>
<div class="row">
        <div class="col-md-6">
            <div>
                <input type="file" name="PDF" accept="application/pdf" required oninvalid="this.setCustomValidity('Nahrejte Polaris ve formátu PDF.')"
                oninput="setCustomValidity('')" >
            </div>
            <br>
            <div> 
                <input type="text" placeholder="Rok" list="yearList" name="year" style="width: 6em; display:inline;" required pattern="[0-9]{4}" oninvalid="this.setCustomValidity('Vyplňte rok ve formátu YYYY')"
                oninput="setCustomValidity('')" autocomplete="off">
                <datalist id="yearList">
                    <option value="{{ "now"|date("Y") }}">
                </datalist>  
         
                <input placeholder="Číslo" type="text" list="cisloList" name="cislo" style="width: 6em; display:inline;" required  pattern="[0-9]{2}" oninvalid="this.setCustomValidity('Vyplňte číslo časopisu ve formátu XX')" oninput="setCustomValidity('')" autocomplete="off">
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
        <div class="col-md-6">
            <button type="submit" id="sendPolaris">Odeslat</button>
            <div id="response"></div>
        </div>
</div>
</form>
        <hr>

<script>
    
</script>


{% set p = page.find('/data/polaris') %}
{% for rok, year in p.header.polaris %}
    <section>
    <h2>{{rok}}</h2>
    <div class="row">
        {% for cislo, pdf in year %}
            <div class="col-sm-6 col-md-3 col-lg-2">
                <div class="polaris">
                    <a href="/data/polaris/{{rok}}/{{pdf}}" target="_blank">
                        <img src="/data/polaris/{{rok}}/{{pdf}}.jpg">
                    </a>
                    <div class="polaris--delete" data-year="{{rok}}" data-cislo="{{pdf[13:2]}}" data-pdf="{{pdf}}" title="Smazat"> 
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
window.addEventListener('DOMContentLoaded', function () {

    const notyf = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        },
        duration: 3500,
    });

    $("#polarisForm").submit(function(e){
        e.preventDefault(); 
        e.stopPropagation();
        var submitButton = document.getElementById("sendPolaris");
        var responseDiv = document.getElementById("response");
        responseDiv.innerHTML = '<br><p><i class="fa fa-spinner fa-pulse fa-3x" aria-hidden="true"></i>  Soubor polarisu se zpracovává. </p>';
        $.ajax({
            url: "/php/polaris/save",
            type: "POST",
            data: new FormData(document.getElementById("polarisForm")),
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

    $(".polaris--delete").click( function(e){
        e.stopPropagation();
        if (confirm("Odstranit Polaris?") == true) {
            var deleteDiv = this.parentElement.parentElement;
            var deletePolarisForm = new FormData();
            deletePolarisForm.append("year", this.getAttribute("data-year") );
            deletePolarisForm.append("cislo", this.getAttribute("data-cislo") );
            deletePolarisForm.append("pdf", this.getAttribute("data-pdf") );
            $.ajax({
                url: "/php/polaris/delete",
                type: "POST",
                data: deletePolarisForm,
                processData: false,
                contentType: false,
                success: function (){
                    $(deleteDiv).fadeOut(1000);      
                    notyf.success("Polaris úspěšně smazán.");   
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





















