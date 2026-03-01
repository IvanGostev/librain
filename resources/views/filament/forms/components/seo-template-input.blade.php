<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="seoTemplateInputComponent({
            state: $wire.entangle('{{ $getStatePath() }}'),
            variables: {{ \Illuminate\Support\Js::from($getVariables()) }},
            isMultiline: {{ $isMultiline() ? 'true' : 'false' }} 
        })"
        class="relative w-full"
    >
        <div class="mb-2 relative">
            <button 
                type="button" 
                @click="showVars = !showVars" 
                @click.away="showVars = false" 
                class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-gray fi-btn-color-gray fi-size-sm fi-btn-size-sm gap-1 px-2.5 py-1.5 text-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10"
            >
                Вставить переменную
            </button>
            <div 
                x-show="showVars" 
                x-transition 
                style="display: none;"
                class="absolute z-10 mt-1 w-64 pt-2 pb-2 rounded-xl bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-1 list-none"
            >
                <template x-for="(label, key) in variables" :key="key">
                    <button 
                        type="button" 
                        @click="insertVariable(key, label)" 
                        class="flex w-full items-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm transition-colors hover:bg-gray-50 outline-none focus:bg-gray-50 dark:hover:bg-white/5 dark:focus:bg-white/5 text-gray-700 dark:text-gray-200"
                    >
                        <span x-text="label" class="font-medium"></span> 
                        <span class="text-xs text-gray-400 dark:text-gray-500" x-text="key"></span>
                    </button>
                </template>
            </div>
        </div>
        
        <div 
            wire:ignore
            x-ref="editor" 
            contenteditable="true" 
            @input="updateState()"
            @keydown="handleKeyDown($event)"
            @paste="
                e => {
                    e.preventDefault();
                    let text = (e.originalEvent || e).clipboardData.getData('text/plain');
                    document.execCommand('insertText', false, text);
                }
            "
            @if ($getPlaceholder()) placeholder="{{ $getPlaceholder() }}" @endif
            class="fi-input block w-full rounded-lg border-none bg-white py-1.5 px-3 text-base text-gray-950 shadow-sm ring-1 ring-inset ring-gray-300 transition duration-75 focus:ring-2 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-white/5 dark:text-white dark:ring-white/10 dark:focus:ring-primary-500 min-h-[{{ $isMultiline() ? '80px' : '42px' }}] empty:before:content-[attr(placeholder)] empty:before:text-gray-400 dark:empty:before:text-gray-500"
            style="min-height: {{ $isMultiline() ? '80px' : '42px' }}; line-height: 1.5; outline: none; word-wrap: break-word;"
        ></div>
        
        <input type="hidden" x-model="state" name="{{ $getName() }}">
    </div>

    @once
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('seoTemplateInputComponent', ({ state, variables, isMultiline }) => ({
                    state: state,
                    variables: variables,
                    isMultiline: isMultiline,
                    showVars: false,
                    
                    init() {
                        this.renderHtml();
                        this.$watch('state', (val) => {
                            let currentHtml = this.htmlToState(this.$refs.editor.innerHTML);
                            if (currentHtml !== (val || '')) {
                                this.renderHtml();
                            }
                        });
                    },
                    
                    renderHtml() {
                        let html = this.state || '';
                        
                        html = html.replace(/&/g, '&amp;')
                                   .replace(/</g, '&lt;')
                                   .replace(/>/g, '&gt;');
                                   
                        for (const ObjectEntry of Object.entries(this.variables)) {
                            let key = ObjectEntry[0];
                            let label = ObjectEntry[1];
                            let escapedKey = key.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1');
                            let regex = new RegExp(escapedKey, 'g');
                            let pillHtml = `<span contenteditable="false" data-var="${key}" class="inline-flex items-center gap-x-1 rounded-md bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 mx-1 select-none">${label}</span>&#8203;`;
                            html = html.replace(regex, pillHtml);
                        }
                        html = html.replace(/\n/g, '<br>');
                        this.$refs.editor.innerHTML = html;
                    },
                    
                    htmlToState(html) {
                        let div = document.createElement('div');
                        div.innerHTML = html;
                        let text = '';
                        for (let node of div.childNodes) {
                            if (node.nodeType === 3) {
                                text += node.textContent;
                            } else if (node.nodeType === 1) {
                                if (node.tagName === 'BR' || node.tagName === 'DIV' || node.tagName === 'P') {
                                    if(text.length > 0 && !text.endsWith('\n')) text += '\n';
                                    if (node.tagName !== 'BR') {
                                        text += this.htmlToState(node.innerHTML);
                                    }
                                } else if (node.dataset.var) {
                                    text += node.dataset.var;
                                } else {
                                    text += node.innerText || node.textContent;
                                }
                            }
                        }
                        return text.replace(/&nbsp;/g, ' ').replace(/\u200B/g, '');
                    },
                    
                    updateState() {
                        this.state = this.htmlToState(this.$refs.editor.innerHTML);
                    },
                    
                    insertVariable(key, label) {
                        this.$refs.editor.focus();
                        
                        let pill = document.createElement('span');
                        pill.contentEditable = 'false';
                        pill.dataset.var = key;
                        pill.className = 'inline-flex items-center gap-x-1 rounded-md bg-primary-100 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 mx-1 select-none';
                        pill.innerText = label;
                        
                        let zws = document.createTextNode('\u200B');
                        
                        let sel;
                        if (window.getSelection) {
                            sel = window.getSelection();
                            if (sel.getRangeAt && sel.rangeCount) {
                                let range = sel.getRangeAt(0);
                                range.deleteContents();
                                
                                range.insertNode(zws);
                                range.insertNode(pill);
                                
                                range.setStartAfter(zws); 
                                range.setEndAfter(zws);
                                sel.removeAllRanges();
                                sel.addRange(range);
                            } else {
                                this.$refs.editor.appendChild(pill);
                                this.$refs.editor.appendChild(zws);
                            }
                        }
                        
                        this.updateState();
                        this.showVars = false;
                    },
                    
                    handleKeyDown(e) {
                        if (e.key === 'Enter') {
                            if(!this.isMultiline) {
                                e.preventDefault();
                            }
                        }
                    }
                }));
            });
        </script>
    @endonce
</x-dynamic-component>
