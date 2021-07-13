import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="product-category" attribute will cause
 * this controller to be executed. The name "product-category" comes from the filename:
 * product_category_controller.js -> "product-category"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    static targets = [ "category" ];

    connect()
    {
        // this.element.innerHTML = 'Hello Stimulus! Edit me in assets/controllers/product_category_controller.js';
        //filter products by selected category
        this.productSelect = this.element.querySelector("[name='product\[category\]']");
        this.parentSelect = this.element.querySelector("[name='product\[parent\]']");
        this.customCategorySelect = this.categoryTarget.querySelector('ul');

        this.customCategorySelect.addEventListener('click', function (event) {
            this.productSelectUpdate(event)
        }.bind(this));
    }

    productSelectUpdate(event)
    {
        let listItem = event.target;
        while (listItem.tagName !== 'LI') {
            listItem = listItem.parentNode;
        }
        let item =  listItem.querySelector('span[data-value-name]');
        this.activeCategory = item.dataset.valueName;

        let options = this.parentSelect.querySelectorAll('optgroup');

        options.forEach(function (element) {
            if (element.attributes.label.value !== this.activeCategory) {
                element.classList.add('hidden');
            } else {
                element.classList.remove('hidden');
            }
        }.bind(this))
    }
}
