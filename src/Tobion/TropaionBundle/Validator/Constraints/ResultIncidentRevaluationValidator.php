<?php

namespace Tobion\TropaionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Tobion\TropaionBundle\Entity\Match;

/**
 * 
 */
class ResultIncidentRevaluationValidator extends ConstraintValidator
{

	public function isValid($match, Constraint $constraint)
	{
		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}
		
		$basePath = strstr($this->context->getPropertyPath(), '.', true);
		
		
		if ($match->result_incident == 'team1_wonbydefault' && !$match->revaluation_against) {
			if (!$match->isTeam1Winner()) {
				$this->addError('.noresult', $constraint->contradictoryNoResult, $this->context->getPropertyPath());
			}
		}
		
		// TODO kampfloser Sieg / Aufgabe und Umwertung passen zum Ergebnis:
		// wenn Aufgabe und nicht umgewertet -> Sieger des gewert. Ergebnisses entspr. Aufgabe
		// wenn Aufgabe und umgewertet -> urspr. Ergebnis nicht validieren, da unvollst. Ergebnis zum Zeitpunkt der Aufgabe
		// wenn kampfloser Sieg und nicht umgewertet -> richtiger Sieger und zu Null beim gewert. Ergebnis
		// wenn kampfloser Sieg und umgewertet -> richtiger Sieger und zu Null beim urspr. Ergebnis
		// wenn umgewertet (nicht gegen beide) und nicht noresult -> richtiger Sieger und zu Null beim gewert. Ergebnis
		
		// TODO evtl. nachfragen (als Flash-Hinweis nach dem Speichern zum nachträgl. Bearbeiten), ob Ergebnis richtig ist:
		// wenn nicht kampfloser Sieg (auch Aufgabe) und nicht umgewertet, aber gewert. Ergebnis zu Null
		// wenn nicht kampfloser Sieg (auch Aufgabe) und umgewertet, aber urspr. Ergebnis zu Null
		

		return true;
	}

}
