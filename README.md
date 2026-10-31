# Webovky SK Brno - Žabovřesky
Staré stránky již klepaly bačkorama, tak jsem se vrhnul na nové a po pár letech vzniklo toto. Začal jsem v první roce nástupu na MUNI FI bakaláře, kdy jsem neznal ani PHP ani Javascript (popravdě jsem ani nevěděl, že je to něco jiného než Java), takže to podle toho taky občas vypadá. Ale s plynoucímy roky, nepočítaně hodin a nikdy nekončícím refaktoringem už to nějak funguje. :D 


**Veškeré připomínky, návrhy a nalezené chyby pište zde do _Issues_.**  
pokud nemáte rádi internety a i tak byste rádi dali zpětnou vazbu nebo něco nefunguje podle vašich představ, tak otakar.hirs@gmail.com

## Development
budeš potřebovat Docker (Linux, nebo Docker Desktop / Podman na macOS a Windows) s `docker compose`

1. naklonovat repozitář  
`git clone https://github.com/otahirs/zbm_web.git`  
`cd zbm_web`  

2. sestavit a spustit testovací prostředí  
`USER_ID=$(id -u) GROUP_ID=$(id -g) docker compose up -d --build`  

3. na `localhost:8000` běží web, stačí upravovat soubory v lokálním naklonovaném repozitáři.  
pro přihlášení do /auth (editorská sekce) nebo /admin (Grav admin sekce) se jde přihlásit pomocí:  
jméno: `test`  
heslo: `Test1234`  
odeslané e-maily se nikam neposílají, zachytí je Mailpit na `localhost:8025`

4. commitnuté změny poslat jako merge request na `master` branch

užitečné příkazy:
* logy: `docker compose logs -f php` a `docker compose exec php tail -f logs/grav.log`
* Grav CLI: `docker compose exec -u www-data php php bin/grav clearcache`
* zastavit: `docker compose down` (s `-v` smaže i nainstalované jádro Gravu a cache)

ps: se zbm web deploymentem se v gitu synchronizuji pouze slozky `pages`, `themes` a `plugins`, ostatni muze byt upraveno pro lokalni deployment

#### testovací prostředí vs. produkce
Prostředí (`docker-compose.yml`, `docker/`) odpovídá produkci na zabiny.club:
nginx → php-fpm 8.3 → Grav 1.7.53.6, cron `bin/grav scheduler` (služba `scheduler`), odesílání pošty přes `sendmail`.
Při aktualizaci PHP nebo Gravu na serveru je potřeba změnit `PHP_VERSION` / `GRAV_VERSION` v `docker/php/Dockerfile`.

Rozdíly:
* jen HTTP, bez rate-limitu, fail2ban a CDN (BunnyCDN)
* použije se základní konfigurace z `config/`, ne `zabiny.club/config/` (ta platí jen pro doménu zabiny.club) – např. vypnutá cache
* `test` účet místo běžných uživatelských účtů (`accounts/` je v gitignore)
* bez synchronizace s gitem (GitSync), pošta jde do Mailpitu (lokální `config/plugins/email.yaml` se vytvoří při startu)

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
