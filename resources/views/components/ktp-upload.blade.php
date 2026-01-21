@props([
    'videoId' => 'webcam_video',
    'guideText' => 'Posisikan KTP Anda di dalam frame',
])

<div {{ $attributes->merge(['class' => '']) }}>
    {{-- Webcam Video with KTP Overlay --}}
    <div class="video-wrapper">
        <video id="{{ $videoId }}" autoplay playsinline></video>
        
        {{-- KTP Overlay Guide --}}
        <div class="ktp-overlay">
            <div class="ktp-frame">
                <div class="ktp-guide-text">{{ $guideText }}</div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Video Wrapper & KTP Overlay */
    .video-wrapper {
        position: relative;
        width: 100%;
        margin-bottom: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    /* Mirror video untuk UX lebih baik */
    #{{ $videoId }} {
        transform: scaleX(-1);
        width: 100%;
        display: block;
    }

    .ktp-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        z-index: 10;
    }

    .ktp-frame {
        position: relative;
        width: 85%;
        aspect-ratio: 1.586;
        max-width: 450px;
        border: 3px dashed #47B9AE;
        border-radius: 12px;
        background: transparent;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.65);
    }

    /* Guide Text */
    .ktp-guide-text {
        position: absolute;
        top: -45px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(71, 185, 174, 0.95);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .ktp-frame {
            width: 90%;
        }
        
        .ktp-guide-text {
            font-size: 12px;
            padding: 8px 16px;
            top: -40px;
        }
    }
</style>
@endpush
