/* Import TinyMCE */
import tinymce from 'tinymce';

/* Default icons are required. After that, import custom icons if applicable */
import 'tinymce/icons/default';

/* Required TinyMCE components */
import 'tinymce/themes/silver';
import 'tinymce/models/dom';

/* Import a skin (can be a custom skin instead of the default) */
import 'tinymce/skins/ui/oxide/skin.css';

/* Import plugins */
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/code';
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

/* content UI CSS is required */
import contentCss from '../sass/tinymce.scss';

tinymce.init({
    selector: 'textarea.tiny-editor',
    plugins: 'advlist code table lists media image link autoresize charmap visualblocks visualchars wordcount',
    toolbar: 'undo redo | image | insert | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link charmap | visualblocks visualchars wordcount',
    browser_spellcheck: true,
    block_unsupported_drop: false,
    automatic_uploads: true,
    file_picker_types: 'image',
    relative_urls: false,
    toolbar_sticky: true,
    content_style: contentCss.toString()
});
