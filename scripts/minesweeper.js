// Sledování stavu hry
let gameGrid = []; // Stav mřížky (miny a čísla)
let mineCount = 0; // Počet min
let isFirstClick = true; // Indikátor, zda hráč provedl první kliknutí
let visited = []; // Sledování navštívených buněk (pro odhalování okolí)
let flagsPlaced = 0; // Počet položených vlajek
let gameOver = false; // Kontrola stavu hry

// Funkce pro vytvoření mřížky hry
function createGameGrid(rows, cols) {
    const gameGridElement = document.getElementById('game-grid');
    gameGridElement.innerHTML = ''; // Vyčistíme obsah pro nový start hry

    for (let row = 0; row < rows; row++) {
        const rowElement = document.createElement('tr');

        for (let col = 0; col < cols; col++) {
            const cellElement = document.createElement('td');
            cellElement.id = `cell-${row}-${col}`;

            // Přidáme event listenery
            cellElement.addEventListener('click', () => handleCellClick(row, col, cellElement));
            cellElement.addEventListener('contextmenu', (event) => handleRightClick(event, row, col, cellElement));

            rowElement.appendChild(cellElement);
        }

        gameGridElement.appendChild(rowElement);
    }

    initializeGameGrid(rows, cols);
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
    if (gameOver || cellElement.classList.contains('flagged')) return;

    if (isFirstClick) {
        createMinesWithSafeArea(row, col, gameGrid.length, gameGrid[0].length, mineCount);
        calculateNumbers(gameGrid.length, gameGrid[0].length);
        isFirstClick = false;
    }

    if (visited[row][col]) return;

    const cellValue = gameGrid[row][col];
    visited[row][col] = true;

    if (cellValue === 'M') {
        cellElement.textContent = '💣';
        cellElement.style.backgroundColor = 'red';
        endGame(false); // Konec hry - prohra
        return;
    }

    cellElement.textContent = cellValue === 0 ? '' : cellValue;
    cellElement.style.backgroundColor = '#ddd';

    if (cellValue === 0) {
        revealEmptyCells(row, col);
    }

    checkWin();
}

// Funkce pro ukončení hry
function endGame(win) {
    gameOver = true;
    if (!win) {
        alert('Game Over! Klikněte na OK pro restart hry.');
    } else {
        alert('Gratulujeme! Vyhrál(a) jste hru! 🎉');
    }
    setTimeout(() => location.reload(), 100); // Restart hry
}

// Funkce pro generování min s bezpečnou oblastí
function createMinesWithSafeArea(startRow, startCol, rows, cols, mineCount) {
    initializeGameGrid(rows, cols);
    let minesPlaced = 0;

    while (minesPlaced < mineCount) {
        const row = Math.floor(Math.random() * rows);
        const col = Math.floor(Math.random() * cols);

        if (Math.abs(row - startRow) <= 1 && Math.abs(col - startCol) <= 1) continue;

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

    directions.forEach(([dx, dy]) => {
        const newRow = row + dx;
        const newCol = col + dy;

        if (
            newRow >= 0 && newRow < gameGrid.length &&
            newCol >= 0 && newCol < gameGrid[0].length &&
            !visited[newRow][newCol] && gameGrid[newRow][newCol] !== 'M'
        ) {
            const adjacentCell = document.getElementById(`cell-${newRow}-${newCol}`);
            visited[newRow][newCol] = true;
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
    let correctlyFlaggedMines = 0;
    let allCellsRevealed = true;

    for (let row = 0; row < gameGrid.length; row++) {
        for (let col = 0; col < gameGrid[row].length; col++) {
            const cellElement = document.getElementById(`cell-${row}-${col}`);
            const cellValue = gameGrid[row][col];

            if (cellValue === 'M' && cellElement.textContent === '🚩') {
                correctlyFlaggedMines++;
            } else if (cellValue === 'M' && cellElement.textContent !== '🚩') {
                allCellsRevealed = false;
            }

            if (cellValue !== 'M' && cellElement.style.backgroundColor !== 'rgb(255, 255, 255)') {
                allCellsRevealed = false;
            }
        }
    }

    if (correctlyFlaggedMines === mineCount && allCellsRevealed) {
        endGame(true); // Konec hry - vítězství
    }
}

// Zpracování pravého kliknutí (položení vlaječky)
function handleRightClick(event, row, col, cellElement) {
    event.preventDefault();

    if (gameOver || visited[row][col]) return;

    if (cellElement.classList.contains('flagged')) {
        cellElement.classList.remove('flagged');
        cellElement.textContent = '';
        flagsPlaced--;
    } else {
        if (flagsPlaced < mineCount) {
            cellElement.classList.add('flagged');
            cellElement.textContent = '🚩';
            flagsPlaced++;
        } else {
            alert('Nemůžete položit více vlajek, než je počet min!');
        }
    }

    updateFlagCounter();
}

// Funkce pro aktualizaci počitadla vlajek
function updateFlagCounter() {
    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Vlajky: ${flagsPlaced}/${mineCount}`;
}

// Spuštění hry
document.addEventListener('DOMContentLoaded', () => {
    const rows = 10;
    const cols = 10;
    mineCount = 10;

    flagsPlaced = 0;
    isFirstClick = true;
    gameOver = false;

    initializeVisited(rows, cols);
    createGameGrid(rows, cols);

    const flagCounterElement = document.getElementById('flag-counter');
    if (!flagCounterElement) {
        const counterDiv = document.createElement('div');
        counterDiv.id = 'flag-counter';
        counterDiv.style.marginTop = '10px';
        counterDiv.textContent = `Vlajky: 0/${mineCount}`;
        document.body.appendChild(counterDiv);
    }
});
