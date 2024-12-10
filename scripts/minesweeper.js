// Sledování stavu hry
let gameGrid = []; // Stav mřížky (miny a čísla)
let mineCount = 0; // Počet min
let isFirstClick = true; // Indikátor, zda hráč provedl první kliknutí
let visited = []; // Sledování navštívených buněk (pro odhalování okolí)
// Sledování počtu vlajek
let flagsPlaced = 0; // Počet položených vlajek
// Funkce pro vytvoření mřížky hry
function createGameGrid(rows, cols) {
    const gameGridElement = document.getElementById('game-grid');
    gameGridElement.innerHTML = ''; // Vyčistíme obsah pro nový start hry

    for (let row = 0; row < rows; row++) {
        const rowElement = document.createElement('tr');

        for (let col = 0; col < cols; col++) {
            const cellElement = document.createElement('td');
            cellElement.id = `cell-${row}-${col}`; // Nastavíme ID pro buňku

            // Přidáme event listenery pro kliknutí
            cellElement.addEventListener('click', () => handleCellClick(row, col, cellElement));
            addRightClickListener(cellElement, row, col); // Přidáme pravé kliknutí

            rowElement.appendChild(cellElement);
        }

        gameGridElement.appendChild(rowElement);
    }

    initializeGameGrid(rows, cols); // Inicializace mřížky (prázdné hodnoty)
}

// Inicializace prázdné mřížky
function initializeGameGrid(rows, cols) {
    gameGrid = Array.from({ length: rows }, () => Array(cols).fill(0));
}

// Funkce pro inicializaci sledování navštívených buněk
function initializeVisited(rows, cols) {
    visited = Array.from({ length: rows }, () => Array(cols).fill(false));
}

// Funkce pro zpracování kliknutí na buňku
function handleCellClick(row, col, cellElement) {
    if (cellElement.textContent === '🚩') return; // Ignorujeme vlaječky

    if (isFirstClick) {
        // Generuj miny s bezpečnou oblastí
        createMinesWithSafeArea(row, col, gameGrid.length, gameGrid[0].length, mineCount);

        // Spočítej čísla
        calculateNumbers(gameGrid.length, gameGrid[0].length);

        // První kliknutí bylo provedeno
        isFirstClick = false;
    }

    const cellValue = gameGrid[row][col];
    if (cellValue === 'M') {
        cellElement.textContent = '💣';
        cellElement.style.backgroundColor = 'red';
        alert('Game Over!');
        return;
    }

    cellElement.textContent = cellValue === 0 ? '' : cellValue;
    cellElement.style.backgroundColor = '#ddd';

    if (cellValue === 0) {
        revealEmptyCells(row, col);
    }

    checkWin(); // Kontrola vítězství
}

// Funkce pro generování min s bezpečnou oblastí
function createMinesWithSafeArea(startRow, startCol, rows, cols, mineCount) {
    initializeGameGrid(rows, cols); // Reset mřížky na začátku
    let minesPlaced = 0;

    while (minesPlaced < mineCount) {
        const row = Math.floor(Math.random() * rows);
        const col = Math.floor(Math.random() * cols);

        // Vynecháme bezpečnou oblast kolem první buňky
        if (Math.abs(row - startRow) <= 1 && Math.abs(col - startCol) <= 1) {
            continue;
        }

        if (gameGrid[row][col] === 0) {
            gameGrid[row][col] = 'M';
            minesPlaced++;
        }
    }
}

// Výpočet čísel kolem min
function calculateNumbers(rows, cols) {
    const directions = [
        [-1, -1], [-1, 0], [-1, 1],
        [0, -1],         [0, 1],
        [1, -1], [1, 0], [1, 1]
    ];

    for (let row = 0; row < rows; row++) {
        for (let col = 0; col < cols; col++) {
            if (gameGrid[row][col] === 'M') continue;

            let mineCount = 0;
            directions.forEach(([dx, dy]) => {
                const newRow = row + dx;
                const newCol = col + dy;
                if (
                    newRow >= 0 && newRow < rows &&
                    newCol >= 0 && newCol < cols &&
                    gameGrid[newRow][newCol] === 'M'
                ) {
                    mineCount++;
                }
            });

            gameGrid[row][col] = mineCount;
        }
    }
}

// Funkce pro odhalení okolních prázdných buněk
function revealEmptyCells(row, col) {
    const directions = [
        [-1, -1], [-1, 0], [-1, 1],
        [0, -1],         [0, 1],
        [1, -1], [1, 0], [1, 1]
    ];

    if (visited[row][col]) return;
    visited[row][col] = true;

    directions.forEach(([dx, dy]) => {
        const newRow = row + dx;
        const newCol = col + dy;

        if (
            newRow >= 0 && newRow < gameGrid.length &&
            newCol >= 0 && newCol < gameGrid[0].length &&
            !visited[newRow][newCol] && gameGrid[newRow][newCol] !== 'M'
        ) {
            const adjacentCell = document.getElementById(`cell-${newRow}-${newCol}`);
            adjacentCell.textContent = gameGrid[newRow][newCol] === 0 ? '' : gameGrid[newRow][newCol];
            adjacentCell.style.backgroundColor = '#ddd';

            if (gameGrid[newRow][newCol] === 0) {
                revealEmptyCells(newRow, newCol);
            }
        }
    });
}

// Kontrola vítězství
function checkWin() {
    let allCellsCorrect = true;

    for (let row = 0; row < gameGrid.length; row++) {
        for (let col = 0; col < gameGrid[row].length; col++) {
            const cellElement = document.getElementById(`cell-${row}-${col}`);
            const cellValue = gameGrid[row][col];

            if (cellValue !== 'M' && cellElement.style.backgroundColor !== 'rgb(221, 221, 221)') {
                allCellsCorrect = false;
            }

            if (cellValue === 'M' && cellElement.textContent !== '🚩') {
                allCellsCorrect = false;
            }
        }
    }

    if (allCellsCorrect) {
        alert('Gratulujeme! Vyhrál(a) jste hru! 🎉');
    }
}

// Přidání pravého kliknutí (položení vlaječky)
function addRightClickListener(cellElement, row, col) {
    cellElement.addEventListener('contextmenu', (event) => {
        event.preventDefault();

        if (cellElement.style.backgroundColor === '#ddd') return; // Ignorujeme odhalené buňky

        if (cellElement.textContent === '🚩') {
            cellElement.textContent = '';
            flagsPlaced--; // Snížíme počet vlajek
        } else {
            if (flagsPlaced < mineCount) {
                cellElement.textContent = '🚩';
                flagsPlaced++; // Zvýšíme počet vlajek
            } else {
                alert('Nemůžete položit více vlajek, než je počet min!');
            }
        }

        updateFlagCounter(); // Aktualizujeme počitadlo vlajek na obrazovce
        checkWin(); // Kontrola vítězství po každém označení vlaječky
    });
}

// Funkce pro aktualizaci počitadla vlajek na obrazovce
function updateFlagCounter() {
    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Vlajky: ${flagsPlaced}/${mineCount}`;
}

// Spuštění hry při načtení stránky
document.addEventListener('DOMContentLoaded', () => {
    const rows = 10;
    const cols = 10;
    mineCount = 10;

    flagsPlaced = 0; // Resetujeme počet vlajek
    isFirstClick = true;

    initializeVisited(rows, cols);
    createGameGrid(rows, cols);

    // Nastavíme počitadlo vlajek
    const flagCounterElement = document.getElementById('flag-counter');
    if (!flagCounterElement) {
        const counterDiv = document.createElement('div');
        counterDiv.id = 'flag-counter';
        counterDiv.style.marginTop = '10px';
        counterDiv.textContent = `Vlajky: 0/${mineCount}`;
        document.body.appendChild(counterDiv);
    }
});

