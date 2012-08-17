<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Tobion\TropaionBundle\Form\EventListener\CalculateTeammatchStatisticsListener;

class BadmintonTeammatchType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
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

		$builder->add('matches', 'collection', array(
			'property_path' => 'Matches',
			'type' => 'bmm',
			'allow_add' => false,
			'allow_delete' => false,
			'prototype' => false,
			'by_reference' => true,
		));

		$builder->addEventSubscriber(new CalculateTeammatchStatisticsListener());
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Tobion\TropaionBundle\Entity\Teammatch',
		));
	}

	public function getName()
	{
		return 'bmtm';
	}
}
