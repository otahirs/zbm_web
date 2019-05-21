---
title: 'Nahrát program'
date: '2018-10-16'
access:
    site:
        nahrat-program: true
---

<ul>
    <li>nahrát lze pouze soubory formátu .csv</li>
    <li>řádek musí být zakončen posloupností symbolů <code>";"</code></li>
    <li>script počítá s defaultním kódováním <em>Windows-1250</em></li>
    <li>každý event musí mít min. <em>název</em> a <em>počáteční datum</em></li>
</ul>
<form id="uploadProgramForm" method="post" action="/php/uploadprogram" enctype="multipart/form-data">
    <input id="csv" name="file" type="file" accept=".csv,text/csv">
    <button id="uploadScvFile" type="submit">Odeslat</button>
</form>
<hr>
<div id="response"></div>

<script>

$("#uploadProgramForm").on('submit',(function(e) {
    e.preventDefault();
    var formResponse = document.getElementById("response");
        formResponse.innerHTML = '<i class="fa fa-spinner fa-pulse" aria-hidden="true"></i> probíhá vytváření souborů';
        formResponse.style.color = "black";

    $.ajax({
        url:  "/php/uploadprogram",
        type: "POST",
        data:  new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data){   
            formResponse.innerHTML = "<br>Úspěšně uloženo";
            formResponse.style.color = "green";
            setTimeout(function(){ 
                formResponse.innerHTML = ""; 
            }, 3000);
        },
        error: function (xhr, desc, err){
            if(err == "Unsupported Media Type"){
                formResponse.innerHTML = "<br>CHYBA!!<br>Lze nahrát pouze soubory CSV.";
                formResponse.style.color = "red";
            }
            else{
            formResponse.innerHTML = "<br>Chyba, zkontrolujte console log";
            formResponse.style.color = "red";
            }
            console.log(err);
            console.log(desc);
            console.log(xhr.response);
        }
    });          
}));

</script>