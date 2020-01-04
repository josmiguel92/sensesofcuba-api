import userService from '../services/user.service';

function loadUser() {
	return JSON.parse(localStorage.getItem('user'));
}

function saveUser(user) {
	localStorage.setItem('user', JSON.stringify(user));
}

function removeUser() {
	localStorage.removeItem('user');
}

export default {
	state: {
		user: loadUser(),
	},
	getters: {
		user: state => state.user
	},
	mutations: {
		setAuthentication(state, user) {
			state.user = user;
			saveUser(user);
		},
		removeAuthentication(state) {
			state.user = null;
			removeUser();
		},
	},
	actions: {
		signIn({rootState, commit}, {email, password}) {
			rootState.loading = true;
			return new Promise((resolve, reject) => {
				userService.signIn(email, password)
					.then(res => {
						rootState.loading = false;
						commit('setAuthentication', res.data);
						resolve();
					})
					.catch(e => {
						rootState.loading = false;
						commit('removeAuthentication');
						reject(e);
					});
			});
		},
		signUp({rootState}, credentials) {
			rootState.loading = true;
			return new Promise((resolve, reject) => {
				userService.signUp(credentials)
					.then(() => {
						rootState.loading = false;
						resolve();
					})
					.catch(e => {
						rootState.loading = false;
						reject(e);
					});
			});
		},
		removeUser({commit}) {
			return new Promise((resolve) => {
				commit('removeAuthentication');
				resolve();
			});
		},
		getCSFRCode() {
			return new Promise((resolve, reject) => {
				userService.getCSRFCode()
					.then(res => {
						resolve(res.data._token);
					})
					.catch(e => {
						reject(e);
					});
			});
		}
	}
};