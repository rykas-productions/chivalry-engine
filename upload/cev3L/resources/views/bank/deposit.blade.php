<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Deposit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-semibold">Deposit Currency</h2>

                <form action="{{ route('bank.processDeposit') }}" method="POST" class="mt-4">
                    @csrf
                    <label for="deposit" class="mr-2">Deposit Amount:</label>
                    <input type="number" name="deposit" id="deposit" min="1" value="1" class="border px-2 py-1 rounded">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Deposit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
