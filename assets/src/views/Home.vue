<template>
	<layout>
		<template #header>
			<navbar :user="user"></navbar>
		</template>
		<template #default>
			<hero></hero>
			<products :products="products" :loading="loading"></products>
			<info :info="info" :loading="loading"></info>
		</template>
		<template #footer>
			<nav-footer></nav-footer>
		</template>
	</layout>
</template>

<script>
import Navbar from '~/components/Navbar.vue';
import NavFooter from '~/components/NavFooter.vue';
import Hero from '~/components/Hero.vue';
import Products from '~/components/Products.vue';
import Info from '~/components/Info.vue';
import { mapGetters, mapActions } from 'vuex';

export default {
	name: 'home',
	components: {
		Navbar,
		Hero,
		Products,
		Info,
		NavFooter
	},
	computed: {
		...mapGetters(['loading', 'products', 'info', 'user'])
	},
	methods: {
		...mapActions(['fetchAll', 'removeUser'])
	},
	mounted() {
		this.fetchAll().then(() => {
			console.log('Home data fetched succesfully.')
		})
		.catch(e => {
			if (e.response) {
				switch (e.response.status) {
					case 401:
						this.removeUser()
							.then(() => {
								this.$router.push({name: 'sign-in'});
							});
						break;
				
					default:
						break;
				}
			}
		});
	}
}
</script>

<style>

</style>