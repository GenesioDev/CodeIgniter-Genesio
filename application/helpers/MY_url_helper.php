<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter MY URL Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Genesio
 * @link
 */

// ------------------------------------------------------------------------

if ( ! function_exists('slugify'))
{
	/**
	 * Slugify
	 *
	 * Transform a text to a slug
	 *
	 * @param	string	$text
	 * @return	mixed
	 */
	function slugify($text = '')
	{
		// replace non letter or digits by -
		$slug = preg_replace('#\W+#u', '-', $text);

		// trim
		$slug = trim($slug, '-');

		// transliterate
		$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);

		// lowercase
		$slug = strtolower($slug);

		// remove unwanted characters
		$slug = preg_replace('#[^-\w]+#', '', $slug);

		if (empty($slug)) {
			return FALSE;
		}

		return $slug;
	}
}