@props(['label', 'readonly' => false, 'filled' => false, 'required' => false, 'error' => null, 'helpText' => null])

<div>
    <label {{ $attributes->merge(['class' => 'block text-[#084E8F] font-semibold mb-2']) }}>
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    <div class="input-wrapper {{ $readonly ? 'readonly' : '' }} {{ $filled ? 'filled' : '' }}">
        {{ $slot }}
    </div>
    @if($error)
        <p class="text-red-500 text-sm mt-2">{{ $error }}</p>
    @endif
    @if($helpText)
        <p class="text-gray-500 text-sm mt-2">{{ $helpText }}</p>
    @endif
</div>
