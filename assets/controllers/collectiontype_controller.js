import { Controller } from 'stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    connect()
    {

        this.addBtn = this.element.querySelector("[data-name='addAttachment']");
        this.delBtn = this.element.querySelector("[data-name='deleteAttachment']");
        this.collectionContainer = this.element.querySelector("[data-prototype]")
        this.fieldPrototype = this.collectionContainer.dataset.prototype;
        this.counter = this.collectionContainer.dataset['counter'] || this.collectionContainer.children.length;

        this.addBtn.addEventListener('click',this.addField.bind(this));


        // this.delBtn.addEventListener('click',this.delField());

    }

    addField () {
        // grab the prototype template
        let newWidget = this.fieldPrototype;
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, this.counter);
        // Increase the counter
        this.counter++;

        // And store it, the length cannot be used if deleting widgets is allowed
        this.collectionContainer.dataset['counter'] = this.counter;

        console.log(this.counter);
        // create a new list element and add it to the list
        let newElem = document.createElement('div');
        newElem.innerHTML = newWidget;

        this.collectionContainer.append(newElem);

    }

    delField() {
        return true;
    }
}
