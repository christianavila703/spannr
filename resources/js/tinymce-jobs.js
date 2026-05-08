/* Import TinyMCE */
import tinymce from 'tinymce';

/* Default icons are required. After that, import custom icons if applicable */
import 'tinymce/icons/default/icons.min.js';

/* Required TinyMCE components */
import 'tinymce/themes/silver/theme.min.js';
import 'tinymce/models/dom/model.min.js';

/* Import a skin (can be a custom skin instead of the default) */
import 'tinymce/skins/ui/oxide/skin.js';

/* Import plugins */
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/media';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/autosave';

import contentCss from "../sass/tinymce.scss";

window.addEventListener('load-job-editors', event => {

    tinymce.init({
        selector: '#job-description',
        plugins: 'advlist code table lists link autoresize charmap visualblocks visualchars wordcount autosave',
        toolbar: 'undo redo | insert | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link charmap | visualblocks visualchars wordcount restoredraft',
        browser_spellcheck: true,
        block_unsupported_drop: false,
        relative_urls: false,
        toolbar_sticky: true,
        content_style: contentCss.toString(),
        autosave_ask_before_unload: true,
        setup: function (editor) {
            editor.on('init change', function () {
                editor.save();
                document.getElementById('description').value = editor.getContent();
                Livewire.emit('updateDescription', editor.getContent());
            });
        }
    });

    tinymce.init({
        selector: '#job-qualifications',
        plugins: 'advlist code table lists link autoresize charmap visualblocks visualchars wordcount autosave',
        toolbar: 'undo redo | insert | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link charmap | visualblocks visualchars wordcount restoredraft',
        browser_spellcheck: true,
        block_unsupported_drop: false,
        relative_urls: false,
        toolbar_sticky: true,
        content_style: contentCss.toString(),
        autosave_ask_before_unload: true,
        setup: function (editor) {
            editor.on('init change', function () {
                editor.save();
                document.getElementById('qualifications').value = editor.getContent();
                Livewire.emit('updateQualifications', editor.getContent());
            });
        }
    });
});
