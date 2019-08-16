

# iPaaS package for Laravel
This package includes
* Driver for Google stack logging
* Exception handler for Google error reporting
* Log-info (`iLog`) helper
	* To collect info on runtime through laravel service container interface
	* To render collected info and attach to each log context
* Middleware
	* To authenticate request
	* To capture initial request for logger
* Request
	* To provide additional methods with request
* Exceptions
	* To report  exception with Log-info context
	* To render exception according to _iPaaS_ set standards
* Response
	* To all context information with response
	* To render error and response  according to _iPaaS_ set standards
* Other helpers
	* Converter
	* [more coming soon]

# Setup
### i. Add Package
Run composer update after adding composer package
```php
ipaas/gapp-laravel: ~1.1.0
```
**OR;** by running

```bash
composer require ipaas/gapp-laravel
```
  
Make sure that the  
 **ENV:** LOG_CHANNEL is set to `stackdriver`; and  
 **ENV:** GCLOUD_PROJECT is set to your `Google-Cloud-Project_Id`  

# API  Documentation
## Log-info (ilog)
Helper to add context information to all log entries.

> Once context is added to ilog it will append to all future logs entries
> ilog refresh with each request and; have same life cycle as of request()

`ilog()` is a helper method returning singleton class `Ipaas\Gapp\Logger\Client.php`
To add context info just call `ilog()` and chain any method available.
Following methods are available:


|                 Method                     |              Usage              |
|--------------------------------------------|---------------------------------|
|`setClientId (string)`                         |set client id/name               |
|`setClientKey (string)`                        |set client key/token             |
|`setRequestId (string)`                        |set request id/token             |
|`setType (string)`                             |type of request                  |
|`prop ((string)value, (string)name)`           |any custom key and value         | 
|`setDate ((string⎮Carbon)value, (string)name)` |any custom date key and value    |
|`setDateFrom (string⎮Carbon)`                  |sync/request date from           |
|`setDateTo (string⎮Carbon)`                    |sync/request date to             |
|`setUuid (string⎮null)`                        |universal unique identifier      |
|`toArray()`                                    |get all info as array            |


> `iLog([data-set])` can be use to re-init* log data. 
> _*can be use to pass log-info to queue jobs_

**Example**
Following example will write log in GCloud Logging with all provided context
```php
/* ------ Class A ------- */
function validateUser(Request $request){
	// validate request
	$request->validate(['user_id'=>'integer|required']);

	// get user details
	$user = User::get($data->user_id);

	if ($user) {	
		
		/********LOG-INFO PROVIDER*******/
		iLog()                          // add user details to context
		->setClientId($user->id)        // client id to context
		->setClientKey($user->key);     // client key to context
		
		// add request details context
		iLog()->setType('Validate user name');

		// Calling other class to resolve request
		return ClassB::validateUserName($user);
	}
}

/* ------ Class B ------- */
// all set context still exist
function validateUserName(User $user){
	// log event info
	// will be logged with context
	Log::info('Validating user name')

	// validate user name
	$name = $user->name;
	if(empty($name) || is_null($name))
	{
		// log warning that name is not valid
		// will be logged all context
		Log::warning('Name is null or empty');
		return false;
	}
}
```
## Middleware

### Validation
By default this library try to validate request by checking headers:
* x-api-key (set on the `auths` table)
the system will try to match the header `x-api-key` with the `auths` table. 
_To enable_, just add the middleware `AuthAndLog` on your desirable route ```Route::get('foo', FooController@bar)->middleware('AuthAndLog')```

### Logging
By default library try to translate and log following details:
```php
$request->header('Authorization')       // Authorization value from header
$request->header('x-api-key')           // Client ID from header
$request->header('Amaka-Request-ID')    // Amaka Request ID from header
$request->uuid                          // request uuid
$request->dateFrom                      // date from
$request->dateTo                        // date to
```

## Request
Request is resolved using `Ipass/Request` controller
to use see the given example:
```php
use Ipaas\Gapp\Request;
use Ipass\Response;
class Accounts extends Response;  
{
	public function index(Request $request)  
	{  
	  $rules = ['name' => 'required|string'];  
	  $request->validate($rules);
	}
}
```

all given function are chain-able when extend method is used
```php
        $request
            ->boolify('EnablePaymentsToAccount')
            ->arrify('Type')
            ->validate($rules);
``` 

**Validate**
Validate request based on given rules set.
```php
// $rules is laravel validation rule set
Func: validate(array $rules)

Return: REQUEST if all sucessfull
Throw: Unprocessable Entity (422) if validation fails
```
**Arrify**
Convert request csv parameter to php array.
```php
// $item is request csv param
Func: arrify(string $item)

Return: modified REQUEST
```
**Boolify**
Convert request string 'true/false' parameter to php boolean.
```php
// $item is request string 'true/false'
Func: boolify(string $item)

Return: modified REQUEST
```
**Requestify**
Replace request given parameter value.
```php
// $item is request parameter name
// $value is new value
Func: requestify(string $item, mixed $value)

Return: modified REQUEST
```

## Response
> Response helper `iresponse`
or use by extending base controller `[YOUR CONTROLLER] extends Ipaas/Response.php`, with that helper you can access sendError() method too, making an exception easier.

**Set Meta**
Chain-able function to set response meta data
```php
return $this->meta(['client_id' => 'unknown'])->sendResponse($data);
```

**Set header**
Chain-able function to set response header data
```php
return $this->header(['content-type' => 'application/json'])->sendResponse($data);
```

## Other Helpers
### Converter
> Ipaas/Helper/Converter-Helpers

**Normalized Name**
Replace ASCII space unicode with ` ` space.

Input: ```te \n sting```

Response: ```te sting```

```php
// $name is unicode string
Func: normalizedName(string $name)

Return: normalized string
```
**Boolify List**
Convert given string 'true/false' parameter to php boolean in provided array.

Input: ```['true', 'false', 'TRUE', 'FALSE', true, false, TRUE, FALSE, 0, 1, '0', '1', '', ' test']```

Response:
```[true, false, true, false, true, false, true, false, false, true, false, true, false, true]```

```php
// $list is haystack array
// $item is needle name
Func: boolifyList(array $list, string $item)

Return: modified list
```

## Note
_ps. google/cloud package is required to run application on google app engine flex environment_
