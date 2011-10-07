<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BadmintonTeammatchType extends AbstractType
{
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
		$builder->add('team1_revaluated_against', 'checkbox', array(
			'required' => false
		));
		$builder->add('team2_revaluated_against', 'checkbox', array(
			'required' => false
		));
		
		$builder->add('matches', 'collection', array(
			'type' => new BadmintonMatchType(), 
			'allow_add' => false, 
			'allow_delete' => false, 
			'prototype' => false,
		));
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
