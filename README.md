# WordpressApi

Simple class for making authenticated requests to the WordPress REST API.  Not affiliated with WordPress.  Currently only supports basic auth.


## Usage

```php
use cjrasmussen\WordpressApi\WordpressApi;

$wpApi = new WordpressApi($url);

// SET BASIC AUTH TOKEN
$wpApi->setAuthBasicToken('jfheriberwoifnoewigah');

// GET THE DATA ABOUT A SPECIFIED POST
$data = $wpApi->request('GET', "v2/posts/{$post_id}");

// SET BASIC AUTH TOKEN VIA USERNAME AND PASSWORD
$wpApi->setAuthUserPass('someuser', '1insecurepassword');

// DELETE A SPECIFIED MEDIA ITEM
$ipb->request('DELETE', "/v2/media/{$media_id}");
```

## Installation

Simply add a dependency on cjrasmussen/wordpress-api to your composer.json file if you use [Composer](https://getcomposer.org/) to manage the dependencies of your project:

```sh
composer require cjrasmussen/wordpress-api
```

Although it's recommended to use Composer, you can actually include the file(s) any way you want.


## License

WordpressApi is [MIT](http://opensource.org/licenses/MIT) licensed.