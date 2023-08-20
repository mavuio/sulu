## typical import

```
bit import mavuio.mavu_sulu/content_logoslider --path bit_components/content_logoslider --override
```

## typical add:

```
bit add bit_components/content_logoslider --id content_logoslider --main _bit_index.js --env teambit.html/html
```

## setup:

### xml:

```
<types>
                <xi:include href="../../../../bit_components/content_logoslider/xml/logoslider.xml" xpointer="xmlns(sulu=http://schemas.sulu.io/template/template)
                                    xpointer(/sulu:template/sulu:properties/sulu:block[@name='blocks']/sulu:types/sulu:type[@name='logoslider'])" />
</types>
```

### twig:

in config/packages/twig.yaml:

```
twig:
    default_path: '%kernel.project_dir%/templates'
    paths:
        'bit_components': 'bit_components'

```

in templates/includes/blocklist.html.twig:

```
	{{ include([
        'includes/blocks/' ~ block.type ~ '.html.twig',
        '@bit_components/content_' ~ block.type ~ '/twig_includes/' ~ block.type ~ '.html.twig'
        ], {'css_id': 'el_' ~ loop.index

```
