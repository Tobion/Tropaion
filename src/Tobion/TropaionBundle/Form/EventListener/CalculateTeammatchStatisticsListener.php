<?php

namespace Tobion\TropaionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

use Tobion\TropaionBundle\Entity\Teammatch;

/**
 * 
 *
 * @author Tobias Schultze <http://www.tobion.de>
 */
class CalculateTeammatchStatisticsListener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::BIND => 'onBindData'
		);
	}

	/**
	 * Updates the teammatch statistics.
	 *
	 * @param FilterDataEvent $event
	 */
	public function onBindData(FormEvent $event)
	{
		$teammatch = $event->getData();

		if (null === $teammatch) {
			return;
		}

		if (!$teammatch instanceof Teammatch) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Teammatch');
		}

		$teammatch->updateStats();

		$event->setData($teammatch);
	}
}
