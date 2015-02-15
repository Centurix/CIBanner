<?php

/* 
 * Banner library for CodeIgniter, help with large text on a CRON page. Doesn't rely on Figlet
 * Author: Chris Read
 * Email: centurix@gmail.com
 * 
 * Usage:
 * $this->load->library('banner');
 * $this->banner->render('Hello mum!');
 *
 */
class banner {
	/* Font starts at this ASCII character, if you require numbers, drop it back */
	private $font_start = 48;

	/* Space is this wide */
	private $font_space_width = 4;

	/* banner fonts from http://patorjk.com/software/taag/
	 * To do your own, go there and type in A-Z, set the width to Full and copy the text replacing the lines below
	 * Then change the $font_end array with the last column number for each character. Change the space width above to suit.
	 */
	private $font_lines = array(
	   /* Font position counter */
	   /*                                                                                                    11111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111112222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222333333333333333333333333333333333333333333333333333 */
	   /*          11111111112222222222333333333344444444445555555555666666666677777777778888888888999999999900000000001111111112222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122222222223333333333444444444455555555556666666666777777777788888888889999999999000000000011111111122222222222333333333344444444445 */
	   /*012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890 */
		' _______  ____  _______  _______  _   ___  _______  ___      _______   _____   _______  ___  ___ < ____ > ______ @ _______  _______  _______  ______   _______  _______  _______  __   __  ___      ___  ___   _  ___      __   __  __    _  _______  _______  _______  ______    _______  _______  __   __  __   __  _     _  __   __  __   __  _______ ',
		'|  _    ||    ||       ||       || | |   ||       ||   |    |       | |  _  | |  _    ||   ||   | |____| |      | |   _   ||  _    ||       ||      | |       ||       ||       ||  | |  ||   |    |   ||   | | ||   |    |  |_|  ||  |  | ||       ||       ||       ||    _ |  |       ||       ||  | |  ||  | |  || | _ | ||  |_|  ||  | |  ||       |',
		'| | |   | |   ||____   ||___    || |_|   ||   ____||   |___ |___    | | |_| | | | |   ||___||___|  ____  |___   | |  |_|  || |_|   ||       ||  _    ||    ___||    ___||    ___||  |_|  ||   |    |   ||   |_| ||   |    |       ||   |_| ||   _   ||    _  ||   _   ||   | ||  |  _____||_     _||  | |  ||  |_|  || || || ||       ||  |_|  ||____   |',
		'| | |   | |   | ____|  | ___|   ||       ||  |____ |    _  |    |   ||   _   || |_|   | ___  ___  |____|   __|  | |       ||       ||       || | |   ||   |___ |   |___ |   | __ |       ||   |    |   ||      _||   |    |       ||       ||  | |  ||   |_| ||  | |  ||   |_||_ | |_____   |   |  |  |_|  ||       ||       ||       ||       | ____|  |',
		'| |_|   | |   || ______||___    ||___    ||_____  ||   | | |    |   ||  | |  ||___    ||   ||_  |         |_____| |       ||  _   | |      _|| |_|   ||    ___||    ___||   ||  ||       ||   | ___|   ||     |_ |   |___ |       ||  _    ||  |_|  ||    ___||  |_|  ||    __  ||_____  |  |   |  |       ||       ||       | |     | |_     _|| ______|',
		'|       | |   || |_____  ___|   |    |   | _____| ||   |_| |    |   ||  |_|  |    |   ||___|  |_|           __    |   _   || |_|   ||     |_ |       ||   |___ |   |    |   |_| ||   _   ||   ||       ||    _  ||       || ||_|| || | |   ||       ||   |    |      | |   |  | | _____| |  |   |  |       | |     | |   _   ||   _   |  |   |  | |_____ ',
		'|_______| |___||_______||_______|    |___||_______||_______|    |___||_______|    |___|                    |__|   |__| |__||_______||_______||______| |_______||___|    |_______||__| |__||___||_______||___| |_||_______||_|   |_||_|  |__||_______||___|    |____||_||___|  |_||_______|  |___|  |_______|  |___|  |__| |__||__| |__|  |___|  |_______|'
	);

	/* Array describing which column each character finishes */
	private $font_end = array(8,14,23,32,41,50,59,68,77,86,91,96,97,103,104,112,113,122,131,140,149,158,167,176,185,190,199,208,217,226,235,244,253,262,272,281,290,299,308,317,326,335,345);

	function __construct() {
		$this->ci =& get_instance();
	}

	/* Render the message out in big fonts, make sure it looks good in source view as well. Handle multiple line messages too. */
	public function render($message) {
		$message = strtoupper($message);
		$blocks[] = "";
		foreach($this->font_lines as $line) {
			$block_index = 0;
			foreach(str_split($message) as $character) {
				$ascii = ord($character);
				if($ascii == 10) { // New line (\n)
					$blocks[$block_index++] .= "\n";
					if(!isset($blocks[$block_index]))
						$blocks[] = "";
				} elseif($ascii >= 32 && $ascii < $this->font_start) {
					$blocks[$block_index] .= str_repeat(' ',$this->font_space_width);
				} elseif($ascii >= $this->font_start) {
					$offset = $ascii - $this->font_start;
					$character_end = $this->font_end[$offset] + 1;
					$character_start = ($offset > 0)?$this->font_end[$offset - 1] + 1:0;
					$blocks[$block_index] .= substr($line,$character_start,$character_end - $character_start);
				}
			}
			$blocks[$block_index] .= "\n";
		}
		echo "<pre>\n".implode($blocks)."</pre>\n";
	}

	/* Test the current font setup */
	public function test_font() {
		$message = '';
		for($index = $this->font_start; $index < count($this->font_end) + $this->font_start; $index++) {
			$message .= chr($index);
		}
		$this->render($message);
	}
}

?>