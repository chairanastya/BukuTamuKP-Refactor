@props([
    'id' => null,
    'name' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'error' => null,
    'errorMessage' => null,
    'showLabel' => true,
    'rows' => 4,
])

<style>
    /* Input Wrapper */
    .input-wrapper {
        border: 2px solid #084E8F;
        border-radius: 8px;
        padding: 8px;
        width: 100%;
        max-width: 100%;
        transition: all 0.2s ease;
        background-color: #F9FCFF;
        box-sizing: border-box;
    }

    .input-wrapper.filled {
        background-color: white;
    }

    .input-wrapper:focus-within {
        box-shadow: 0 0 0 3px rgba(8, 78, 143, 0.1);
    }

    .input-wrapper input,
    .input-wrapper textarea {
        background-color: transparent;
        width: 100%;
        max-width: 100%;
        border: none;
        outline: none;
        box-sizing: border-box;
    }

    /* Error States */
    .input-wrapper.error,
    .input-wrapper:has(input:invalid:not(:placeholder-shown)),
    .input-wrapper:has(textarea:invalid:not(:placeholder-shown)),
    .upload-area.error {
        border-color: #dc2626 !important;
        background-color: #fef2f2;
    }

    /* Error Message */
    .error-message {
        color: #dc2626;
        font-size: 14px;
        margin-top: 8px;
        display: none;
        word-wrap: break-word;
    }

    .error-message.show {
        display: block;
    }
</style>

<div {{ $attributes }}>
    @if($showLabel && $label)
        <label for="{{ $id }}" class="block text-[#084E8F] font-bold mb-2">
            {{ $label }}
        </label>
    @endif
    
    <div class="input-wrapper {{ $error ? 'error' : '' }}">
        @if($type === 'textarea')
            <textarea 
                id="{{ $id }}" 
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                rows="{{ $rows }}"
                {{ $required ? 'required' : '' }}
            >{{ $value }}</textarea>
        @else
            <input 
                type="{{ $type }}" 
                id="{{ $id }}" 
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                value="{{ $value }}"
                {{ $required ? 'required' : '' }}
            />
        @endif
    </div>

    @if($error)
        <div class="error-message show">
            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
            {{ $error }}
        </div>
    @elseif($errorMessage)
        <div id="{{ $id }}_error" class="error-message">
            @svg('heroicon-o-x-circle', 'inline w-4 h-4 mr-1')
            {{ $errorMessage }}
        </div>
    @endif
</div>