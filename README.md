# Laravel - Crowdin-exporter

Laravel сrowdin-exporter supports functionalities to sort entries and find strings that aren't localized yet and send them
from `resources/lang/**/*` to crowdin service and also remove "unused" translations from the crowdin service..

You can export translation entries from lang folder into a **[crowdin](https://crowdin.com/)** service, edit the
translations and export them back to Laravel PHP files or database.

Laravel сrowdin-exporter also supports functionalities to sort entries and find strings that aren't localized yet.

## 🚀 Installation

You can install the package using composer

```bash
composer require pointpay/crowdin-exporter
```

Then add the service provider to `config/app.php`.  
This step *can be skipped* if package auto-discovery is enabled.

```php
'providers' => [
     Pointpay\CrowdinExporter\CrowdinExporterProvider::class
];
```

## ⚙ Publishing the config file

Publishing the config file is optional:

```bash
php artisan vendor:publish --provider="Pointpay\CrowdinExporter\CrowdinExporterProvider" --tag="crowdin-exporter"
```

## 👓 Usage

1. Set up env `CROWDIN_PROJECT_ID` and `CROWDIN_ACCESS_TOKEN`
2. Run command `php artisan pointpay-crowdin:export` find strings that aren't localized yet and send them
   from `resources/lang/**/*` to crowdin service.

## 📃 Changelog

Please see the [CHANGELOG.md](CHANGELOG.md) for more information
on what has changed recently.

## 🏅 Credits

- [Vitaliy Ilinov](https://gitlab.com/pointpayio/crowdin-exporter)
- [All Contributors](https://gitlab.com/pointpayio/crowdin-exporter/contributors)

