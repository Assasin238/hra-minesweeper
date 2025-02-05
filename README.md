# Minesweeper Game

## Popis projektu
Tato webová aplikace je implementací klasické hry Minesweeper (Hledání min). Hráč odkrývá pole na mřížce a snaží se vyhnout minám. Čísla na odkrytých polích indikují počet min v okolních políčkách. Cílem hry je odkrýt všechna bezpečná pole a označit miny pomocí vlajek.

## Funkce
- Výběr obtížnosti (lehká, střední)
- Dynamická velikost mřížky podle rozlišení obrazovky
- Časovač pro sledování doby hry
- Zvukové efekty při klíčových akcích
- Možnost označování min vlajkami
- Statistiky hráčů
- Přihlašování a registrace
- Administrátorský panel

## Použité technologie
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Databáze**: MySQL

## Instalace
1. Naklonujte repozitář:
   ```sh
   git clone https://github.com/uzivatel/minesweeper.git
   ```
2. Nahrajte soubory na váš server.
3. Vytvořte databázi a importujte soubor `database.sql`.
4. Konfigurujte připojení k databázi v souboru `config.php`.
5. Spusťte projekt v prohlížeči.

## Ukládání dat
Data o hráčích, statistikách a výsledcích jsou ukládána v MySQL databázi. Hra sleduje:
- Přihlašovací údaje hráčů
- Nejlepší časy hráčů na různých obtížnostech
- Celkový počet odehraných her

## Použití
- Kliknutím na pole jej odkryjete.
- Pravým kliknutím označíte pole vlajkou.
- Pokud odkryjete minu, hra končí.
- Pokud odkryjete číslo, ukazuje počet min v sousedních polích.
- Hra končí úspěchem, pokud označíte všechny miny správně a odkryjete všechna bezpečná pole.

## Autor
Vyvinuto uživatelem **Assasin238** a testovacími hráči.
