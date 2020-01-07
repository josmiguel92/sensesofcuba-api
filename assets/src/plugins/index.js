// Global Styles
import '~/assets/vendor/font-awesome/css/font-awesome.min.css';
import '~/scss/app.scss';

import GlobalComponents from './global-components';
import VueLazyLoad from 'vue-lazyload';

export default {
	install(Vue) {
		Vue.use(GlobalComponents);
		Vue.use(VueLazyLoad);
	}
};