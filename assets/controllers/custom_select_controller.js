import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="product-category" attribute will cause
 * this controller to be executed. The name "custom-select" comes from the filename:
 * custom_select_controller.js -> "custom-select"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static targets = [ "value", "image", "field" ]
    connect()
    {
        let selectedItem = this.element.querySelector('span[data-selected-value]');
        if (selectedItem !== null) {
            this.valueTarget.innerHTML = selectedItem.dataset.valueName;
            this.imageTarget.src = selectedItem.parentNode.querySelector("img").src;
        }
        // this.element.innerHTML = 'Hello Stimulus! Edit me in assets/controllers/product_category_controller.js';
    }
    select(event)
    {
        event.preventDefault();
        let listItem = event.target;
        while (listItem.tagName !== 'LI') {
            listItem = listItem.parentNode;
        }
        let item =  listItem.querySelector('span[data-value-name]');
        let checkIcon =  listItem.querySelector('span.check-icon');
        let allCheckIcons = listItem.parentNode.querySelectorAll('span.check-icon');

        if (item !== null && item.dataset.valueName !== 'undefined') {
            //remove previous checkmarks
            allCheckIcons.forEach(value => {value.classList.add('hidden')});

            this.valueTarget.innerHTML = item.dataset.valueName;
            this.fieldTarget.querySelector('select').value = item.dataset.valueIndex;
            this.imageTarget.src = item.parentNode.querySelector("img").src;
            checkIcon.classList.remove('hidden');
        }

    }
}
