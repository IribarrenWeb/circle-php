# Circle php sdk

Circle php sdk support for laravel.

## Supported circle apis
* Payments
* Payouts

## Installation

Use the package manager composer to install the circle sdk.

```bash
composer require keinher/circle-php
```
## Oficial circle documentation
[Circle documentation](https://developers.circle.com/docs)

## Usage

Create a instance of circle sdk.
```php
use Keinher\Circle;

$circle = new Circle('sandbox',['payments' => $payments_api_key), 'payouts' => $payouts_api_key]); 
```
### - Payments api
```php
use Keinher\Circle\Entities\User;
use Keinher\Circle\Entities\Billing;

$circle->payments->create(
		array $source,
		User $userMetadata,
		float $amount,
		string $currency = 'USD',
		string $verification = 'none',
		string $description = 'Payment description',
		?array $data = null
	); // Create payment

$circle->payments->createCard(
		Billing $billing,
		string $encryptedData,
		string $keyId,
		User $userMetadata,
		string $expMonth,
		string $expYear
	); // Create card

$circle->payments->createAchBank(
		string $plaidToken,
		Billing $billing,
		User $userMetadata
	); // Create Ach bank

$circle->payments->get($id); // Get payment
$circle->payments->getCard($id); // Get card
$circle->payments->getBank($id); // Get bank
$circle->payments->cancel($id); // Cancel/refund payment
```

### - Payouts api
```php
$circle->payouts->create(
        string $type = 'ach',
        float $amount,
        string $destination_id,
        string $beneficiary_email,
        string $currency = 'USD',
        ?array $source = null
    ); // Create payout

$circle->payouts->get($id); // Get payout
```
## Contributing
Pull requests are welcome.

## License
[MIT](https://choosealicense.com/licenses/mit/)