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
import 'tinymce/plugins/autosave';

/* content UI CSS is required */
import contentCss from '../sass/tinymce.scss';

const iframeConfig = {
    title: 'Insert iFrame',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'iframeUrl',
                label: 'Embed URL'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Insert',
            buttonType: 'primary'
        }
    ],
    initialData: {
        iframeUrl: '',
    },
    onSubmit: (api) => {
        const data = api.getData();
        tinymce.activeEditor.execCommand('mceInsertContent', false, `<div class="ratio ratio-16x9"><iframe src="${data.iframeUrl}" scrolling="no" frameborder="0" style="border: none;" data-external="1"></iframe></div>`);
        api.close();
    }
};

const collapseConfig = {
    title: 'Insert Collapsable Content',
    body: {
        type: 'panel',
        items: [
            {
                type: 'input',
                name: 'heading',
                label: 'Heading'
            },
            {
                type: 'selectbox',
                name: 'headingType',
                label: 'Heading Type',
                items: [
                    {value: 'h3', text: 'h3'},
                    {value: 'h4', text: 'h4'},
                    {value: 'h5', text: 'h5'},
                    {value: 'h6', text: 'h6'},
                    {value: 'p', text: 'p'},
                    {value: 'h1', text: 'h1'},
                    {value: 'h2', text: 'h2'},
                ]
            },
            {
                type: 'input',
                name: 'anchor',
                label: 'Anchor'
            }
        ]
    },
    buttons: [
        {
            type: 'cancel',
            name: 'closeButton',
            text: 'Cancel'
        },
        {
            type: 'submit',
            name: 'submitButton',
            text: 'Insert',
            buttonType: 'primary'
        }
    ],
    onSubmit: (api) => {
        const data = api.getData();
        tinymce.activeEditor.execCommand('mceInsertContent', false, `<div class="collapsable-content-group"><${data.headingType} class="h5"><a role="button" href="#${data.anchor}" data-bs-toggle="collapse" aria-expanded="false" aria-controls="${data.anchor}">${data.heading}</a></${data.headingType}><div class="collapse" id="${data.anchor}"><div class="collapsable-content-container">REPLACE WITH COLLAPSABLE CONTENT :)</div></div></div>`);
        api.close();
    }
};

tinymce.init({
    selector: 'textarea.tiny-editor',
    plugins: 'advlist code table lists media image link autoresize charmap visualblocks visualchars wordcount autosave',
    toolbar: 'undo redo | image | insert | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | link charmap | visualblocks visualchars wordcount restoredraft insert-iframe insert-collapsable-content',
    browser_spellcheck: true,
    block_unsupported_drop: false,
    automatic_uploads: true,
    file_picker_types: 'image',
    relative_urls: false,
    toolbar_sticky: true,
    content_style: contentCss.toString(),
    autosave_ask_before_unload: true,
    paste_webkit_styles: 'strong font-style',
    paste_preprocess: (editor, args) => {
        let content = args.content;
        let newContent = content.replaceAll(' dir="ltr"', '');
        args.content = newContent.replaceAll('</p><br /><p', '</p><p');
    },
    setup: (editor) => {
        editor.ui.registry.addButton('insert-iframe', {
            icon: 'embed-page',
            onAction: () => editor.windowManager.open(iframeConfig)
        });
        editor.ui.registry.addButton('insert-collapsable-content', {
            icon: 'duplicate-row',
            onAction: () => editor.windowManager.open(collapseConfig)
        });
    },
    style_formats: [
        { title: 'Image caption', block: 'p', classes: 'image-caption-text' },
    ],
    style_formats_merge: true
});
