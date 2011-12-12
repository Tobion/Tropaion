<?php

namespace Tobion\TropaionBundle\Twig\Extension;

class TropaionExtension extends \Twig_Extension
{
	public function getFilters()
	{
		return array(
			'roman_numeral' => new \Twig_Filter_Function('\Tobion\TropaionBundle\Util\RomanNumeral::convertIntToRoman'),
			'pad' => new \Twig_Filter_Method($this, 'padFilter', array('pre_escape' => 'html', 'is_safe' => array('html'))),
			'class_attribute' => new \Twig_Filter_Method($this, 'classAttributeFilter', array('is_safe' => array('html'))),
			'class_names' => new \Twig_Filter_Method($this, 'classNamesFilter', array('is_safe' => array('html'))),
		);
	}


	public function padFilter($value, $pad_length = 2, $pad_string = '&#8199;', $pad_type = STR_PAD_LEFT)
	{
		// &#160; = &nbsp; = non-breaking space
		// &#8199; = figure space
		return str_replace('~', $pad_string, str_pad((string) $value, $pad_length, '~', $pad_type));
		// digits of number: floor(log10($number)) + 1
	}


	/**
	 * Transforms an array (usually a hash in twig) to a class attribute class="..." with the array keys as class names if the corresponding value evaluates to true.
	 * If no classes are applied then the class attribute is also not returned because an empty class attribute is invalid html.
	 * This filter can be regarded as a combination of the filters {{ hash | filter | keys | join(' ') }} but the filter 'filter' is not included in twig core anyway.
	 * Example usage
	 * {{ {
	 *     'promoted': loop.index <= league.promotedNumber, 
	 *     'relegated': loop.revindex <= league.relegatedNumber,
	 *     'withdrawn': teamStanding.withdrawn
	 * } | class_attribute }}
	 * Instead of the traditional version which is verbose and problematic because of whitespace (either hard to read expression sequences or 'spaceless' tag) 
	 * and empty class attribute (invalid html):
	 * class="{% 
	 *     if loop.index <= league.promotedNumber %}promoted {% endif %}{% 
	 *     if loop.revindex <= league.relegatedNumber %}relegated {% endif %}{% 
	 *     if teamStanding.withdrawn %}withdrawn {% endif %}"
	 *
	 */	
	public function classAttributeFilter($array)
	{
		$classes = self::classNamesFilter($array);
		return ($classes === '') ? '' : 'class="' . $classes . '"';
	}

	public function classNamesFilter($array)
	{
		// escape " and '
		// article about allowed characters in id and class attribute: http://mathiasbynens.be/notes/html5-id-class
		return htmlspecialchars(implode(' ', array_keys(array_filter($array))), ENT_QUOTES); // twig_get_array_keys_filter + twig_join_filter + twig_escape_filter
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'tropaion';
	}
}