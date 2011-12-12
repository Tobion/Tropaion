<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 *
 */
class BadmintonResult extends Constraint
{
    public $invalidMatchResult = 'Invalid Badminton match result';
	public $invalidGameResult = 'Invalid Badminton game result';
	public $contradictoryNoResult = 'Contradictory: no result <> match result';
	
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}
}
