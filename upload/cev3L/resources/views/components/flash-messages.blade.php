@props(['type' => 'success', 'message' => null])

<x-dialog-modal :id="'flashMessageModal'" :maxWidth="'sm'">
    <div class="px-6 py-4">
        <div class="text-lg font-medium text-gray-900 dark:text-gray-100">
            @if ($type === 'success')
                Success
            @elseif ($type === 'error')
                Error
            @endif
        </div>

        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            {{ $message }}
        </div>
    </div>

    <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 dark:bg-gray-800 text-right">
        <button onclick="hideFlashMessage()" class="bg-gray-300 text-gray-700 px-3 py-2 rounded-md hover:bg-gray-400">Close</button>
    </div>
</x-dialog-modal>

<script>
    function showFlashMessage() {
        document.getElementById('flashMessageModal').style.display = 'block';
    }

    function hideFlashMessage() {
        document.getElementById('flashMessageModal').style.display = 'none';
    }
</script>