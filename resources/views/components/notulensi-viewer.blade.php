@props(['content'])

<div class="input-wrapper notulensi-content">
    {!! $content !!}
</div>

@once
    @push('styles')
        <style>
            /* Make rendered notulensi look similar to Quill editor */
            .notulensi-content {
                background: white;
                padding: 12px;
                min-height: 220px;
                border-radius: 6px;
                font-size: 16px; /* increased font size for notulensi text only */
                line-height: 1.6; /* improve readability */
            }

            /* Headings coming from Quill editor (h1-h6) */
            .notulensi-content h1,
            .notulensi-content h2,
            .notulensi-content h3,
            .notulensi-content h4,
            .notulensi-content h5,
            .notulensi-content h6 {
                margin: 0 0 0.75rem;
                line-height: 1.25;
                font-weight: 600;
            }

            .notulensi-content h1 { font-size: 1.5rem; }
            .notulensi-content h2 { font-size: 1.25rem; }
            .notulensi-content h3 { font-size: 1.1rem; }
            .notulensi-content h4 { font-size: 1rem; }
            .notulensi-content h5 { font-size: 0.95rem; }
            .notulensi-content h6 { font-size: 0.9rem; }

            .notulensi-content p {
                margin: 0 0 0.75rem;
            }

            .notulensi-content ul,
            .notulensi-content ol {
                margin: 0 0 0.75rem 1.25rem;
                padding-left: 1.25rem;
                list-style-position: outside;
            }

            .notulensi-content ul {
                list-style-type: disc;
            }

            .notulensi-content ol {
                list-style-type: decimal;
            }

            .notulensi-content strong,
            .notulensi-content b {
                font-weight: 600;
            }

            /* Link styling for rendered notulensi: blue + underline, visited -> pink/purple */
            .notulensi-content a.notulensi-link,
            .notulensi-content a {
                color: #1a73e8;
                text-decoration: underline;
                text-decoration-color: #1a73e8;
                text-underline-offset: 2px;
                transition: color 0.15s ease;
            }

            .notulensi-content a:hover {
                color: #0b5ed7;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Normalize and style links inside rendered Quill HTML
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.querySelector('.notulensi-content');
                if (!container) return;

                container.querySelectorAll('a').forEach(function (a) {
                    try {
                        let rawHref = a.getAttribute('href') || '';

                        // If href looks like plain domain without scheme, prepend https://
                        if (rawHref && !/^[a-zA-Z][a-zA-Z0-9+.-]*:/.test(rawHref) && !rawHref.startsWith('#')) {
                            a.setAttribute('href', 'https://' + rawHref);
                        }

                        // Open in new tab and safe settings
                        a.setAttribute('target', '_blank');
                        a.setAttribute('rel', 'noopener noreferrer');

                        // Shorten visible text: remove protocol and trailing slash
                        const visible = (a.textContent || a.getAttribute('href') || '').toString();
                        const cleaned = visible.replace(/^https?:\/\//i, '').replace(/^www\./i, '').replace(/\/$/, '');
                        a.textContent = cleaned;

                        // Ensure styling class applied
                        a.classList.add('notulensi-link');
                    } catch (err) {
                        console.error('Error normalizing link in notulensi view:', err);
                    }
                });
            });
        </script>
    @endpush
@endonce
