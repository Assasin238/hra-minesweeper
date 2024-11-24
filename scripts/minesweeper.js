// Funkce pro vytvoření mřížky hry
function createGameGrid(rows, cols) {
    const gameGridElement = document.getElementById('game-grid');
    gameGridElement.innerHTML = ''; // Vyčistíme obsah pro nový start hry

    for (let row = 0; row < rows; row++) {
        const rowElement = document.createElement('tr');

        for (let col = 0; col < cols; col++) {
            const cellElement = document.createElement('td');
            cellElement.id = `cell-${row}-${col}`; // Nastavíme ID pro buňku

            // Přidáme event listener pro kliknutí na buňku
            cellElement.addEventListener('click', () => handleCellClick(row, col, cellElement));

            rowElement.appendChild(cellElement);
        }

        gameGridElement.appendChild(rowElement);
    }
}


// Funkce pro zpracování kliknutí na buňku
function handleCellClick(row, col, cellElement) {
    const cellValue = gameGrid[row][col];

    // Logování pro kontrolu kliknutí
    console.log(`Klik na buňku: Řádek ${row}, Sloupec ${col}, Hodnota: ${cellValue}`);

    // Pokud je mina, hra končí
    if (cellValue === 'M') {
        cellElement.textContent = '💣'; // Zobrazí minu
        cellElement.style.backgroundColor = 'red'; // Zvýrazní výbuch
        alert('Game Over!'); // Hra končí
        return;
    }

    // Zobraz hodnotu buňky
    cellElement.textContent = cellValue === 0 ? '' : cellValue;
    cellElement.style.backgroundColor = '#ddd'; // Odhalení buňky

    // Pokud je hodnota buňky 0, odhalíme okolní buňky
    if (cellValue === 0) {
        revealEmptyCells(row, col);
    }

    // Zamezíme opakovanému kliknutí
    cellElement.removeEventListener('click', () => handleCellClick(row, col, cellElement));
}

// Pole pro sledování navštívených buněk (abychom se vyhnuli zacyklení)
let visited = [];

// Funkce pro inicializaci sledování navštívených buněk
function initializeVisited(rows, cols) {
    visited = Array.from({ length: rows }, () => Array(cols).fill(false));
}

// Funkce pro odhalení okolních prázdných buněk
function revealEmptyCells(row, col) {
    const directions = [
        [-1, -1], [-1, 0], [-1, 1], // Horní řádek
        [0, -1],         [0, 1],    // Levá a pravá strana
        [1, -1], [1, 0], [1, 1]     // Spodní řádek
    ];

    // Pokud jsme už tuto buňku navštívili, neprovádíme nic
    if (visited[row][col]) return;

    // Označíme buňku jako navštívenou
    visited[row][col] = true;

    directions.forEach(([dx, dy]) => {
        const newRow = row + dx;
        const newCol = col + dy;

        // Ověření, zda buňka existuje, není již navštívená a není mina
        if (
            newRow >= 0 && newRow < gameGrid.length &&
            newCol >= 0 && newCol < gameGrid[0].length &&
            !visited[newRow][newCol] && gameGrid[newRow][newCol] !== 'M'
        ) {
            const adjacentCell = document.getElementById(`cell-${newRow}-${newCol}`);
            
            // Pokud je hodnota buňky 0, odhalíme ji a pokračujeme v odhalování okolí
            adjacentCell.textContent = gameGrid[newRow][newCol] === 0 ? '' : gameGrid[newRow][newCol];
            adjacentCell.style.backgroundColor = '#ddd'; // Odhalení buňky

            // Pokud je hodnota 0, odhalíme okolní buňky rekurzivně
            if (gameGrid[newRow][newCol] === 0) {
                revealEmptyCells(newRow, newCol);
            }
        }
    });
}

// Spuštění hry při načtení stránky
document.addEventListener('DOMContentLoaded', () => {
    const rows = 10;
    const cols = 10;
    const mineCount = 10;

    initializeVisited(rows, cols); // Inicializace sledování navštívených buněk
    createMines(rows, cols, mineCount); // Generování min
    calculateNumbers(rows, cols); // Výpočet čísel
    createGameGrid(rows, cols); // Vykreslení mřížky
    console.log(gameGrid); // Pro kontrolu stavu mřížky
});

// Generování min do mřížky
let gameGrid = []; // Pro uložení stavu hry (miny a čísla)

function createMines(rows, cols, mineCount) {
    // Naplníme mřížku nulami (0 = žádná mina)
    gameGrid = Array.from({ length: rows }, () => Array(cols).fill(0));

    let minesPlaced = 0;

    while (minesPlaced < mineCount) {
        const row = Math.floor(Math.random() * rows);
        const col = Math.floor(Math.random() * cols);

        // Pokud na této pozici ještě není mina, umístíme ji
        if (gameGrid[row][col] === 0) {
            gameGrid[row][col] = 'M'; // 'M' označuje minu
            minesPlaced++;
        }
    }
}

// Výpočet čísel kolem min
function calculateNumbers(rows, cols) {
    const directions = [
        [-1, -1], [-1, 0], [-1, 1], // Horní řádek
        [0, -1],         [0, 1],    // Levá a pravá strana
        [1, -1], [1, 0], [1, 1]     // Spodní řádek
    ];

    for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
            if (gameGrid[row][col] === 'M') continue; // Přeskočíme miny

            let mineCount = 0;

            // Projdeme všechny sousední buňky
            directions.forEach(([dx, dy]) => {
                const newRow = row + dx;
                const newCol = col + dy;

                // Ověříme, zda sousední buňka existuje a je mina
                if (
                    newRow >= 0 && newRow < rows &&
                    newCol >= 0 && newCol < cols &&
                    gameGrid[newRow][newCol] === 'M'
                ) {
                    mineCount++;
                }
            });

            gameGrid[row][col] = mineCount; // Uložíme počet min
        }
    }
}
