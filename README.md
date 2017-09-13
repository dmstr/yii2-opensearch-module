Yii2 Opensearch Module
===============

Opensearch widgets using twig templates

Config
---

```
...
'modules => [
	'opensearch' => [
		'class'     => '\hrzg\opensearch\Module',
		'apiUrl'    => 'https://oss.dmstr.net',
		'apiKey'    => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
		'apiLogin'  => 'oss_user',
	]
]
...
```

### Usage

Examples with `yii2-prototype-module`

- [Yii 2.0 Twig extension](https://github.com/yiisoft/yii2-twig/tree/master/docs/guide)
- [Twig documentation](http://twig.sensiolabs.org/documentation)

```
{{ use ('dmstr/opensearch/widgets') }}
{{ opensearch_widget({index: 'hrzg'}) }}
```

```
{{ use ('dmstr/opensearch/widgets') }}
{{ opensearch_iframe_widget({index: 'hrzg', renderer: 'default'}) }}
```