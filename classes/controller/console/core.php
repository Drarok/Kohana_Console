<?php

/**
 * This is the main implementation of the Console.
 */

class Controller_Console_Core extends Controller {
	/**
	 * Use the before() method to get set up for CLI operation.
	 */
	public function before() {
		// Immediately exit if we're not on the command line.
		if (PHP_SAPI != 'cli') {
			exit(1);
		}
		
		// Use our error and exception handlers.
		set_error_handler('Controller_Console::error_handler');
		set_exception_handler('Controller_Console::exception_handler');
		
		// Disable any buffering.
		ob_end_flush();
	}
	
	/**
	 * This method is the main juicy bit.
	 */
	public function action_index() {
		echo 'Enter an empty line to exit.', PHP_EOL;
		
		// Set up our state.
		$_kohana_console_line = FALSE;
		$_stack = array();
		$_prompt = 'Kohana ' . Kohana::VERSION . '> ';
		
		// Keep going until there's an empty line entered (except if there's a stack).
		while ($_kohana_console_line !== '') {
			echo $_prompt;
			
			// Reset return value and get code from the user.
			$_kohana_console_cmd = NULL;
			$_kohana_console_line = rtrim(fgets(STDIN), "\r\n");
			
			// If the braces aren't balanced, or we have the beginnings of a stack, add to it.
			if ((bool) $_kohana_console_line AND (count($_stack) > 0) OR ($this->count_braces($_kohana_console_line) > 0)) {
				$_stack[] = $_kohana_console_line;
				$_prompt = '...> ';
				continue;
			}
			
			// Convert the stack to a "line" to eval, clear the stack, reset the prompt.
			if ((bool) $_stack) {
				$_kohana_console_line = implode(PHP_EOL, $_stack);
				$_stack = array();
				$_prompt = 'Kohana ' . Kohana::VERSION . '> ';
			}
			
			if ((bool) $_kohana_console_line) {
				try {
					$_kohana_console_result = NULL;

					if (! preg_match('/foreach|for|while|echo/', $_kohana_console_line)) {
						$_kohana_console_line = '$_kohana_console_result = ' . $_kohana_console_line . ';';
					} else {
						$_kohana_console_line .= ';';
					}

					eval($_kohana_console_line);
				} catch (Exception $e) {
					echo sprintf('Uncaught exception (%s): %s', get_class($e), $e->getMessage()), PHP_EOL;
					echo $e->getTraceAsString(), PHP_EOL;
				}

				// Output the value returned.
				if ($_kohana_console_result !== NULL) {
					if (is_object($_kohana_console_result) AND method_exists($_kohana_console_result, '__toString')) {
						echo $_kohana_console_result->__toString(), PHP_EOL;
					} else {
						var_dump($_kohana_console_result);
					}
				}
			}
		}
	}
	
	/**
	 * Count the braces in a line, returning -ve for too many closing braces,
	 * +ve for too many opening braces, and 0 for matched braces.
	 * @param string $line The string to count.
	 * @return integer Counted braces.
	 */
	protected function count_braces($line) {
		$result = 0;
		
		for ($i = 0; $i < strlen($line); ++$i) {
			$c = $line[$i];
			
			if ($c == '{') {
				++$result;
			} elseif ($c == '}') {
				--$result;
			}
		}
		
		return $result;
	}
	
	public static function error_handler($errno, $errstr) {
		echo sprintf('Error (%d): %s', $errno, $errstr), PHP_EOL;
		
		// Suppress the normal error handler.
		return TRUE;
	}
	
	public static function exception_handler($exception) {
		echo 'Uncaught exception: ', $exception->getMessage(), PHP_EOL;
		echo $exception->getTraceAsString(), PHP_EOL;
	}
}