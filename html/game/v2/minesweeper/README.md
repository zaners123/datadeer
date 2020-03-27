#Minesweeper
A server-side minesweeper (so leader boards are valid)
(Couldn't be client-side because they could cheat to see the entire board)

Game State:
- Server generates valid board, sends client stripped version (where mines are unknown)
- Javascript client (play.php) shows user board, where they respond with a click through request.php
- Client chooses coordinates (through AJAX CALL), server responds with new game state (new board info)
##Notes
- Flags are client-side
- If coordinates of mine sent as click, bored is set to DEAD