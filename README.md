# Chivalry Engine - Modern Edition
Chivalry Engine is a free to use and open source engine created by TheMasterGeneral. This modern edition has been upgraded to Bootstrap 5.3 and includes numerous enhancements for improved gameplay and user experience.

# Requirements
<<<<<<< Updated upstream
A web-server with PHP 7.0+ and MySQLi support. Users need to have Javascript enabled or a lot of the core features won't work. With the inclusion of Bootstrap V4, you cannot run this engine on clients running Internet Explorer 9 or older. Its recommended that users on Android use a browser different to the default one, unless they're running Android 5.0 or newer.  
=======
A web-server with PHP 7.4+ and MySQLi support. Users need to have Javascript enabled or a lot of the core features won't work. With the inclusion of Bootstrap 5.3, this engine supports all modern browsers and provides excellent mobile responsiveness. 
>>>>>>> Stashed changes
  
# Live Game
Want to give v1 of the engine a test in a live game instance? Check out [Chivalry is Dead](https://chivalryisdeadgame.com)!
 
<<<<<<< Updated upstream
# Goals for V3
Chivalry Engine V3 is a full rewrite of Chivalry Engine with an object-orientated programming style in mind. This hopefully makes life easier when developing and running a game on Chivalry Engine. This will likely not be compatible with previous version of Chivalry Engine.

# Major Changes in V3
N/A
=======
# Modern Edition Features
This enhanced version includes all the improvements from V2 plus additional modern enhancements:

## Core Upgrades
- **Bootstrap 5.3**: Complete upgrade from Bootstrap 4 to 5.3 with modern components
- **Real-time Stats**: HP and energy update instantly without page refresh
- **Enhanced UI**: Improved sidebar, better theming, and mobile-first responsive design
- **Performance Optimization**: Faster loading times and improved database queries

## New Systems
- **VIP Benefits System**: Comprehensive VIP membership with shop discounts, gym bonuses, and exclusive features
- **Marriage System**: Complete relationship system with proposals, gifts, and partner management
- **Enhanced Estates**: Modern estate system with realistic economics and VIP benefits
- **Theme Management**: Advanced theme switching with 5 distinct themes including medieval and high contrast modes

## Restored Content
- **Complete Item Types**: All 18 original item categories restored from backup
- **Towns & Shops**: Fully detailed towns and shops with immersive descriptions
- **Game Balance**: Original pricing, bonuses, and progression systems maintained

## Installation
1. Copy files to your web server
2. Copy `config.example.php` to `config.php` and configure database settings
3. Import `cengine.sql` to create the database structure  
4. Run `restore_game_data.php` as admin to restore all original content
5. Configure your web server to point to the upload directory 
>>>>>>> Stashed changes

# Chivalry Engine V3 License
MIT License

Copyright (c) 2019 TheMasterGeneral

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
