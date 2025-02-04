# VAT Validator for UK Businesses

BillingServ

**VAT Validator** is a Composer package developed by **BillingServ** that allows UK businesses to verify VAT numbers using the [HMRC VAT API](https://developer.service.hmrc.gov.uk/api-documentation/docs/api/service/vat-registered-companies-api/2.0/oas/page#tag/organisations). This package supports both **live** and **sandbox** environments.

## Features
- Verify UK VAT numbers.
- Obtain a **consultation number** when a business needs to verify a customer's VAT number.
- Supports **live** and **sandbox** environments.
- Fully open-source and free to use.

## Installation

### Requirements
- PHP 8.0+
- Composer
- Laravel

### Install via Composer
```sh
composer require billingserv/uk-vat-validator
```

### Publish Configuration (Laravel Only)
```sh
php artisan vendor:publish --tag=config
```

## Configuration

After installation, add your **HMRC API credentials** to your `.env` file:

```ini
HMRC_CLIENT_ID=your-client-id
HMRC_CLIENT_SECRET=your-client-secret
HMRC_GRANT_TYPE=client_credentials
HMRC_SCOPE=read:vat
HMRC_USE_SANDBOX=true  # Set to false for live mode
```

## Usage

### 1. Verify a VAT Number

```php
use VatValidator\VatValidatorService;

$vatService = app(VatValidatorService::class);
$result = $vatService->verifyVatNumber('553557881');
print_r($result);
```

### 2. Obtain a Consultation Number

```php
$consultation = $vatService->getConsultationNumber('553557881', '948561936944');
print_r($consultation);
```

## Live and Sandbox Mode

Set `HMRC_USE_SANDBOX=true` in the `.env` file to enable **sandbox mode**. If set to `false`, the package will use **live mode** automatically.

## Testing

To run tests, use PHPUnit:

```sh
vendor/bin/phpunit
```

## License
This package is open-source and available for everyone to use under the **MIT License**.

## Credits
Developed by **BillingServ**. Contributions are welcome!

## Support
For issues, please open an issue on GitHub or contact **support@billingserv.com**.
