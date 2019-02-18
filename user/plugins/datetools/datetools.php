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

use Grav\Common\Plugin;

/**
 * DateTools Class
 * 
 * The DateTools Plugin provides provides date tools to use inside of Twig 
 * for filtering pages. With the release of Grav 0.9.13 `startDate` and 
 * `endDate` were introduced to collection parsing. You can use the 
 * following `datetools` to set various dates for retrieving collections. 
 *
 * @package     DateTools
 * @author      Kaleb Heitzman <kalebheitzman@gmail.com>
 * @version 	1.0.6
 * @since 		1.0.0 Initial Release
 * @todo 		Implement Date Formats
 * @todo 		Implement ICS Feeds
 * @todo 		Implement All Day Option
 */
class DateToolsPlugin extends Plugin
{
	/**
	 * Get Subscribed Events
	 *
	 * @since  1.0.0 Initial Release
	 *
	 * @return array
	 */
	public static function getSubscribedEvents() 
	{
		return [
			'onPluginsInitialized' => ['onPluginsInitialized', 0],
		];
	}

	/**
	 * Initialize plugin configuration
	 *
	 * Initialize twig site variables hook to allow formatting
	 *
	 * @since  1.0.0 Initial Release
	 *
	 * @return  void
	 */
	public function onPluginsInitialized()
	{
		if ( $this->isAdmin() ) {
			$this->active = false;
			return;
		}

		$this->enable([
//			'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
			'onTwigPageVariables' => ['onTwigSiteVariables', 0]
		]);
	}

	/**
	 * Add datetools variable 
	 *
	 * Add datetools variable to twig templates for use in filtering 
	 * collections
	 *
	 * @since  1.0.0 Initial Release
	 *
	 * @return void
	 */
	public function onTwigSiteVariables()
	{
		require_once __DIR__ . '/classes/datetools.php';

		$args = [];
		$args['config'] = $this->grav['config'];

		$twig = $this->grav['twig'];
		$twig->twig_vars['datetools'] = new DateTools($args);
		//$twig->twig->addGlobal('datetools', new DateTools($args));
	}

}