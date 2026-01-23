<div class="relative">
    <button onclick="toggleDropdown()" class="flex items-center gap-2">
        <span>{{ $userName }}</span>
        @svg('uiw-down', 'w-5 h-5')
    </button>
    <div id="dropdown" class="hidden absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg overflow-hidden z-10">
        <form method="POST" action="{{ $logoutRoute }}">
            @csrf
            <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 text-gray-700">
                Log Out
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdown');
        dropdown.classList.toggle('hidden');
    }

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('dropdown');
        const button = event.target.closest('button');
        
        if (!button || button.getAttribute('onclick') !== 'toggleDropdown()') {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
