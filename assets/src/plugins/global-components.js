// Global Components
import Layout from '~/components/Layout.vue';
import FormInput from '~/components/FormInput.vue';
import Card from '~/components/Card.vue';
import BButton from '~/components/BButton.vue';
import BSelect from '~/components/BSelect.vue';
import Modal from '~/components/Modal.vue';
import PDFModal from '~/components/PDFModal.vue';
import Spinner from '~/components/Spinner.vue';

export default {
	install(Vue) {
		Vue.component(Layout.name, Layout);
		Vue.component(FormInput.name, FormInput);
		Vue.component(Card.name, Card);
		Vue.component(BButton.name, BButton);
		Vue.component(BSelect.name, BSelect);
		Vue.component(Modal.name, Modal);
		Vue.component(Spinner.name, Spinner);
	}
};