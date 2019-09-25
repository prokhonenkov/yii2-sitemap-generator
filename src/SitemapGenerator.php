<?php

namespace prokhonenkov\sitemapgenerator;

use prokhonenkov\sitemapgenerator\interfaces\SitemapItemInterface;
use prokhonenkov\sitemapgenerator\interfaces\SitemapSourceInterface;
use samdark\sitemap\Index;
use samdark\sitemap\Sitemap;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * Class SitemapGenerator
 * @package prokhonenkov\sitemapgenerator
 */
class SitemapGenerator extends Component
{
	/**
	 * @var string
	 */
	public $sitemapPath = null;
	/**
	 * @var array
	 */
	public $models = [];
	/**
	 * @var array
	 */
	public $languages = [];
	/**
	 * @var null
	 */
	public $baseUrl = null;
	/**
	 * @var string
	 */
	public $siteMmapName = 'sitemap.xml';
	/**
	 * @var string
	 */
	private $sitemapFolder = 'sitemap';

	/**
	 * @throws InvalidConfigException
	 */
	public function init()
	{
		if(!$this->baseUrl) {
			throw new InvalidConfigException('basuUrl is required');
		}

		if(!$this->models) {
			throw new InvalidConfigException('models is required');
		}

		foreach ($this->models as $model) {
			if(!is_subclass_of($model, SitemapSourceInterface::class)) {
				throw new \Exception('model must implements SitemapSourceInterface');
			}
		}
	}

	/**
	 * @throws \Exception
	 */
	public function generate()
	{
		/** @var array $indexes */
		$indexes = $this->languages;
		if(!$indexes) {
			$indexes = [null];
		}

		$sitemapFolder = $this->getSitemapFolderPath($this->sitemapFolder) ;

		$sitemapUrl = $this->getSitemapUrl($this->sitemapFolder);

		$node = new Index($this->getSitemapPath());

		$lastModifiedIndex = [];

		foreach ($indexes as $index) {
			$indexFileName = $index . '.xml';
			$sitemapFolderIndex = $this->getSitemapFolderPath($index, $sitemapFolder);

			$sitemapUrlIndex = $this->getSitemapUrl($index, $sitemapUrl);

			$nodeIndex = new Index($sitemapFolder . $indexFileName);

			/** @var string $modelClass */
			foreach ($this->models as $modelClass) {
				/** @var SitemapSourceInterface $model */
				$model = new $modelClass;

				$sitemapFolderModel = $this->getSitemapFolderPath($model->getSitemapName(), $sitemapFolderIndex);

				$sitemapUrlModel = $this->getSitemapUrl($model->getSitemapName(), $sitemapUrlIndex);

				$itemsByYear = [];
				$lastModifiedModel = [];

				/** @var SitemapItemInterface $item */
				foreach ($model->getSitemapItems() as $item) {
					if(!$item instanceof SitemapItemInterface) {
						throw new \Exception('model must implements SitemapItemInterface');
					}

					/** @var mixed $index */
					$lastModifiedIndex[] = $item->getLastModified();
					$lastModifiedModel[] = $item->getLastModified();
					$itemsByYear[date('Y', $item->getLastModified())][] = $item;
				}
				/**
				 * @var int $year
				 * @var array $items
				 */
				foreach ($itemsByYear as $year => $items) {
					$yearFileName = $year . '.xml';
					$lastModifiedYear = [];

					$sitemap = new Sitemap($sitemapFolderModel . $yearFileName);

					foreach ($items as $item) {
						$lastModifiedYear[] = $item->getLastModified();
						$sitemap->addItem($item->getLocation($index), $item->getLastModified(), $item->getChangeFrequency(), $item->getPriority());
					}
					$sitemap->write();
					$node->addSitemap($sitemapUrlModel . $yearFileName, max($lastModifiedYear));
				}
			}
		}
		$node->write();
	}

	/**
	 * @return bool|string
	 */
	private function getSitemapPath()
	{
		return \Yii::getAlias(
			$this->sitemapPath .
			DIRECTORY_SEPARATOR .
			$this->siteMmapName
		);
	}

	/**
	 * @param $folder
	 * @param null $path
	 * @return string
	 * @throws \yii\base\Exception
	 */
	private function getSitemapFolderPath($folder, $path = null): string
	{
		if(!$path) {
			$path = \Yii::getAlias($this->sitemapPath) .
				DIRECTORY_SEPARATOR .
				$folder .
				DIRECTORY_SEPARATOR;
		} else {
			$path = $path . $folder . DIRECTORY_SEPARATOR;
		}

		FileHelper::createDirectory($path);

		return $path;
	}

	/**
	 * @param $folder
	 * @param null $url
	 * @return string
	 */
	private function getSitemapUrl($folder, $url = null): string
	{
		if(!$url) {
			$url = $this->baseUrl . '/' . $folder . '/';
		} else {
			$url = $url . $folder . '/';
		}

		return $url;
	}
}
