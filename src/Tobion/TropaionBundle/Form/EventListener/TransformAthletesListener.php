<?php

namespace Tobion\TropaionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\NotValidException;

use Symfony\Bridge\Doctrine\RegistryInterface;

use Tobion\TropaionBundle\Entity\Match;

/**
 * 
 *
 * @author Tobias Schultze <http://www.tobion.de>
 */
class TransformAthletesListener implements EventSubscriberInterface
{
	protected $registry;

	public function __construct(RegistryInterface $registry)	
	{	
		$this->registry = $registry;
	}

	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA => 'onPreSetData', 
			FormEvents::BIND => 'onBindData'
		);
	}

	/**
	 * Sets the default readable ID for the athletes
	 *
	 * @param FilterDataEvent $event
	 */
	public function onPreSetData(FormEvent $event)
	{
		$match = $event->getData();
		$form = $event->getForm();

		if (null === $match) {
			return;
		}

		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		$match->team1_player_readable_id = $match->getTeam1Player() ? 
			$match->getTeam1Player()->getReadableId() :	'';
		$match->team1_partner_readable_id = $match->getTeam1Partner() ? 
			$match->getTeam1Partner()->getReadableId() : '';
		$match->team2_player_readable_id = $match->getTeam2Player() ? 
			$match->getTeam2Player()->getReadableId() :	'';
		$match->team2_partner_readable_id = $match->getTeam2Partner() ? 
			$match->getTeam2Partner()->getReadableId() : '';

		$event->setData($match);
	}

	/**
	 * Updates the match athletes according the user input
	 * Adds errors to the fields if it is ambiguous
	 *
	 * @param FilterDataEvent $event
	 */
	public function onBindData(FormEvent $event)
	{
		$match = $event->getData();
		$form = $event->getForm();

		if (null === $match) {
			return;
		}

		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		// this clears the readable id correctly on the object
		// but the form still displays the submitted data because it works on the client data
		// so we would need to do this on BIND_CLIENT_DATA event

//		if ($match->team1_noplayer) {
//			$match->team1_player_readable_id = null;
//			$match->team1_partner_readable_id = null;
//		}
//		if ($match->team2_noplayer) {
//			$match->team2_player_readable_id = null;
//			$match->team2_partner_readable_id = null;
//		}


		//echo 'BINDNORM:';
		//var_dump($match->team1_player_readable_id);
		
		//$form->get('games')->addError(new FormError('Invalid'));

		try {
			$match->setTeam1Player($this->findAthlete($match->team1_player_readable_id));
		} catch (NotValidException $e) {
			$form->get('team1_player_readable_id')->addError(new FormError($e->getMessage()));
			$match->setTeam1Player(null);
		}

		try {
			$match->setTeam2Player($this->findAthlete($match->team2_player_readable_id));
		} catch (NotValidException $e) {
			$form->get('team2_player_readable_id')->addError(new FormError($e->getMessage()));
			$match->setTeam2Player(null);
		}

		if ($form->has('team1_partner_readable_id')) {
			try {
				$match->setTeam1Partner($this->findAthlete($match->team1_partner_readable_id));
			} catch (NotValidException $e) {
				$form->get('team1_partner_readable_id')->addError(new FormError($e->getMessage()));
				$match->setTeam1Partner(null);
			}
		}

		if ($form->has('team2_partner_readable_id')) {
			try {
				$match->setTeam2Partner($this->findAthlete($match->team2_partner_readable_id));
			} catch (NotValidException $e) {
				$form->get('team2_partner_readable_id')->addError(new FormError($e->getMessage()));
				$match->setTeam2Partner(null);
			}
		}

		// echo 'Player:'.$match->getTeam2Player();

		$event->setData($match);
	}

	/**
	 * Try to fetch the entity from its id in the database
	 * 
	 * @param  string  $readableId Human-readable id
	 * 
	 * @throws NotValidException when Athlete could not be found
	 *
	 * @return Athlete|null The found Athlete or null
	 */
	private function findAthlete($readableId)
	{
		if ($readableId === null || $readableId === '') {
			return null;
		} else if (is_numeric($readableId)) {
			$name = '';
			$id = (int) $readableId;
		} else if (preg_match('/(.*)[\[\(](\d+)[\]\)]/', $readableId, $regExMatches)) {
			$name = trim($regExMatches[1]);
			$id = (int) $regExMatches[2];
		} else {
			$name = $readableId;
			$id = null;
		}

		if ($id !== null) { // find by ID
			$athlete = $this->registry->getManager()->getRepository('TobionTropaionBundle:Athlete')->find($id);
			if (!$athlete) {
				throw new NotValidException('Athlete not found by it\'s ID');
			} else if (stripos($athlete->getFullName(), $name) === false) {
				throw new NotValidException('Athlete name does not correspond to it\'s ID');
			}
			return $athlete;
		}

		// find by name TODO
		throw new NotValidException('No ID given. Name search not supported yet.');
	}
}
