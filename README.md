Yii 2 CMS Project Template
===============================

Yii 2 CMS Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for developing complex
Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/jonneyless/yii2-app-cms/v/stable.png)](https://packagist.org/packages/jonneyless/yii2-app-cms)
[![Total Downloads](https://poser.pugx.org/jonneyless/yii2-app-cms/downloads.png)](https://packagist.org/packages/jonneyless/yii2-app-cms)
[![Latest Unstable Version](https://poser.pugx.org/jonneyless/yii2-app-cms/v/unstable.svg)](https://packagist.org/packages/jonneyless/yii2-wechat-sdk)
[![License](https://poser.pugx.org/jonneyless/yii2-app-cms/license.svg)](https://packagist.org/packages/jonneyless/yii2-wechat-sdk)

DIRECTORY STRUCTURE
-------------------

```
apps
    backend
        assets/          contains application assets such as JavaScript and CSS
        config/          contains backend configurations
        controllers/     contains Web controller classes
        models/          contains backend-specific model classes
        runtime/         contains files generated during runtime
        views/           contains view files for the Web application
    console
        config/          contains console configurations
        controllers/     contains console controllers (commands)
        migrations/      contains database migrations
        models/          contains console-specific model classes
        runtime/         contains files generated during runtime
    frontend
        assets/          contains application assets such as JavaScript and CSS
        config/          contains frontend configurations
        controllers/     contains Web controller classes
        models/          contains frontend-specific model classes
        runtime/         contains files generated during runtime
        views/           contains view files for the Web application
        widgets/         contains frontend widgets
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
environments/            contains environment-based overrides
vendor/                  contains dependent 3rd-party packages
tests                    contains various tests for the advanced application
    codeception/         contains tests developed with Codeception PHP Testing Framework
webs
    backend/             contains the entry script and Web resources
    frontend/            contains the entry script and Web resources
```
