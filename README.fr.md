# Tictactoe_AI

## Description
Ce projet était un bonus dans un cours de Techniques avancées JavaScript.  
Dans ce cours, nous avons surtout appris comment utiliser des API.  
Ainsi, bien que la majorité du cours portait sur le langage JavaScript, nous avions aussi un peu de PHP pour les API.  

Ce projet avait donc pour but, avec un site fourni (HTML, CSS, JavaScript déjà fait), de créer une IA, en PHP, qui rivaliserait au TicTacToe avec différents niveaux d'IA connectés au projet.  

De ce fait, la seule partie du projet sur laquelle j'ai travaillée est le contenu du dossier backend ; le fichier ai.php.  
À noter que les 4 premières lignes étaient déjà présentes !

## Organisation du code
Tout d'abord, il y a quelques lignes seules, qui servaient à récupérer les informations (grille actuelle du tictactoe et le pion (X ou O) que mon IA jouait)  

Ensuite, il y a la fonction qui détermine le coup à jouer. Celle-ci déterminait combien de tours avaient été jouées sur cette grille et pour appeller la fonction appropriée.

Suite à cela, il y a plusieurs fonctions qui servir à trouver la meilleure case où jouer. 
Évidemment, pour déterminer cela, certaines fonctions vont simplement vérifier certaines cases et/ou combinaisons de cases (par exemple, s'il y a un tictactoe possible).  
Il y a aussi quelques fonctions servant à effectuer des opérations qui revenaient souvent.  

Finalement, il y a aussi une fonction qui joue un coup au hasard sur la grille, qui sert uniquement à éviter toute erreur potentielle suite à une situation jusqu'à présent jamais rencontré pour laquelle aucune solution n'aurait été pensée !
