import { getTemplate, myGetFetch } from "./functions.js";

export class OptionSuggestor {

    /** @type {HTMLElement} */
    #homeForm;

    /** @type {HTMLInputElement} */
    #input;

    /** @type {HTMLElement} */
    #optionsList;

    
    /** @type {string} */
    #optionTemplateId;

    /** @type {HTMLElement} */
    #load;

    /** @type {string} */
    #api;
    
    /** @type {string} */
    #inputId;

    /** @type {HTMLElement} */
    #inputFilled


    /**
     * @param {string} inputId 
     * @param {HTMLElement} homeForm 
     * @param {HTMLElement} optionsList 
     * @param {string} optionTemplateId 
     * @param {HTMLElement} load
     * @param {string} api
     */
    constructor(inputId, homeForm, optionsList, optionTemplateId, load, api) {
        this.#api = api;
        this.#homeForm = homeForm;
        this.#optionsList = optionsList;
        this.#optionTemplateId = optionTemplateId;
        this.#load = load;

        this.#inputId = inputId;
        this.#input = this.#homeForm.querySelector('input#'+inputId);

        this.#input.addEventListener('input', (e) => this.#onInput(e));
        this.#input.addEventListener('blur', (e) => this.#onBlur(e));
        this.#optionsList.addEventListener('mouseover', (e) => this.#onMouseoverOption(e));
        this.#optionsList.addEventListener('click', (e) => this.#onSelectOption(e));
    }


    /**
     * @param {Event} e
     */
    async #onBlur(e) {
        this.#input.value = '';
        const optionsList = this.#optionsList;
        setTimeout(function() {
            optionsList.innerHTML = '';
            optionsList.classList.remove('options-border');
        }, 500);
    }

    /**
     * 
     * @param {Event} e 
     */
    #onMouseoverOption(e) {
        const optionOnOver = e.target;
        optionOnOver.classList.add('suggest-option-selected');
        for(const option of this.#optionsList.children) {
            if(option !== optionOnOver && option.classList.contains('suggest-option-selected')) {
                option.classList.remove('suggest-option-selected');
            }
        }
    }

    /**
     * 
     * @param {Event} e             // a refactoriser
     */
    #onSelectOption(e) {
        const optionSelected = e.target;
        this.#inputFilled = document.createElement('div');
        this.#inputFilled.innerHTML = optionSelected.innerText + '<i class="bi bi-x-lg" style="cursor: pointer; float: right; font-weight: bold; font-size: 1.3em;"></i>';
        this.#inputFilled.querySelector('i').addEventListener('click', (e) => this.#onCancelOption(e));
        this.#inputFilled.classList.add(this.#input.classList);
        this.#input.replaceWith(this.#inputFilled);
        this.#homeForm.append(this.#createHidden(this.#inputId, optionSelected.getAttribute('value')));
        this.#optionsList.innerHTML = '';
        this.#optionsList.classList.remove('options-border');
    }

    /**
     * 
     * @param {Event} e 
     */
    #onCancelOption(e) {
        this.#inputFilled.replaceWith(this.#input);
        this.#input.value = '';
        document.querySelector('input[type=hidden][name='+this.#inputId+']').remove();
    }


    /**
     * 
     * @param {InputEvent} e 
     */
    async #onInput(e) {
        const q = this.#input.value;

        try {
            this.#load.style.display = '';
            
            const items = await myGetFetch(this.#api+'/'+q);

            this.#optionsList.innerHTML = '';
            this.#load.style.display = 'none';

            for(const item of items) {
                const option = getTemplate(this.#optionTemplateId).firstElementChild;
                option.innerHTML = item.label;
                option.setAttribute('value', item.id.toString())
                this.#optionsList.append(option);
            }
            
            this.#optionsList.classList.add('options-border');

        } catch(e) {
            console.error(e);
        }
    }


    
    /**
     * 
     * @param {string} name 
     * @param {mixed} value 
     * @return {HTMLInputElement}
     */
     #createHidden(name, value) {
        const hidden = document.createElement('input');
        hidden.setAttribute('type', 'hidden');
        hidden.setAttribute('name', name);
        hidden.setAttribute('value', value);
        return hidden;
    }

}