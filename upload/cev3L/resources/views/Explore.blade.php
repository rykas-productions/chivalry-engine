<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Explore') }}
        </h2>
    </x-slot>

    <div class="container mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Markets and Shops</h3>
                <div><a href="{{ url('/ItemMarket') }}">Item Market</a></div>
                <div><a href="{{ url('/Market') }}">Market</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Accounting and Money</h3>
                <div><a href="{{ url('/Bank') }}">Bank</a></div>
                <div><a href="{{ url('/Estate') }}">Estate Agent</a></div>
                <div><a href="{{ url('/Travel') }}">Travel Agent</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Personal Work</h3>
                <div><a href="{{ url('/Gym') }}">Gym</a></div>
                <div><a href="{{ url('/Crimes') }}">Crimes</a></div>
                <div><a href="{{ url('/Academy') }}">Academy</a></div>
                <div><a href="{{ url('/Work') }}">Work</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Game Administration</h3>
                <div><a href="{{ url('/Users') }}">Player List</a></div>
                <div><a href="{{ url('/GameStaff') }}">Game Staff</a></div>
                <div><a href="{{ url('/FederalDungeon') }}">Federal Dungeon</a></div>
                <div><a href="{{ url('/GameStats') }}">Game Stats</a></div>
                <div><a href="{{ url('/PlayerReport') }}">Player Report</a></div>
                <div><a href="{{ url('/Announcements') }}">Announcements</a></div>
                <div><a href="{{ url('/ItemAppendix') }}">Item Appendix</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">High Risk Gambling</h3>
                <div><a href="{{ url('/Slots') }}">Slots</a></div>
                <div><a href="{{ url('/Roulette') }}">Roulette</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Guild Territory</h3>
                <div><a href="{{ url('/Guilds') }}">Known Guilds</a></div>
                <div><a href="{{ url('/GuildWars') }}">Known Guild Wars</a></div>
            </div>

            <div class="col-auto bg-white dark:bg-gray-400 overflow-hidden shadow-sm sm:rounded-lg flex flex-col p-4">
                <h3 class="text-center">Social</h3>
                <div><a href="{{ url('/Dungeon') }}">Dungeon</a></div>
                <div><a href="{{ url('/Infirmary') }}">Infirmary</a></div>
                <div><a href="{{ url('/Forums') }}">In-Game Forums</a></div>
                <div><a href="{{ url('/Newspaper') }}">Newspaper</a></div>
                <div><a href="{{ url('/HallOfFame') }}">Hall Of Fame</a></div>
                <div><a href="{{ url('/{PollingCenter}') }}">Polling Center</a></div>
                <div><a href="{{ url('/GameTutorial') }}">Game Tutorial</a></div>
            </div>

        </div>
    </div>
</x-app-layout>