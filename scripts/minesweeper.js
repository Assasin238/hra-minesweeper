// Sledování stavu hry
let gameGrid = []; // Stav mřížky (miny a čísla)
let mineCount = 0; // Počet min
let isFirstClick = true; // Indikátor, zda hráč provedl první kliknutí
let visited = []; // Sledování navštívených buněk (pro odhalování okolí)
let flagsPlaced = 0; // Počet položených vlajek
let gameOver = false; // Kontrola stavu hry
let timer; // Časovač
let startTime; // Počáteční čas

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
            addRightClickListener(cellElement, row, col);

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
    if (gameOver || cellElement.textContent === '🚩') return;

    if (isFirstClick) {
        startTimer();
        createMinesWithSafeArea(row, col, gameGrid.length, gameGrid[0].length, mineCount);
        calculateNumbers(gameGrid.length, gameGrid[0].length);
        isFirstClick = false;
    }

    const cellValue = gameGrid[row][col];
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
    stopTimer();
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
        endGame(true); // Konec hry - vítězství
    }
}

// Přidání pravého kliknutí (položení vlaječky)
function addRightClickListener(cellElement, row, col) {
    cellElement.addEventListener('contextmenu', (event) => {
        event.preventDefault();

        // Pokud je hra ukončena, zakážeme další interakci
        if (gameOver) return;

        // Zakážeme pokládání vlajek na odhalených polích (čísla nebo prázdná pole)
        if (cellElement.style.backgroundColor === '#ddd') return;

        // Přepínání vlajky
        if (cellElement.textContent === '🚩') {
            // Pokud je vlajka, odstraníme ji
            cellElement.textContent = '';
            flagsPlaced--; 
        } else {
            // Pokud není vlajka a počet vlajek je menší než počet min
            if (flagsPlaced < mineCount) {
                cellElement.textContent = '🚩';
                flagsPlaced++; 
            } else {
                alert('Nemůžete položit více vlajek, než je počet min!');
            }
        }

        updateFlagCounter();
        checkWin();
    });
}

// Funkce pro aktualizaci počitadla vlajek
function updateFlagCounter() {
    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Vlajky: ${flagsPlaced}/${mineCount}`;
}

// Funkce pro výběr obtížnosti
function setDifficulty(difficulty) {
    let rows, cols;
    switch (difficulty) {
        case 'easy':
            rows = 8;
            cols = 8;
            mineCount = 10;
            break;
        case 'medium':
            rows = 16;
            cols = 16;
            mineCount = 40;
            break;
        case 'hard':
            rows = 16;
            cols = 30;
            mineCount = 99;
            break;
        default:
            rows = 10;
            cols = 10;
            mineCount = 10;
    }

    flagsPlaced = 0;
    isFirstClick = true;
    gameOver = false;
    stopTimer();

    initializeVisited(rows, cols);
    createGameGrid(rows, cols);

    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Vlajky: 0/${mineCount}`;

    const timerElement = document.getElementById('timer');
    timerElement.textContent = 'Čas: 0s';
}

// Spuštění časovače
function startTimer() {
    startTime = Date.now();
    timer = setInterval(() => {
        const currentTime = Date.now();
        const elapsedSeconds = Math.floor((currentTime - startTime) / 1000);
        const timerElement = document.getElementById('timer');
        timerElement.textContent = `Čas: ${elapsedSeconds}s`;
    }, 1000);
}

// Zastavení časovače
function stopTimer() {
    clearInterval(timer);
}

// Spuštění hry
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('game-container');

    if (!container) {
        console.error('Element s ID "game-container" nebyl nalezen.');
        return;
    }

    // Přidání výběru obtížnosti
    const difficultySelector = document.createElement('select');
    difficultySelector.id = 'difficulty-selector';

    const difficulties = [
        { value: 'easy', label: 'Lehká' },
        { value: 'medium', label: 'Střední' },
        { value: 'hard', label: 'Těžká' }
    ];

    difficulties.forEach(diff => {
        const option = document.createElement('option');
        option.value = diff.value;
        option.textContent = diff.label;
        difficultySelector.appendChild(option);
    });

    difficultySelector.addEventListener('change', (event) => {
        setDifficulty(event.target.value);
    });

    container.insertBefore(difficultySelector, container.firstChild);

    // Přidání časovače
    const timerElement = document.createElement('div');
    timerElement.id = 'timer';
    timerElement.textContent = 'Čas: 0s';
    container.insertBefore(timerElement, container.firstChild);

    // Inicializace hry
    setDifficulty('easy'); // Defaultní obtížnost
});
