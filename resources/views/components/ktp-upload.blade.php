@props([
    'type' => 'webcam', // 'webcam' or 'file'
    'id' => 'ktp-upload',
    'videoId' => 'webcam_video',
    'guideText' => 'Posisikan KTP Anda di dalam frame',
])

<div {{ $attributes->merge(['class' => '']) }}>
    @if($type === 'webcam')
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
    @else
        {{-- File Upload Area --}}
        <div class="upload-area" onclick="document.getElementById('{{ $id }}').click()">
            <div class="upload-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <p style="color: #084E8F; font-weight: 600; font-size: 18px; margin-bottom: 8px;">
                Upload KTP Anda
            </p>
            <p style="color: #6b7280; font-size: 14px;">
                Klik untuk memilih file atau drag & drop
            </p>
            <p style="color: #9ca3af; font-size: 12px; margin-top: 8px;">
                Format: JPG, PNG (Maks. 5MB)
            </p>
            <input type="file" id="{{ $id }}" accept="image/*" style="display: none;">
        </div>
    @endif
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

    /* Upload Area */
    .upload-area {
        border: 2px dashed #084E8F;
        border-radius: 12px;
        padding: 48px 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background-color: #F9FCFF;
    }

    .upload-area:hover {
        background-color: white;
        border-color: #0C4777;
    }

    .upload-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 16px;
        color: #084E8F;
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
        
        .upload-area {
            padding: 32px 24px;
        }
        
        .upload-icon {
            width: 60px;
            height: 60px;
        }
    }
</style>
@endpush
