<?php

namespace Tobion\TropaionBundle\Util;

abstract class RomanNumeral
{
	/**
	 * Function to convert an arabic number ($num) to a roman numeral. $num must be between 0 and 9,999
	 * Copyright 2000 David L. Weiner <davew@webmast.com>. All Rights Reserved
	 * 
	 * @return string|mixed roman numeral as string or passed value if conversion not possible
	*/
	public static function convertIntToRoman($num) 
	{
		if (!is_numeric($num) || $num < 0 || $num > 9999) { 
			return $num; // out of range
		} 

		$r_ones = array(1=> "I", 2=>"II", 3=>"III", 4=>"IV", 5=>"V", 6=>"VI", 7=>"VII", 8=>"VIII", 9=>"IX");
		$r_tens = array(1=> "X", 2=>"XX", 3=>"XXX", 4=>"XL", 5=>"L", 6=>"LX", 7=>"LXX", 8=>"LXXX", 9=>"XC");
		$r_hund = array(1=> "C", 2=>"CC", 3=>"CCC", 4=>"CD", 5=>"D", 6=>"DC", 7=>"DCC", 8=>"DCCC", 9=>"CM");
		$r_thou = array(1=> "M", 2=>"MM", 3=>"MMM", 4=>"MMMM", 5=>"MMMMM", 6=>"MMMMMM", 7=>"MMMMMMM", 8=>"MMMMMMMM", 9=>"MMMMMMMMM");

		$ones = $num % 10;
		$tens = ($num - $ones) % 100;
		$hundreds = ($num - $tens - $ones) % 1000;
		$thou = ($num - $hundreds - $tens - $ones) % 10000;

		$tens = $tens / 10;
		$hundreds = $hundreds / 100;
		$thou = $thou / 1000;

		$rnum = '';
		if ($thou) { $rnum .= $r_thou[$thou]; }
		if ($hundreds) { $rnum .= $r_hund[$hundreds]; }
		if ($tens) { $rnum .= $r_tens[$tens]; }
		if ($ones) { $rnum .= $r_ones[$ones]; }

		return $rnum;
	}

}