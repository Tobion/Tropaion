<?php

namespace Tobion\TropaionBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

use Tobion\TropaionBundle\Entity\Match;

/**
 * Adds form fields for athletes depending on the match type
 *
 * @author Tobias Schultze <http://www.tobion.de>
 */
class AddAthletesListener implements EventSubscriberInterface
{
	private $factory;

	public function __construct(FormFactoryInterface $factory)
	{
		$this->factory = $factory;
	}

	public static function getSubscribedEvents()
	{
		// Tells the dispatcher that we want to listen on the form.pre_set_data
		// event and that the preSetData method should be called.
		return array(FormEvents::PRE_SET_DATA => 'preSetData');
	}

	public function preSetData(DataEvent $event)
	{
		$match = $event->getData();
		$form = $event->getForm();

		// During form creation setData() is called with null as an argument
		// by the FormBuilder constructor. We're only concerned with when
		// setData is called with an actual Entity object in it (whether new,
		// or fetched with Doctrine). This if statement let's us skip right
		// over the null condition.
		if (null === $match) {
			return;
		}

		if (!$match instanceof Match) {
			throw new UnexpectedTypeException($match, 'Tobion\TropaionBundle\Entity\Match');
		}

		$matchType = $match->getMatchType();

		$fieldOptions = array(
			'required' => false,
			'attr' => array(
				'class' => 'athlete',
				'data-gender' => $matchType->getGender() == 0 ? 'both' : 
					($matchType->getGender() == 2 ? 'female' : 'male'),
				'data-discipline' => $matchType->getDiscipline()
			)
		);

		$form->add($this->factory->createNamed('text', 'team1_player_readable_id', null, $fieldOptions));
		$form->add($this->factory->createNamed('text', 'team2_player_readable_id', null, $fieldOptions));

		// in doubles add second field for both teams
		if ($match->getMatchType()->getXOnX() != 1) {
			// second athlete is female when match type has mixed gender
			$fieldOptions['attr']['data-gender'] = $matchType->getGender() == 0 ? 'both' : 
				($matchType->getGender() == 1 ? 'male' : 'female');

			$form->add($this->factory->createNamed('text', 'team1_partner_readable_id', null, $fieldOptions));
			$form->add($this->factory->createNamed('text', 'team2_partner_readable_id', null, $fieldOptions));
		}
	}
}
