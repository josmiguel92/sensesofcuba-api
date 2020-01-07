import Vue from 'vue';
import Vuex from 'vuex';

import home from './store/home.module.js';
import user from './store/user.module.js';

Vue.use(Vuex);

const store = new Vuex.Store({
	state: {
		loading: false
	},
	getters: {
		loading: state => state.loading
	},
	modules: {
		home,
		user
	}
});

export default function initStore() {
	return store;
}