@props(['name' => 'isi_notulensi', 'value' => ''])

<div class="input-wrapper">
    <div id="quill-editor" class="bg-white" style="min-height:220px;">{!! $value !!}</div>
    <input type="hidden" name="{{ $name }}" id="{{ $name }}_input" value="{{ $value }}">
</div>

@once
    @push('styles')
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var initialContent = @json($value);
                var quill;
                if (document.querySelector('#quill-editor')) {
                    quill = new Quill('#quill-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'header': [1, 2, 3, false] }],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['link']
                            ]
                        }
                    });

                    if (initialContent) {
                        quill.root.innerHTML = initialContent;
                    }

                    // Disable image paste and drop to prevent inserting images
                    quill.root.addEventListener('paste', function (e) {
                        try {
                            if (e.clipboardData && e.clipboardData.items) {
                                for (var i = 0; i < e.clipboardData.items.length; i++) {
                                    var item = e.clipboardData.items[i];
                                    if (item && item.type && item.type.indexOf('image') !== -1) {
                                        e.preventDefault();
                                        return;
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Error handling paste event:', err);
                        }
                    });

                    quill.root.addEventListener('drop', function (e) {
                        try {
                            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                                // Prevent dropping files (images)
                                e.preventDefault();
                                return;
                            }
                        } catch (err) {
                            console.error('Error handling drop event:', err);
                        }
                    });

                    var form = document.getElementById('notulensi-form');
                    if (form) {
                        form.addEventListener('submit', function (e) {
                            var html = quill.root.innerHTML;
                            document.getElementById('{{ $name }}_input').value = html;
                        });
                    }
                }
            });
        </script>
    @endpush
@endonce
