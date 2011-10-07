YUI().use('autocomplete', 'array-extras', 'highlight-base', 'text-wordbreak', function (Y) { // 'autocomplete-filters', 'autocomplete-highlighters'

	Y.one('body').addClass('yui3-skin-sam');

var athleteTemplate =
	'<div class="athlete-suggest">' +
		'{highlighted}' +
		// ' <span class="id">[{athlete_id}]</span>' + // hightlighted includes ID (see resultTextLocator)
		'{activity}' +
		'<div class="lineup">{lineup}</div>' +
	'</div>';

 
// Custom formatter for athletes
function athleteFormatter(query, results) {
	// Iterate over the array of athlete result objects and return an array of HTML strings
	return Y.Array.map(results, function (result) {
		var athlete = result.raw;
	 
		// Use string substitution to fill out the athlete template and return an HTML string for this result
		/*
		YUI 3.3 Autocomplete bug: See my filed bug report at http://yuilibrary.com/projects/yui3/ticket/2529945
		So use plain text result when query is empty.
		Seit custom Hightlighter nicht mehr nötig.
		*/
		
		return Y.Lang.sub(athleteTemplate, {
			highlighted   : result.highlighted, // query == '' ? result.text : result.highlighted,
			activity      : athlete.num_team_activity > 0 ? 
				'<div class="activity"><em>' + athlete.num_team_activity + '</em> Einsätze</div>' : 
				'',
			lineup        : athlete.level == 255 ? 
				'<em>Ersatz</em>spieler' : 
				athlete.is_team_starter == 1 ? 
					'<em title="Pos. ' + athlete.position + '">Stamm</em>spieler' : 
					'<em title="Pos. ' + athlete.position + '">' + athlete.team_number + '.</em> Mannschaft (' + 
						athlete.league_abbr + (athlete.division == 0 ? '' : '-' + athlete.division) + ')'
		});
	});
}


/*
Mögliche Verbesserung: individueller flexibler "partialWordMatch" Filter und Highlighter (Kombination aus phraseMatch und wordMatch,
um auch andere Eingabeformen, wie "Zuname, Vorname" zu erlauben, statt der vorgeschriebenen (Vorname Zuname). 
Dies ist besonders wichtig, wenn man Namen anderer Kulturen eingeben möchte, wie chinesiche Namen, deren Reihenfolge umgedreht ist. Dies sorgt bei Nutzern oft zu Verunsicherung.
Praktische Beispiele, die möglich sein sollten:
Sophie-Anne statt Anne-Sophie (Verwechslung)
Schulz, Basti statt Sebastian Schulze (Spitzname)
T. Schultze (Abkürzung)
Schultze, Tobias (Getauscht)
Max Müller, Dr. statt Dr. Müller
Noch nicht möglich sind Initialien, wie TS, aber T S oder T.S. würde schon funktionieren auch wenn nicht Beschränkt auf den Anfang des Textes
Der Highlighter könnte dann den Markup liefern, der den Namen und die ID voneinander unterscheidet.
Inspiration siehe https://github.com/yui/yui3/blob/master/src/autocomplete/js/autocomplete-filters.js und https://github.com/yui/yui3/blob/master/src/autocomplete/js/autocomplete-highlighters.js
*/
// Potentiell nicht ganz eindeutige Filterung einer bestehenden Auswahl, wenn Name und Id in einem einem anderen result enthalten ist. Bsp: Tobi Schultz [80] und Tobias Schultze [480]
// -> erledigt durch IdSearch
function queryFilter(query, results) {
	if (query == '') return results; // performance optimization because the following calls will not have any effect in this case anyway
	query = query.toLowerCase();
	var queryWords = Y.Text.WordBreak.getUniqueWords(query), // mögliches problem: trennt wörter mit bindestruch, siehe http://yuilibrary.com/projects/yui3/ticket/2529948
		IdSearchRegEx = /[\[\(](\d+)[\]\)]/, // '[' or '(' + Number + ']' or ')'
		IdSearchResult = IdSearchRegEx.exec(query); 
	if (IdSearchResult) {
		IdSearchResult = parseInt(IdSearchResult[1], 10);
	}
	// Y.log(queryWords);
	// Y.log(IdSearchResult);
	return Y.Array.filter(results, function (result) {
		return queryWords.every(function (word) {
			return result.raw.first_name.toLowerCase().indexOf(word) !== -1 || 
			       result.raw.last_name.toLowerCase().indexOf(word) !== -1 || 
			       result.raw.athlete_id.indexOf(word) !== -1;
		}) && (IdSearchResult === null || IdSearchResult == result.raw.athlete_id); // result.raw.athlete_id is of type string
		
	});
}


 /**
     * Returns an array of results where all the words of the query are partially found in any word of the result. 
     * Non-word characters like whitespace and certain punctuation are ignored. Case-insensitive.
     * This is basically a combination of <code>wordMatch()</code> by ignoring whitespace and word order and <code>phraseMatch()</code> by allowing partial matching instead of whole words.
     * Example use case: Trying to find personal names independently of name order (Western or Eastern order)  and supporting immediate feedback by allowing partial occurences. So "J. Doe" and "Deo, John" and "J. D." would all find "John Doe".
     *
     * @method partialWordMatch
     * @param {String} query Query to match
     * @param {Array} results Results to filter
     * @return {Array} Filtered results
     * @static
     */
var YArray     = Y.Array,
    YObject    = Y.Object,
    WordBreak  = Y.Text.WordBreak,
	Highlight = Y.Highlight;
	
function partialWordMatch(query, results, caseSensitive) {
    // The caseSensitive parameter is only intended for use by
    // partialWordMatchCase(). It's intentionally undocumented.
	
	if (query == '') return results; // performance optimization because the following calls will not have any effect in this case anyway
	
	var queryWords = WordBreak.getUniqueWords(query, {ignoreCase: !caseSensitive});

    return YArray.filter(results, function (result) {
		var resultText = caseSensitive ? result.text : result.text.toLowerCase();
		return queryWords.every(function (queryWord) {
			return resultText.indexOf(queryWord) !== -1;
		});
		
		/*
		var resultWords = WordBreak.getUniqueWords(caseSensitive ? result.text : result.text.toLowerCase());
		
		return queryWords.every(function (queryWord) {
			return resultWords.some(function (resultWord) {
				return resultWord.indexOf(queryWord) !== -1;
			});
		});
		*/
    });
}

function partialWordMatchCase(query, results) {
    return partialWordMatch(query, results, true);
}


function partialWordMatchHighlighter(query, results, caseSensitive) {
    // The caseSensitive parameter is only intended for use by
    // partialWordMatchCase(). It's intentionally undocumented.
	
	var queryWords = WordBreak.getUniqueWords(query, {
        ignoreCase: !caseSensitive
    });
	
	return YArray.map(results, function (result) {
		return Highlight.all(result.text, queryWords, {
            caseSensitive: caseSensitive
        });
		/*words = WordBreak.getWords(result.text, {
            includePunctuation: true,
            includeWhitespace : true
        });
	
		return YArray.map(words, function (word) {
            return Highlight.all(word, queryWords, { caseSensitive: caseSensitive });
        }).join('');
		*/
		
		/*
		// we do not pass query as needles because it will not be used (and could not due to casting to hash inside Highlight.words)
		// this is also more performant since we only apply WordBreak once (see above) and reuse the result
		return Highlight.words(result.text, [], { 
            caseSensitive: caseSensitive,
			mapper: function (word, needles) {
		        return Highlight.all(word, queryWords, { caseSensitive: caseSensitive });
		    }
        });
		*/
		
	});
}


function partialWordMatchCaseHighlighter(query, results, caseSensitive) {
    return partialWordMatchHighlighter(query, results, true);
}


function queryHighlighter(query, results) {
	var queryWords = Y.Text.WordBreak.getUniqueWords(query, {ignoreCase: true}),
		highlightTemplate =
			'<div class="searchable">' +
				'<span class="firstname">{firstname}</span> <span class="lastname">{lastname}</span> ' + 
				'<small class="id">[{id}]</small>' +
			'</div>';
	return Y.Array.map(results, function (result) {
		if (queryWords.length) {
			return Y.Lang.sub(highlightTemplate, {
				firstname   : Y.Highlight.all(result.raw.first_name, queryWords, { caseSensitive: false }),
				lastname    : Y.Highlight.all(result.raw.last_name, queryWords, { caseSensitive: false }),
				id          : Y.Highlight.all(result.raw.athlete_id, queryWords, { caseSensitive: false })
			});
		}
		else {
			return Y.Lang.sub(highlightTemplate, {
				firstname   : Y.Escape.html(result.raw.first_name),
				lastname    : Y.Escape.html(result.raw.last_name),
				id          : Y.Escape.html(result.raw.athlete_id)
			});
		}
	});
}


function genderFilter(query, results, acNode) {
	var gender = acNode.getAttribute('data-gender');
	return Y.Array.filter(results, function (result) {
		return result.raw.gender == gender;
	});
	// Y.one("input:focus").getAttribute('data-gender')
}

/* 
	Filtert die wahrscheinlichste Auswahl heraus, wenn noch kein Suchkriterium eingegeben wurde
*/
function defaultSuggestionsFilter(query, results, acNode) {
	if (query == '') {
		var team = acNode.ancestor('.hometeam') ? 'hometeam' : 'awayteam', // bezieht sich die Abfrage, auf die Heimmannschaft oder Auswärtsmannschaft
			discipline = acNode.getAttribute('data-discipline'),
			teamAthleteInputs = acNode.ancestor('tbody').all('.' + team + ' input.athlete');
		
		return Y.Array.filter(results, function (result) {
			var athleteLineupCount = 0;
				
			/*
			Spieler von der Liste entfernen, die eigentlich in diesem Spiel nicht aufgestellt werden dürften
			Keine Spieler direkt vorschlagen, die bereits zwei Mal (oder öfter) in diesem Teamspiel aufgestellt wurden oder schon in der gleichen Disziplin ausgewählt wurden. Dadurch werden 3 falsche Aufstellungen herausgefiltert:
			1) im gleichen Doppel-Match (z.B. 1.HD) wird ein bereits ausgewählter Spieler nicht nochmal als möglicher Partner vorgeschlagen
			2) in unterschiedlichen Matches der gleichen Disziplin (z.B. 1.HE, 2.HE) werden bereits ausgewählte nicht wieder vorschlagen
			3) der 3. Einsatz des gleichen Spielers wird nicht vorgeschlagen (z.B. ausgewählt in HD und Mix -> dann nicht noch im HE vorschlagen)
			*/
			if (teamAthleteInputs.some(function (athleteInput) {
				if (athleteInput.get('value') == result.text) {
					athleteLineupCount++;
					return (athleteLineupCount > 1) || (athleteInput.getAttribute('data-discipline') == discipline);
				}
				return false;
			})
			) {
				return false;
			}
			// Y.all('.' + team + ' input[data-discipline=' + acNode.getAttribute('data-discipline') + ']:contains(' + result.text + ')').size() == 0
			
			/*
			Standardmäßig nur die wahrscheinlichsten Spieler anzeigen. Diese sind:
			1) Stammspieler der Mannschaft
			2) Spieler mit vorhandenen Einsätzen in dieser Mannschaft
			3) Spieler die in einem anderen Spiel des Mannschaftswettkampfs bereits ausgewählt wurden, falls dem keine der obigen Regeln widerspricht
			*/
			return (result.raw.is_team_starter == 1 || result.raw.num_team_activity > 0 || athleteLineupCount > 0);
		});
	}
	
	return results;
}


	Y.all('input.athlete').each(function (inputNode) {
		inputNode.plug(Y.Plugin.AutoComplete, {
			maxResults: 10,
			minQueryLength: 0,
			queryDelay: 0,
			scrollIntoView: false, // true funktioniert schlecht, da es in jeden Fall scrollt - auch wenn die Liste schon einsehrbar ist
			activateFirstItem: true,
			tabSelect: true, // in Kombination mit activateFirstItem sorgt es für eine möglichst schnelle Eingabe
			source: inputNode.ancestor('.hometeam') ? club1_athletes : club2_athletes,
			// resultTextLocator: 'last_name', // einfache Version
			resultTextLocator: function (result) {
				return result.first_name + ' ' + result.last_name + ' [' + result.athlete_id + ']';
			},		
			// Chain together several filters
			resultFilters: [Y.rbind(genderFilter, null, inputNode), queryFilter, Y.rbind(defaultSuggestionsFilter, null, inputNode)], // 'phraseMatch'  partialWordMatch
			resultHighlighter: queryHighlighter, // partialWordMatchHighlighter, // 'phraseMatch'
			resultFormatter: athleteFormatter
		});
	});
	
	
	// When the input node receives focus, send a query to display the full list of suggestions
	Y.all('input.athlete').on('focus', function (e) {
		//e.target.select(); // pre-select text so it can be overridden directly by entering text
		e.target.ac.sendRequest(e.target.get('value'));
		//e.target.ac.show();
	});
	
	/*
	Y.all('.team input').each(function(node, index){
		node.ac.on("query", function (e) {
			if (index = e.query.indexOf(',') !== -1) {
				e.query =  e.query.substr(index + 1, e.query.length - index) + e.query.substr(0, index);
				alert(e.query);
			}
		});
	});
	*/


/*
	Maybe use jQuery Data Linking for this:
	https://github.com/jquery/jquery-datalink
*/	
	
function calcStats() {

	var teammatch_team1_points = 0, teammatch_team2_points = 0;
	var teammatch_team1_games = 0, teammatch_team2_games = 0;
	var teammatch_team1_matches = 0, teammatch_team2_matches = 0;
	
	Y.all('.scoresheet .match').each(function (matchNode) {
		var match_team1_games = 0, match_team2_games = 0;
		
		matchNode.all('.games .effective > li').each(function (gameNode) {
			var inputs = gameNode.all('input.score'); // input:not([type=hidden])

			var game_team1_score = inputs.item(0) !== null ? parseInt(inputs.item(0).get('value'), 10) : Number.NaN,
				game_team2_score = inputs.item(1) !== null ? parseInt(inputs.item(1).get('value'), 10) : Number.NaN;
			
			teammatch_team1_points += Y.Lang.isNumber(game_team1_score) ? game_team1_score : 0;
			teammatch_team2_points += Y.Lang.isNumber(game_team2_score) ? game_team2_score : 0;
			
			if (Y.Lang.isNumber(game_team1_score) && Y.Lang.isNumber(game_team2_score)) {
				match_team1_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score > game_team2_score;
				match_team2_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score < game_team2_score;
			}
		});
		
		if (match_team1_games + match_team2_games == 0) {
			if (matchNode.one('.noresult input').get('checked')) {
				matchNode.one('.match-result').set('innerHTML', '&#8211;:&#8211;'); //Auslassungszeichen als Halbgeviertstrich
			} else {
				matchNode.one('.match-result').set('innerHTML', '?:?');
			}
		} else {
			matchNode.one('.match-result').set('innerHTML', match_team1_games + ':' + match_team2_games);
		}
		
		teammatch_team1_games += match_team1_games;
		teammatch_team2_games += match_team2_games;
		if (match_team1_games !== 0 ||  match_team2_games !== 0) {
			teammatch_team1_matches += match_team1_games === match_team2_games ? 0.5 : match_team1_games > match_team2_games;
			teammatch_team2_matches += match_team1_games === match_team2_games ? 0.5 : match_team1_games < match_team2_games;
		}		
	});
	
	Y.one('.sum .games output').set('innerHTML', teammatch_team1_points + ' : ' + teammatch_team2_points);
	Y.one('.sum .score output').set('innerHTML', teammatch_team1_games + ' : ' + teammatch_team2_games);
	Y.one('#bmtm_team1_score').set('value', teammatch_team1_matches);
	Y.one('#bmtm_team2_score').set('value', teammatch_team2_matches);
	
}


function allowOnlyNumbers(event) {
	var charCode = (event.which) ? event.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		event.halt(); // stopPropagation() and preventDefault()
		return false;
	}
	return true;
}

function isBadmintonGameScoreInRange(score) {
	return (score == Number.NaN || score >= 0 && score <= 30);
}

/**
* Überprüft, ob ein Badminton-Satz ein mögliches Ergebnis aufweist
* allowPotential boolean
*	Falsche Werte, die aber durch weitere nachfolgende Eingaben noch zu einem richtigen Ergebnis führen können, sollen  als gültig bewertet werden
*	Dadurch wird bsp. folgndes Problem behoben: Bei der Live-Validierung einer Eingabe von 9:2 wird schon ein Fehler angezeigt, obwohl man noch den Focus hat und wahrscheinlich gleich 9:21 eingeben möchte. 
*	Sollte eigentlich nicht stören, da der User normalerweise ohne Zeitverzögerung die Zahlen eingibt, aber es kann sehr verwirren. Es spielen also auch die Anzahl der eingegebenen Zeichen eine Rolle und 
*	bei einstelligen Zahlen wird nur unter unmöglichen Fortsetzungen ein Fehler geworfen.
* allowEmpty boolean
*	Ob unausgefüllte Werte erlaubt sind.
*/
function isBadmintonGameValid(score1, score2, allowPotential, allowEmpty) {
	if (allowEmpty && (isNaN(score1) || isNaN(score2))) {
		if (!isNaN(score1)) return isBadmintonGameScoreInRange(score1); // only score1 != NaN
		if (!isNaN(score2)) return isBadmintonGameScoreInRange(score2); // only score2 != NaN
		return true; // both NaN
	}
	else if (isNaN(score1) || isNaN(score2)) {
		return false;
	}
	
	var biggerScore = Math.max(score1, score2),
		smallerScore = Math.min(score1, score2);
	
	// Wenn kein Kriterium trifft, dann gilt immernoch die vollständige Überprüfung (siehe unten)
	if (allowPotential) {
		if (biggerScore < 21 && smallerScore <= 9) {
			return (score1 == 2 || score1 == 3 || score2 == 2 || score2 == 3);
		}
		// wenn höheres Ergebnis größer 21 und kleiner gleich 30 ist und das kleinere Ergebnis einstellig, was eigentlich falsch wäre,
		// dann erlaube trotzdem ein kleineres Ergebnis mit 2 beginnend (z.B. 22:2x oder 30:2x ) bzw. 2 oder 3 beginnend bei höheres Ergebnis von 28 oder 29 (z.B. 28:3[0], 28:2[6])
		if (biggerScore >= 22 && biggerScore <= 30 && smallerScore <= 9) {
			return (biggerScore == 28 || biggerScore == 29) ? (smallerScore == 2 || smallerScore == 3) : (smallerScore == 2);
		}
	} 	
	
	if (biggerScore == 21) {
		return (smallerScore >= 0 && smallerScore <= 19)
	} 
	else if (biggerScore > 21 && biggerScore <= 29) {
		return (smallerScore == biggerScore - 2)
	} 
	else if (biggerScore == 30) {
		return (smallerScore == 28 || smallerScore == 29)
	}
	
	return false;
}

function validateBadmintonGamePartially(gameNode) {
	var inputs = gameNode.all('input.score');

	if (isBadmintonGameValid(
		parseInt(inputs.item(0).get('value'), 10), 
		parseInt(inputs.item(1).get('value'), 10),
		true, true
	)) {
		gameNode.removeClass('error');
	} else {
		gameNode.addClass('error');
	}
}

function validateBadmintonGameFully(gameNode) {
	var inputs = gameNode.all('input.score');

	if (!isBadmintonGameValid(
		parseInt(inputs.item(0).get('value'), 10), 
		parseInt(inputs.item(1).get('value'), 10),
		false, false
	)) {
		gameNode.addClass('error');
	}
}
/**
	Überprüfen auf korrektes Satzergebnis eines Badmintonspiels (kein 3:0)
	Nicht nur das Ergebnis, sondern auch die Reihenfolge ist wichtig. D.h. bei 2:0 Führung sollte es keinen 3. Satz geben.
*/
function validateBadmintonMatch(matchGamesNode) {
	var match_team1_games = 0, match_team2_games = 0,
		bestOfThreeOver = false, wrongGameSequence = false;
	
	matchGamesNode.all('li').each(function (gameNode) {
		var inputs = gameNode.all('input.score');

		var game_team1_score = parseInt(inputs.item(0).get('value'), 10),
			game_team2_score = parseInt(inputs.item(1).get('value'), 10);
			
		wrongGameSequence = bestOfThreeOver && (inputs.item(0).get('value') != '' || inputs.item(1).get('value') != '');
			
		if (Y.Lang.isNumber(game_team1_score) && Y.Lang.isNumber(game_team2_score)) {
			match_team1_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score > game_team2_score;
			match_team2_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score < game_team2_score;
			bestOfThreeOver = bestOfThreeOver || match_team1_games === 2 || match_team2_games === 2;
		}
	});
		
	if ( 
		wrongGameSequence ||
		(match_team1_games !== 2 || match_team2_games !== 0) && 
		(match_team1_games !== 0 || match_team2_games !== 2) &&
		(match_team1_games !== 2 || match_team2_games !== 1) &&
		(match_team1_games !== 1 || match_team2_games !== 2)
	) {
		matchGamesNode.ancestor('fieldset').addClass('error');
	}
}

	
function checkOptionalThirdGame(event) {
	if (event) {
		manipulateOptionalThirdGame(event.target.ancestor('ol'));
	}
	else {
		Y.all('.games ol').each(function (gameNode) {
			manipulateOptionalThirdGame(gameNode);
		});
	}
}

function manipulateOptionalThirdGame(matchGamesNode) {
	/*
		3, Satz nur entfernen, wenn nichts drin steht, da sonst beim Editieren ungewollt Daten verloren gehen können
		Z.B. beim Editieren des 1. Satzes von 24:22, 16:21, 18:21 zu 20:22 würde der 3. Satz ungewollt verwschwinden und geleert.
		Dann muss auch ein Badminton Match Validator eingeführt werden, um z.B. ein 3:0 Satzergebnis zu alamieren
	*/
	if (isGamesEmpty(matchGamesNode.one('li:last-child'))) 
	{
		var match_team1_games = 0, match_team2_games = 0;

		// slice(0,2) did not work for some reason
		matchGamesNode.all('li:not(:last-child)').each(function (gameNode) {
			var inputs = gameNode.all('input.score'); // input:not([type=hidden])
			var game_team1_score = inputs.item(0) !== null ? parseInt(inputs.item(0).get('value'), 10) : Number.NaN,
				game_team2_score = inputs.item(1) !== null ? parseInt(inputs.item(1).get('value'), 10) : Number.NaN;
				
			if (Y.Lang.isNumber(game_team1_score) && Y.Lang.isNumber(game_team2_score)) {
				match_team1_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score > game_team2_score;
				match_team2_games += game_team1_score === game_team2_score ? 0.5 : game_team1_score < game_team2_score;
			}
		});
	
		// not the sum because two draws would allow third game
		if (match_team1_games === 2 && match_team2_games === 0 || match_team1_games === 0 && match_team2_games === 2) {
			// remove possible value in last game and hide it. resetting value it important to guarantee that not an invisible value gets submitted and the statistics are correctly calculated
			// not neccessary because third set has already been tested to be empty
			// matchGamesNode.all('li:last-child input.score').set('value', '');
			matchGamesNode.one('li:last-child').hide();
		}
		else {
			matchGamesNode.one('li:last-child').show();
		}
	}
}

function checkNoPlayer(event) {
	if (event) {
		manipulateNoPlayer(event.target);
		event.stopPropagation();
	}
	else {
		Y.all('.noplayer input').each(function (noPlayerNode) {
			manipulateNoPlayer(noPlayerNode);
		});
	}
}

function manipulateNoPlayer(noPlayerNode) {
	var athletes = noPlayerNode.ancestor('.team').all('input.athlete');
	// Paarung nicht angetreten
	if (noPlayerNode.get('checked')) {
		/*
			Spielerauswahl leeren und verstecken
		*/
		athletes.set('value', '');
		athletes.hide();
		var match = noPlayerNode.ancestor('.match');
		var matchEffectiveGamesNode = match.one('.effective');
		
		/*
			Wenn Gegner ebenfalls nicht angetreten und Spiel nicht umgewertet wurde, dann kein Ergebnis setzen, was wiederum das gewertete Ergebnis streicht
			Die Beschränkung auf nicht umgewertet ist wichtig, da sonst Ergebnisse gelöscht werden, bei denen beide nicht angetreten sind und dann das Spiel umgewertet wurde
		*/
		var opponentNoPlayer = match.one('.' + (noPlayerNode.ancestor('.hometeam') ? 'awayteam' : 'hometeam') + ' .noplayer input').get('checked');
		var noRevaluation = match.one('.revaluation select').get('value') == '';
		if (opponentNoPlayer && noRevaluation) {
			var noResultNode = match.one('.noresult input');
			noResultNode.set('checked', true);
			manipulateNoResult(noResultNode); // muss explicit getriggert werden, da onchange event von noResultNode nicht gefeuert wird, wenn der Wert durch Script geändert wurde
			// wenn kein Ursprungsergebnis vorliegt auf das sich die kampflose Markierung beziehen könnte, dann die kampflose Markierung auf beiden Seiten entfernen, die evtl. vorher durch Nicht-Antreten nur einer Paarung gesetzt wurde (siehe folgend)
			if (isGamesEmpty(match.one('.annulled'))) {
				match.all('.result-incident select').set('selectedIndex', 0);
				checkNoticeValue();
			}
		}
		/* 
			Bei nur einer nicht angetretenen Paarung und falls Spiel nicht bereits als wertungslos/kein Ergebnis ausgewählt wurde oder Satzergebnisse vorhanden sind,
			dann die Satzergebnisse entsprechend setzen.
			Die Einschränkung ist wichtig, um nicht vorhandene Daten beim Initiallauf zu löschen (beim Editieren). Bei der Ersteingabe, sind die Felder aber eh leer. 
			Hat der User schon was eingegeben, muss er es halt explicit löschen - explicit ist besser als implicit!
			Wurden die Satzergebnisse verändert, muss der 3. Satz entfernt und die Statistiken neu berechnet werden. Zusätzlich kampflose Markierung setzten, wenn kein Originalergebnis vorhanden.
		*/
		else if (!match.one('.noresult input').get('checked') && isGamesEmpty(matchEffectiveGamesNode)) {
			var thisTeam = noPlayerNode.ancestor('.hometeam') ? 'team1' : 'team2';
			var	otherTeam = thisTeam == 'team2' ? 'team1' : 'team2';
			
			matchEffectiveGamesNode.all('.score.'+thisTeam).set('value', '0');
			matchEffectiveGamesNode.all('.score.'+otherTeam).set('value', '21');
			// 3. Satz wieder entfernen
			matchEffectiveGamesNode.one('li:last-child').all('.score').set('value', '');
			
			// remove possible previous validation erros
			matchEffectiveGamesNode.all('li').removeClass('error');
			matchEffectiveGamesNode.ancestor('fieldset').removeClass('error');

			manipulateOptionalThirdGame(matchEffectiveGamesNode);
			if (isGamesEmpty(match.one('.annulled'))) {
				match.one('option[value='+otherTeam+'_wonbydefault]').set('selected', true);
				checkNoticeValue();
			}
			calcStats();
		}
	}
	else {
		athletes.show();
	}
}


function checkNoResult(event) {
	if (event) {
		manipulateNoResult(event.target);
		event.stopPropagation();
	}
	else {
		Y.all('.noresult input').each(function (noResultNode) {
			manipulateNoResult(noResultNode);
		});
	}
}

function manipulateNoResult(noResultNode) {
	var effectiveGamesSet = noResultNode.ancestor('.match').one('.effective-fieldset');
	if (noResultNode.get('checked')) {
		effectiveGamesSet.all('input.score').set('value', '');
		effectiveGamesSet.all('li').removeClass('error');
		effectiveGamesSet.hide();
		calcStats();
	}
	else {
		effectiveGamesSet.show();
		calcStats(); // calculation actually not necessary but the display format needs to be updated
	}
}

function checkAnnulledGames(event) {
	if (event) {
		manipulateAnnulledGames(event.target.ancestor('.match'));
	}
	else {
		Y.all('.match').each(function (matchNode) {
			manipulateAnnulledGames(matchNode);
		});
	}
}

function manipulateAnnulledGames(matchNode) {
	var annulledGamesSet = matchNode.one('.annulled-fieldset');
	
	var isInputCheckedCausingAnnulledGames = 
		matchNode.one('.revaluation select').get('value') != '' ||
		matchNode.one('.result-incident select').get('value') == 'team1_givenup' ||
		matchNode.one('.result-incident select').get('value') == 'team2_givenup';

	/*
		Nur ausblenden, wenn keine Werte vorhanden und kein Input markiert, das ursprüngliche Ergebnisse erlauben würde (Umwertung oder Aufgabe)
	*/
	if (isGamesEmpty(annulledGamesSet.one('.annulled')) && !isInputCheckedCausingAnnulledGames) {
		annulledGamesSet.hide();
	}
	else {
		annulledGamesSet.show();
	}
}


function isGamesEmpty(matchGamesNode) {
	return !(matchGamesNode.all('input.score').some(function (scoreNode) {				
		return scoreNode.get('value') != '';
	}));
}


function checkNoticeValue(event) {
	if (event) {
		manipulateNoticeValue(event.target);
	}
	else {
		Y.all('.revaluation select, .result-incident select').each(function (selectNode) {
			manipulateNoticeValue(selectNode);
		});
	}
}

function manipulateNoticeValue(selectNode) {
	if (selectNode.get('value') != '') {
		selectNode.removeClass('default-value');
		selectNode.addClass('notice-value');
	}
	else {
		selectNode.addClass('default-value');
		selectNode.removeClass('notice-value');
	}
}

	/*
		TODO
		- Remove validation erros when hiding/showing match results
		- Umwertung gegen -> Gewertetes Ergebnis in Ursprungsergebnis setzten
		- Result Incident Trennstrich -> radio uncheck
		- Badminton Validate Result (done)
			- Ursprüngliches Ergebnis nicht validieren, wenn aufgegeben
		- Teammatch nicht angetreten -> alle Spieler nicht angetreten
		- Status der ursprünglichen Ergebnisse (done)
		-  Badminton Match Validator eingeführt werden, um z.B. ein 3:0 Satzergebnis zu alamieren (done)
	*/
	
	/*
		initial manipulation checks
		e.g. hiding unused fields
	*/
	checkOptionalThirdGame();
	checkAnnulledGames();
	checkNoPlayer();
	checkNoResult();
	checkNoticeValue();
	
	/*
		Using event delegation (Event Bubbling)
		
		unobtrusive scripting techniques: wiring up your events and scripts without any code inline in your HTML. 
		You're going to use your access to the DOM and simply attach scripts to the objects on the page using a proper separation of content, presentation, now through JavaScript, behavior
		
		Hooking up all those handlers comes with a price, not only in the number of steps to hook them up, but in the cost associated with storing redundant pieces of code in the browsers memory. 
		Not only that, but if your DOM changes, you've got to rewire your events because a new additions won't be aware of code run onload.
	*/
	
	// http://developer.yahoo.com/yui/3/event/#focusblur
	
	
	// nur die gewerteten Ergebnisse müssen für die Berechnung der Statistiken gebunden werden
	// bind both keyup and change events. together they handle most circumstances
	// - keyup does not respond to mouse input (e.g. pasting)
	// - change only fires after blur -> not live feedback
	
	Y.on('keyup', function(e) {
		// Sicherstellen, dass der Ursprung des Event Bubblings ein Score Input Feld ist
		// Muss eigentlich sein, da kein anderes Element in Frage kommt, das diesen Event Handler (keyup) unterstützt
		if (e.target.hasClass('score')) {
			calcStats();
		}
    }, '.games .effective');
	
	Y.on('change', function(e) {
		if (e.target.hasClass('score')) {
			calcStats();
		}
    }, '.games .effective');
	
	Y.on('keyup', function(e) {
		if (e.target.hasClass('score')) {
			manipulateOptionalThirdGame(e.target.ancestor('ol'));
		}
    }, '.games');
	
	Y.on('change', function(e) {
		if (e.target.hasClass('score')) {
			manipulateOptionalThirdGame(e.target.ancestor('ol'));
		}
    }, '.games');
	
	Y.on('keypress', function(e) {
		if (e.target.hasClass('score')) {
			allowOnlyNumbers(e);
		}
    }, '.scoresheet');
	
	Y.on('keyup', function(e) {
        if (e.target.hasClass('score')) {
			validateBadmintonGamePartially(e.target.ancestor('li'));
		}
    }, '.games');
	
	Y.on('focus', function(e) {
		// Y.log(e.target.hasClass('score'));
        if (e.target.hasClass('score')) {
			e.target.ancestor('li').removeClass('error'); // validateBadmintonGamePartially(e.target.ancestor('li'));
			e.target.ancestor('fieldset').removeClass('error');
		}
    }, '.games');
	
	Y.on('blur', function(e) {
        if (e.target.hasClass('score')) {
			validateBadmintonGameFully(e.target.ancestor('li'));
			validateBadmintonMatch(e.target.ancestor('ol'));
		}
    }, '.games');
	
	/*
	Y.on('click', function(e) {
		Y.log(e.target.get('tagName'));
    }, '.scoresheet');
	*/
	


	
	/*
	Y.log(Y.Text.WordBreak.getUniqueWords("Lena-Meyer - Berlin -Berlin-"));
	// Returns: ["Lena", "Meyer", "Berlin"]
	// Expected: ["Lena-Meyer", "Berlin"]
	// Additionally it's inconsistent with
	Y.log(Y.Text.WordBreak.getUniqueWords("Lena.Meyer"));
	// Returns: ["Lena.Meyer"]
	*/
	
	/*
	alert(Y.Highlight.all("Tobias Schultze", ['tze', 'sch']));
	alert(Y.Highlight.all("Tobias Schultze", ['tze', 'ultz', 'sch']));
	
	alert(Y.Highlight.all("123456789", ['123', '345', '678']));
	<b class="yui3-highlight">123</b>45<b class="yui3-highlight">678</b>9
	Expected: <b class="yui3-highlight">12<b class="yui3-highlight">3</b>45</b><b class="yui3-highlight">678</b>9
	*/
	
	// use click event handler. it works as it should. also when changing value with keyboard
	// change event handler fires in internet explorer only after losing focus of the element, which does not give immediate feedback
	
	Y.all('.noplayer input').on('click', checkNoPlayer);
	
	Y.all('.noresult input').on('click', checkNoResult);
	
	// auch bei kampflosem Radio Field den Event Listener registrieren, da bei deren Auswahl natürlich das Aufgabe Radio Field (.withdrawn input) unchecked wird
	Y.all('.revaluation select, .result-incident select').on('change', checkAnnulledGames);
	
	Y.all('.revaluation select, .result-incident select').on('change', checkNoticeValue);

	
	/*
	// Old style without event delegation
	
	Y.all('.games .effective input.score').on('keyup', calcStats);
	Y.all('.games .effective input.score').on('change', calcStats);
	
	Y.all('.games input.score').on('keyup', checkGamesState);
	Y.all('.games input.score').on('change', checkGamesState);
	
	Y.all('.games input.score').on('keypress', allowOnlyNumbers);
	Y.all('.games input.score').on('keyup', validateBadmintonGame);
	Y.all('.games input.score').on('focus', validateBadmintonGame);
	Y.all('.games input.score').on('blur', validateBadmintonGameBlur);
	*/	
	
})