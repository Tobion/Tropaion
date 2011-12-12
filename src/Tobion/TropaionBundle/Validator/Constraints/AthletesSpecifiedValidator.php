<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Tobion\TropaionBundle\Entity\Match;
use Tobion\TropaionBundle\Entity\Game;

/**
 * 
 */
class AthletesSpecifiedValidator extends ConstraintValidator
{

	public function isValid($match, Constraint $constraint)
	{
		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}
		
		$basePath = $this->context->getPropertyPath();

		// validate on readable_id (e.g. $match->team1_player_readable_id) 
		// instead of real athlete ($match->getTeam1Player())
		// otherwise 2 errors will be raised for an incorret athlete readable id:
		// 'blank' and 'athlete could not be retrieved'

		if (!$match->team1_noplayer) {
			if ($match->team1_player_readable_id === null || $match->team1_player_readable_id === '') {
				$this->addError('.team1_player_readable_id', $constraint->invalidBlankAthlete, $basePath);
			}
			if ($match->getMatchType()->getXOnX() != 1 && 
				($match->team1_partner_readable_id === null || $match->team1_partner_readable_id === '')) {
				$this->addError('.team1_partner_readable_id', $constraint->invalidBlankAthlete, $basePath);
			}
		} else {
			if (!($match->team1_player_readable_id === null || $match->team1_player_readable_id === '')) {
				$this->addError('.team1_player_readable_id', $constraint->invalidFilledAthlete, $basePath);
			}
			if (!($match->team1_partner_readable_id === null || $match->team1_partner_readable_id === '')) {
				$this->addError('.team1_partner_readable_id', $constraint->invalidFilledAthlete, $basePath);
			}
		}

		if (!$match->team2_noplayer) {
			if ($match->team2_player_readable_id === null || $match->team2_player_readable_id === '') {
				$this->addError('.team2_player_readable_id', $constraint->invalidBlankAthlete, $basePath);
			}
			if ($match->getMatchType()->getXOnX() != 1 && 
				($match->team2_partner_readable_id === null || $match->team2_partner_readable_id === '')) {
				$this->addError('.team2_partner_readable_id', $constraint->invalidBlankAthlete, $basePath);
			}
		} else {
			if (!($match->team2_player_readable_id === null || $match->team2_player_readable_id === '')) {
				$this->addError('.team2_player_readable_id', $constraint->invalidFilledAthlete, $basePath);
			}
			if (!($match->team2_partner_readable_id === null || $match->team2_partner_readable_id === '')) {
				$this->addError('.team2_partner_readable_id', $constraint->invalidFilledAthlete, $basePath);
			}
		}

		return true;
	}
	
	
	private function addError($propertyPath, $message, $basePath = '')
	{
		$this->context->setPropertyPath($basePath . $propertyPath);
		$this->context->addViolation($message, array(), null);
	}
	

}
