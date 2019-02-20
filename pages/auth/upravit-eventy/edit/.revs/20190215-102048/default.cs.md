---
title: Upravit
date: '2018-10-24'
process:
    twig: true
    markdown: false
---

{{phpFormEditEvent()}}
<script>
    /**** prevent submit on enter ***/
        $(document).on("keypress", "input", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });
    /**** autoresize textareas ***/
        $.fn.extend({
            autoresize: function () {
            $(this).on("change keyup keydown paste cut", function () {
            $(this).height(0).height(this.scrollHeight);
            }).change();
            }
        });
        $("textarea").autoresize();
        // trigger resize on load
        $("textarea").each(function(){
            $(this).height( this.scrollHeight);
        });

    /*** delete <br> tags from textareas ***/ 
        String.prototype.replaceAll = function (find, replace) {
            var result = this;
            do {
                var split = result.split(find);
                result = split.join(replace);
            } while (split.length > 1);
            return result;
        };
        var newline = String.fromCharCode(13, 10);
        $("textarea").each(function() {
            this.value = this.value.replaceAll('<br>', '');
        });

    /* submit */
    var save_btn = document.getElementById("saveEvent"),
        form = document.getElementById("editEvent"),
        date1 = document.getElementById("date1"),
        date2 = document.getElementById("date2"),
        formResponse = document.getElementById("formResponse");

    save_btn.onclick = function(e){
        e.preventDefault();
        //check if form is valid
        if(form.checkValidity()){

            var formData = new FormData(form);
            $.ajax({
                url: "/php/editevent",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (){   
                    formResponse.innerHTML = "<br>Úspěšně uloženo, stránka se nyní obnoví.";
                    formResponse.style.color = "green";
                    setTimeout(function(){ 
                        formResponse.innerHTML = ""; 
                    }, 3000);
                    window.location.replace(location.href);
                },
                error: function (xhr, desc, err){

                    if(xhr.responseText){
                        formResponse.innerHTML = xhr.responseText;
                    }
                    else{
                        formResponse.innerHTML = "<br>Chyba, zkontrolujte console log";
                    }
                    formResponse.style.color = "red";
                    console.log(err);
                    console.log(desc);
                    console.log(xhr);
                    }
            });

        }
        else{
           if(date1.validity || date2.validity){
                  formResponse.innerHTML ='<br>Datum musí být ve formátu "yyyy-mm-dd"';
                  formResponse.style.color = "red";
           }
           
            if($("#name").val().trim() == ""){
                formResponse.innerHTML ='<br>Název události nesmí být prázdný';
                formResponse.style.color = "red";
            }

        }
    }
    </script>