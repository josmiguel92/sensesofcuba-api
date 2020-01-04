// Global Directives
import clickOutside from '~/directives/click-outside';

export default {
	install(Vue) {
		Vue.directive('click-outside', clickOutside);
	}
};