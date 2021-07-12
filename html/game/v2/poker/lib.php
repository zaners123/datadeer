<?php

require_once "/var/www/php/deercoinLib.php";
require_once "/var/www/html/game/v2/sprinklerLib.php";

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
 * server board:
 *      [cards] main set by dealCards()
 *          A card is stored as (suit,value); something like D4 for a four of diamonds, or HA for ace of hearts
 *          [shown] = {0|3|4|5}
 *          [hands] = [bob:[d4,d5],joe:[d6,d7]]
 *          [deck] = [d8,d9, ... ] All cards not in shown or hands. Shown pulls from here.
 *      [pot] = [{players:[bob,joe],amount:50, toFill:0}, ... ]
 *          toFill is if someone goes all in, the next players should give toFill pots first, then once full, give to the primary pot (where toFill is always -1)
 *      deprecated: cause use [pot] instead:  [players_betting] = [username:{still_betting}]
 *      [callFrom] = username = equals last betting player's name
 *      [call] = number = equals last betting player's bet, or zero
 *      [dealer] = playerName = the person before the small bluff, and two before the big bluff
 *      [activeSince] = time the user started at (UNIXTIME). If it hits 60 seconds, then they are kicked and fold
 * Client filtered table:
 *      [hand] = (board.cards.hands[user]) = ["card1","card2"]
 *      [shown] = (board.cards.shown)
 *      [players] = [{"name":"bob","money":1500}]
 *      [pot] = (board.pot)
 *      [turn] = "playerName"
 *      [status] = WAITING|RUNNING
 *      [dealer] = "playerName"
 *      [call] = (board.call)
 *      [callFrom] = (board.callFrom)
 * Client Logic:
 *      Client view would refresh secondly to show who's turn it is, money, etc.
 *      Top and center it would show the player list, looking something like "Playername has $123"
 *          An arrow with bold would pass around signifying who's turn it is.
 *      Below would be active pots, usually saying "Pot: $1234", unless it was split, then it would say "Pot[all]: $500 ; Pot[bob,joe]: $100"
 */
class PokerBoard extends GameBoard {

	const WAITING = "WAITING";
	const RUNNING = "RUNNING";
	const SMALL_BLUFF_SIZE = 1;

	const ACTION_FOLDING = 0;
	const ACTION_CALLING = 1;
	const ACTION_LEAVING = 2;
	const ACTION_UPDATE = 3;
	const ACTION_RAISING = 3;

	//testing put back to 60
	const SECONDS_PER_TURN = 6000;

	const BIG_BLUFF_SIZE = self::SMALL_BLUFF_SIZE * 2;
	const MIN_JOIN_AMOUNT = self::BIG_BLUFF_SIZE * 5;

	public function effectivelyFold() {
		//remove them from all pots
		error_log("FOLDING:".$_SESSION["username"]);
		foreach ($this->board["pot"] as &$pot) {
			foreach ($pot["players"] as $k => $player) if ($player==$_SESSION["username"]) unset($pot["players"][$k]);
		}
		error_log("POT:".json_encode($this->board["pot"]));
	}

	public function resetActiveSince() {
		$this->board["activeSince"] = $_SERVER["REQUEST_TIME_FLOAT"];
	}

	public function takeInput() {
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		$action = filter_input(INPUT_POST,"action",FILTER_VALIDATE_INT);


		if ($this->board==self::WAITING) {
			$this->endGame($conn);
			$this->sqlUpdateBoard();
			return;
		}

		if ($_SERVER["REQUEST_TIME"] > $this->board["activeSince"] + self::SECONDS_PER_TURN) {
			//reset so not everyone gets kicked...
			$this->resetActiveSince();
			//kick the player who's turn it is
			$this->effectivelyFold();
			self::kickPlayer($conn, $this->id, self::getPlayerByTurnNumber($conn, $this->id, 0));
			return;
		}

		if ($action == self::ACTION_LEAVING) {
			$this->effectivelyFold();
			self::kickPlayer($conn, $this->id, $_SESSION["username"]);
			return;
		}

		//all other moves require 2+ people
		if ($this->getNumPlayers($conn, $this->id)<2) return;

		if ($action == self::ACTION_FOLDING || $action == self::ACTION_CALLING || $action == self::ACTION_RAISING) {
			//stop betting if its not your turn
			if (self::getActivePlayer($conn, $this->id) !== $_SESSION["username"]) return;

			if ($action == self::ACTION_FOLDING) {
				$this->effectivelyFold();
			} else if ($action == self::ACTION_CALLING || $action == self::ACTION_RAISING) {
				if ($action == self::ACTION_CALLING) {
					$betLeft = $this->board["call"];
				} else {
					$betLeft = filter_input(INPUT_POST,"bet",FILTER_VALIDATE_INT);
					if (!$betLeft) return;
					//cant bet under call
					if ($betLeft < $this->board["call"]) return;
				}

				error_log("BEFORE TAKING BET ".json_encode($this->board["pot"]));

				//main take bet
				$curUsersBet = transferCoins($conn, $_SESSION["username"],"dealer",$betLeft,"Poker Bet");
				if (!$curUsersBet) {
					error_log("COULDNT AFFORD BET: ".$betLeft);
//					$this->effectivelyFold();
//					self::kickPlayer($conn, $this->id, $_SESSION["username"]);//kicked for not being able to play bet
					return;
				}
				error_log("BETTING ".$betLeft." OF CALL ".$this->board["call"]);

				//raise the call, if necessary
				if ($betLeft > $this->board["call"] || !isset($this->board["callFrom"]) || $this->board["callFrom"]==null) {
					$this->board["call"] = $betLeft;
					$this->board["callFrom"] = $_SESSION["username"];
				}

				//if they went all in
				if ($betLeft === getCoinsOfUser($conn,$_SESSION["username"])) {
					//raise the respective pot containing remaining players, then make a new empty pot between remaining players
					$newPot = [];
					$newPot["players"] = $this->getPlayersStillInMainPot();
					$newPot["amount"] = $betLeft;$betLeft=0;
					$newPot["toFill"] = $betLeft * (count($newPot["players"])-1);// they already filled it
					//kick them from the -1 pot
					foreach ($this->board["pot"] as &$p) if ($p["toFill"]==-1) {unset($p["players"][$_SESSION["username"]]);break;}
					$this->board["pot"][] = $newPot;
				}

				//prioritize all pots with toFill>0
				foreach ($this->board["pot"] as &$p) if ($p["toFill"]>0) {
					if ($p["toFill"] >= $betLeft) {
						//if you can't overfill the pot, put everything you can into it
						$p["toFill"] -= $betLeft;
						$p["amount"] += $betLeft;
						$betLeft = 0;
						break;
					} else {
						//if you can overfill the pot, empty it then go to the next one
						$betLeft -= $p["toFill"];
						$p["amount"] += $p["toFill"];
						$p["toFill"] = 0;
					}
				}
				//then put rest in primary pot
				foreach ($this->board["pot"] as &$p) if ($p["toFill"]==-1) {$p["amount"]+=$betLeft;break;}

				error_log("AFTER TAKING BET ".json_encode($this->board["pot"]));
			}

			//main restart max turn clock. This is checked by every client, and if it's past 1 minute (or any time, really), they're kicked
			$this->resetActiveSince();

			//check all pots. If all pots only have one person, give it to that person.
			$playersLeft = $this->getPlayersStillInMainPot();
			error_log("PLAYERS LEFT: ".json_encode($playersLeft));
			if (count($playersLeft)==1) {
				error_log("Only one person in main pot");
				$this->endGame($conn);
				$this->sqlUpdateBoard();
				return;
			}
			//go to next person, or add cards, or if all cards added and all people passed, end game
			$this->cycleBoardState($conn);
		}
		$this->sqlUpdateBoard();
	}

	private function getPlayersStillInMainPot() {
		foreach ($this->board["pot"] as $p) if ($p["toFill"]==-1) return $p["players"];//foreach ($b["players"] as $p) if (!in_array($p, $playersLeft))$playersLeft[] = $p;
		error_log("ERR no main pot?: ".json_encode($this->board));
		return null;
	}

	private static function getCardIndex($card) {
		$card = strtolower($card[1])[0];
		switch ($card) {
			case "a":return 14;
			case "k":return 13;
			case "q":return 12;
			case "j":return 11;
			case "t":return 10;
			//number cards
			default:return ord($card)-ord('0');
		}
	}

	private static function hasOfAKindOrMore($hand, $numOfAKind) {
		$runningCardIndex = 0;
		$runningTotal = 0;
		$usedCards = [];
		//helps that it's sorted
		for ($x=0;$x<5;$x++) {
			$card = $hand[$x];
			$curCardIndex = self::getCardIndex($card);
			if ($curCardIndex==$runningCardIndex) {
				$runningTotal++;
				$usedCards[] = $x;
				if ($runningTotal == $numOfAKind) return $usedCards;
			} else {
				$usedCards = [];
				$runningCardIndex = $curCardIndex;
				$runningTotal = 1;
			}
		}
		return false;
	}

	public static function getPairRankCounts($hand) {
		$runningCardIndex = 0;
		$runningTotal = 0;
		$counts = [];
		$usedCards = [];
		//helps that it's sorted
		for($x=0;$x<count($hand);$x++) {
			$card = $hand[$x];
			$curCardIndex = self::getCardIndex($card);
			if ($curCardIndex==$runningCardIndex) {
				$runningTotal++;
				$usedCards[] = $x;
			} else {
				$counts[] = array($runningTotal,$usedCards);
				$runningCardIndex = $curCardIndex;
				$runningTotal = 1;
			}
		}
		$counts[] = $runningTotal;
		return $counts;
	}

	private static function isStraight($hand) {
		$prevCardVal = self::getCardIndex($hand[0]);
		foreach ($hand as $card) {
			$prevCardVal++;
			if (self::getCardIndex($card) != $prevCardVal) return false;
		}
		return [0,1,2,3,4];
	}
	private static function isFlush($hand) {
		$suit = $hand[0][0];
		foreach ($hand as $card) if ($card[0]!=$suit) return false;
		return [0,1,2,3,4];
	}

	/**@param array hand - array of cards, could be any length since this is used to compare pairs and full houses and such
	 * @return int compares high card */
	private function compareHighCards($left, $right) {
		if (count($left) != count($right)) {
			error_log("ERROR compareHighCards not getting even count:".json_encode($this->board));
			exit("ERROR COMP MISMATCH??? IDK HOW");
		}
		for ($x=count($left)-1;$x>=0;$x--) {
			$diff = self::getCardIndex($right[$x]) - self::getCardIndex($left[$x]);
			if ($diff>0) return  1;
			if ($diff<0) return -1;
		}
		return 0;
	}

	/**Both params are arrays of 5 cards*/
	private function compareHands($left, $right) {
		$cmpCards = function($cardA,$cardB) {return self::getCardIndex($cardB) - self::getCardIndex($cardA);};
		usort($left ,$cmpCards);
		usort($right,$cmpCards);
		//everything that values two hands
		$isFlush = function ($hand) {return self::isFlush($hand);};
		$isStraight = function ($hand) {return self::isStraight($hand);};
		$isStraightFlush = function($hand){return self::isStraight($hand) && self::isFlush($hand);};
		$isFourOfAKind = function ($hand) {return self::hasOfAKindOrMore($hand,4);};
		$isFullHouse = function ($hand) {
			$prc = self::getPairRankCounts($hand);
			$hasThree = false;
			$hasTwo = false;
			$cardsUsed = [];
			foreach ($prc as $p) {
				if ($p[0]==3) {
					$hasThree = true;
					foreach ($p[1] as $card) $cardsUsed[] = $card;
				} else if ($p[0]==2) {
					$hasTwo=true;
					foreach ($p[1] as $card) $cardsUsed[] = $card;
				}
			}
			if ($hasThree && $hasTwo){
				return $cardsUsed;
			}  else {
				return false;
			}
		};
		$isTwoPairs = function ($hand) {
			$prc = self::getPairRankCounts($hand);
			$twos = 0;
			$cardsUsed = [];
			foreach ($prc as $p) {
				if ($p[0]==2) {
					$twos++;
					foreach ($p[1] as $card) $cardsUsed[] = $card;
				}
			}
			if ($twos == 2) {
				return $cardsUsed;
			}  else {
				return false;
			}
		};

		$isThreeOfAKind = function ($hand) {return self::hasOfAKindOrMore($hand,3);};
		$isPair = function ($hand) {return self::hasOfAKindOrMore($hand,2);};
		$thingsThatCount = array(
			"Straight Flush"=>$isStraightFlush,
			"Four of a Kind"=>$isFourOfAKind,
			"Full House"=>$isFullHouse,
			"Flush"=>$isFlush,
			"Straight"=>$isStraight,
			"Three of a Kind"=>$isThreeOfAKind,
			"Two Pairs"=>$isTwoPairs,
			"Pair"=>$isPair
		);
		//if one user has this, and the other doesn't, then they win.
		foreach ($thingsThatCount as $name => $func) {
			$leftHas = $func($left);
			$rightHas = $func($right);
			if ($leftHas && !$rightHas) return -1;
			if (!$leftHas && $rightHas) return  1;
			if ($leftHas && $rightHas) {
				//of used cards, who has the highest cards (in order)
				return $this->compareHighCards($leftHas, $rightHas);
			}
		}
		//winner is high card
		return $this->compareHighCards($left,$right);
	}

	/**@param $array array - An array
	 * @return Generator array of size equal to given array, arrays similar to the one given but each one missing a different item
	 */
	private function permuteWithoutOne($array) {
		foreach ($array as $key=>$val) {
			$ret = $array;
			unset($ret[$key]);
			yield $ret;
		}
	}

	private function getWinningHandPlayers($players) {
		//each player has 72 hands of 5. Find the best of these hands permutations (or find a better algorithm that's not 7C5 time).
		$bestHand = null;
		$bestPlayers = [];
		foreach ($players as $player) {
			//their 7-card hand is their 2 cards plus the shown cards
			$hand7cards = array_merge(
				$this->board["cards"]["hands"][$player],
				$this->board["cards"]["shown"]);
			foreach ($this->permuteWithoutOne($hand7cards) as $hand6cards) {
				foreach ($this->permuteWithoutOne($hand6cards) as $hand5cards) {
					if (!$bestHand) {
						//first hand is initially the best hand
						$bestHand = $hand5cards;
						$bestPlayers = [$player];
					} else {
						$cmp = $this->compareHands($bestHand, $hand5cards);
						if ($cmp==0) {
							//same hand value? pot split
							$bestPlayers[] = $player;
						} else if ($cmp>0) {
							//new best hand
							$bestHand = $hand5cards;
							$bestPlayers = [$player];
						}
					}
				}
			}
		}
		return $bestPlayers;
	}

	private function putCardsOut($numCards) {
		for ($x=0;$x<$numCards;$x++) $this->board["cards"]["shown"][] =array_pop($this->board["cards"]["deck"]);
	}

	/**go to next person, or add cards, or if all cards added and all people passed, end game
	 *
	 */
	private function cycleBoardState($conn) {
		//after they do their thing, cycle the turn until it's to someone in
		$stillIn = $this->getPlayersStillInMainPot();

		if (empty($stillIn)) {
			error_log("Trying to cycleBoardState on empty arr: ".json_encode($this->board));
		}

		$passedCallFrom = false;

		for($x=0;$x<5+count($stillIn);$x++) {
			$this->toggleTurn($conn);
			$active = self::getActivePlayer($conn, $this->id);
			if ($this->board["callFrom"] == $active) $passedCallFrom = true;
			//if they have no coins, they went all-in recently
			if (in_array($active, $stillIn)) {
				break;
			}
			if ($x > 1+count($stillIn)) {
				error_log("cycleBoardState can't find next person: ".json_encode($this->board));
			}
		}
		//if callFrom equals current player, then cycle the board state
		if ($passedCallFrom) {
			//reset call once a card goes out
			$this->board["call"] = 0;
			//if 0,3 or 4 cards are out, this shows more cards
			//if 5 cards are out, this looks at all hands, and gives the winner(s) the pot. Could be many winners if they have the same top 5 cards.
			$prevCardsOut = count($this->board["cards"]["shown"]);
			if ($prevCardsOut==0) {
				$this->putCardsOut(3);
			} else if ($prevCardsOut==3) {
				$this->putCardsOut(1);
			} else if ($prevCardsOut==4) {
				$this->putCardsOut(1);
			} else if ($prevCardsOut==5) {
				//reset game so they can start playing again.
				$this->endGame($conn);
			} else {
				error_log("Poker game had weird amount of cards out:".json_encode($this->board));
			}
		}
	}

	private function resetBoardCards($players) {
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

	private function resetBoardBluffs($conn, $players) {
		$smallBluffPlayer = self::getPlayerByTurnNumber($conn, $this->id,1);
		$bigBluffPlayer = self::getPlayerByTurnNumber($conn, $this->id,2);
		//can big bluff pay? This prevents the small buff from being paid unless big buff can be paid
		error_log($smallBluffPlayer."=SmallBluff");
		error_log($bigBluffPlayer."=BigBluff");
		if (getCoinsOfUser($conn, $bigBluffPlayer) < self::BIG_BLUFF_SIZE) {
			self::kickPlayer($conn, $this->id, $bigBluffPlayer);
			error_log("BLUFF FAILED A");
			return false;
		}
		//small bluff
		$couldPaySmallBluff = transferCoins($conn, $smallBluffPlayer,"dealer",self::SMALL_BLUFF_SIZE,"poker small bluff");
		if (!$couldPaySmallBluff) {
			self::kickPlayer($conn, $this->id, $smallBluffPlayer);
			error_log("BLUFF FAILED B");
			return false;
		}
		//big bluff
		$couldPaySmallBluff = transferCoins($conn, $bigBluffPlayer,"dealer",self::BIG_BLUFF_SIZE,"poker big bluff");
		if (!$couldPaySmallBluff) {
			self::kickPlayer($conn, $this->id, $bigBluffPlayer);
			error_log("BLUFF FAILED C");
			return false;
		}
		//after bluffs have been met, put them in the pot
		$this->board["pot"][] = [
			"players"=>$players,
			"amount"=>self::SMALL_BLUFF_SIZE + self::BIG_BLUFF_SIZE,
			"toFill"=>-1
		];
		return true;
	}

	public function canPlayerJoinGame($conn, $players) {
		//only if they have over the MIN_JOIN_AMOUNT
		if (getCoinsOfUser($conn, $_SESSION["username"]) < self::MIN_JOIN_AMOUNT) return false;
		//add them to the game
		return "Player num ".$players;
	}

	/**
	 * Starts a new board, sets bluffs, sets pot, etc.
	*/
	public function endGame($conn) {

		if (isset($this->board)) {
			//save board to be reviewed later by players
			$lastBoard = $this->board;
			if (isset($lastBoard["lastBoard"])) unset($lastBoard["lastBoard"]);
		}

		//give winning player(s) dived pots
		if (isset($this->board["pot"]) && count($this->board["pot"])>0) {
			foreach ($this->board["pot"] as $pot) {
				//efficiency: could just get each player's best hand, and then compare it to divy pots
				$winningPlayers = $this->getWinningHandPlayers($pot["players"]);
				$value = $pot["amount"];
				$individualPrize = floor($value / count($winningPlayers));
				foreach ($winningPlayers as $p) {
					transferCoins($conn, "dealer",$p,$individualPrize,"Won Pot");
				}
			}
		}
		$couldMakeBluffs = false;
		for ($x=0;$x<self::getNumPlayers($conn, $this->id);$x++) {
			$this->board = [];
			$players = self::getPlayerList($conn, $this->id);
			$numPlayers = self::getNumPlayers($conn, $this->id);
			if ($numPlayers < 2) {exit (json_encode(array("status"=>"Not enough players... Waiting for new game")));}
			$this->resetBoardCards($players);
			$this->resetActiveSince();
			//reset all pots
			$this->board["pot"] = [];
			$this->board["call"] = 0;
			if (isset($this->board["dealer"])) {
				//next dealer
				$this->board["dealer"] = self::getPlayerByTurnNumber($conn, $this->id,
					(1+self::getPlayerTurnNumber($conn,$this->id,$this->board["dealer"])) % $numPlayers
				);
				//set dealer to playerID zero so that bluffs can be paid by the right people
				for($x=0;$x<$numPlayers;$x++) {
					if (self::getPlayerTurnNumber($conn,$this->id,$this->board["dealer"])==0) break;
					$this->toggleTurn($conn);
				}
			} else {
				$this->board["dealer"] = self::getPlayerByTurnNumber($conn, $this->id,0);
			}
			$this->board["callFrom"] = $this->board["dealer"];
			$this->board["lastBoard"] = $lastBoard;
			if (self::getPlayerTurnNumber($conn,$this->id,$this->board["dealer"])!=0) {
				error_log("Dealer is not player 0:".json_encode($this->board));
				exit("CODE ERROR tell admin");
			}
			//set bluffs. If bluffs can't be played, kick applicable player and restart game
			$couldMakeBluffs = $this->resetBoardBluffs($conn, $players);
			//testing dump board data (with cards lol)
			if ($couldMakeBluffs) break;
		}
		if (!$couldMakeBluffs) {
			error_log("CODE ERROR. Noone could make bluffs yet it still has players? CouldMakeBluffs=".($couldMakeBluffs?"T":"F")." -- ".json_encode($this->board));
		}
	}

	public function populateByGenerate($size) {
		$this->gametype = self::POKER;
		$this->size = $size;
		$this->board = self::WAITING;
		$this->sqlInsertBoard();
	}

	public function getAllPlayersMoney($conn) {
		$ret = [];
		$players = self::getPlayerList($conn,$this->id);
		foreach ($players as $p) {
			$ret[$p] = getCoinsOfUser($conn,$p);
		}
		return $ret;
	}

	public function getSanitizedBoard()	{
		$conn = mysqli_connect("localhost","website",parse_ini_file("/var/www/php/pass.ini")["mysql"],"userdata");
		//would return player's hand, the pot, etc.
		if (!is_array($this->board)) return $this->board;
		$filtered = array();
		$filtered["hand"]  = $this->board["cards"]["hands"][$_SESSION["username"]];
		$filtered["shown"] = $this->board["cards"]["shown"];
		$filtered["pot"] = $this->board["pot"];
		$filtered["call"] = $this->board["call"];
		$filtered["callFrom"] = $this->board["callFrom"];
		$filtered["turn"] = self::getActivePlayer($conn,$this->id);
		$filtered["status"] = $this->board==self::WAITING?self::WAITING:self::RUNNING;
		$filtered["activeSince"] = $this->board["activeSince"];
		$filtered["players"] = $this->getAllPlayersMoney($conn);
		$filtered["lastBoard"] = isset($this->board["lastBoard"])?$this->board["lastBoard"]:"";
		//testing (DUH)
//		echo json_encode($this->board);
		return json_encode($filtered);
	}
}