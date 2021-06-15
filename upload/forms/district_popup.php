<div class="modal fade" id="district_info" tabindex="-2" role="dialog" aria-labelledby="district_info" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShortcutLabel">Guild Districts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Think of the districts just like the game of Risk. The districts are played on a guild-level, and everything 
				is funded from the guilt itself. To advance and become a powerful contender on the guild districts board, 
				you must have a very active guild, lots of members. The goal is to reward both the well established players, 
				and those who strive really hard to be the best.<hr />
				<b>Getting Started</b><br />
				The board itself is a <?php echo $districtConfig['MaxSizeX']; ?> x <?php echo $districtConfig['MaxSizeY']; ?> grid. To get on the board, you must attack the outside tiles, and then make your way in. You may attack in any direction 
				from a tile.<br />
				Recruit troops for your guild via Guild Info. The amount of troops you may recruit per day is limited by 
				your guild's level, up to a maximum of 400/200 troops a day.
				<hr />
				<b>Units</b><br />
				There's four units in the districts: Warriors, Archers, Generals and Captains. <b>Warriors</b> are melee units, who get up and 
				personal with the enemy. Without proper archer cover, however, they're good as gone. Likewise, <b>archers</b> without a 
				frontline of Warriors are toast.<br />
				<b>Generals</b> are a purely defensive unit which may be assigned to tiles you own. This grants that tile an extra <?php echo round($districtConfig['GeneralBuff']*100); ?>% defense 
				buff. You may only place <?php echo $districtConfig['maxGenerals']; ?> generals on a single tile at a time.<br />
				<b>Captains</b>, on the flip side, are an offensive unit. Each battle a captain particpates in increases offesnive abilities 
				by <?php echo round($districtConfig['GeneralBuff']*100); ?>%. Note that Captains charge <?php echo number_format($districtConfig['CaptainCostUse']) ?> Copper Coins 
				per battle they're involved.
				<hr />
				<b>District Tiles</b><br />
				Tiles can very easily be identified by the color of their background and border.<br />
				Blue Tiles - Water.<br />
				Green tiles - Tile owned by your guild.<br />
				Red tiles - Tile owned by others.<br />
				Tan tiles - Tiles unowned.<br />
				Green border - Fortified tile owned by your guild.<br />
				Red border - Fortified tile owned by other guild.<br />
				Inset border - High ground. Increases tile's attack/defense by 25%.<br />
				Ridge border - Low ground. Decreases tile's attack/defense by 25%.<br />
				Dashed border - Market (-<?php echo number_format($districtConfig['townLessCost'] * 100); ?>% troop cost, stacks)<br />
				Dotted border - Outpost (+<?php echo number_format($districtConfig['outpostExtraTroops'] * 100); ?>% troops per day, stacks)
				<hr />
				<b>Recruiting Troops</b><br />
				Recruiting troops helps strength your army. Stronger units cost more. Warriors cost <?php echo number_format($districtConfig['WarriorCost']); ?> Copper Coins; Archers 
				charge <?php echo number_format($districtConfig['ArcherCost']); ?> Copper Coins; Generals charge <?php echo number_format($districtConfig['GeneralCost']); ?> Copper Coins; 
				Captains charge <?php echo number_format($districtConfig['CaptainCost']); ?> Copper Coins. Warriors are melee, Archers are ranged, Generals 
				are defensive supportive, Captains are offensive support.<br />
				<br />
				If you lose a tile with a general on it, your general will be executed by the enemy guild.<br />
				<u>There may be more units in the future depending on how they fit and balance.</u>
				<hr />
				<b>Movement</b><br />
				Each day, your guild will be given two movements to spend on transferring troops, attacking, or however you 
				see fit. You may get an extra two turns if you successfully attack and conquer a tile. Most actions, other than 
				buying troops, exhausts a movement point.
				<hr />
				<b>Upkeep</b><br />
				Units actively on duty cost your guild! You will be charged for each unit currently on the board. Warriors on 
				cost your guild <?php echo number_format($districtConfig['WarriorCostDaily']); ?> Copper Coins, Archers cost your guild <?php echo number_format($districtConfig['WarriorCostDaily']); ?> Copper Coins, and Generals cost your guild 
				<?php echo number_format($districtConfig['GeneralCostDaily']); ?> Copper Coins. This is taken from your guild's vault once a day.
				<hr />
				<b>Fortification</b><br />
				District tiles may be fortified up to <?php echo $districtConfig['maxFortify']; ?> times. Fortification increases your defensive value by <?php echo ceil($districtConfig['fortifyBuffMulti']*100); ?>% per 
				fortification level. A fortification level requires the following: Guild Experience and Chivalry Tokens, both of 
				which scale with the district's fortication level. These must be in your guild's vault/armory. Later fortification levels 
				will require Generals on the tile before you can upgrade! 
				Fortifications are lost when you lose ownership of the tile it was on.
				<hr />
				<b>Tips</b><br />
				*Defending from the high-ground will be easy, but taking a high-ground tile will prove a little more difficult.<br />
				*Attacking a low-ground tile will be easy, defending it will not.<br />
				*Market tiles are highly sought after. Once you take one, you will have to defend it hard.<br />
				*A tile cannot be more than one tile type at a time. (IE: High ground + market)<br />
				*You can recruit troops more than once a day. This is helpful is your guild level's up.<br />
				*Warriors and Archers can be recruited at a 20:10:1 ratio. 20 Warriors: 10 Archers: 1 Guild Level<br />
            </div>
        </div>
    </div>
</div>