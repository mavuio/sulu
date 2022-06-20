
// Add project specific javascript code here:
import {listItemActionRegistry,formToolbarActionRegistry,listToolbarActionRegistry} from 'sulu-admin-bundle/views';
import {ckeditorPluginRegistry, ckeditorConfigRegistry, fieldRegistry} from 'sulu-admin-bundle/containers';

import DuplicateItemAction from "./listItemActions/DuplicateItemAction";
listItemActionRegistry.add('mavu_mysite.duplicate', DuplicateItemAction);


import MavuSvelteField from "./fields/MavuSvelteField.js";
fieldRegistry.add('mavu_svelte_field', MavuSvelteField);


ckeditorConfigRegistry.add((config) => {
    return {
      heading: {
          options:  [
            {
                "model": "paragraph",
                "title": "Paragraph",
                "class": "ck-heading_paragraph"
            },
            {
                "model": "heading1",
                "view": "h1",
                "title": "Heading 1",
                "class": "ck-heading_heading1"
            },
            {
                "model": "heading2",
                "view": "h2",
                "title": "Heading 2",
                "class": "ck-heading_heading2"
            },
            {
                "model": "heading3",
                "view": "h3",
                "title": "Heading 3",
                "class": "ck-heading_heading3"
            },
            {
                "model": "heading4",
                "view": "h4",
                "title": "Heading 4",
                "class": "ck-heading_heading4"
            }
        ]
    },
    toolbar: [...config.toolbar,  '|', 'undo', 'redo'],
    };
});


