@push('styles')
    <style>
        /* Base Styles */
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: white;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #084E8F;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 32px;
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

        /* Autocomplete Dropdown */
        .autocomplete-dropdown {
            position: absolute;
            margin-top: 10px;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #084E8F;
            border-radius: 8px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 50;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .autocomplete-dropdown.show {
            display: block;
        }

        .autocomplete-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        .autocomplete-item:hover {
            background-color: #F9FCFF;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-name {
            color: #1e40af;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .autocomplete-detail {
            color: #6b7280;
            font-size: 14px;
        }

        /* Karyawan Search & Card */
        .karyawan-search-row {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .karyawan-search-container {
            flex: 1;
            position: relative;
            height: 50px;
        }

        .karyawan-action-buttons {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .karyawan-card {
            display: flex;
            align-items: center;
            padding: 0 12px;
            background-color: white;
            border: 2px solid #084E8F;
            border-radius: 8px;
            width: 100%;
            height: 50px;
            box-sizing: border-box;
            cursor: pointer;
            transition: all 0.2s;
        }

        .karyawan-card:hover {
            background-color: #f0f9ff;
            border-color: #0C4777;
        }

        .karyawan-card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 4px 0;
            gap: 2px;
            min-width: 0;
            overflow: hidden;
        }

        .karyawan-card-name {
            color: #084E8F;
            font-weight: 600;
            font-size: 15px;
            line-height: 1.3;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .karyawan-card-detail {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

        /* Input Wrapper */
        .input-wrapper {
            border: 2px solid #084E8F;
            border-radius: 8px;
            padding: 8px;
            width: 100%;
            transition: all 0.2s ease;
            background-color: #F9FCFF;
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
            border: none;
            outline: none;
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
        }

        .error-message.show {
            display: block;
        }
    </style>
@endpush