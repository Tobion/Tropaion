
(function($) {
/*
 * Function: fnGetColumnData
 * Purpose:  Return an array of table values from a particular column.
 * Returns:  array string: 1d data array 
 * Inputs:   object:oSettings - dataTable settings object. This is always the last argument past to the function
 *           int:iColumn - the id of the column to extract the data from
 *           bool:bUnique - optional - if set to false duplicated values are not filtered out
 *           bool:bFiltered - optional - if set to false all the table data is used (not only the filtered)
 *           bool:bIgnoreEmpty - optional - if set to false empty values are not filtered from the result array
 * Author:   Benedikt Forchhammer <b.forchhammer /AT\ mind2.de>
 */
$.fn.dataTableExt.oApi.fnGetColumnData = function ( oSettings, iColumn, bUnique, bFiltered, bIgnoreEmpty, bStripHtml, oRegExExtractor ) {
	// check that we have a column id
	if ( typeof iColumn == "undefined" ) return new Array();
	
	// by default we only want unique data
	if ( typeof bUnique == "undefined" ) bUnique = true;
	
	// by default we do want to only look at filtered data
	if ( typeof bFiltered == "undefined" ) bFiltered = true;
	
	// by default we do not want to include empty values
	if ( typeof bIgnoreEmpty == "undefined" ) bIgnoreEmpty = true;
	
	// by default we strip html tags
	if ( typeof bStripHtml == "undefined" ) bStripHtml = true;
	
	// list of rows which we're going to loop through
	var aiRows;
	
	// use only filtered rows
	if (bFiltered == true) aiRows = oSettings.aiDisplay; 
	// use all rows
	else aiRows = oSettings.aiDisplayMaster; // all row numbers

	// set up data array	
	var asResultData = new Array();
	
	for (var i=0,c=aiRows.length; i<c; i++) {
		iRow = aiRows[i];
		var aData = this.fnGetData(iRow);
		var sValue = aData[iColumn];
		
		if (!(typeof oRegExExtractor == "undefined" || oRegExExtractor === null)) {
			var aHits= oRegExExtractor.exec(sValue);
			sValue = aHits[1];
		}
		
		if (bStripHtml == true) sValue = sValue.replace( /<.*?>/g, "" );
		
		// ignore empty values?
		if (bIgnoreEmpty == true && sValue.length == 0) continue;

		// ignore unique values?
		else if (bUnique == true && jQuery.inArray(sValue, asResultData) > -1) continue;
		
		// else push the value onto the result data array
		else asResultData.push(sValue);
	}
	
	return asResultData;
}}(jQuery));


function numsort (a, b) {
	return a - b;
}


function fnAppendOptions(sSelectSelector, aData)
{
	var r = '', i, iLen = aData.length;
	for ( i = 0 ; i < iLen ; i++ )
	{
		r += '<option value="'+aData[i]+'">'+aData[i]+'</option>';
	}
	$(sSelectSelector).append(r);
}
	
	
/* Custom type sorting functions */

jQuery.fn.dataTableExt.oSort['time-element-asc']  = function(a,b) {
	var x = Date.parse(a.match(/datetime="(.*?)"/)[1]);
	var y = Date.parse(b.match(/datetime="(.*?)"/)[1]);
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['time-element-desc'] = function(a,b) {
	var x = Date.parse(a.match(/datetime="(.*?)"/)[1]);
	var y = Date.parse(b.match(/datetime="(.*?)"/)[1]);
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
		
jQuery.fn.dataTableExt.oSort['margin-of-victory-asc']  = function(a,b) {
	var x = a.match(/data-mov="(.*?)"/)[1];
	var y = b.match(/data-mov="(.*?)"/)[1];
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
		
jQuery.fn.dataTableExt.oSort['margin-of-victory-desc'] = function(a,b) {
	var x = a.match(/data-mov="(.*?)"/)[1];
	var y = b.match(/data-mov="(.*?)"/)[1];
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};
		
jQuery.fn.dataTableExt.oSort['num-html-asc']  = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['num-html-desc'] = function(a,b) {
	var x = a.replace( /<.*?>/g, "" );
	var y = b.replace( /<.*?>/g, "" );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};


jQuery.fn.dataTableExt.oSort['league-level-division-asc']  = function(a,b) {
	var x = parseInt(a.match(/data-level="(.*?)"/)[1]);
	var y = parseInt(b.match(/data-level="(.*?)"/)[1]);
	if (x == y) {
		x = parseInt(a.match(/data-division="(.*?)"/)[1]);
		y = parseInt(b.match(/data-division="(.*?)"/)[1]);
	}
	return x - y;
};

jQuery.fn.dataTableExt.oSort['league-level-division-desc'] = function(a,b) {
	var x = parseInt(a.match(/data-level="(.*?)"/)[1]);
	var y = parseInt(b.match(/data-level="(.*?)"/)[1]);
	if (x == y) {
		x = parseInt(a.match(/data-division="(.*?)"/)[1]);
		y = parseInt(b.match(/data-division="(.*?)"/)[1]);
	}
	return y - x;
};


/* 
	Global variables used to access the matrix column in the filtering functions
	Can be overridden. Apply defaults here.
*/
if ( typeof filter_date_cellindex == "undefined" ) filter_date_cellindex = null;
if ( typeof filter_stage_cellindex == "undefined" ) filter_stage_cellindex = null;
if ( typeof filter_homeaway_cellindex == "undefined" ) filter_homeaway_cellindex = null;
if ( typeof filter_self_cellindex == "undefined" ) filter_self_cellindex = null;
if ( typeof filter_opponent_cellindex == "undefined" ) filter_opponent_cellindex = null;
if ( typeof filter_matchtype_cellindex == "undefined" ) filter_matchtype_cellindex = null;
if ( typeof filter_players_cellindex == "undefined" ) filter_players_cellindex = null;
if ( typeof filter_teams_cellindex == "undefined" ) filter_teams_cellindex = null;

/* Custom filtering functions */

if (filter_date_cellindex !== null) {	
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var dateSelection = document.getElementById('date-filter').value;
			var teammatchDate = Date.parse(aData[filter_date_cellindex].match(/datetime="(.*?)"/)[1]);
			var now = new Date();
				
			switch (dateSelection) {
				case "current":
					var minus1Week = new Date().setDate(now.getDate() - 7);
					var plus1Week = new Date().setDate(now.getDate() + 7);
					return teammatchDate >= minus1Week && teammatchDate <= plus1Week;
				case "past":
					return teammatchDate < now;
				case "future":
					return teammatchDate >= now;
				case "rearranged":
					return aData[filter_date_cellindex].indexOf('<em>') > -1;
			}
			return true;
		}
	);
}

if (filter_stage_cellindex !== null) {
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var stage = document.getElementById('stage-filter').value;
			return stage == "" || aData[filter_stage_cellindex].indexOf('data-stage="' + stage + '"') > -1;
		}
	);
}

if (filter_homeaway_cellindex !== null) {
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var homeaway = document.getElementById('homeaway-filter').value;
			return homeaway == "" || aData[filter_homeaway_cellindex].indexOf('data-homeaway="' + homeaway + '"') > -1;
		}
	);
}

if (filter_self_cellindex !== null) {
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var self = document.getElementById('self-filter').value;
			return self == "" || aData[filter_self_cellindex].indexOf('>' + self + '<') > -1 || aData[filter_self_cellindex].indexOf('data-sameclubopponentteam="' + self + '"') > -1;
		}
	);
}

if (filter_matchtype_cellindex !== null) {
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var matchtype = document.getElementById('matchtype-filter').value;
			switch (matchtype) {
				case "":
					return true;
				case "all-singles":
					return aData[filter_matchtype_cellindex].indexOf('data-matchtype-xonx="1"') > -1;
				case "all-doubles":
					return aData[filter_matchtype_cellindex].indexOf('data-matchtype-xonx="2"') > -1;
				default:
					return aData[filter_matchtype_cellindex].indexOf('data-matchtype-name="' + matchtype + '"') > -1;
			}
		}
	);
}

if (filter_teams_cellindex !== null) {
	$.fn.dataTableExt.afnFiltering.push(
		function( oSettings, aData, iDataIndex ) {
			var team1 = aData[filter_teams_cellindex[0]].replace( /<.*?>/g, "" ).toLowerCase(),
			    team2 = aData[filter_teams_cellindex[1]].replace( /<.*?>/g, "" ).toLowerCase(),
			    teamsSearch = document.getElementById('teams-filter').value.toLowerCase();
			/* Do not filter on the placeholder text */
			return $('#teams-filter').hasClass('placeholder') || teamsSearch == "" || team1.indexOf(teamsSearch) > -1 || team2.indexOf(teamsSearch) > -1;
		}
	);
}


$(document).ready(function() {
	/* 
		Reset filters element in order to override browser history when using the back button
		Since we have no outer form we cannot use form.myform.reset();
	*/
	if (filter_date_cellindex !== null) {
		document.getElementById('date-filter').selectedIndex = 0;
	}
	if (filter_stage_cellindex !== null) {
		document.getElementById('stage-filter').selectedIndex = 0;
	}
	if (filter_homeaway_cellindex !== null) {
		document.getElementById('homeaway-filter').selectedIndex = 0;
	}
	if (filter_self_cellindex !== null) {
		document.getElementById('self-filter').selectedIndex = 0;
	}
	if (filter_opponent_cellindex !== null) {
		document.getElementById('opponent-filter').value = "";
	}
	if (filter_matchtype_cellindex !== null) {
		document.getElementById('matchtype-filter').selectedIndex = 0;
	}
	if (filter_players_cellindex !== null) {
		document.getElementById('players-filter').value = "";
	}
	if (filter_teams_cellindex !== null) {
		document.getElementById('teams-filter').value = "";
	}
	
	/* active placeholder fallback solution for older browser, e.g. IE6-IE9 */
	$('.filter input').placeholder();
} );