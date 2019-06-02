<?php
/**
 * Created by Vitaliy Prokhonenkov <prokhonenkov@gmail.com>
 * Date 30.04.19
 * Time 13:48
 */

namespace prokhonenkov\sitemapgenerator\interfaces;

/**
 * Interface SitemapSourceInterface
 * @package prokhonenkov\sitemapgenerator\interfaces
 */
interface SitemapSourceInterface
{
	/**
	 * It returns list of objects that implement the SitemapItemInterface
	 * @return array
	 */
	public function getSitemapItems(): array ;

	/** It returns name of sitemap
	 * @return string
	 */
	public function getSitemapName(): string ;
}