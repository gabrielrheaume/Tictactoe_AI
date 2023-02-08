<?php

// CORS
header("Access-Control-Allow-Origin: " . (isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '*'));
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization");
header("Access-Control-Allow-Credentials: true");

/* GET VALUES */
$grille = empty($_POST["grille"]) ? json_decode($_GET["grille"]) : json_decode($_POST["grille"]);
$pion = empty($_POST["pion"]) ? $_GET["pion"] : $_POST["pion"];

/* MAIN */
$grille[play($grille, $pion)] = $pion;
echo json_encode($grille);

/* FUNCTIONS */
/**
 * Find the best move to do
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - X or O, depending if AI begins or not
 * @return int - index where to play in the game grid
 */
function play($grille, $pion)
{
    $nb_turn = findTurn($grille);

    // For X player
    if($nb_turn == 1) return findCorner($grille, ""); // play in an empty corner

    if($nb_turn == 3)
    {
        $index = oppositeCorner($grille, $pion); // play at the opposite corner
        if($index != -1) return $index;
        
        $index = findCorner($grille, "");
        if($index != -1) return $index;

        // should not reach this step
        return random($grille);
    }

    if($nb_turn == 5 || $nb_turn == 7 || $nb_turn == 9)
    {
        $index = ticTacToe($grille, $pion); // find if there is a possible tictactoe
        if($index != -1) return $index;

        $other_pion = $pion == 'x' ? 'o' : 'x';
        $index = ticTacToe($grille, $other_pion); // if the oppnent blocks, I have to block his/her tictactoe
        if($index != -1) return $index;

        $index = findDoubleMenace($grille, $pion)[0];
        if($index != -1) return $index;

        // should not reach this step
        return random($grille);
    }

    // For O player
    if($nb_turn == 2)
    {
        if(!empty($grille[4])) return findCorner($grille, ""); // if he/she puts in the middle
        return 4; // put in the middle instead
    }
    
    if($nb_turn == 4 || $nb_turn == 6 || $nb_turn == 8)
    {
        $index = ticTacToe($grille, $pion); // find if there is a possible tictactoe for O
        if($index != -1) return $index;

        $index = ticTacToe($grille, otherPion($pion)); // O has to block a tictactoe
        if($index != -1) return $index;

        $index = findDoubleMenace($grille, $pion)[0]; // find if O can do two tictactoe menaces
        if($index != -1) return $index;

        $index = counterTTTMenace($grille, $pion);
        if($index != -1) return $index;

        //////////////////// improve here ///////////////////////////////
        $index = findDoubleMenace($grille, otherPion($pion))[0]; // find if the opponent can do two tictactoe menaces
        if($index != -1) return $index;

        // should not reach this step
        return random($grille);
    }

    // Cannot play more than 9 times in the same grid
    return false;
}

/**
 * Find the first empty index of the game grid
 * It is used to avoid errors
 *
 * @param array $grille - actual state of the game grid
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function random($grille)
{
    for($i = 0; $i < count($grille); $i++)
    {
        if(empty($grille[$i])) return $i;
    }
}

/**
 * Get the pion of the opponent
 *
 * @param char $pion
 * @return char
 */
function otherPion($pion)
{
    return $pion == 'x' ? 'o' : 'x';
}

/**
 * Find the number of the actual turn
 *
 * @param array $grille - actual state of the game grid
 * @return int turn number
 */
function findTurn($grille)
{
    $nb_pion = 0;
    for($i = 0; $i < count($grille); $i++)
    {
        if(!empty($grille[$i])) $nb_pion++;
    }
    return $nb_pion + 1;
}

/**
 * FInd the first empty corner containing the requested value
 *
 * @param array $grille - actual state of the game grid
 * @param char|empty $value - empty or char, depending on what is requested
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function findCorner($grille, $value)
{
    if($grille[0] == $value) return 0;
    if($grille[2] == $value) return 2;
    if($grille[6] == $value) return 6;
    if($grille[8] == $value) return 8;
    return -1;
}

/**
 * Find the opposite corner of a value
 *
 * @param array $grille - actual state of the game grid
 * @param char|empty $value - empty or char, depending on what is requested
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function oppositeCorner($grille, $value)
{
    if($grille[0] == $value && empty($grille[8])) return 8;
    if($grille[2] == $value && empty($grille[6])) return 6;
    if($grille[6] == $value && empty($grille[2])) return 2;
    if($grille[8] == $value && empty($grille[0])) return 0;

    return -1;
}

/**
 * Find the index that makes a tictactoe
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function ticTacToe($grille, $pion)
{
    $eachPossibilities = eachTTTPossibilities();

    $index = -1;
    foreach($eachPossibilities as $possibility)
    {
        if($index == -1) $index = thatCodeRepeats($grille, $pion, $possibility[0], $possibility[1], $possibility[2]);
    }

    return $index;
}

/**
 * Find the right index in a line for a tictactoe
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @param int $a - first index
 * @param int $b - second index
 * @param int $c - third index
 * @param int $index - -1 if a possible tictactoe is still not find
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function thatCodeRepeats($grille, $pion, $a, $b, $c)
{
    if($grille[$a] == $pion && $grille[$b] == $pion && empty($grille[$c])) return $c;
    if($grille[$a] == $pion && $grille[$c] == $pion && empty($grille[$b])) return $b;
    if($grille[$b] == $pion && $grille[$c] == $pion && empty($grille[$a])) return $a;

    return -1;
}

/**
 * Return every line that could make a tictactoe
 *
 * @return array
 */
function eachTTTPossibilities()
{
    return [
        [0, 1, 2],
        [3, 4, 5],
        [6, 7, 8],
        [0, 3, 6],
        [1, 4, 7],
        [2, 5, 8],
        [0, 4, 8],
        [2, 4, 6],
    ];
}

/**
 * Find the first index where to play to do two menaces
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @return array - [index, line1, line2]; index where to play in the game grid; -1 if no index is found. lines that share this index or empty arrays
 */
function findDoubleMenace($grille, $pion, $searched_index = null)
{
    $eachPossibilities = eachDoubleMenacePossibilities();

    foreach($eachPossibilities as $possibility)
    {
        $index = getIndexOfDoubleMenace($grille, $pion, $possibility[0], $possibility[1], $searched_index);
        if($index > -1) return [$index, $possibility[0], $possibility[1]];
    }

    return [-1, [], []];
}

/**
 * Return every combinations of lines for a double menace
 *
 * @return array
 */
function eachDoubleMenacePossibilities()
{
    return [
        [[0, 1, 2], [0, 3, 6]],
        [[0, 1, 2], [1, 4, 7]],
        [[0, 1, 2], [2, 5, 8]],
        [[0, 1, 2], [0, 4, 8]],
        [[0, 1, 2], [2, 4, 6]],

        [[3, 4, 5], [0, 3, 6]],
        [[3, 4, 5], [1, 4, 7]],
        [[3, 4, 5], [2, 5, 8]],
        [[3, 4, 5], [0, 4, 8]],
        [[3, 4, 5], [2, 4, 6]],

        [[6, 7, 8], [0, 3, 6]],
        [[6, 7, 8], [1, 4, 7]],
        [[6, 7, 8], [2, 5, 8]],
        [[6, 7, 8], [0, 4, 8]],
        [[6, 7, 8], [2, 4, 6]],

        [[0, 3, 6], [0, 4, 8]],
        [[0, 3, 6], [2, 4, 6]],

        [[1, 4, 7], [0, 4, 8]],
        [[1, 4, 7], [2, 4, 6]],

        [[2, 5, 8], [0, 4, 8]],
        [[2, 5, 8], [2, 4, 6]],

        [[0, 4, 8], [2, 4, 6]],
    ];
}
/**
 * Determine if two lines can make a double menace
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @param array $line1 - indexes of first line
 * @param array $line2 - indexes of second line
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function getIndexOfDoubleMenace($grille, $pion, $line1, $line2, $searched_index = null)
{
    $key = -1;
    for($i = 0; $i < count($line1); $i++)
    {
        for($j = 0; $j < count($line2); $j++)
        {
            if($line1[$i] == $line2[$j]) $key = $line1[$i];
        }
    }

    if($searched_index != null && $searched_index != $key) return -1;
    if(!empty($grille[$key])) return -1;

    $cases = [];
    foreach($line1 as $index)
    {
        if($index != $key) $cases[] = $index;
    }

    if(!($grille[$cases[0]] == $pion && empty($grille[$cases[1]]) || $grille[$cases[1]] == $pion && empty($grille[$cases[0]]))) return -1;

    $cases = [];
    foreach($line2 as $index)
    {
        if($index != $key) $cases[] = $index;
    }

    if(!($grille[$cases[0]] == $pion && empty($grille[$cases[1]]) || $grille[$cases[1]] == $pion && empty($grille[$cases[0]]))) return -1;
    
    return $key;
}

/**
 * Block an attack of the opponent and attack
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function counterTTTMenace($grille, $pion)
{
    // ?grille=["","","","","o","x","","x",""]&pion=o
    $infos = findDoubleMenace($grille, otherPion($pion));
    if($infos[0] != -1) $index = blockDoubleMenaceAndAttack($grille, $pion, $infos[0], $infos[1], $infos[2]); // prendre une des positions pour la bloquer

    var_dump($infos); exit();
    // vérifier si l'une de ses positions fait une doublemenace
    // sinon bloque simplement
}

/**
 * Find a position that block a double menace and do a double menace too
 *
 * @param array $grille - actual state of the game grid
 * @param char $pion - char representing a player
 * @param int $index
 * @param array $line1
 * @param array $line2
 * @return int - index where to play in the game grid; -1 if no index is found
 */
function blockDoubleMenaceAndAttack($grille, $pion, $index, $line1, $line2)
{
    $empty_indexes = [BDMAAloop($grille, $line1, $index), BDMAAloop($grille, $line2, $index)];
    foreach($empty_indexes as $searched_index)
    {
        var_dump(tttMenace($grille, $pion, $searched_index)); exit();
    }
}

/**
 * Loop on each index of a line that constitute a double menace to find the empty index who's not the one shared by both lines
 *
 * @param array $grille - actual state of the game grid
 * @param array $line - a line that consitute a double menace
 * @param int $index - the index of the shared index between two lines
 * @return void
 */
function BDMAAloop($grille, $line, $index)
{
    foreach($line as $line_index)
    {
        if($grille[$line_index] == "" && $line_index != $index) return $line_index;
    }

    return -1;
}

function tttMenace($grille, $pion, $index)
{
    $eachPossibilities = eachTTTPossibilities();

    /////////////////// here ////////////////////////
}