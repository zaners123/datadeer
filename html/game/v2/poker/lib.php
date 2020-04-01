<?php
/**
 * Money Logic:
 *      Upon someone pressing "Call","Raise", or making a blind bet, their money immediately goes to the dealer, and the respective pot goes up.
 *      All player's can see everyone else's available table money (same as in poker except total money = table money)
 * Turn Logic:
 *      A new player would be treated the same as someone in no pots (someone who folded)
 *      If, there's only one person in a pot, they win.
 *      If someone's in no pots, their turn is skipped.
 *      The only time someone can do something when it's not their turn is click "Leave Game (Folds any active bets)"
 * Player's options:
 *      When it comes to their turn, they can fold, call, or raise.
 *          Folding drops them from all pots
 *          Calling (or raising) adds money to the pot
 * board:
 *      [phase] = betting always, unnecessary
 *      [cards] main set by dealCards()
 *          A card is stored as (suit,value); something like D4 for a four of diamonds, or HA for ace of hearts
 *          [shown] = {0|3|4|5}
 *          [hands] = [bob:[d4,d5],joe:[d6,d7]]
 *          [deck] = [d8,d9, ... ] All cards not in shown or hands. Shown pulls from here.
 *      [pot] = [{players:[bob,joe],amount:50}, ... ]
 *      [player_states] = [username:{
 *              //the round state of the player, !still_in means they folded or timed out
 *              still_in = true | false,
 *          }
 *      [call] = number = equals last betting player's bet, or zero
 *
 * Client filtered table:
 *      [hand] = (board.cards.hands[user]) = ["card1","card2"]
 *      [shown] = (board.cards.shown)
 *      [players] = [{"name":"bob","money":1500}]
 *      [pot] = (board.pot)
 *      [turn] = "playerName"
 *
 * Client Logic:
 *      Client view would refresh secondly to show who's turn it is, money, etc.
 *      Top and center it would show the player list, looking something like "Playername has $123"
 *          An arrow with bold would pass around signifying who's turn it is.
 *      Below would be active pots, usually saying "Pot: $1234", unless it was split, then it would say "Pot[all]: $500 ; Pot[bob,joe]: $100"
 */
class PokerBoard extends GameBoard {

	public function takeInput() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		if ($this->getPlayerCount($conn, $this->id)<2) return;
		//stop input if its not your turn
		if (self::getActivePlayer($conn, $this->id) !== $_SESSION["username"]) return;

		//if zero, they fold. If
		$bet = filter_input(INPUT_POST,"bet",FILTER_VALIDATE_INT);
		if (!$bet || $bet<0) return;
		$isFolding = $bet==0;

		//todo skip player's turns until someone who hasn't folded can bet

	}

	public function getSanitizedBoard()	{
		//would return player's hand, the pot, etc.
		$filtered = [];
		$filtered["pot"] = $this->board["pot"];
		$filtered["shown"] = $this->board["cards"]["shown"];
		$filtered["hand"]  = $this->board["cards"]["hands"][$_SESSION["username"]];

	}

	public function resetBoardCards($players) {
		$deck = [];
		$suits = ['h','c','d','s'];
		$values = ['2','3','4','5','6','7','8','9','t','j','q','k','a'];
		foreach ($suits as $s) {
			foreach ($values as $v) {
				$deck[] = $s.$v;
			}
		}
		//the perfect function for this...
		shuffle($deck);
		$playerCards = [];
		//give every player two cards
		foreach ($players as $player) {
			$playerCards[$player] = array(array_pop($deck),array_pop($deck));
		}
		$this->board["cards"] = array(
			"shown" => [],
			"hands" => $playerCards,
			"deck" => $deck
		);
	}

	public static function canPlayerJoinGame($players) {
		//add them to the game
		$role = "Player num ".$players;
		return true;
	}

	public function makeNewBoard($conn) {
		$this->board = [];
		$players = self::getPlayers($conn, $this->id);
		$this->resetBoardCards($players);
		$this->board["pot"] = [];
		$this->board["call"] = 0;
	}

	public function populateByGenerate($size) {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$this->size = $size;
		$this->makeNewBoard($conn);
		$this->sqlInsertBoard();
	}
}