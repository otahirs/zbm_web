---
title: 'O nás'
date: '2019-10-09'
primaryImage: {  }
---

<style>
/* The actual timeline (the vertical ruler) - BEGINNING */
.timeline {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
}

.timeline::after {
  content: '';
  position: absolute;
  width: 6px;
  background-color: black;
  top: 0;
  bottom: 5px;
  left: 50%;
  margin-left: -3px;
}
/* The actual timeline (the vertical ruler) - END */

/* Container around content - BEGINNING */
.container_timeline {
  padding: 10px 40px;
  position: relative;
  background-color: inherit;
  width: 50%;
}

/*The circles on the timeline*/
.container_timeline.main::after {
  content: '';
  position: absolute;
  width: 25px;
  height: 25px;
  right: -13px;
  background-color: white;
  border: 4px solid #e03a35;
  top: 29px;
  border-radius: 50%;
  z-index: 1;
}

/*Place the container to the left*/
.container_timeline.left {
  left: 0;
}

/*Place the container to the right*/
.container_timeline.right {
  left: 50%;
}

/*Add arrows to the left container (pointing right)*/
.container_timeline.main.left::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 26px;
  width: 0;
  z-index: 1;
  right: 25px;
  border: medium solid #e03a35;
  border-width: 15px 0 15px 15px;
  border-color: transparent transparent transparent #e03a35;
}

/*Add arrows to the right container (pointing left)*/
.container_timeline.main.right::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 26px;
  width: 0;
  z-index: 1;
  left: 25px;
  border: medium solid #e03a35;
  border-width: 15px 15px 15px 0;
  border-color: transparent #e03a35 transparent transparent;
}

.container_timeline.main.left.black::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 26px;
  width: 0;
  z-index: 1;
  right: 25px;
  border: medium solid #000000;
  border-width: 15px 0 15px 15px;
  border-color: transparent transparent transparent #000000;
}

.container_timeline.main.right.black::before {
  content: " ";
  height: 0;
  position: absolute;
  top: 26px;
  width: 0;
  z-index: 1;
  left: 25px;
  border: medium solid #000000;
  border-width: 15px 15px 15px 0;
  border-color: transparent #000000 transparent transparent;
}

/*Fix the circle for containers on the right side */
.container_timeline.main.right::after {
  left: -13px;
}
/* Container around content - END */

/* The actual content - BEGINNING */
.content_timeline {
  background-color: white;
  position: relative;
  border-radius: 6px;
  border: 4px solid #e03a35;
}

.content_timeline.black {
  border: 4px solid #000000;
}

.content_timeline_header {
  font-size: 1.75em;
  font-weight: bold;
  padding: 5px 30px;
  background-color: #e03a35;
  color: white;
}

.content_timeline_body {
  padding: 20px 30px;
  background-color: white;
  color: black;
}
/*The actual content - END*/

/* SCREENS less than 600px wide - BEGINNING */
@media screen and (max-width: 600px) {
/* Place the timelime to the left*/
  .timeline::after {
    left: 31px;
  }

/*Full-width containers*/
  .container_timeline {
    width: 100%;
    padding-left: 70px;
    padding-right: 25px;
  }

/*Make sure that all arrows are pointing leftwards*/
  .container_timeline.main.right::before, .container_timeline.main.left::before {
    left: 56px;
    border: medium solid #e03a35;
    border-width: 15px 15px 15px 0;
    border-color: transparent #e03a35 transparent transparent;
  }

  .container_timeline.main.right.black::before, .container_timeline.main.left.black::before {
    left: 56px;
    border: medium solid #000000;
    border-width: 15px 15px 15px 0;
    border-color: transparent #000000 transparent transparent;
  }

/*Make sure all circles are at the same spot*/
  .container_timeline.main.left::after, .container_timeline.main.right::after {
    left: 18px;
  }

/*Make all right containers behave like the left ones*/
  .container_timeline.right {
    left: 0%;
  }
/* SCREENS less than 600px wide - BEGINNING */
}
</style>

Dlouhodobě patříme mezi největší a nejúspěšnější oddíly orientačního běhu v České republice. Naše členy najdete i v českých reprezentačních týmech. Protože reprezentační týmy se pravdidelně mění, se seznam k dispozici na stránkách [reprezentace](https://reprezentace.orientacnibeh.cz).

Oddíl pořádá každoročně několik závodů. Pravidelně to jsou oblastní žebříčky či závody Brněnské zimní ligy. Od začátku století jsme uspořádali také všechny možné větší akce spojené s českým orientačním během. Tou největší byl Světový pohár v orientačním běhu v roce 2002 v režii všech brněnských oddílů. Dále jsme uspořádali všechny typy Mistrovství ČR: na krátké trati (2006, Růžená), na dlouhé trati (2008, Jedovnice), na klasické trati (2009, Blansko-Palava, 2020, Vír, 2023, Neslovice), noční (2011, Ruprechtov, 2015, Bukovinka), štafet a klubů (2012, Březina), a sprintu a sprintových štafet (2017, Brno).
V roce 2025 bude oddíl organizovat Mistrovství Evropy dorostu.
 Pravidelně organizujeme tréninky pro všechny věkové i výkonnostní kategorie od nejmenších dětí přes dorostence až po hobby skupinu s veterány.

## Jak to v Žabinách chodí?

Orienťácký rok je trochu posunutý od toho civilního. Obvykle začíná v listopadu, kdy startuje zimní tréninková příprava. Přes týden probíhají různé společné tréninky. Jednou za 14 dní se v pátek vydáváme na lesní noční mapový trénink. O víkendech se pak členové oddílu pravidelně účastní Brněnského běžeckého poháru, dlouhých výběhů s mapou a jednou za měsíc v neděli bývá Brněnská zimní liga. Před Vánocemi a také v novém roce nás čekají bežecká nebo lyžařská soustředění, záleží na počasí.

Na jaře se pak postupně rozjíždí závodní sezóna. Každé úterý máme mapové tréninky a o víkendu jezdíme zúročit naběhané kilometry na celostátní závody. Závodní sezóna má v létě krátkou přestávku, v červenci a srpnu následuje pro obyčejné běžce zvolnění spojené s účastí na odpočinkových vícedenních závodech. Pro členy oddílu je možné zúčastnit se pravidelného výjezdu do zajímavých destinací po celé Evropě, ale především do center orientačního běhu, takže nejčastěji Švédsko, Norsko, Finsko. Pro děti bývá v srpnu pořádán tábor s náplní nejen orientačního běhu, ale i celotáborové hry. V září opět propuknou celostátní závody. Sezónu zakončí vrchol v podobně MČR tříčlenných štafet a sedmičlenných (4 kluci a 3 holky) družstev. Tím de facto sezóna končí. I orientační běžec potřebuje někdy odpočívat, takže říjen se nese ve znamení regenerace, která je zakončena oslavou uplynulé sezóny na oddílovém přeboru. A pak se jede zase od začátku.

Tento přehled je trochu zjednodušený. Ve skutečnosti je společných tréninků a soustředění mnohem více. Záleží na věku, výkonnosti a chuti trénovat. 🙂

## A jak šel s Žabinami čas?

  <div class="timeline">
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1973
        </div>
        <div class="content_timeline_body">
          Vzniká oddíl orientačního běhu při TJ Sokol Žabovřesky, později registrovaný pod zkratkou ZBM. Jeho prvním předsedou se stává úspěšný sportovec - lyžař a běžec - Karel Hlaváč.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Před vznikem oddílu se závodů v orientačním běhu příležitostně účastnili někteří z členů turistického odboru TJ Sokol Žabovřesky pod vedením Dr. F. Vojty.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Oddíl orientačního běhu TJ Sokol Žabovřesky pořádá první závod 28. 4. 1973.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1977
        </div>
        <div class="content_timeline_body">
          Předsedou oddílu se stává Jan Dittrich. Oddíl má 15 dospělých členů.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Začíná pracovat s mládeží. Děti začínají většinou ve věku osmi až deseti let. Je jich dostatek.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Zakládá pionýrský sportovní oddíl KBM při Pionýrském domě v Lužánkách. KBM zlepšuje zázemí pro fungování oddílu. Členové oddílu orientačního běhu TJ Sokol Žabovřesky často závodí pod hlavičkou KBM - až do roku 2000.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Významnou spolupracovnicí oddílu KBM při Pionýrském domě v Lužánkách se stává Nina Babáková, později Dittrichová.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Za ZBM či KBM se závodů účastní žactvo a dospělí. Dorost běhá za oddíl orientačního běhu TJ Tesla Brno, TBM. Toto rozdělení trvá až do roku 1981.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Trénuje se třikrát týdně.<br />
          Léto: mapový trenink v lese kolem Brna, vytrvalostní běh, atletická příprava.<br />
          Zima: teorie v klubovně, vytrvalostní běh, obratnost a posilování v tělocvičně.
          Mimo to se oddíl účastní jednodenních i vícedenních soutěží a o volných víkendech pořádá vycházky s mapou nebo dlouhé výběhy.
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1979
        </div>
        <div class="content_timeline_body">
          První oddílový tábor - byl i prvním orientačním táborem v Československu.</div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Byl třítýdenní - cestovalo se vlakem a spalo se ve stanech. Dětí se zúčastnilo asi 20.
          Náplní prvního týdne byla kanoistika u Lipna, druhý týden účast na mezinárodních pětidenních závodech v OB a v třetím týdnu turistika v Prachovských skalách.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Letní OB tábor se od tohoto roku koná každoročně.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1982
        </div>
        <div class="content_timeline_body">
          Dorost se přestává přesouvat do TBM. Za ZBM či KBM tak běhají všechny věkové kategorie - žactvo, dorost, dospělí.
        </div>
      </div>
    </div>
    <div class="container_timeline main left black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          1988
        </div>
        <div class="content_timeline_body">
          Vzniká nová disciplína - závod na krátké trati. Jako Mistrovství republiky se běhá od roku 1990.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1990
        </div>
        <div class="content_timeline_body">
          TJ Sokol Žabovřesky  prochází velkými změnami. Vyčlenil se z něj sportovní klub SK
          Brno-Žabovřesky.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Oddíl orientačního běhu od tohoto roku patří pod SK Brno-Žabovřesky.       
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Oddíl má 109 členů (25 dospělých, 47 dorostu, 37 žactva). Mimo jiné pořádá různé akce pro veřejnost - V lese s mapou, Pohár KDDM a Kufrování s Dominem.        
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1991
        </div>
        <div class="content_timeline_body">
          Začíná znovu vycházet oddílová ročenka.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
          Ročenky vycházely již v letech 1978 až 1985. Ty se ale nedochovaly.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
           První "novodobou" ročenku na psacím stroji připravil a ve formátu A5 vydal Michal Gross.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
           Další oddílové ročenky jsou již připravovány na počítači a jejich grafická úprava roste. Vycházejí pravidelně až do vzniku oddílového časopisu Polaris.
        </div>
      </div>
    </div>
    <div class="container_timeline main right black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          1993
        </div>
        <div class="content_timeline_body">
           Dorostenecké a juniorské kategorie se do roku 1993 označovaly jako DH 15, 17, 19.
           Od roku 1993 se označení mění na DH 16, 18, 20.
        </div>
      </div>
    </div>
    <div class="container_timeline main left black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          1995
        </div>
        <div class="content_timeline_body">
           Světovou premiéru má parkový závod v orientačním běhu. Běžel se v Praze na Petříně.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1997
        </div>
        <div class="content_timeline_body">
           Oddílové dresy mění design. 
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
           Dosavadní klubové barvy, bílou a červenou, doplňuje černá. Nové dresy zdobí vertikální pruhy, střídavě v červené a černé barvě.
           <br />
           Stejná trojice barev je s oddílem spojena dodnes.
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          1999
        </div>
        <div class="content_timeline_body">
           Začíná vycházet oddílový časopis Polaris.
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline black">
        <div class="content_timeline_body">
           Na MČR se začíná běhat sprint. Na mistrovství světa se tato disciplína poprvé představila v roce 2001.
        </div>
      </div>
    </div>
    <div class="container_timeline main right black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          2000
        </div>
        <div class="content_timeline_body">
           Na závodech se běžně začíná používat SPORTident, který nahradil kleštičky.
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2002
        </div>
        <div class="content_timeline_body">
           Předsedou oddílu se stává Libor Zřídkaveselý. Oddíl čítá více než 100 členů různých věkových kategorií.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2008
        </div>
        <div class="content_timeline_body">
           Oddíl má kolem 165 členů všech věkových skupin. Žactva je ale málo.
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
           V rámci oddílu vzniká skupina MTBO - orientačních závodů na horských kolech. Skupina má v tomto roce 18 členů a fungovala do roku 2011. 
        </div>
      </div>
    </div>
    <div class="container_timeline main left black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          2010
        </div>
        <div class="content_timeline_body">
           Od tohoto roku nepatří mezi mistrovské závody dlouhá trať.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2013
        </div>
        <div class="content_timeline_body">
           Oddíl roste - přibývá dětí i trenérů. 
        </div>
      </div>
    </div>
    <div class="container_timeline right">
      <div class="content_timeline">
        <div class="content_timeline_body">
           Kromě žactva, dorostu a dospělých přibývají dvě nové skupiny: Žabičky - rodiče s dětmi a Pulci - školáci do 10 let.
        </div>
      </div>
    </div>
    <div class="container_timeline main left black">
      <div class="content_timeline black">
        <div class="content_timeline_header">
          2014
        </div>
        <div class="content_timeline_body">
           Sprintové štafety se začínají běhat jako mistrovský závod.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2020
        </div>
        <div class="content_timeline_body">
           Předsedou oddílu se stává Jan Fiala. Oddíl má 240 členů všech věkových kategorií. 
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2021
        </div>
        <div class="content_timeline_body">
           Mezi stávající skupiny oddílu přibývá Hobby skupina. 
        </div>
      </div>
    </div>
    <div class="container_timeline left">
      <div class="content_timeline">
        <div class="content_timeline_body">
           První trénink se koná 12.5. Jde většinou o rodiče dětí, které už se OB věnují, a kteří se chtějí naučit orientační běh, popř. se zdokonalit a sami trénovat.
        </div>
      </div>
    </div>
    <div class="container_timeline main right">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2023
        </div>
        <div class="content_timeline_body">
           Na mistrovstvích republiky jsme mezi lety 1990 a 2022 získali v dorosteneckých a dospělých kategoriích celkem 330 mistrovských medailí. 
        </div>
      </div>
    </div>
    <div class="container_timeline main left">
      <div class="content_timeline">
        <div class="content_timeline_header">
          2025
        </div>
        <div class="content_timeline_body">
           Úspěšně jsme uspořádali Mistrovství Evropy dorostu přímo u nás v Brně.
        </div>
      </div>
    </div>
  </div>
  
