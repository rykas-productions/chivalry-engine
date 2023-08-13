<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bank') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Display user's bank account information -->
                @if ($userStats->primaryCurrencyBank == -1)
                    <h2 class="text-2xl font-semibold">Buy Bank Account</h2>
                    <!-- Display buy bank button -->
                    <button type="button" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showModal()">Buy Bank Account</button>
                    <!-- Include the modal content directly -->
                    <div class="modal" tabindex="-1" role="dialog" id="buyConfirmationModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <p>Are you sure you want to buy a bank account?</p>
                                </div>
                                <div class="modal-footer">
                                <form method="POST" action="{{ route('bank.purchase') }}">
                                    @csrf
                                    <input type="show" name="confirm_purchase" id="confirmPurchase" value="1"> <!-- Hidden input field -->
                                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Yes, Buy Bank Account</button>
                                </form>
                                <button onclick="hideModal()" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-400">No, Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <h2 class="text-2xl font-semibold">Bank Account</h2>
                    <p class="mb-4">Current Bank Currency Held: {{ $userStats->primaryCurrencyBank }}</p>
                    <p class="mb-4">Current Held Currency: {{ $userStats->primaryCurrencyHeld }}</p>
                    
                    <!-- Display deposit and withdraw forms -->
                    @if ($userStats->primaryCurrencyBank >= 0)
                        <!-- Display deposit form -->
                        <form action="{{ route('bank.Deposit') }}" method="POST" class="mb-4">
                            @csrf
                            <label for="deposit" class="mr-2">Deposit Amount:</label>
                            <input type="number" name="deposit" id="deposit" min="1" value="{{ old('deposit') }}" class="border px-2 py-1 rounded">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Deposit</button>
                        </form>

                        <!-- Display withdraw form -->
                        <form action="{{ route('bank.Withdraw') }}" method="POST" class="mb-4">
                            @csrf
                            <label for="withdraw" class="mr-2">Withdraw Amount:</label>
                            <input type="number" name="withdraw" id="withdraw" min="1" value="{{ old('withdraw') }}" class="border px-2 py-1 rounded">
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Withdraw</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>


    <script>
        function showModal() {
            document.getElementById('buyConfirmationModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('buyConfirmationModal').style.display = 'none';
        }
        function confirmPurchase() {
        document.getElementById('confirmPurchase').value = 1; // Set the value to 1 when confirmed
        }   
    </script>
</x-app-layout>