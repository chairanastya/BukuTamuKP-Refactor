@push('styles')
    <style>
        /* Base Styles */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: white;
        }

        /* Karyawan Search Layout */
        .karyawan-search-row {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            width: 100%;
        }

        .karyawan-search-container {
            flex: 1 1 auto;
            position: relative;
            height: 50px;
            min-width: 200px;
            max-width: 100%;
        }

        .karyawan-action-buttons {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .karyawan-search-input {
            background-color: transparent;
            width: 100%;
            border: none;
            outline: none;
        }

        /* Action Buttons */
        .karyawan-add-btn,
        .karyawan-minus-btn {
            width: 50px;
            height: 50px;
            border: 2px dashed #084E8F;
            border-radius: 6px;
            background-color: white;
            color: #084E8F;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
            font-weight: bold;
            padding: 0;
        }

        .karyawan-add-btn:hover,
        .karyawan-minus-btn:hover {
            background-color: #f0f9ff;
            transform: scale(1.05);
        }

        .karyawan-add-btn svg,
        .karyawan-minus-btn svg {
            width: 28px;
            height: 28px;
        }

        .karyawan-minus-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            background-color: #f3f4f6;
            border-color: #d1d5db;
            color: #9ca3af;
        }

        .karyawan-minus-btn:disabled:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            transform: none;
        }

        /* Video Wrapper & KTP Overlay */
        .video-wrapper {
            position: relative;
            width: 100%;
            margin-bottom: 0;
            border-radius: 8px;
            overflow: hidden;
        }

        /* Mirror video untuk UX lebih baik */
        #webcam_video {
            transform: scaleX(-1);
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
            border-radius: 8px;
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
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            color: #084E8F;
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

        /* Form Container Layout */
        .form-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .form-layout {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .form-left {
            flex: 1;
            min-width: 0;
            max-width: 100%;
        }

        .form-right {
            width: 100%;
        }

        /* iPad Landscape (min 1024px) - Layout 2 kolom */
        @media (min-width: 1024px) and (orientation: landscape) {
            .form-layout {
                flex-direction: row;
                gap: 2rem;
            }

            .form-left {
                flex: 1;
                min-width: 0;
            }

            .form-right {
                width: 320px;
                flex-shrink: 0;
                position: sticky;
                top: 1rem;
                align-self: flex-start;
            }

            .form-container {
                padding: 0 1.5rem;
            }
        }

        /* Desktop besar - lebar form right lebih besar */
        @media (min-width: 1025px) {
            .form-layout {
                flex-direction: row;
                gap: 2rem;
            }

            .form-left {
                flex: 1;
                min-width: 0;
            }

            .form-right {
                width: 384px;
                flex-shrink: 0;
                position: sticky;
                top: 1rem;
                align-self: flex-start;
            }
        }

        /* Responsive untuk Zoom 110%-150% */
        @media (min-width: 1024px) and (max-width: 1600px) {
            .form-container {
                max-width: calc(100vw - 150px);
                padding: 0 1.5rem;
            }
        }

        /* Mobile Portrait, Mobile Landscape, iPad Portrait - vertikal layout */
        @media (max-width: 1023px) {
            .form-container {
                max-width: 100%;
                padding: 0 1rem;
            }

            .form-layout {
                flex-direction: column;
            }

            .form-right {
                width: 100%;
            }
        }
    </style>

    {{-- Include component styles by rendering hidden components --}}
    <div style="display: none;">
        <x-input-wrapper id="style_loader" name="style_loader" />
        <x-autocomplete-dropdown id="style_loader_autocomplete" />
        <x-karyawan-card nama="Loader" />
    </div>
@endpush