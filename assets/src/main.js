import Vue from 'vue';
import App from './App.vue';
import PDFModal from '~/components/PDFModal.vue';
import clickOutside from '~/directives/click-outside';

Vue.config.productionTip = false;

Vue.directive('click-outside', clickOutside);
const modal = Vue.prototype.$modal = new Vue(PDFModal).$mount();
document.body.appendChild(modal.$el);

// Mixins
import errorHandler from './mixins/error-handler';
Vue.mixin(errorHandler);

// Plugins
import plugins from './plugins';
Vue.use(plugins);

// Router
import initRouter from './_router';

// Store
import initStore from './_store';

// I18n
import initI18n from './_i18n';

new Vue({
	el: '#app',
	router: initRouter(),
	store: initStore(),
	i18n: initI18n(),
	render: h => h(App)
});