# DynFormBundle

Symfony bundle for dynamic forms

__experimental version!__


#### Dependencies:

* symfony/framework-standard-edition "~2.8 | ~3.0"


### Quick setup:

* create project with Symfony framework

* composer require b3da/dyn-form-bundle "dev-master"

* `AppKernel.php`
```php
new b3da\DynFormBundle\b3daDynFormBundle(),
```

* `routing.yml`
```yml
b3da_dyn_form:
    resource: "@b3daDynFormBundle/Resources/config/routing.yml"
```

* `config.yml`
```yml
# uncomment translator
translator:     { fallbacks: ["%locale%"] }
# add Bootstrap form theme under Twig configuration
twig:
    form_themes: ['bootstrap_3_horizontal_layout.html.twig']
```

* _create_ __database__ and __schema__, _clear_ __cache__, _install_ __assets__;

* navigate to `/forms/`