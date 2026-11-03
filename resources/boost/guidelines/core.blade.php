# eloquage/healthcheck-operations

Checks if the `vector` extension (operations) is enabled and active in PostgreSQL.

## Usage

```php
use IllumaLaw\HealthCheckOperations\OperationsExtensionCheck;
use Spatie\Health\Facades\Health;

Health::checks([
    OperationsExtensionCheck::new()
        ->required(true), // If true, FAIL if missing. If false, WARNING.
]);
```

## Configuration

Publish config: `php artisan vendor:publish --tag="healthcheck-operations-config"`

Options in `config/healthcheck-operations.php`:
- `required`: (bool) Global default for strictness.
