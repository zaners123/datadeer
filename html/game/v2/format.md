# V2 Game Format
To summarize, the format primarily has these parts:
- ./lib.php is for saving/loading games. This should be extended by each game
- ./request.php is the game's server interface

- ./(game)/gameselect.php is the game menu (board size, leader board, etc)
- ./(game)/lib.php is the game logic (allowed moves, win states, etc)
- ./(game)/play.php is the client game interface (sends AJAX to request.php)
- ./(game)/request.php is the game client API (used by play.php)