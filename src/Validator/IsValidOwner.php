<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsValidOwner extends Constraint
{
    public $message = 'Cannot set owner to a different user.';

    public $anonymousMessage = 'Cannot set owner unless you are authenticated.';
}
