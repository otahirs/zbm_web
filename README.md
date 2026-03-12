# Webovky SK Brno - Žabovřesky
Staré stránky již klepaly bačkorama, tak jsem se vrhnul na nové a po pár letech vzniklo toto. Začal jsem v první roce nástupu na MUNI FI bakaláře, kdy jsem neznal ani PHP ani Javascript (popravdě jsem ani nevěděl, že je to něco jiného než Java), takže to podle toho taky občas vypadá. Ale s plynoucímy roky, nepočítaně hodin a nikdy nekončícím refaktoringem už to nějak funguje. :D 


**Veškeré připomínky, návrhy a nalezené chyby pište zde do _Issues_.**  
pokud nemáte rádi internety a i tak byste rádi dali zpětnou vazbu nebo něco nefunguje podle vašich představ, tak otakar.hirs@gmail.com

## Development
budeš potřebovat Linux a nainstalován Docker  

1. naklonovat repozitář  
`git clone https://github.com/otahirs/zbm_web.git`  
`cd zbm_web`  

2. sestavit a spustit docker image  
`docker build -t zbm_web_img --build-arg USER_ID=$(id -u) .`  
`docker run --name zbm_web -d -p 8000:80 -v $(pwd):/var/www/html/user:Z zbm_web_img`  

3. na `localhost:8000` beží web, stačí upravovat soubory v lokálním naklonovaném repozitáři.  
pro přihlášení do /auth (editorská sekce) nebo /admin (Grav admin sekce) se jde přihlásit pomocí:  
jméno: `test`  
heslo: `Test1234`

4. commitnuté změny poslat jako merge request na `dev` branch

#### dalsi nastavení pro lokální vývoj
* CDN plugin je vypnutý (`config/plugins/cdn.yaml`) - v produkci přepisuje URL assetů na CDN doménu, lokálně tak nefunguji styly, apod
* PHP memory limit je potřeba zvýšit na 512M+ (stránka kontaktů s ~30 PNG obrázky vyčerpá výchozích 128M při zpracování přes GD knihovnu):
  po prvním spuštění kontejneru:
  `docker exec zbm_web bash -c 'echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/php-recommended.ini'`
  `docker restart zbm_web`
  I tak je potreba stranku reloadovat nekolikrat (s tim ze vyprsi timeout 30s) nez se to nejspis nacacheuje a stranka se konecne zobrazi

#### poznámky
Postavené nad flat-file CMS Grav https://getgrav.org/.  
Stránky jsou ve složce `pages`.  
Data k událostem, novinkám,.. se nachází v `pages/data`.  
Web se automaticky synchronizuje s master větví.  
dokumentace ke gravu -> https://learn.getgrav.org/

Každá stránka se zkládá z hlavičky (`header/frontmatter`) obsahující "databázová" data zapsané v YAML a z vlastního obsahu (`content`). Stránka události může vypadat nějak takto:
```
---
title: 'mapový trénink'
place: Žebětín
---
Ahoj, já se vykreslím na stránce.
jsem událost nazvaná {{page.header.title}} a konám se zde {{page.header.place}}.

```
Stránky jako novinky a události však obsahují většinou pouze YAML data a vykreslují se až následně pomocí [šablon](themes/editorial/templates) (šablona pomocí které se vykresluje se vybírá dle názvu souboru) nebo se sesbírají data z hlaviček do kolekcí a ty se pak vykreslí např na [domovské stránce](https://github.com/otahirs/zbm_web/blame/master/pages/01.home/home.cs.md#L35).

Na vykreslování stránek a šalon se používá trochu víc fancy PHP aka [Twig](https://twig.symfony.com/doc/3.x/templates.html).  
Občas bylo třeba zašpinit si ruce vlastním PHP kódem, ten se nachází [zde](plugins/PHP/twig/).

#### rozdíly oproti nasazené verzi
* kešování je vypnuto
* `test` účet místo běžných uživatelských účtů
* nenastavena synchronizace s gitem a spojení na mailový server
