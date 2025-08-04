# Chivalry Engine Feature Analysis & Merge Plan

## Features from chivalry-is-dead-game to Merge

### 1. **Farming System** (farm.php)
- Complete farming mechanics with fields, crops, water management
- Experience and leveling system for farming
- Seasonal bonuses and events
- Integration with skills system

### 2. **Mining System** (mine.php)
- Mining for resources with power/energy management
- Mining experience and leveling
- Auto-miners functionality
- Special mining events and bonuses
- Potion effects for mining

### 3. **Stock Market/Investment System** (investmarket.php)
- Complete asset investment market
- Buy/sell shares mechanics
- Portfolio management
- Market history tracking
- Profit/loss tracking

### 4. **Husbandry System** (husbandry.php)
- Animal raising and breeding
- Animal care mechanics
- Production system (milk, eggs, etc.)

### 5. **Woodcutting System** (woodcut.php)
- Tree cutting mechanics
- Wood resource gathering
- Experience system

### 6. **Smithing/Smelting System** (smelt.php)
- Item crafting from raw materials
- Smelting ores into usable materials
- Progressive smithing levels

### 7. **Special Event Systems**
- Multiple holiday events (Halloween, Christmas, Easter, etc.)
- Anniversary events (1yrann.php, 2yrann.php)
- Seasonal events with special rewards

### 8. **Advanced Features**
- Achievements system (more comprehensive)
- Daily rewards system (dailyreward.php)
- Scratch tickets (scratchticket.php)
- Raffle system (raffle.php)
- Russian roulette gambling (russianroulette.php)
- Hi-Low card game (hilow.php)
- Roulette gambling (roulette.php)

### 9. **Social/Community Features**
- Marriage system with perks (marriage.php, marriage_perks.php)
- Poke system (poke.php)
- Polling/voting system (polling.php, vote.php)
- Milestones tracking (milestones.php)

### 10. **Economy Features**
- Token bank system (tokenbank.php, alltoken.php)
- Big bank system (bigbank.php)
- All bank system (allbank.php)
- Bank store (bankstore.php)
- Secondary market (secmarket.php)

### 11. **Combat/PvP Features**
- Street bum fighting (streetbum.php)
- Bounty system (bounty.php)
- Theft system (theft.php)
- Enemy system (enemy.php)

### 12. **Utility Features**
- Notepad system (notepad.php)
- User logs (userlogs.php)
- Item appendix (itemappendix.php)
- Search functionality (search.php, searchitem.php)

## Features Already in Modern Repo
- Pet system
- Skill tree system
- Crafting system (basic)
- Events system
- Leaderboards
- World boss system
- Guild wars
- Battle royale
- Achievements (basic)
- Daily rewards (basic)
- Dungeons

## Implementation Priority

### High Priority (Core Game Systems)
1. Farming System - Adds depth to resource gathering
2. Mining System - Essential resource generation
3. Stock Market - Economic depth
4. Smithing/Smelting - Crafting expansion

### Medium Priority (Engagement Features)
5. Husbandry System
6. Woodcutting System
7. Advanced gambling games
8. Marriage system

### Low Priority (Nice to Have)
9. Holiday events
10. Miscellaneous social features

## Database Tables Needed

From analysis of the code, these tables will need to be created:
- `farm_users` - User farming data
- `farm_fields` - Field management
- `mining` - User mining data
- `asset_market` - Stock market assets
- `asset_market_owned` - User stock holdings
- `asset_market_history` - Market history
- `asset_market_profit` - User profits
- `husbandry_users` - Animal management
- `woodcutting` - Woodcutting progress
- `smithing` - Smithing data
- `marriages` - Marriage records
- `milestones` - Achievement milestones

## Notes
- Many features use older PHP/MySQL syntax that needs updating for PHP 8+
- Bootstrap classes need updating from v3/v4 to v5.3
- Some features may have security issues that need addressing
- CSRF protection needs to be added using getHtmlCSRF()
- Database queries need to use proper escaping