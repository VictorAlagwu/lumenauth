<?php
return [
    /*
|--------------------------------------------------------------------------
| JWT time to live
|--------------------------------------------------------------------------
|
| Specify the length of time (in minutes) that the token will be valid for.
| Defaults to 1 hour
|
*/

    'ttl' => env('JWT_TTL', 60), // valid for 1 month
];
