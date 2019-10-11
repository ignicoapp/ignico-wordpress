<?php
/**
 * Logger class provided for log important information to log file.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc/core
 */

namespace IgnicoWordPress\Core;

use DateTime;
use RuntimeException;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Logger class provided for log important information to log file.
 *
 * @package    IgnicoWordPress
 * @subpackage IgnicoWordPress/inc
 *
 * original work by:
 *
 * @author  Kenny Katzgrau <katzgrau@gmail.com>
 * @link    https://github.com/katzgrau/KLogger
 */
class Logger extends AbstractLogger {

	/**
	 * KLogger options
	 *  Anything options not considered 'core' to the logging library should be
	 *  settable view the third parameter in the constructor
	 *
	 *  Core options include the log file path and the log threshold
	 *
	 * @var array
	 */
	protected $options = array(
		'extension'      => 'log',
		'date_format'    => 'Y-m-d G:i:s.u',
		'filename'       => false,
		'flushFrequency' => false,
		'prefix'         => 'log_',
		'appendContext'  => true,
	);

	/**
	 * Path to the log file
	 *
	 * @var string
	 */
	private $log_file_path;

	/**
	 * Current minimum logging threshold
	 *
	 * @var integer
	 */
	protected $log_level_threshold = LogLevel::DEBUG;

	/**
	 * The number of lines logged in this instance's lifetime
	 *
	 * @var int
	 */
	private $log_line_count = 0;

	/**
	 * Log Levels
	 *
	 * @var array
	 */
	protected $log_levels = array(
		LogLevel::EMERGENCY => 0,
		LogLevel::ALERT     => 1,
		LogLevel::CRITICAL  => 2,
		LogLevel::ERROR     => 3,
		LogLevel::WARNING   => 4,
		LogLevel::NOTICE    => 5,
		LogLevel::INFO      => 6,
		LogLevel::DEBUG     => 7,
	);

	/**
	 * This holds the file handle for this instance's log file
	 *
	 * @var resource
	 */
	private $file_handle;

	/**
	 * This holds the last line logged to the logger
	 *  Used for unit tests
	 *
	 * @var string
	 */
	private $last_line = '';

	/**
	 * Octal notation for default permissions of the log file
	 *
	 * @var integer
	 */
	private $default_permission = 0777;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param CoreInit $plugin Ignico plugin container.
	 *
	 * @return Init
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;
	}

	/**
	 * Initialize logger
	 *
	 * @param string $log_directory      File path to the logging directory.
	 * @param string $log_level_threshold The LogLevel Threshold.
	 * @param array  $options
	 *
	 * @internal param string $logFilePrefix The prefix for the log file name.
	 * @internal param string $logFileExt The extension for the log file.
	 */
	public function init( $log_directory, $log_level_threshold = LogLevel::DEBUG, array $options = array() ) {
		$this->log_level_threshold = $log_level_threshold;
		$this->options             = array_merge( $this->options, $options );

		$log_directory = rtrim( $log_directory, DIRECTORY_SEPARATOR );
		if ( ! file_exists( $log_directory ) ) {
			mkdir( $log_directory, $this->default_permission, true );
		}

		if ( strpos( $log_directory, 'php://' ) === 0 ) {
			$this->set_log_to_stdout( $log_directory );
			$this->set_file_handle( 'w+' );
		} else {
			$this->set_log_file_path( $log_directory );
			if ( file_exists( $this->log_file_path ) && ! is_writable( $this->log_file_path ) ) {
				throw new RuntimeException( 'The file could not be written to. Check that appropriate permissions have been set.' );
			}
			$this->set_file_handle( 'a' );
		}

		if ( ! $this->file_handle ) {
			throw new RuntimeException( 'The file could not be opened. Check permissions.' );
		}
	}

	/**
	 * @param string $stdOutPath
	 */
	public function set_log_to_stdout( $stdOutPath ) {
		$this->log_file_path = $stdOutPath;
	}

	/**
	 * @param string $log_directory
	 */
	public function set_log_file_path( $log_directory ) {
		if ( $this->options['filename'] ) {
			if ( strpos( $this->options['filename'], '.log' ) !== false || strpos( $this->options['filename'], '.txt' ) !== false ) {
				$this->log_file_path = $log_directory . DIRECTORY_SEPARATOR . $this->options['filename'];
			} else {
				$this->log_file_path = $log_directory . DIRECTORY_SEPARATOR . $this->options['filename'] . '.' . $this->options['extension'];
			}
		} else {
			$this->log_file_path = $log_directory . DIRECTORY_SEPARATOR . $this->options['prefix'] . date( 'Y-m-d' ) . '.' . $this->options['extension'];
		}
	}

	/**
	 * @param $writeMode
	 *
	 * @internal param resource $file_handle
	 */
	public function set_file_handle( $writeMode ) {
		$this->file_handle = fopen( $this->log_file_path, $writeMode );
	}

	/**
	 * Terminate logger
	 */
	public function terminate() {
		if ( $this->file_handle ) {
			fclose( $this->file_handle );
		}
	}

	/**
	 * Sets the date format used by all instances of KLogger
	 *
	 * @param string $date_format Valid format string for date()
	 */
	public function set_date_format( $date_format ) {
		$this->options['date_format'] = $date_format;
	}

	/**
	 * Sets the Log Level Threshold
	 *
	 * @param string $log_level_threshold The log level threshold
	 */
	public function set_log_level_threshold( $log_level_threshold ) {
		$this->log_level_threshold = $log_level_threshold;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level
	 * @param string $message
	 * @param array  $context
	 * @return null
	 */
	public function log( $level, $message, array $context = array() ) {
		if ( $this->log_levels[ $this->log_level_threshold ] < $this->log_levels[ $level ] ) {
			return;
		}
		$message = $this->format_message( $level, $message, $context );
		$this->write( $message );
	}

	/**
	 * Writes a line to the log without prepending a status or timestamp
	 *
	 * @param string $message Line to write to the log
	 * @return void
	 */
	public function write( $message ) {
		if ( null !== $this->file_handle ) {
			if ( fwrite( $this->file_handle, $message ) === false ) {
				throw new RuntimeException( 'The file could not be written to. Check that appropriate permissions have been set.' );
			} else {
				$this->last_line = trim( $message );
				$this->log_line_count++;

				if ( $this->options['flushFrequency'] && $this->log_line_count % $this->options['flushFrequency'] === 0 ) {
					fflush( $this->file_handle );
				}
			}
		}
	}

	/**
	 * Get the file path that the log is currently writing to
	 *
	 * @return string
	 */
	public function get_log_file_path() {
		return $this->log_file_path;
	}

	/**
	 * Get the last line logged to the log file
	 *
	 * @return string
	 */
	public function get_last_log_line() {
		return $this->last_line;
	}

	/**
	 * Formats the message for logging.
	 *
	 * @param  string $level   The Log Level of the message
	 * @param  string $message The message to log
	 * @param  array  $context The context
	 * @return string
	 */
	protected function format_message( $level, $message, $context ) {
		$entry    = '';
		$datetime = $this->datetime();
		$ip       = $this->ip();
		$user     = $this->user();

		// Append message properties
		$entry .= ( $datetime ) ? "[{$datetime}]" : '';
		$entry .= ( $ip ) ? "[{$ip}]" : '';
		$entry .= ( $user ) ? "[{$user}]" : '';
		$entry .= "[{$level}]";

		// Append " " character to to align messages
		$entry = str_pad( $entry, 50, ' ' );

		// Append message
		$entry .= "{$message}";

		if ( $this->options['appendContext'] && ! empty( $context ) ) {
			$entry .= PHP_EOL . $this->indent( $this->contextToString( $context ) );
		}

		return $entry . PHP_EOL;
	}

	/**
	 * Gets the correctly formatted Date/Time for the log entry.
	 *
	 * PHP DateTime is dump, and you have to resort to trickery to get microseconds
	 * to work correctly, so here it is.
	 *
	 * @return string
	 */
	private function datetime() {
		return ( new DateTime() )->format( $this->options['date_format'] );
	}

	/**
	 * Gets the ip address for the log entry.
	 *
	 * @return string
	 */
	private function ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	/**
	 * Gets the WordPress user email if exists
	 *
	 * @return string
	 */
	private function user() {
		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			return $user->user_email;
		}

		return null;
	}

	/**
	 * Takes the given context and coverts it to a string.
	 *
	 * @param  array $context The Context
	 * @return string
	 */
	protected function contextToString( $context ) {
		$export = '';
		foreach ( $context as $key => $value ) {
			$export .= "{$key}: ";
			$export .= preg_replace(
				array(
					'/=>\s+([a-zA-Z])/im',
					'/array\(\s+\)/im',
					'/^  |\G  /m',
				), array(
					'=> $1',
					'array()',
					'    ',
				), str_replace( 'array (', 'array(', var_export( $value, true ) )
			);
			$export .= PHP_EOL;
		}
		return str_replace( array( '\\\\', '\\\'' ), array( '\\', '\'' ), rtrim( $export ) );
	}

	/**
	 * Indents the given string with the given indent.
	 *
	 * @param  string $string The string to indent
	 * @param  string $indent What to use as the indent.
	 * @return string
	 */
	protected function indent( $string, $indent = '    ' ) {
		return $indent . str_replace( "\n", "\n" . $indent, $string );
	}
}
