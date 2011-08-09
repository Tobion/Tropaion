<table class="scoresheet" id="scoresheet-teammatch-<?php echo $teammatch['id'] ?>" about="<?php echo $teammatch->getCurie(); ?>" typeof="sport:Match">
	<caption><a href="#scoresheet-teammatch-<?php echo $teammatch['id'] ?>"><?php echo __('Scoresheet'); ?></a>
		<details>
		<summary><?php echo __('Help'); ?></summary>
		<p><?php echo __('Scoresheet of the team match with all its individual matches and games. Substitutes are written in gray.'); ?></p>
		</details>
	</caption>
	<thead rel="sport:outcome">
		<tr>
			<th scope="col" class="matchtype"><?php echo __('Match type'); ?></th>
			<th scope="col" class="team hometeam" title="<?php echo __('home team'); ?>" about="[_:hometeam]" typeof="sport:TeamScore" rel="sport:participant">
				<?php if ($teammatch->isTeam1Winner()): ?>
				<strong class="winner" xml:lang="de" about="<?php echo $teammatch->getTeam1()->getCurie(); ?>" typeof="sport:IndependentTeam" property="sport:teamNumber" content="<?php echo $teammatch->getTeam1()->getTeamNumber(); ?>" rel="sport:associatedSportsClub">
				<?php else: ?>
				<span xml:lang="de" about="<?php echo $teammatch->getTeam1()->getCurie(); ?>" typeof="sport:IndependentTeam" property="sport:teamNumber" content="<?php echo $teammatch->getTeam1()->getTeamNumber(); ?>" rel="sport:associatedSportsClub">
				<?php endif; ?>
				<span about="<?php echo $teammatch->getTeam1()->getClub()->getCurie(); ?>" typeof="sport:SportsClub" property="sport:name"><?php echo $teammatch->getTeam1()->getClub()->getName(); ?></span>
				<?php echo $teammatch->getTeam1()->getTeamNumberAsRomanNumeral(); ?>
				<?php if ($teammatch->isTeam1Winner()): ?>
				</strong>
				<?php else: ?>
				</span>
				<?php endif; ?>
			</th>
			<td class="score"><div><abbr class="vs" title="<?php echo __('versus'); ?>"><?php echo __('vs'); ?></abbr> <mark class="teammatch-result" title="<?php 
				if ($teammatch->isBothTeamsLost())
					echo __('team match result: both lost'); 
				else if ($teammatch->isDraw()) 
					echo __('team match result: draw'); 
				else echo __('team match result: %team% wins %result%', 
					array(
						'%team%' => $teammatch->getWinnerTeam()->__toString(),  
						'%result%' => $teammatch->getTeam1Score() . ' : ' . $teammatch->getTeam2Score())); 
				?>"><?php echo $teammatch->getTeam1Score(); ?> : <?php echo $teammatch->getTeam2Score(); ?></mark></div></td>
			<th scope="col" class="team awayteam" title="<?php echo __('away team'); ?>" about="[_:awayteam]" typeof="sport:TeamScore" rel="sport:participant">
				<?php if ($teammatch->isTeam2Winner()): ?>
				<strong class="winner" xml:lang="de" about="<?php echo $teammatch->getTeam2()->getCurie(); ?>" typeof="sport:IndependentTeam" property="sport:teamNumber" content="<?php echo $teammatch->getTeam2()->getTeamNumber(); ?>" rel="sport:associatedSportsClub">
				<?php else: ?>
				<span xml:lang="de" about="<?php echo $teammatch->getTeam2()->getCurie(); ?>" typeof="sport:IndependentTeam" property="sport:teamNumber" content="<?php echo $teammatch->getTeam2()->getTeamNumber(); ?>" rel="sport:associatedSportsClub">
				<?php endif; ?>
				<span about="<?php echo $teammatch->getTeam2()->getClub()->getCurie(); ?>" typeof="sport:SportsClub" property="sport:name"><?php echo $teammatch->getTeam2()->getClub()->getName(); ?></span>
				<?php echo $teammatch->getTeam2()->getTeamNumberAsRomanNumeral(); ?>
				<?php if ($teammatch->isTeam2Winner()): ?>
				</strong>
				<?php else: ?>
				</span>
				<?php endif; ?>
			</th>	
			<th scope="col" class="games"><?php echo __('Games'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr class="sum">
			<th scope="row"><?php echo __('Sum'); ?></th>
			<td></td>
			<td class="score" title="<?php echo __('game result'); ?>"><?php echo $teammatch->sumTeam1WonGames() . ' : ' . $teammatch->sumTeam2WonGames(); ?></td>
			<td></td>
			<td class="games" title="<?php echo __('point result'); ?>"><?php echo $teammatch->sumTeam1Score() . ' : ' . $teammatch->sumTeam2Score(); ?></td>
		</tr>
		<tr class="result">
			<th scope="row"><?php echo __('Result'); ?></th>
			<td colspan="4">
				<mark class="teammatch-result" title="<?php echo __('team match result'); ?>">
					<span about="[_:hometeam]" property="sport:homeAdvantage" datatype="xsd:boolean" content="true"><span property="sport:score" datatype="xsd:unsignedShort"><?php echo $teammatch['team1_score'] ?></span></span>
					<abbr> : </abbr>
					<span about="[_:awayteam]" property="sport:homeAdvantage" datatype="xsd:boolean" content="false"><span property="sport:score" datatype="xsd:unsignedShort"><?php echo $teammatch['team2_score'] ?></span></span>
				</mark>
			</td>
		</tr>
	</tfoot>
	<tbody xml:lang="de" rel="sport:subMatch">
		<?php foreach ($teammatch->Matches as $match): ?>
		<tr class="match" id="match-<?php echo $match['id'] ?>" about="<?php echo $match->getCurie(); ?>" typeof="sport:Match">
			<th scope="row" class="matchtype" property="sport:characteristic" datatype="xsd:string"><a href="#match-<?php echo $match['id'] ?>"><?php echo $match->getMatchType(); ?></a></th>
			<td class="team hometeam" rel="sport:outcome">
				<span typeof="sport:TeamScore" property="sport:score" datatype="xsd:unsignedShort" content="<?php echo $match->numberTeam1WonGames(); ?>" rel="sport:participant">
				<?php if ($match->isTeam1Winner()): ?><em class="winner"<?php elseif (!$match->isTeam1Single()): ?><span<?php endif; ?><?php if (!$match->isTeam1Single()): ?> about="<?php echo $match->getTeam1PlayersCurie(); ?>" typeof="sport:Team" rel="sport:teamMate"<?php endif; ?><?php if ($match->isTeam1Winner() || !$match->isTeam1Single()): ?>><?php endif; ?>
				<?php if (is_null($match->Team1_Player) && is_null($match->Team1_Partner)): ?>
				<small>&lt;&lt;&lt; <?php echo __('no player'); ?> &gt;&gt;&gt;</small>
				<?php endif; ?>
				<?php if (!is_null($match->Team1_Player)): ?>
				<span class="athlete<?php if ($match->isTeam1PlayerSubstitute()): ?> substitute<?php endif; ?>" about="<?php echo $match->Team1_Player->getCurie(); ?>" typeof="sport:Athlete">
					<span property="sport:lastName"><?php echo $match->Team1_Player->getLastName(); ?></span>, 
					<span property="sport:firstName"><?php echo $match->Team1_Player->getFirstName(); ?></span>
				</span>
				<?php endif; ?>
				<?php if (!is_null($match->Team1_Partner)): ?>
				<abbr title="<?php echo __('and'); ?>"> / </abbr>
				<span class="athlete<?php if ($match->isTeam1PartnerSubstitute()): ?> substitute<?php endif; ?>" about="<?php echo $match->Team1_Partner->getCurie(); ?>" typeof="sport:Athlete">
					<span property="sport:lastName"><?php echo $match->Team1_Partner->getLastName(); ?></span>, 
					<span property="sport:firstName"><?php echo $match->Team1_Partner->getFirstName(); ?></span>
				</span>
				<?php endif; ?>
				<?php if ($match->isTeam1Winner()): ?></em><?php elseif (!$match->isTeam1Single()): ?></span><?php endif; ?>
				</span>
			</td>
			<td class="score" title="<?php echo __('match result'); ?>"><span class="match-result"><?php echo $match->numberTeam1WonGames(); ?><abbr> : </abbr><?php echo $match->numberTeam2WonGames(); ?></span></td>
			<td class="team awayteam" rel="sport:outcome">
				<span typeof="sport:TeamScore" property="sport:score" datatype="xsd:unsignedShort" content="<?php echo $match->numberTeam2WonGames(); ?>" rel="sport:participant">
				<?php if ($match->isTeam2Winner()): ?><em class="winner"<?php elseif (!$match->isTeam2Single()): ?><span<?php endif; ?><?php if (!$match->isTeam2Single()): ?> about="<?php echo $match->getTeam2PlayersCurie(); ?>" typeof="sport:Team" rel="sport:teamMate"<?php endif; ?><?php if ($match->isTeam2Winner() || !$match->isTeam2Single()): ?>><?php endif; ?>
				<?php if (is_null($match->Team2_Player) && is_null($match->Team2_Partner)): ?>
				<small>&lt;&lt;&lt; <?php echo __('no player'); ?> &gt;&gt;&gt;</small>
				<?php endif; ?>
				<?php if (!is_null($match->Team2_Player)): ?>
				<span class="athlete<?php if ($match->isTeam2PlayerSubstitute()): ?> substitute<?php endif; ?>" about="<?php echo $match->Team2_Player->getCurie(); ?>" typeof="sport:Athlete">
					<span property="sport:lastName"><?php echo $match->Team2_Player->getLastName(); ?></span>, 
					<span property="sport:firstName"><?php echo $match->Team2_Player->getFirstName(); ?></span>
				</span>
				<?php endif; ?>
				<?php if (!is_null($match->Team2_Partner)): ?>
				<abbr title="<?php echo __('and'); ?>"> / </abbr>
				<span class="athlete<?php if ($match->isTeam2PartnerSubstitute()): ?> substitute<?php endif; ?>" about="<?php echo $match->Team2_Partner->getCurie(); ?>" typeof="sport:Athlete">
					<span property="sport:lastName"><?php echo $match->Team2_Partner->getLastName(); ?></span>, 
					<span property="sport:firstName"><?php echo $match->Team2_Partner->getFirstName(); ?></span>
				</span>
				<?php endif; ?>
				<?php if ($match->isTeam2Winner()): ?></em><?php elseif (!$match->isTeam2Single()): ?></span><?php endif; ?>
				</span>
			</td>
			<td class="games" rel="sport:subMatch" resource="[_:match-<?php echo $match['id']; ?>_game1]">
				<ol>
				<?php $gamenum = 1; ?>
				<?php foreach ($match['effective_games'] as $game): ?>
					<li id="game-<?php echo $game['id'] ?>" about="[_:match-<?php echo $match['id']; ?>_game<?php echo $gamenum; ?>]" typeof="rdf:List" rel="rdf:first" resource="<?php echo $game->getCurie(); ?>">
						<span about="<?php echo $game->getCurie(); ?>" typeof="sport:Match" rel="sport:outcome">
							<span typeof="sport:TeamScore">
								<span style="display:none;" rel="sport:participant" resource="<?php echo $match->getTeam1Curie(); ?>"></span>
								<span class="score" property="sport:score" datatype="xsd:unsignedShort"><?php echo $game['team1_score'] ?></span>
							</span>
							<abbr> : </abbr>
							<span typeof="sport:TeamScore">
								<span style="display:none;" rel="sport:participant" resource="<?php echo $match->getTeam2Curie(); ?>"></span>
								<span class="score" property="sport:score" datatype="xsd:unsignedShort"><?php echo $game['team2_score'] ?></span>
							</span>
						</span>
						<span style="display:none;" about="[_:match-<?php echo $match['id']; ?>_game<?php echo $gamenum; ?>]" rel="rdf:rest" resource="[<?php echo ($gamenum == count($match['effective_games'])) ? 'rdf:nil' : "_:match-{$match['id']}_game".(++$gamenum); ?>]"></span>
					</li>
				<?php endforeach; ?>
				</ol>
				<?php if (count($match['annulled_games']) > 0): ?>
				<del<?php if ($teammatch->getApprovedAt()): ?> datetime="<?php echo $teammatch->getApprovedAt(); ?>"<?php endif; 
					?><?php if ($teammatch->getWrongdoer()): ?> cite="#revaluation-reason"<?php endif; ?>>
				<ol class="annulled" title="<?php echo __('original result'); ?>">
				<?php foreach ($match['annulled_games'] as $game): ?>
					<li id="game-<?php echo $game['id'] ?>">
						<span class="score"><?php echo $game['team1_score'] ?></span><abbr> : </abbr><span class="score"><?php echo $game['team2_score'] ?></span>
					</li>
				<?php endforeach; ?>
				</ol>
				</del>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>