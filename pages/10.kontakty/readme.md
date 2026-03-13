# Fotky trenérů

Každý trenér má 2–3 fotky ve dvou verzích:
- **originál** (`jmeno-prijmeni-1.jpg`) — full-size, max 3500px na delší straně
- **náhled** (`jmeno-prijmeni-1-thumb.jpg`) — 400x266px, zobrazuje se na webu

## Přidání / úprava fotek

### 1. Převod z PNG na JPG (pokud je zdrojová fotka PNG)

```bash
mogrify -format jpg *.png
```

### 2. Resize na max 3500px (delší strana)

```bash
mogrify -resize '3500x3500>' *.jpg
```

Příznak `>` zajistí, že se zmenší pouze obrázky větší než 3500px — menší zůstanou beze změny.

### 3. Vygenerování náhledů (400px šířka)

```bash
for f in *-[0-9].jpg; do
    convert "$f" -resize 400x -quality 85 "${f%.jpg}-thumb.jpg"
done
```

### Pojmenování souborů

Formát: `jmeno-prijmeni-N.jpg` kde `N` je číslo fotky (1, 2, případně 3).

Příklad: `jan-novak-1.jpg`, `jan-novak-1-thumb.jpg`, `jan-novak-2.jpg`, `jan-novak-2-thumb.jpg`

## Přidání / úprava trenéra na stránce

Seznam trenérů se spravuje v souboru `pages/10.kontakty/defaut-no-title.cs.md`.

Každý trenér se vkládá pomocí Twig include:

```twig
{% include 'partials/trainer.html.twig' with {
    name: 'Jméno Příjmení',
    email: 'email@example.com',
    phone: '777 123 456',
    photo: 'jmeno-prijmeni-1'
} %}
```

Parametry:
- **name** (povinný) — jméno trenéra
- **email** (povinný) — e-mail
- **phone** (volitelný) — telefonní číslo
- **photo** (volitelný) — název fotky bez přípony (např. `jan-novak-1`). Pokud není uveden, trenér se zobrazí bez fotky.

Trenéři jsou seskupeni podle tréninkových skupin v `<div>` blocích s ID odpovídajícím skupině (`zabicky`, `pulci1`, `pulci2`, `zaci1`, `zaci2`, `dorost`, `hobby`).
