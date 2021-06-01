// import 'trix/dist/trix'
// import 'trix/dist/trix.css'
// require 'https://cdn.tiny.cloud/1/z0slbjd1c4bl0a75tvm11pqdtd945h4jn0o16uyuslzkkk5p/tinymce/5/tinymce.min.js';
import tinymce from "tinymce/tinymce.min";
import "tinymce/themes/silver/theme";

import "tinymce/skins/content/default/content.css";
import "tinymce/skins/ui/oxide/skin.min.css";
import "tinymce/skins/ui/oxide/content.min.css";

import "tinymce/icons/default/icons.min";
import "tinymce/plugins/importcss/plugin.min";
import "tinymce/plugins/table/plugin.min";
import "tinymce/plugins/link/plugin.min";
import "tinymce/plugins/hr/plugin.min";
import "tinymce/plugins/template/plugin.min";
import "tinymce/plugins/toc/plugin.min";
import "tinymce/plugins/lists/plugin.min";
import "tinymce/plugins/wordcount/plugin.min";
import "tinymce/plugins/imagetools/plugin.min";
import "tinymce/plugins/image/plugin.min";
import "tinymce/plugins/quickbars/plugin.min";
import "tinymce/plugins/print/plugin.min";
import "tinymce/plugins/preview/plugin.min";
import "tinymce/plugins/paste/plugin.min";
import "tinymce/plugins/importcss/plugin.min";
import "tinymce/plugins/searchreplace/plugin.min";
import "tinymce/plugins/autolink/plugin.min";
import "tinymce/plugins/code/plugin.min";
import "tinymce/plugins/visualblocks/plugin.min";
import "tinymce/plugins/media/plugin.min";
import "tinymce/plugins/autosave/plugin.min";
import "tinymce/plugins/save/plugin.min";
import "tinymce/plugins/textpattern/plugin.min";
import "tinymce/plugins/fullpage/plugin.min";
import "tinymce/plugins/fullscreen/plugin.min";
import "tinymce/plugins/textcolor/plugin.min";
import "tinymce/plugins/insertdatetime/plugin.min";
import "tinymce/plugins/anchor/plugin.min";
import "tinymce/plugins/visualchars/plugin.min";
import "tinymce/plugins/advlist/plugin.min";
(function () {

    let useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

    var element = document.querySelector('textarea.__editor')
    //
    var add_attachment_url = element.dataset.add_attachment;

    tinymce.init({
        selector: 'textarea.__editor',
        language: 'es',
        plugins: 'print preview paste importcss searchreplace autolink autosave save  code visualblocks visualchars fullscreen image link media template table hr anchor toc insertdatetime advlist lists wordcount imagetools textpattern quickbars',
        // imagetools_cors_hosts: ['festhabana.wip', 'festivaldepoesiadelahabana.com'],
        images_upload_url: add_attachment_url,
        images_upload_handler: function (blobInfo, success, failure, progress) {
            var xhr, formData;

            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', add_attachment_url);

            xhr.upload.onprogress = function (e) {
                progress(e.loaded / e.total * 100);
            };

            xhr.onload = function () {
                var json;

                if (xhr.status === 403) {
                    failure('HTTP Error: ' + xhr.status, { remove: true });
                    return;
                }

                if (xhr.status < 200 || xhr.status >= 300) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }

                json = JSON.parse(xhr.responseText);

                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }

                success(json.location);
            };

            xhr.onerror = function () {
                failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };

            let post_title = document.querySelector('#post_title').value;
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('post[title]', post_title);

            xhr.send(formData);
        },

        menubar: 'file edit view insert format tools table',
        toolbar: 'undo redo | bold italic underline strikethrough | fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat  | fullscreen  preview save print | insertfile image media template link anchor',
        toolbar_sticky: true,
        autosave_ask_before_unload: true,
        autosave_interval: '30s',
        autosave_prefix: '{path}{query}-{id}-',
        autosave_restore_when_empty: false,
        autosave_retention: '2m',
        image_advtab: true,
        link_list: [
            // { title: 'My page 1', value: 'https://www.tiny.cloud' },
            // { title: 'My page 2', value: 'http://www.moxiecode.com' }
        ],
        image_list: [
            // { title: 'My page 1', value: 'https://www.tiny.cloud' },
            // { title: 'My page 2', value: 'http://www.moxiecode.com' }
        ],
        image_class_list: [
            // { title: 'None', value: '' },
            // { title: 'Some class', value: 'class-name' }
        ],
        importcss_append: true,

        /* enable title field in the Image dialog*/
        image_title: true,
        /* enable automatic uploads of images represented by blob or data URIs*/
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            /*
              Note: In modern browsers input[type="file"] is functional without
              even adding it to the DOM, but that might not be the case in some older
              or quirky browsers like IE, so you might want to add it to the DOM
              just in case, and visually hide it. And do not forget do remove it
              once you do not need it anymore.
            */

            input.onchange = function () {
                var file = this.files[0];

                var reader = new FileReader();
                reader.onload = function () {
                    /*
                      Note: Now we need to register the blob in TinyMCEs image blob
                      registry. In the next release this part hopefully won't be
                      necessary, as we are looking to handle it internally.
                    */
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    /* call the callback and populate the Title field with the file name */
                    cb(blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        },
        templates: [
                // { title: 'New Table', description: 'creates a new table', content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>' },
            // { title: 'Starting my story', description: 'A cure for writers block', content: 'Once upon a time...' },
            // { title: 'New list with dates', description: 'New List with dates', content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>' }
        ],
        template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
        template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
        height: 600,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        toolbar_mode: 'sliding',
        contextmenu: 'link image imagetools table',
        skin: useDarkMode ? 'oxide-dark' : 'oxide',
        content_css: useDarkMode ? 'dark' : 'default',
        content_style: 'body { font-family:Montserrat,Helvetica,Arial,sans-serif; font-size:14px }'
    });



    // if (HOST === null) {
    //     console.log("The attachment uploads are disabled. You must define the data-addurl attribute");
    //     return;
    // }
    //
    // addEventListener("trix-attachment-add", function (event) {
    //     if (event.attachment.file) {
    //         uploadFileAttachment(event.attachment)
    //     }
    // })
    //
    // function uploadFileAttachment(attachment)
    // {
    //     uploadFile(attachment.file, setProgress, setAttributes)
    //
    //     function setProgress(progress)
    //     {
    //         attachment.setUploadProgress(progress)
    //     }
    //
    //     function setAttributes(attributes)
    //     {
    //         attachment.setAttributes(attributes)
    //     }
    // }
    //
    // function uploadFile(file, progressCallback, successCallback)
    // {
    //     var formData = createFormData(file)
    //     var xhr = new XMLHttpRequest()
    //
    //     xhr.open("POST", HOST, true)
    //
    //     xhr.upload.addEventListener("progress", function (event) {
    //         var progress = event.loaded / event.total * 100
    //         progressCallback(progress)
    //     })
    //
    //     xhr.addEventListener("load", function (event) {
    //         var attributes = {
    //             url: xhr.responseText,
    //             href: xhr.responseText + "?content-disposition=attachment"
    //         }
    //         successCallback(attributes)
    //     })
    //
    //     xhr.send(formData)
    // }
    //
    // function createFormData(file)
    // {
    //     var data = new FormData()
    //     data.append("Content-Type", file.type)
    //     data.append("file", file)
    //     return data
    // }
})();