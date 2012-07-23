<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use Tobion\TropaionBundle\Entity\Match;
use Tobion\TropaionBundle\Entity\Game;

/**
 *
 */
class AthletesSpecifiedValidator extends ConstraintValidator
{
	public function validate($match, Constraint $constraint)
	{
		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}
		
		// validate on readable_id (e.g. $match->team1_player_readable_id)
		// instead of real athlete ($match->getTeam1Player())
		// otherwise 2 errors will be raised for an incorret athlete readable id:
		// 'blank' and 'athlete could not be retrieved'

		// TODO validate athlete gender based on matchtype

		if (!$match->team1_noplayer) {
			if ($match->team1_player_readable_id === null || $match->team1_player_readable_id === '') {
				$this->context->addViolationAtSubPath('team1_player_readable_id', $constraint->invalidBlankAthlete, array(), $match->team1_player_readable_id);
			}
			if ($match->getMatchType()->getXOnX() != 1 &&
				($match->team1_partner_readable_id === null || $match->team1_partner_readable_id === '')) {
				$this->context->addViolationAtSubPath('team1_partner_readable_id', $constraint->invalidBlankAthlete, array(), $match->team1_partner_readable_id);
			}
		} else {
			if (!($match->team1_player_readable_id === null || $match->team1_player_readable_id === '')) {
				$this->context->addViolationAtSubPath('team1_player_readable_id', $constraint->invalidFilledAthlete, array(), $match->team1_player_readable_id);
			}
			if (!($match->team1_partner_readable_id === null || $match->team1_partner_readable_id === '')) {
				$this->context->addViolationAtSubPath('team1_partner_readable_id', $constraint->invalidFilledAthlete, array(), $match->team1_partner_readable_id);
			}
		}

		if (!$match->team2_noplayer) {
			if ($match->team2_player_readable_id === null || $match->team2_player_readable_id === '') {
				$this->context->addViolationAtSubPath('team2_player_readable_id', $constraint->invalidBlankAthlete, array(), $match->team2_player_readable_id);
			}
			if ($match->getMatchType()->getXOnX() != 1 &&
				($match->team2_partner_readable_id === null || $match->team2_partner_readable_id === '')) {
				$this->context->addViolationAtSubPath('team2_partner_readable_id', $constraint->invalidBlankAthlete, array(), $match->team2_partner_readable_id);
			}
		} else {
			if (!($match->team2_player_readable_id === null || $match->team2_player_readable_id === '')) {
				$this->context->addViolationAtSubPath('team2_player_readable_id', $constraint->invalidFilledAthlete, array(), $match->team2_player_readable_id);
			}
			if (!($match->team2_partner_readable_id === null || $match->team2_partner_readable_id === '')) {
				$this->context->addViolationAtSubPath('team2_partner_readable_id', $constraint->invalidFilledAthlete, array(), $match->team2_partner_readable_id);
			}
		}
	}
}
