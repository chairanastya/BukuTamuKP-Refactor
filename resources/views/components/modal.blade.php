@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'title' => null,
    'showHeader' => true,
    'showCloseButton' => true,
    'id' => null,
    'useAlpine' => true,
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    'large' => 'sm:max-w-3xl',
][$maxWidth] ?? 'sm:max-w-2xl';

$modalId = $id ?? $name;
@endphp

<style>
    .modal-overlay {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 50;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
    }

    .modal-content.large {
        max-width: 900px;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #084E8F;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 2rem;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
        line-height: 1;
    }

    .modal-close:hover {
        color: #374151;
    }
</style>
    .modal-close:hover {
        color: #374151;
    }
</style>

@if($useAlpine)
    <div
        x-data="{
            show: @js($show),
            focusables() {
                let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
                return [...$el.querySelectorAll(selector)]
                    .filter(el => ! el.hasAttribute('disabled'))
            },
            firstFocusable() { return this.focusables()[0] },
            lastFocusable() { return this.focusables().slice(-1)[0] },
            nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
            prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
            nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
            prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
        }"
        x-init="$watch('show', value => {
            if (value) {
                document.body.classList.add('overflow-y-hidden');
                {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
            } else {
                document.body.classList.remove('overflow-y-hidden');
            }
        })"
        x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
        x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
        x-on:close.stop="show = false"
        x-on:keydown.escape.window="show = false"
        x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
        x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
        x-show="show"
        class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
        style="display: {{ $show ? 'block' : 'none' }};"
    >
        <div
            x-show="show"
            class="fixed inset-0 transform transition-all"
            x-on:click="show = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div
            x-show="show"
            class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            @if($showHeader && $title)
                <div class="modal-header px-6 pt-6 pb-4">
                    <h2 class="modal-title">{{ $title }}</h2>
                    @if($showCloseButton)
                        <button type="button" class="modal-close" x-on:click="show = false">&times;</button>
                    @endif
                </div>
                <div class="px-6 pb-6">
                    {{ $slot }}
                </div>
            @else
                {{ $slot }}
            @endif
        </div>
    </div>
@else
    {{-- Legacy Modal (Vanilla JS) --}}
    <div id="{{ $modalId }}" class="modal-overlay" {{ $attributes }}>
        <div class="modal-content {{ $maxWidth === 'sm:max-w-3xl' || $maxWidth === 'sm:max-w-4xl' ? 'large' : '' }}">
            @if($showHeader)
                <div class="modal-header">
                    @if($title)
                        <h2 class="modal-title">{{ $title }}</h2>
                    @else
                        {{ $header ?? '' }}
                    @endif
                    @if($showCloseButton)
                        @if(isset($closeButton))
                            {{ $closeButton }}
                        @else
                            <button type="button" class="modal-close" onclick="closeModal('{{ $modalId }}')">&times;</button>
                        @endif
                    @endif
                </div>
            @endif
            <div class="modal-body">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.classList.add('overflow-y-hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.classList.remove('overflow-y-hidden');
        }

        // Close on overlay click
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('show');
                document.body.classList.remove('overflow-y-hidden');
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.show').forEach(modal => {
                    modal.classList.remove('show');
                    document.body.classList.remove('overflow-y-hidden');
                });
            }
        });
    </script>
@endif