<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 */
class ResultIncidentRevaluation extends Constraint
{
    public $invalidMatchResult = 'Invalid Badminton match result';
	public $invalidGameResult = 'Invalid Badminton game result';
	
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
