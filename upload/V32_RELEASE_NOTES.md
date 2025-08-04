# Chivalry Engine v3.2 Release Notes

## 🎉 New Features

### 🌾 **Farming System**
- **Location**: Activities → Farming
- Complete agricultural system with crops and fields
- Water management with wells
- 9 different crops with varying grow times and rewards
- Experience and leveling system
- Field health affects harvest yield
- Seasonal bonuses available

### 📈 **Stock Market System**
- **Location**: Economy → Stock Market  
- Buy and sell shares in 8 different companies
- Portfolio management with profit/loss tracking
- Risk levels: Low, Medium, High
- Market history and trends
- Leaderboard for top investors
- Real-time price fluctuations

### ⚡ **Percentage-Based Energy Regeneration**
- Energy, Will, Brave, and HP now regenerate by percentage
- **Regular Users**: 1.5% per minute
- **VIP Users**: 3% per minute (2x bonus!)
- Event-driven system (no cron required)
- Capped at 4 hours offline regeneration

## 📊 System Requirements
- PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Bootstrap 5.3 (included)

## 🔧 Installation

### For Admins (userid 1)
1. You'll see an update notification on your dashboard
2. Click "Run Database Update" or navigate to `uplift_check.php`
3. Review the updates needed
4. Click "Apply Updates" to install v3.2 features
5. Database will automatically update to v3.2.0

### Database Changes
**New Tables:**
- `farm_users` - User farming data
- `farm_fields` - Field management
- `farm_crops` - Crop definitions
- `asset_market` - Stock market assets
- `asset_market_owned` - User holdings
- `asset_market_history` - Price history
- `asset_market_profit` - Profit tracking

**New Columns:**
- `users.last_regen` - For percentage-based regeneration

## 🎮 How to Use New Features

### Farming
1. Visit Activities → Farming
2. Build your well (one-time cost: 10,000 gold)
3. Plant crops in your fields
4. Tend fields regularly to maintain health
5. Harvest when ready for gold or items
6. Level up to unlock better crops

### Stock Market
1. Visit Economy → Stock Market
2. Review available stocks and their risk levels
3. Buy shares when prices are low
4. Monitor your portfolio
5. Sell when prices rise for profit
6. Check leaderboard for top investors

## 🌟 VIP Benefits
- **2x Energy Regeneration**: 3% per minute instead of 1.5%
- Applies to Energy, Will, Brave, and HP
- Significant advantage in all activities

## 🐛 Bug Fixes
- Fixed farming system xp_needed display error
- Fixed uplift_check version detection
- Fixed menu items showing before installation
- Fixed private property access in FarmingSystem class

## 📝 Technical Notes

### For Developers
- All new features use Bootstrap 5.3
- CSRF protection on all forms using `getHtmlCSRF()`
- Event-driven regeneration in `event_system.php`
- Features check for v3.2 installation before displaying

### API Changes
- Energy regeneration now percentage-based in `processEnergyRegeneration()`
- New classes: `FarmingSystem`, `StockMarketSystem`

## 🔜 Coming Soon (v3.3+)
- Clan/Alliance System
- Weather System
- Faction Warfare
- More crops and stocks
- Advanced trading features

## 📞 Support
If you encounter any issues:
1. Ensure you've run `uplift_check.php`
2. Check that your database shows version 3.2.0
3. Clear browser cache if menu items don't appear
4. Contact admin if features are missing

## 📊 Version History
- **v3.0.0**: Base modernization with Bootstrap 5.3
- **v3.1.0**: Skill trees, world bosses, pets, dungeons
- **v3.2.0**: Farming, stock market, percentage energy

---

**Thank you for playing Chivalry Engine!**

*Remember: With great features comes great responsibility. Farm wisely, invest carefully!*