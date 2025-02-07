# Minesweeper Game

## Popis projektu
Tato webová aplikace byla vytvořena jako školní projekt a je implementací klasické hry **Minesweeper (Miny)**. Hráč odkrývá pole na mřížce a snaží se vyhnout minám. Čísla na odkrytých polích indikují počet min v okolních políčkách. 

**Cílem hry** je odkrýt všechna bezpečná pole a správně označit miny pomocí vlajek.

---

## 🎮 Funkce
- ✅ Výběr obtížnosti (**lehká, střední**)
- 📏 **Dynamická velikost mřížky** podle rozlišení obrazovky
- ⏱ **Časovač** pro sledování doby hry
- 🔊 **Zvukové efekty**
- 📊 **Statistiky hráčů**
- 🔐 **Přihlašování a registrace**
- 🛠 **Administrátorský panel**

---

## 🛠 Použité technologie
### **Frontend**
- HTML
- CSS
- JavaScript

### **Backend**
- PHP
- MySQL

---

## 📥 Instalace
1. Naklonujte repozitář:
   ```sh
   https://github.com/Assasin238238/hra-minesweeper.git
   ```
2. Nahrajte soubory na váš server.
3. Vytvořte databázi.
4. Spusťte projekt přes localhost.

---

## 🗄 Ukládání dat
Data o hráčích, statistikách a výsledcích jsou ukládána v **MySQL databázi**. Hra sleduje:
- **Přihlašovací údaje hráčů**
- **Nejlepší časy hráčů** na různých obtížnostech & a v db jsou uloženy i předchozí skóre

---

## 🎯 Použití
- **Levé kliknutí** na pole jej odkryje.
- **Pravé kliknutí** označí pole vlajkou.
- **Odkrytí miny** znamená konec hry.
- **Pole s číslem** ukazuje počet min v sousedních polích.
- **Hra končí vítězstvím**, pokud správně označíte všechny miny a odkryjete všechna bezpečná pole.

---

## 👤 Autor
Vytvořeno **Assasin238** & otestováno Testery <3
