import contentService from '../services/content.service';

/**
 * Finds all the childrens for a parent node
 * @param {Object} root 
 * @param {Array} data 
 */
function buildTree(root, data) {
	let children = data.filter(e => e.child_of == root.id);
	if (children.length) {
		Object.assign(root, { children: children });
		root.children.map(e => {
			buildTree(e, data);
			return e;
		});
	}
}

export default {
	state: {
		products: [],
		info: []
	},
	getters: {
		products: state => state.products,
		info: state => state.info
	},
	mutations: {
		setProducts(state, products) {
			console.log('Building product tree...');
			const pTree = products.filter(p => !p.child_of).map(p => {
				buildTree(p, products);
				return p;
			});
			console.log('Done!');
			state.products = pTree;
		},
		setInfo(state, info) {
			console.log('Setting info items...');
			state.info = info;
			console.log('Done!');
		}
	},
	actions: {
		fetchAll({dispatch, rootState}) {
			rootState.loading = true;
			return new Promise((resolve, reject) => {
				Promise.all([
					dispatch('fetchProducts'),
					dispatch('fetchInfo')
				])
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
		fetchProducts({commit}) {
			return new Promise((resolve, reject) => {
				contentService.getProducts()
					.then(res => {
						commit('setProducts', res.data);
						resolve();
					})
					.catch(e => {
						reject(e);
					});
			});
		},
		fetchInfo({commit}) {
			return new Promise((resolve, reject) => {
				contentService.getInfo()
					.then(res => {
						commit('setInfo', res.data);
						resolve();
					})
					.catch(e => {
						reject(e);
					});
			});
		}
	}
};