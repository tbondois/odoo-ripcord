# Ripoo (Ripcord Odoo)

Ripoo is a PHP7 XML-RPC client handler for [Odoo][1]. 

Fork of [robroypt/odoo-client][2], using [darkaonline/ripcord][5], the library used in example in the [Odoo External API documentation for PHP][6].

## Supported versions

This library should work with all versions of Odoo, **at least between 8.0 and 11.0**, Community & Enterprise Editions. It can be used in all PHP frameworks, like Symfony, Laravel or Magento2.
If you find any incompatibilities, please create an issue or submit a pull request.

## Installation

```bash
composer require tbondois/odoo-ripcord
```

## Usage

- Instantiate a new client via instance itself :

```php
use Ripoo\ClientHandler;
........
$host = 'example.odoo.com:8080';
$db = 'example-database';
$user = 'user@email.com';
$password = 'yourpassword';

$client = new ClientHandler($host, $db, $user, $password);
```
- Or you can instanciate new client via ClientFactory, to centralize configuration use good Design Patterns . 

Basic sample for Magento2 :

```php
class OdooManager
{
    private $clientFactory;
    private $client;
    ...
    
    function __construct(
        \Ripoo\ClientHandlerFactory $clientFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->clientFactory = $clientFactory;
        $this->scopeConfig   = $scopeConfig;
    }

    public function createClient() : \Ripoo\ClientHandler
    {
        // TODO secure injections
        $odooUrl  = $this->scopeConfig->getValue('my/settings/odoo_url');
        $odooDb   = $this->scopeConfig->getValue('my/settings/odoo_database');
        $odooUser = $this->scopeConfig->getValue('my/settings/odoo_user');
        $odooPwd  = $this->scopeConfig->getValue('my/settings/odoo_pwd');

        $this->client = $this->clientFactory->create(
            $host,
            $odooDb,
            $odooUser,
            $odooPwd
        );
        return $this->client;
    }
    
    public function getClient() : \Ripoo\ClientHandler
    {
        // You can force nenewing a Client based on createdAt
        if (!$this->client) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }
}
```

For the client to work you have to exclude the `http://` and `/xmlrpc/2` parts in the url. If you want to use another Odoo API, put it in the optional 5th parameter of constructor.

### xmlrpc/2/common endpoint

Getting version information:

```php
$client->version();
```

There is no login/authenticate method. The client does authentication for you, that is why the credentials are passed as constructor arguments.

### xmlrpc/2/object endpoint

Search for records:

```php
$criteria = [
  ['customer', '=', true],
];
$offset = 0;
$limit = 10;

$client->search('res.partner', $criteria, $offset, $limit);
```

Search and count records.

```php
$criteria = [
  ['customer', '=', true],
];

$client->search_count('res.partner', $criteria);
```

Reading records:

```php
$ids = $client->search('res.partner', [['customer', '=', true]], 0, 10);

$fields = ['name', 'email', 'customer'];

$customers = $client->read('res.partner', $ids, $fields);
```

Search and Read records:

```php
$criteria = [
  ['customer', '=', true],
];

$fields = ['name', 'email', 'customer'];

$customers = $client->search_read('res.partner', $criteria, $fields, 10);
```

Creating records:

```php
$data = [
  'name' => 'John Doe',
  'email' => 'foo@bar.com',
];

$id = $client->create('res.partner', $data);
```

Updating records:

```php
// change email address of user with current email address foo@bar.com
$ids = $client->search('res.partner', [['email', '=', 'foo@bar.com']], 0, 1);

$client->write('res.partner', $ids, ['email' => 'baz@quux.com']);

// 'uncustomer' the first 10 customers
$ids = $client->search('res.partner', [['customer', '=', true]], 0, 10);

$client->write('res.partner', $ids, ['customer' => false]);
```

Deleting records:

```php
$ids = $client->search('res.partner', [['email', '=', 'baz@quuz.com']], 0, 1);

$client->unlink('res.partner', $ids);
```

# How to get list of models

- Model `ir.models` will return a list of reachable models.
- You can also use `erppeek` :
```bash
sudo pip install -U erppeek
erppeek  --server=http://odoo.example.com -d your_db -u admin -p password
your_db >>> models()
```


[1]: https://www.odoo.com/
[2]: https://github.com/robroypt/odoo-client
[5]: https://github.com/DarkaOnLine/Ripcord
[6]: https://www.odoo.com/documentation/11.0/api_integration.html

# License
MIT License. Copyright (c) 2018 Thomas Bondois.
