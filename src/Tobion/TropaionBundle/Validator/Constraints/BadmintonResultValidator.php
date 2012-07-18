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
class BadmintonResultValidator extends ConstraintValidator
{
	public function validate($match, Constraint $constraint)
	{
		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		if ($match->noresult) {
			// wenn noresult -> gewert. Ergebnis muss leer sein
			if (count($match->getEffectiveGames()) > 0) {
                //var_dump($this->context->getPropertyPath());
                $this->context->addViolationAtSubPath('noresult', $constraint->contradictoryNoResult, array(), $match->noresult);
			}
		} else {
			if (!$this->isBadmintonMatchResultValid($match)) {
                $this->context->addViolationAtSubPath('[games]', $constraint->invalidMatchResult);
			}

			foreach ($match->getGames() as $index => $game) {
				if (!$game->getAnnulled()) { // do not validate annulled / given-up game
					if (!$this->isBadmintonGameResultValid($game)) {
                        $this->context->addViolationAtSubPath("[games][$index]", $constraint->invalidGameResult, array(), $game);
					}
				}
			}
		}
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
	 * @see isGameSequenceValid
	 *
	 * @param Match $match
	 * @return boolean
	 */
	private function isBadmintonMatchResultValid(Match $match)
	{
		$numberGames = count($match->getEffectiveGames());

		if ($numberGames === 2) {
			return ($match->getTeam1Score() === 2 && $match->getTeam2Score() === 0)
				|| ($match->getTeam1Score() === 0 && $match->getTeam2Score() === 2);

		} else if ($numberGames === 3) {
			return (($match->getTeam1Score() === 2 && $match->getTeam2Score() === 1)
					|| ($match->getTeam1Score() === 1 && $match->getTeam2Score() === 2))
				&& $this->isGameSequenceValid($match->getEffectiveGames(), 3);
		} else {
			return false;
		}
		
	}

	/**
	 * Validates whether the games results are in correct sequence in a best-of competition
	 * The "best-of" format refers to a head-to-head competition where the
	 * two competitors compete to first win the majority of the games allotted to win the "series".
	 * If a competitor wins a majority of the games, the remaining games are discarded.
	 * Example: In a best-of-three playoff there should not be a third game when one team
	 * already won the first two games.
	 *
	 * @param array $games array of Games
	 * @param integer $bestOf best-of format
	 * @return boolean
	 */
	private function isGameSequenceValid($games, $bestOf = 3)
	{
		$team1Games = 0;
		$team2Games = 0;
		$bestOfOver = $bestOf == 0;
		$firstTo = floor($bestOf / 2) + 1;

		foreach ($games as $game) {
			if ($bestOfOver) {
				return false;
			}

			$team1Games += $game->isDraw() ? 0.5 : $game->isTeam1Winner();
			$team2Games += $game->isDraw() ? 0.5 : $game->isTeam2Winner();

			$bestOfOver = $bestOfOver || $team1Games === $firstTo || $team2Games === $firstTo;
		}

		return true;
	}
}
