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

    // Nastavení velikosti pole podle obtížnosti
    const difficulty = getDifficulty();
    const sizes = {
        easy: { width: 450, height: 360 },
        medium: { width: 540, height: 420 }
    };

    const { width, height } = sizes[difficulty];
    gameGridElement.style.width = `${width}px`;
    gameGridElement.style.height = `${height}px`;

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
    
    const elapsedTime = Math.floor((Date.now() - startTime) / 1000); // Čas hry

    if (!win) {
        alert('Game Over! Klikněte na OK pro restart hry.');
    } else {
        alert('Gratulujeme! Vyhrál(a) jste hru! 🎉');
        // Odeslání skóre před restartem
        onGameWin(getDifficulty(), elapsedTime);
    }

    // Restart hry po chvíli, aby se stihlo odeslat skóre
    setTimeout(() => location.reload(), 2000); // Restart hry
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

// Přidání posluchače pro pravé kliknutí na každou buňku
function handleRightClick(event, row, col, cellElement) {
    event.preventDefault();

    // Kontrola, zda je hra ukončená nebo buňka již odhalená
    if (gameOver || cellElement.classList.contains('revealed')) return;

    if (cellElement.classList.contains('flagged')) {
        cellElement.classList.remove('flagged');
        cellElement.textContent = '';
        flagsPlaced--;
    } else {
        if (flagsPlaced < mineCount) {
            cellElement.classList.add('flagged');
            cellElement.textContent = '🚩';
            flagsPlaced++;
            playSound('place-flag');
        } else {
            alert('Nemůžete položit více vlajek, než je počet min!');
        }
    }

    updateFlagCounter();
}


// sound u vlajky
function playSound(action) {
    const sounds = {
        'place-flag': '../assets/place-flag.mp3' // Opravená cesta
    };

    const audio = new Audio(sounds[action]);
    audio.play().catch((error) => {
        console.error("Chyba při přehrávání zvuku:", error);
    });
}

// Funkce pro aktualizaci počitadla vlajek
function updateFlagCounter() {
    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Flags: ${flagsPlaced}/${mineCount}`;
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
        default:
            rows = 8;
            cols = 8;
            mineCount = 10;
    }

    flagsPlaced = 0;
    isFirstClick = true;
    gameOver = false;
    stopTimer();

    initializeVisited(rows, cols);
    createGameGrid(rows, cols);

    const flagCounterElement = document.getElementById('flag-counter');
    flagCounterElement.textContent = `Flags: 0/${mineCount}`;

    const timerElement = document.getElementById('timer');
    timerElement.textContent = 'Time: 0s';
}

// Spuštění časovače
function startTimer() {
    startTime = Date.now();
    timer = setInterval(() => {
        const currentTime = Date.now();
        const elapsedSeconds = Math.floor((currentTime - startTime) / 1000);
        const timerElement = document.getElementById('timer');
        timerElement.textContent = `Time: ${elapsedSeconds}s`;
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
        { value: 'easy', label: 'Easy' },
        { value: 'medium', label: 'Medium' }
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
    timerElement.textContent = 'Time: 0s';
    container.insertBefore(timerElement, container.firstChild);

    // Inicializace hry
    setDifficulty('easy'); // Defaultní obtížnost
});

function sendScoreToServer(difficulty, time) {
    console.log("Preparing to send score to server...");
    fetch("../subpages/minesweeper.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            action: "save_score",
            difficulty: difficulty,
            time: time
        }),
    })
    .then(response => response.text())  // Změníme na text, abychom viděli, co server vrací
    .then(data => {
        console.log("Server response:", data);  // Vypíše odpověď serveru
    
        try {
            const jsonData = JSON.parse(data);  // Pokusí se to převést na JSON
            if (jsonData.success) {
                alert("Score successfully saved!");
            } else {
                alert("Failed to save score: " + (jsonData.error || "Unknown error"));
            }
        } catch (e) {
            console.error("Error parsing JSON:", e);
            alert("Server returned invalid response.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
    });
}

function onGameWin(difficulty, elapsedTime) {
    console.log("onGameWin called with difficulty:", difficulty, "and time:", elapsedTime);
    sendScoreToServer(difficulty, elapsedTime);
    alert("You won! Your time: " + elapsedTime + " seconds.");
}

function getDifficulty() {
    const difficultySelector = document.getElementById('difficulty-selector');
    return difficultySelector ? difficultySelector.value : 'easy'; // Pokud není vybraná obtížnost, vrátí 'easy'
}
