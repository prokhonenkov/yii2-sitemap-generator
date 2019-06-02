<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 30.04.19
 * Time 13:48
 */

namespace prokhonenkov\sitemapgenerator\interfaces;

/**
 * Interface SitemapItemInterface
 * @package prokhonenkov\sitemapgenerator\interfaces
 */
interface SitemapItemInterface
{
	/**
	 * @return int
	 */
	public function getLastModified(): int ;

	/**
	 * @param null $index
	 * @return string
	 */
	public function getLocation($index = null): string ;

	/**+
	 * @return string
	 */
	public function getChangeFrequency(): string ;

	/**
	 * @return string
	 */
	public function getPriority(): string ;
}