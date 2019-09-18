<?php
/**
 *                  __ _           _ _           _    _
 *                 / _| |         | | |         | |  | |
 *   ___ _ __ __ _| |_| |_ ___  __| | |__  _   _| | _| |__
 *  / __| '__/ _` |  _| __/ _ \/ _` | '_ \| | | | |/ / '_ \
 * | (__| | | (_| | | | ||  __/ (_| | |_) | |_| |   <| | | |
 *  \___|_|  \__,_|_|  \__\___|\__,_|_.__/ \__, |_|\_\_| |_|
 *                                          __/ |
 * Designed + Developed by Kaleb Heitzman  |___/
 * (c) 2016
 */
namespace Calendar;

/**
 * Events Plugin Calendar Class
 *
 * The Events Calendar Class provides variables for Twig to create a dynamic
 * calendar with previous and next links that relate to month and year. This
 * class is also used to display a traditional calendar and form the rows
 * and columns that make up the calendar. It does not calculate dates or
 * manipulate any information. It's simply for displaying a nice **calendar
 * page** on your Grav website. It is referenced under the
 * `onTwigSiteVariables` hook in the root events plugin file.
 *
 * @package    Events
 * @author     Kaleb Heitzman <kalebheitzman@gmail.com>
 * @copyright  2016 Kaleb Heitzman
 * @license    https://opensource.org/licenses/MIT MIT
 * @version    1.0.15
 * @link       https://github.com/kalebheitzman/grav-plugin-events
 * @since      1.0.0 Initial Release
 */
class CalendarProcessor
{

	function getStartOfWeek($date){
		while(date_format($date, "N") != 1){ // 1 == monday
			$date->modify('-1 day');
		}
		return $date;
	}

	function getSundayAfterEndOfMonth($date){
		$numOfDaysInMonth = date_format($date, "t");
		$date->modify('+' . $numOfDaysInMonth . 'days');

		while(date_format($date, "N") != 7){ // 7 == sunday
			$date->modify('+1 day');
		}
		return $date;
	}

	/**
	 * Twig Calendar Vars
	 *
	 * Adds a url to the event header and stores each event in an associative
	 * array that can be accessed from `calendar.html.twig` via **year,
	 * month, and day** params. Here is an example of accessing a particular
	 * day on the calendar.
	 *
	 * ```twig
	 * {% for events in calendar.events[calendar.year][calendar.month][day] %}
	 *  	{% for event in events %}
   *          {% if event.title %}
   *              <div class="event"><a href="{{ event.url }}">{{ event.title }}</a></div>
   *          {% endif %}
   *      {% endfor %}
	 * {% endfor %}
	 * ```
	 *
	 * @since  1.0.0 Initial Release
	 * @param  object $collection Grav Collection
	 * @return array              Calendar variables for Twig
	 */
	public function calendarVars( $collection )
	{
		// build a calendar array to use in twig
		$calendar = array();

		$collection->order('date', 'asc');

		foreach($collection as $event) {
			$header = $event->header();
			$start = $header->start;
			$end = $header->end;
			//$meetTime = $header->meetTime;
			$i = 0;
			while ($start <= $end){
				
				// build dates to create an associate array
				$dateStart = date_create($start);
				$timestamp = $dateStart->getTimestamp();

				$eventItem = $event->toArray();
				$eventItem['header']['url'] = $event->url();

				// add the event to the calendar
				$calendar[$timestamp][] = $eventItem;

				// $start++
				$start = date('Y-m-d',strtotime($start . "+1 days"));
			} 
		}

		return $calendar;
	}

	/**
	 * Twig Display Vars
	 *
	 * Returns vars used to navigate and display content in the calendar twig
	 * template. **Past, present, and future** vars are provided to twig for
	 * creating custom navigation ui's. Below is a listing of some of the
	 * variables that are available.
	 *
	 * ```twig
	 * {% calendar.prevYear %}
	 * {% calendar.nextYear %}
	 * {% calendar.daysInMonth %}
	 * {% calendar.currentDay %}
	 * {% calendar.date %}
	 * {% calendar.year %}
	 * {% calendar.month %}
	 * {% calendar.day %}
	 * {% calendar.next %}
	 * {% calendar.prev %}
	 * ```
	 *
	 * @param  object $yearParam  	Grav URI `year:` param
	 * @param  object $monthParam  	Grav URI `month:` param
	 * @since  1.0.0. Initial Release
	 * @return array              	Twig Array
	 */
	public function twigVars($yearParam, $monthParam)
	{
		if ( $yearParam === false ) {
			$yearParam = date('Y');
		}

		if ( $monthParam === false ) {
			$monthParam = date('m');
		}

		$monthYearString = "${yearParam}-${monthParam}-01";
		$date = date_create($monthYearString);

		$startDay = $this->getStartOfWeek(clone $date);
		$endDay = $this->getSundayAfterEndOfMonth(clone $date);

		// add vars for use in the calendar twig var
		$twigVars['calendar']['daysInMonth'] = (clone $date)->format("t");
		$twigVars['calendar']['selectedMonth'] = (clone $date);
		$twigVars['calendar']['startDay'] = $startDay;
		$twigVars['calendar']['endDay'] = $endDay;
		$twigVars['calendar']['weekCount'] = (date_diff($startDay, $endDay)->days + 1)/7;


		
		// current dates
		$twigVars['calendar']['year'] = (clone $date)->format("Y");
		$twigVars['calendar']['month'] = (clone $date)->format("m");

		// months
		$twigVars['calendar']['nextMonth'] = (clone $date)->modify("+1 month");
		$twigVars['calendar']['prevMonth'] = (clone $date)->modify("-1 month");

		// years
		$twigVars['calendar']['prevYear'] = (clone $date)->modify("-1 year");
		$twigVars['calendar']['nextYear'] = (clone $date)->modify("+1 year");

		return $twigVars;
	}
}
