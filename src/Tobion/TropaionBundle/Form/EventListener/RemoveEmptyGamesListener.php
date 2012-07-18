<?php

namespace Tobion\TropaionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Tobion\TropaionBundle\Entity\Match;

/**
 * This listener removes empty games from the games collection of matches.
 * It also reassigns the game_sequence of games.
 * 
 *
 * @author Tobias Schultze <http://www.tobion.de>
 */
class RemoveEmptyGamesListener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::BIND => 'onBindData'
		);
	}

	/**
	 * Inspired by MergeCollectionListener
	 * https://github.com/symfony/symfony/blob/master/src/Symfony/Bridge/Doctrine/Form/EventListener/MergeCollectionListener.php
	 *
	 * @param FilterDataEvent $event
	 */
	public function onBindData(FormEvent $event)
	{
		$match = $event->getData();

		if (null === $match) {
			return;
		}

		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		$effectiveGameSequence = 1;
		$annulledGameSequence = 1;

		foreach ($match->getGames() as $game) {
			if ($game->getTeam1Score() === null && $game->getTeam2Score() === null) {
				$match->getGames()->removeElement($game);
			} else if ($game->getAnnulled()) {
				$game->setGameSequence($annulledGameSequence);
				$annulledGameSequence++;
			} else {
				$game->setGameSequence($effectiveGameSequence);
				$effectiveGameSequence++;
			}
		}

		$event->setData($match);
	}
}
