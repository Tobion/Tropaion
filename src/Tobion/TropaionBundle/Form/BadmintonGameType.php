<?php

namespace Tobion\TropaionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BadmintonGameType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('annulled', 'hidden');

		$builder->add('team1_score', 'integer', array(
			// 'precision' => 0, // for 'number' field type
			'required' => false,
			'attr' => array(
				'class' => 'score team1',
				'min' => 0,
				'max' => 30,
				'autocomplete' => 'off'
			)
		));
		$builder->add('team2_score', 'integer', array(
			// 'precision' => 0, // for 'number' field type
			'required' => false,
			'attr' => array(
				'class' => 'score team2',
				'min' => 0,
				'max' => 30,
				'autocomplete' => 'off'
			)
		));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'Tobion\TropaionBundle\Entity\Game',
			'error_bubbling' => false
		));
	}

	public function getName()
	{
		return 'bmg';
	}
}
