<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Foundation\Auth\Access\Authorizable;

class AuthenticatableSnowflake extends SnowflakeModel implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable, Authorizable;
}
