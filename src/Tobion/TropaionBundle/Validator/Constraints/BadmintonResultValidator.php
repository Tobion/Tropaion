<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Tobion\TropaionBundle\Entity\Match;
use Tobion\TropaionBundle\Entity\Game;

/**
 * 
 */
class BadmintonResultValidator extends ConstraintValidator
{

	public function isValid($match, Constraint $constraint)
	{
		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		if ($match->noresult) {
			// wenn noresult -> gewert. Ergebnis muss leer sein
			if (!$this->isNoResult($match)) {
				$this->addError('.noresult', $constraint->contradictoryNoResult, $this->context->getPropertyPath());
			}
		} else {
			
			$basePath = strstr($this->context->getPropertyPath(), '.', true);
			
			if (!($matchResultValid = $this->isBadmintonMatchResultValid($match))) {
				$this->addError('[games]', $constraint->invalidMatchResult, $basePath);
			}

			foreach ($match->getGames() as $index => $game) {
				if (!$game->getAnnulled()) { // do not validate annulled / given-up game
					if ($matchResultValid && $game->getTeam1Score() === null && $game->getTeam2Score() === null) {
						// do not validate empty third game as long as the match result is valid
						// drawback: this way also empty first/second set is ignored (e.g. ?:? 21:16 21:9)
						continue; 
					}
					if (!$this->isBadmintonGameResultValid($game)) {
						$this->addError("[games][$index]", $constraint->invalidGameResult, $basePath);
					}
				}
			}
			
		}		

		return true; // always true, we added the violations already
	}
	
	private function addError($propertyPath, $message, $basePath = '')
	{
		$this->context->setPropertyPath($basePath . $propertyPath);
		$this->context->addViolation($message, array(), null);
	}
	
	/**
	 * Whether the match has no result (i.e. effective games are empty)
	 *
	 * @param Match $match 
	 */
	private function isNoResult(Match $match)
	{
		foreach ($match->getEffectiveGames() as $game) {
			if ($game->getTeam1Score() !== null || $game->getTeam2Score() !== null) {
				return false;
			}
		}
		
		return true;
	}

	private function isBadmintonGameResultValid(Game $game)
	{		
		$biggerScore = max($game->getTeam1Score(), $game->getTeam2Score());
		$smallerScore = min($game->getTeam1Score(), $game->getTeam2Score());

		if ($biggerScore === 21) {
			return ($smallerScore >= 0 && $smallerScore <= 19 && is_int($smallerScore)); // is_int because NULL >= 0 -> true
		} 
		else if ($biggerScore > 21 && $biggerScore <= 29) {
			return ($smallerScore === $biggerScore - 2);
		} 
		else if ($biggerScore === 30) {
			return ($smallerScore === 28 || $smallerScore === 29);
		}

		return false;
	}
	
	/**
	 * Überprüfen auf korrektes Satzergebnis eines Badmintonspiels (z.B. kein 3:0)
	 * Nicht nur das Ergebnis, sondern auch die Reihenfolge ist wichtig. 
	 * D.h. bei 2:0 Führung sollte es keinen 3. Satz geben.
	 *
	 * @param Match $match
	 * @return boolean 
	 */
	private function isBadmintonMatchResultValid(Match $match)
	{
		$team1Games = 0;
		$team2Games = 0;
		$bestOfThreeOver = false;
		$wrongGameSequence = false;
		
		//$match->calcTeam1Games();

		foreach ($match->getEffectiveGames() as $game) {
			$wrongGameSequence = $bestOfThreeOver && ($game->getTeam1Score() !== null || $game->getTeam2Score() !== null);
			
			if ($game->getTeam1Score() !== null && $game->getTeam2Score() !== null) {
				$team1Games += $game->getTeam1Score() === $game->getTeam2Score() ? 0.5 : $game->getTeam1Score() > $game->getTeam2Score();
				$team2Games += $game->getTeam1Score() === $game->getTeam2Score() ? 0.5 : $game->getTeam1Score() < $game->getTeam2Score();
				$bestOfThreeOver = $bestOfThreeOver || $team1Games === 2 || $team2Games === 2;
			}			
		}
		
		return !($wrongGameSequence ||
			($team1Games !== 2 || $team2Games !== 0) && 
			($team1Games !== 0 || $team2Games !== 2) &&
			($team1Games !== 2 || $team2Games !== 1) &&
			($team1Games !== 1 || $team2Games !== 2)
		);
	}
}
