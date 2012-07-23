<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Tobion\TropaionBundle\Form\EventListener\AddAthletesListener;
use Tobion\TropaionBundle\Form\EventListener\TransformAthletesListener;
use Tobion\TropaionBundle\Form\EventListener\RemoveEmptyGamesListener;

class BadmintonMatchType extends AbstractType
{
	protected $registry;

	public function __construct(RegistryInterface $registry)	
	{	
		$this->registry = $registry;
	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('noresult', 'checkbox', array(
			'required' => false
		));

		$builder->add('team1_noplayer', 'checkbox', array(
			'required' => false
		));
		$builder->add('team2_noplayer', 'checkbox', array(
			'required' => false
		));

		$builder->add('revaluation_wrongdoer', 'choice', array(
			'required' => false,
			'choices'   => array(
				'1' => 'Umw. gegen: Heim',
				'2' => 'Umw. gegen: Gast',
				'3' => 'Umw. gegen: Beide',
			),
			'multiple'  => false,
			'expanded'  => false,
			'empty_value'  => 'Umwertung?',
		));

		$builder->add('result_incident', 'choice', array(
			'required' => false,
			'choices'   => array(
				'team1_wonbydefault' => 'Kampfl. Sieg: Heim',
				'team2_wonbydefault' => 'Kampfl. Sieg: Gast',
				'team1_givenup' => 'Aufgabe: Heim',
				'team2_givenup' => 'Aufgabe: Gast',
			),
			'multiple'  => false,
			'expanded'  => false,
			'empty_value'  => 'Kampflos/Aufgabe?',
		));


		$builder->add('games', 'collection', array(
			'property_path' => 'Games',
			'type' => new BadmintonGameType(),
			'allow_add' => false,
			'allow_delete' => false,
			'prototype' => false,
			'by_reference' => true,
			'error_bubbling' => false // so we can assign errors to this collection
		));

		$builder->addEventSubscriber(new AddAthletesListener($builder->getFormFactory()));
		$builder->addEventSubscriber(new TransformAthletesListener($this->registry));
		$builder->addEventSubscriber(new RemoveEmptyGamesListener());

	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Tobion\TropaionBundle\Entity\Match',
		));
	}

	public function getName()
	{
		return 'bmm';
	}
}
