<?php
/**
 * Grav DateTools Plugin
 *
 * The DateTools Plugin provides provides date tools to use inside of Twig 
 * for filtering pages. With the release of Grav 0.9.13 `startDate` and 
 * `endDate` were introduced to collection parsing. You can use the 
 * following `datetools` to set various dates for retrieving collections. 
 *
 * PHP version 5.6+
 *
 * @package    DateTools
 * @author     Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright  2016 Kaleb Heitzman
 * @license    https://opensource.org/licenses/MIT MIT
 * @version    1.0.6
 * @link       https://github.com/kalebheitzman/grav-plugin-datetools
 * @since      1.0.0 Initial Release
 */

namespace Grav\Plugin;

require_once __DIR__.'/../vendor/autoload.php';

use Grav\Common\Grav;
use Grav\Common\GravTrait;
use \Carbon\Carbon;

class DateTools
{
	use GravTrait;

	/**
	 * @var object Config
	 */ 
	protected $config;

	/**
	 * @var string Date format
	 */
	protected $dateFormat; 

	/**
	 * @var object Current Carbon datetime
	 */
	protected $now;

	/**
	 * Various on the fly dates 
	 */
	public $today;
	public $tomorrow;
	public $yesterday;
	public $startOfWeek;
	public $endOfWeek;
	public $startOfMonth;
	public $endOfMonth;
	public $startOfYear;
	public $endOfYear;

	/**
	 * Date variables for processing from today
	 */
	protected $year;
	protected $month;
	protected $day;
	protected $hour;
	protected $min;
	protected $dayOfWeek;   
	protected $dayOfYear;   
	protected $weekOfMonth; 
	protected $weekOfYear;  
	protected $daysInMonth;

	/**
	 * Construct
	 */ 
	public function __construct( $args )
	{
		// get the config
		$this->config = $args['config'];

		// date format
		$this->dateFormat = $this->config->get('plugins.datetools.dateFormat.default');
		// get today
		$this->now = $this->now();
		
		// initialize processing vars
		$this->initProcessingVars();

		// initialize common dates
		$this->initCommonDates();
	}

	/**
	 * Initialize processing vars
	 * 
	 * @internal
	 */ 
	private function initProcessingVars()
	{
		$this->year = $this->now->year;
		$this->month = $this->now->month;
		$this->day = $this->now->day;
		$this->hour = $this->now->hour;
		$this->minute = $this->now->minute;
		$this->dayOfWeek = $this->now->dayOfWeek;   
		$this->dayOfYear = $this->now->dayOfYear;   
		$this->weekOfMonth = $this->now->weekOfMonth; 
		$this->weekOfYear = $this->now->weekOfYear;  
		$this->daysInMonth = $this->now->daysInMonth;
	}

	/**
	 * Initialize common dates
	 * 
	 * @internal
	 */ 
	private function initCommonDates()
	{
		$this->today	 	= $this->now()->format($this->dateFormat);
		$this->tomorrow 	= $this->tomorrow()->format($this->dateFormat);
		$this->yesterday 	= $this->yesterday()->format($this->dateFormat);
		$this->startOfWeek 	= $this->startOfWeek()->format($this->dateFormat);
		$this->endOfWeek 	= $this->endOfWeek()->format($this->dateFormat);
		$this->startOfMonth = $this->startOfMonth()->format($this->dateFormat);
		$this->endOfMonth 	= $this->endOfMonth()->format($this->dateFormat);
		$this->startOfYear 	= $this->startOfYear()->format($this->dateFormat);
		$this->endOfYear 	= $this->endOfYear()->format($this->dateFormat);
	}

	/**
	 * Parse a relative date
	 *
	 * @param string $string Relative Date string
	 * @return string DateTime
	 */
	public function parseDate($string = null)
	{
		if ($string == null) {
			return null;
		}

		return Carbon::parse($string);
	}

	/**
	 * Get today's date
	 * 
	 * @return string DateTime 
	 */
	public function now()
	{
		return Carbon::now();
	}

	/**
	 * Get tomorrow's date
	 * 
	 * @return string DateTime
	 */
	public function tomorrow()
	{
		return Carbon::tomorrow();
	} 

	/**
	 * Get yesterday's date
	 * 
	 * @return string DateTime
	 */
	public function yesterday()
	{
		return Carbon::yesterday();
	} 

	/**
	 * Get start of week
	 * 
	 * @return string DateTime
	 */
	public function startOfWeek()
	{
		return Carbon::parse('last monday');
	}

	/**
	 * Get end of week
	 * 
	 * @return string DateTime
	 */ 
	public function endOfWeek()
	{
		return Carbon::parse('next monday');
	}

	/**
	 * Get start of month
	 * 
	 * @return string DateTime
	 */
	public function startOfMonth()
	{
		return Carbon::create($this->year, $this->month, 1, 0, 0, 0);
	}

	/**
	 * Get end of month
	 * 
	 * @return string DateTime
	 */ 
	public function endOfMonth()
	{
		return Carbon::create($this->year, $this->month, $this->daysInMonth, 0, 0, 0);
	}

	/**
	 * Get start of year
	 * 
	 * @return string DateTime
	 */
	public function startOfYear()
	{
		return Carbon::create($this->year, 1, 1, 0, 0, 0);
	}

	/**
	 * Get end of year
	 * 
	 * @return string DateTime
	 */ 
	public function endOfYear()
	{
		return Carbon::create($this->year, 12, 31, 0, 0, 0);
	}

}