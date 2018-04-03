
# iPaaS package for Laravel
This package includes
* Driver for Google stack logging
* Exception handler for Google error reporting
* Log-info (`iLog`) helper
  * To collect info on runtime through laravel service container interface
  * To render collected info and attach to each log context
* Exception helpers
	* To report  exception with Log-info context
	* To render exception according to _iPaaS_ set standards
* Other helpers
  * Response helper
  * Request modifiers
  * Request helper
  * Generator
  * Converter

# Setup
Copy package files to `app/Ipaas`
Register service provider by adding
```php
App\Ipaas\Ipaas::class
```
before _Application Service Providers_ in `config/app.php`
Add log channel by adding
```php
"stackdriver" => [
	"driver" => "custom",
    "via" => \App\Ipaas\GLogger::class,
],
 ```
in `config/logging.php`
  
Make sure that the
 **ENV:** LOG_CHANNEL is set to `stackdriver`; and
**ENV:** GCLOUD_PROJECT is set to your `Google-Cloud-Project_Id`

# API  Documentation
## Log-info (ilog)
Helper to add context information to all log entries.

> Once context is added to ilog it will append to all future logs entries
> ilog refresh with each request and; have same life cycle as of request()

`ilog()` is a helper method returning singleton class `IPaaS/Info/Client.php`
To add context info just call `ilog()` and chain any method available.
Following methods are available:
|                 Method                     |              Usage              |
|--------------------------------------------|---------------------------------|
|`client (string)`                           |set client id/name               |
|`key (string)`                              |set client key/token             |
|`type (string)`                             |type of request                  |
|`prop ((string)value, (string)name)`        |any custom key and value         | 
|`date ((string|Carbon)value, (string)name)` |any custom date key and value    |
|`dateFrom (string|Carbon)`                  |sync/request date from           |
|`dateTo (string|Carbon)`                    |sync/request date to             |
|`uuid (string|null)`                        |universal unique identifier      |
|`toArray()`                                 |get all info as array            |

> `iLog([data-set])` can be use to re-init* log data. 
> _*can be use to pass log-info to queue jobs_

**Example**
Following example will write log in GCloud Logging with all provided context
```php
/* ------ Class A ------- */
function validateUser(Request $request){
	// validate request
	$data = validate(['user_id'=>'integer|required']);

	// get user details
	$user = User::get($data->user_id);

	if ($user) {	
		
		/********LOG-INFO PROVIDER*******/
		iLog() 			// add user details to context
		->client($user->id) // client id to context
		->key($user->key);  // client key to context
		
		// add request details context
		iLog()->type('Validate user name');

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

## Exceptions
>These helper function can be call directly

|                 Method                   	|                     Usage                      		|
|-------------------------------------------|-------------------------------------------------------|
|`iThrow(Exception, HTTP_CODE)`             |process and throw any exception with custom code		|
|`UnauthorizedException(Exception)`			|process and throw **Unauthorized** exception (401)		|
|`BadRequestException(Exception)`			|process and throw **Bad Request** exception (400)		|
|`TooManyRequestException(Exception)`		|process and throw **Too Many Request** exception (429)	|
|`NotFoundException(Exception)`				|process and throw **Not Found** exception (429)		|
|`InternalServerException(Exception)`		|process and throw **Internal Server** exception (429)	|

**Example**
Following exception with 
```php
/* ------ Class C ------- */

/**
* Validate information
* @param MetaData $metaData 'A laravel model'
* @return boolean
* @throws Exception
* /
function validateMetaData(MetaData $metaData){
	
	iLog()
	->data($info) // add complete model to context
	->type('simple meta validation') // add type context

	if ($metaData->method == 'advance'){
		// context override
		iLog()->type('advance meta validation');
	}
	
	// lets log event info
	Log::info('Validating meta data');
	
	// validating
	try {
		return CronClient::get($metaData);
	} catch (Exception $e) {
	
		// check if error is too many request
		if($e->getCode === 433) {
			TooManyRequestException($e); // will throw with code 429
		} else {
			InternalServerException($e); // will throw with code 500
		}
	}
}
```
## Other Helpers
### Response
> Response helper `iresponse`
or
> Response decorator controller
`[YOUR CONTROLLER] extends Ipaas/Response.php` 

**Set Meta**
Chain-able function to set response meta data
```php
return $this->meta(['client-id'=>'unknown'])->sendResponse($data);
```

**Set header**
Chain-able function to set response header data
```php
return $this->header(['content-type'=>'application/json'])->sendResponse($data);
```

**AllErrors** 
* `errorValidation()`
* `errorUnauthorized()`
* `errorBadRequest()`
* `errorTooManyRequest()`
* `errorNotFound()`
* `errorNotImplemented()`
* `errorInternalServer()`
* Main: `sendError()`


### Request
> Request helper `irequest()`  
`code/app/Ipaas/Ipaas.php`

all given function are chain-able when extend method is used
```php
        $request = irequest()
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
### Converter
> Ipaas/Helper/Converter

**Normalized Name**
Replace ASCII space unicode with ` ` space.
```php
// $name is unicode string
Func: normalizedName(string $name)

Return: normalized string
```
**Boolify List**
Convert given string 'true/false' parameter to php boolean in provided array.
```php
// $list is haystack array
// $item is needle name
Func: boolifyList(array $list, string $item)

Return: modified list
```

