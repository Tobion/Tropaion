<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Tobion\TropaionBundle\Form\EventListener\CalculateTeammatchStatisticsListener;

class BadmintonTeammatchType extends AbstractType
{
	protected $registry;

	public function __construct(RegistryInterface $registry)	
	{	
		$this->registry = $registry;
	}

	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('performed_at', 'datetime');
		$builder->add('venue', 'entity', array(
			'class' => 'Tobion\TropaionBundle\Entity\Venue'
		));
		$builder->add('description', 'textarea', array(
			'required' => false
		));
		$builder->add('annulled', 'checkbox', array(
			'required' => false
		));
		$builder->add('team1_score', 'integer', array(
			// 'precision' => 0
		));
		$builder->add('team2_score', 'integer', array(
			// 'precision' => 0
		));

		$builder->add('team1_nofight', 'checkbox', array(
			'required' => false
		));
		$builder->add('team2_nofight', 'checkbox', array(
			'required' => false
		));
		
		$builder->add('revaluation_against', 'choice', array(
			'required' => false,
			'choices'   => array(
				'team1' => 'Umw. gegen: Heim',
				'team2' => 'Umw. gegen: Gast',
				'both' => 'Umw. gegen: Beide',
			),
			'multiple'  => false,
			'expanded'  => false,
			'empty_value'  => 'Umwertung?',
		));

		$builder->add('matches', 'collection', array(
			'type' => new BadmintonMatchType($this->registry), 
			'allow_add' => false, 
			'allow_delete' => false, 
			'prototype' => false,
			'by_reference' => true,
		));

		$builder->addEventSubscriber(new CalculateTeammatchStatisticsListener());
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Tobion\TropaionBundle\Entity\Teammatch',
		);
	}

	public function getName()
	{
		return 'bmtm';
	}
}
