---
title: 'Ukázka akce'
media_order: 'ukazka2.txt,ukazka.png'
date: '29-06-2018 00:00'
taxonomy:
    skupina:
        - zabicky
        - pulci
        - zaci
        - dorost
    typ:
        - trenink
        - zavod
        - tabor
        - soustredeni
    ubytovani:
        - postel
        - karimatka
misto: Říčany
misto_GPS: ''
sraz: 'u Bohémy'
sraz_GPS: ''
cas: '8:30'
tydeni_plan: ne
---

!! Není aktuální 

Soubor akce může být umístěn kdekoliv v souborovém stromu, ale **každá akce musí mít svou vlastní složku**. - V jedné složce může být umístěn pouze jeden event.

Podle akce se soubor ukládá jako:
* trenink.cs.md
* zavod.cs.md
* soustredeni.cs.md
* tabor.cs.md
* novinka.cs.md

**Nepsat Event repeat.. dělá to bordel**

Přibližně takto by měl vypadat soubor s akcí. 

Luf:
* Jako základ bych vzal stávající event (uz je tam start/end, klidne rozsirit o sraz, dale jiz je location, coordinates).
* Vše, co jde do kalendáře (na základě typu) bych dal do týdenního přehledu, stejně tak vše, co bude mít novinku dát do novinek.

Chce to vytvořit php stránku nebo nějaký Grav formulář, kde se jen vyplní políčka a uloží se soubor k ostatním akcím.

[Stáhnout ukázka.txt](ukazka3.txt)

![ukázka](ukazka3.png)
