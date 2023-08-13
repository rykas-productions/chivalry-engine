<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Withdraw') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold">Withdraw Currency</h2>

                <form action="{{ route('bank.processWithdraw') }}" method="POST" class="mt-4">
                    @csrf
                    <label for="withdraw" class="mr-2">Withdraw Amount:</label>
                    <input type="number" name="withdraw" id="withdraw" min="1" value="1" class="border px-2 py-1 rounded">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Withdraw</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
