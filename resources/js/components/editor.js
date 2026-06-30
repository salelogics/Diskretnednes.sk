import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Paragraph from '@editorjs/paragraph';
import Image from '@editorjs/image';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import Table from '@editorjs/table';
import LinkTool from '@editorjs/link';
import Embed from '@editorjs/embed';
import Marker from '@editorjs/marker';
import InlineCode from '@editorjs/inline-code';

class EditorJSComponent {
    constructor(holderId, data = null, readOnly = false) {
        this.holderId = holderId;
        this.data = data;
        this.readOnly = readOnly;
        this.editor = null;
        this.init();
    }

    getCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        console.warn('CSRF token meta tag not found');
        return '';
    }

    init() {
        this.editor = new EditorJS({
            holder: this.holderId,
            readOnly: this.readOnly,
            data: this.data,
            tools: {
                header: {
                    class: Header,
                    config: {
                        placeholder: 'Zadajte nadpis...',
                        levels: [1, 2, 3, 4, 5, 6],
                        defaultLevel: 2
                    }
                },
                paragraph: {
                    class: Paragraph,
                    config: {
                        placeholder: 'Začnite písať váš obsah...'
                    }
                },
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'unordered'
                    }
                },
                image: {
                    class: Image,
                    config: {
                        endpoints: {
                            byFile: '/admin/clanky/upload-image',
                            byUrl: '/admin/clanky/upload-image-by-url'
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    }
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                    shortcut: 'CMD+SHIFT+O',
                    config: {
                        quotePlaceholder: 'Zadajte citát',
                        captionPlaceholder: 'Autor citátu'
                    }
                },
                delimiter: Delimiter,
                table: {
                    class: Table,
                    inlineToolbar: true,
                    config: {
                        rows: 2,
                        cols: 3
                    }
                },
                linkTool: {
                    class: LinkTool,
                    config: {
                        endpoint: '/admin/clanky/fetch-url',
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    }
                },
                embed: {
                    class: Embed,
                    config: {
                        services: {
                            youtube: true,
                            vimeo: true,
                            instagram: true,
                            twitter: true,
                            facebook: true
                        }
                    }
                },
                marker: {
                    class: Marker,
                    shortcut: 'CMD+SHIFT+M'
                },
                inlineCode: {
                    class: InlineCode,
                    shortcut: 'CMD+SHIFT+M'
                }
            },
            placeholder: 'Začnite písať váš obsah...',
            minHeight: 300
        });
    }

    async save() {
        try {
            const outputData = await this.editor.save();
            return outputData;
        } catch (error) {
            console.error('Saving failed: ', error);
            return null;
        }
    }

    async render(data) {
        try {
            // Čakáme kým bude editor pripravený
            await this.editor.isReady;
            
            // Vymažeme aktuálny obsah a vložíme nový
            await this.editor.render(data);
            
            console.log('Content rendered successfully');
        } catch (error) {
            console.error('Rendering failed: ', error);
            throw error;
        }
    }

    destroy() {
        if (this.editor) {
            this.editor.destroy();
            this.editor = null;
        }
    }

    isReady() {
        return this.editor.isReady;
    }
}

// Globálne dostupné pre použitie v Blade templates
window.EditorJSComponent = EditorJSComponent;

export default EditorJSComponent; 