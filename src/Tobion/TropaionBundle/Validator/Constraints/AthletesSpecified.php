<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 */
class AthletesSpecified extends Constraint
{
    public $invalidBlankAthlete = 'Not blank';
	public $invalidFilledAthlete = 'Should be blank';
	
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
