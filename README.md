
# AlpineRestApi

Wordpress rest api. Easy to make and customise

Supported method
```
    'GET',
    'POST',
    'PUT',
    'PATTCH',
    'DELETE',
    'PURGE',
    'UNLINK',
    'HEAD',
```

Method parameter 

    Route::get('namespace', 'endpoint', 'callback',  'permission_callback');

Example

``` php    
use Sagar290\RestApi\AlpineRestApi as Route 

function method($request) {
    $name  =  $request['name'];
    wp_send_json_success($name, 200);
}

Route::post('namespace/v1', '/addProduct', "method");
```

You can access this by 

Body
``` json	
{
    "name": "sagar"
}
```    
    POST https://yourdomin.com/wp-json/namespace/v1/addProduct
Response
``` json
{

    "success":  true,
    "data":  "Sagar"

}
```