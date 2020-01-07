import Vue from 'vue';
import App from './App';
import PDFModal from '~/components/PDFModal.vue';
import './style.scss';

import plugins from './plugins';
Vue.use(plugins);

import { date } from './filters';
Vue.filter('date', date);

import initRouter from './router';
import initStore from './store';
import initI18n from './i18n';

// PDFModal
const pdfModal = Vue.prototype.$pdfModal = new Vue(PDFModal).$mount();
document.body.appendChild(pdfModal.$el);

new Vue({
    el: '#app',
    router: initRouter(),
    store: initStore(),
    i18n: initI18n(),
    render: h => h(App)
});

