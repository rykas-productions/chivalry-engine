<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Explore') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-s sm:rounded-lg">

        <b><center>Work In Progress</center></b>

    </div>

    <div class="container mx-auto">    
            <div class="grid grid-cols-4 grid-flow-row gap-4">

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Markets and Shops</center></h3>
                    <a href="#">Item Market</a><br />
                    <a href="#">Market</a>
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Accounting and Money</center></h3>
                    <a href="bank.php">Bank</a><br />
                    <a href="#">Estate Agent</a><br />
                    <a href="#">Travel Agent</a>
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Personal Work</center></h3>
                    <a href="gym.php">Gym</a><br />
                    <a href="#">Crimes</a><br />
                    <a href="#">Academy</a><br />
                    <a href="#">Work</a>
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Game Administration</center></h3>
                    <a href="users.php">Player List</a><br />
                    <a href="#">Game Staff</a><br />
                    <a href="#">Federal Dungeon</a><br />
                    <a href="#">Game Stats</a><br />
                    <a href="#">Player Report</a><br />
                    <a href="announcements.php">Announcements</a><br />
                    <a href="#">Item Appendix</a><br />
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>High Risk Gambling</center></h3>
                    <a href="slots.php">Slots</a><br />
                    <a href="#">Roulette</a><br />
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Guild Territory</center></h3>
                    <a href="#">Known Guilds</a><br />
                    <a href="#">Known Guild Wars</a>
                </div>

                <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-s sm:rounded-lg">
                    <h3><center>Social</center></h3>
                    <a href="#">Dungeon</a><br />
                    <a href="infirmary.php">Infirmary</a><br />
                    <a href="#">In-Game Forums</a><br />
                    <a href="#">Newspaper</a><br />
                    <a href="#">Hall of Fame</a><br />
                    <a href="#">Polling Center</a><br />
                    <a href="#">Game Tutorial</a><br />
                </div>
            </div>
    </div>
</x-app-layout>