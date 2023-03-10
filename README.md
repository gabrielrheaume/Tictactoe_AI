# Tictactoe_AI

## Description  
This project was a bonus in an Advanced JavaScript Techniques course.  
In this course, we mainly learned how to use APIs.  
So, although the majority of the course was on the JavaScript language, we also had some PHP for the APIs.  

This project therefore aimed, with a site provided (HTML, CSS, JavaScript already done), to create an AI, in PHP, which would compete at TicTacToe against different levels of AIs connected to the project.  

Therefore, the only part of the project I worked on was the contents of the backend folder; the ai.php file.  
Note that the first 4 lines were already present !  

## Code organization  
First of all, there are a few lines alone, which were used to retrieve the information (current grid of the tictactoe and the pawn (X or O) that my AI was playing)  

Then there is the function that determines the move to play. This determined how many rounds had been played on that grid and to call the appropriate function.  

Following this, there are several functions that serve to find the best slot to play.  
Obviously, to determine this, some functions will simply check certain slots and/or combinations of boxes (for example, if there is a possible tictactoe).  

There are also a few functions used to perform operations that came up often.  

Finally, there is also a function that plays a random move on the grid, which only serves to avoid any potential error following a situation never encountered until now for which no solution would have been thought !  
