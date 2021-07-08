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
        // this.element.innerHTML = 'Hello Stimulus! Edit me in assets/controllers/product_category_controller.js';
    }
    select(event)
    {
        event.preventDefault();
        let item =  event.target.parentNode.querySelector('span[data-value-name]');
        if (item !== null && item.dataset.valueName !== 'undefined') {
            this.valueTarget.innerHTML = item.dataset.valueName;
            this.fieldTarget.querySelector('select').value = item.dataset.valueIndex;
        }

    }
}
