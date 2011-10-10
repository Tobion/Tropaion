<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BadmintonMatchType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
		$builder->add('match_type_id', 'hidden');
		
		$builder->add('team1_player_readable_id', 'text', array(
			'required' => false,
			'attr' => array(
				'class' => 'athlete'
			)
		));
		$builder->add('team1_partner_readable_id', 'text', array(
			'required' => false,
			'attr' => array(
				'class' => 'athlete'
			)
		));
		$builder->add('team2_player_readable_id', 'text', array(
			'required' => false,
			'attr' => array(
				'class' => 'athlete'
			)
		));
		$builder->add('team2_partner_readable_id', 'text', array(
			'required' => false,
			'attr' => array(
				'class' => 'athlete'
			)
		));
		
		$builder->add('noresult', 'checkbox', array(
			'required' => false
		));
		
		$builder->add('team1_noplayer', 'checkbox', array(
			'required' => false
		));
		$builder->add('team2_noplayer', 'checkbox', array(
			'required' => false
		));
		
		/*
		$builder->add('team1_revaluated_against', 'checkbox', array(
			'required' => false
		));
		$builder->add('team2_revaluated_against', 'checkbox', array(
			'required' => false
		));
		*/
		
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
			'type' => new BadmintonGameType(), 
			'allow_add' => false, 
			'allow_delete' => false, 
			'prototype' => false,
		));
		
    }
    
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'Tobion\TropaionBundle\Entity\Match',
		);
	}
	
    public function getName()
    {
		return 'bmm';
    }
}