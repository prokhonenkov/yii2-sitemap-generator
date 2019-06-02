Sitemap generator
==============
This extension generates sitemap.xml file. The extension uses https://github.com/samdark/sitemap. 

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar composer require prokhonenkov/yii2-sitemap-generator
```

or add

```
"prokhonenkov/yii2-sitemap-generator": "*"
```

to the require section of your `composer.json` file.

Configuration
-------------

Add component declaration to your config file for web config:
```php
<?php

return [
    // ... your config
    'components' => [
        'sitemap' => [
            'class' => \prokhonenkov\sitemapgenerator\SitemapGenerator::class, // The class which implements SitemapSourceInterface, SitemapItemInterface
            'baseUrl' => 'https://sitename.com',
            'sitemapPath' => '@webroot',
            'models' => [
                \app\models\PostsSitemap::class,
                \app\models\ProductsSitemap::class,
            ],
            'lanuages' => [
                'ru-RU',
                'en-US',
                'kk-KZ',
            ],
        ]
    ]
];

```

You need to create two classes. First class should to implements SitemapSourceInterface, SitemapItemInterface and second class should implement SitemapItemInterface:
```php
<?php

use app\modules\posts\models\Posts;
use prokhonenkov\sitemapgenerator\interfaces\SitemapItemInterface;
use prokhonenkov\sitemapgenerator\interfaces\SitemapSourceInterface;
use samdark\sitemap\Sitemap;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class PostsSitemap extends Posts implements SitemapSourceInterface, SitemapItemInterface
{
	public function getSitemapItems(): array
	{
		return ArrayHelper::merge([new PostsSitemapItem()], self::find()->all());
	}

	public function getSitemapName(): string
	{
		return 'posts';
	}

	public function getLastModified(): int
	{
		return strtotime($this->updated_at);
	}

	public function getLocation($index = null): string
	{
		return Url::to(['/posts/view', 'id' => $this->id], true);
	}

	public function getChangeFrequency(): string
	{
		return Sitemap::MONTHLY;
	}

	public function getPriority(): string
	{
		return 0.5;
	}
}

```

```php
<?php

use app\modules\posts\models\Posts;
use prokhonenkov\sitemapgenerator\interfaces\SitemapItemInterface;
use samdark\sitemap\Sitemap;
use yii\helpers\Url;

class PostsSitemapItem extends Posts implements SitemapItemInterface
{
	public function getLastModified(): int
	{
		return strtotime( Posts::find()
			->max('updated_at'));
	}

	public function getLocation($language = null): string
	{
		\Yii::$app->language = $language;
		return Url::to(['/posts/index'], true);
	}

	public function getPriority(): string
	{
		return 1;
	}

	public function getChangeFrequency(): string
	{
		return Sitemap::DAILY;
	}
}

```

Usage
-----

Put this code in your ActiveRecord model in afterSave method: 
```php
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    \Yii::$app->sitemap->generate();
}
```

Eventually will be created a structure of files:

![screnshot](https://uc81ffaa5452e2f5eee66466d57e.previews.dropboxusercontent.com/p/thumb/AAe2C7m8U5vmbCtQzkNSjntCoBr_LU8X5wEWHcSIxKTHJdOnZXMykIRtb6G_AWFfBWhCSlcB5zpLMWilv35rq1hxBXlAWeduxO6hg0hKbcWMfQLXgxrS_6xTwGIesw2bZdunC1A10q8waTJSwZa-az3cGM49qegyKmO663KzLJfcV6ks_-0BPWdtfFvOmrNGhv64g4Dz1g1NUX0FvOvQDZ9N4NL_MZRE9A6_oayUKVz21aDOt-wxFhF5-11Wl4u1xgdWNmiddmMf5HhCQvPV1BApWObOOP4Nsw6iMsCcn66sliSY5qB-SewrKAXUt8NFq2vdYdA2mOTxbCaCTIh9ntq7BZHNpVQLZba9Nzb3y-iB3CnAKQYSbpxN0R1uyedXZJM/p.png)  