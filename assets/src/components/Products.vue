<template>
	<section id="products" class="section">
		<svg class="divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
				<path class="bg" fill="#fdc416" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
				<path class="arc" fill="#fafafa" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
		</svg>
		<div class="container">
			<div class="content">
				<h3 class="title">Our <br> <em>Products</em></h3>
				<PulseLoader :loading="loading" color="#fafafa"></PulseLoader>
				<fade-transition mode="out-in" origin="center" :duration="150">
					<products-grid :tree="products"></products-grid>
				</fade-transition>
			</div>
		</div>
	</section>
</template>

<script>
import ProductsGrid from '~/components/ProductsGrid.vue';
import { FadeTransition } from 'vue2-transitions';
import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

export default {
	name: 'products',
	props: ['products', 'loading'],
	components: {
		ProductsGrid,
		FadeTransition,
		PulseLoader
	}
}
</script>

<style lang="scss" scoped>
#products {
	background-color: var(--primary);
	padding-top: 2rem;
	padding-bottom: 12rem;
	position: relative;
	.title {
		font-weight: 700;
		text-transform: uppercase;
		color: #212121;
		text-align: center;
		position: absolute;
		top: -10px;
		em {
			color: #fafafa;
			font-style: normal;
		}
	}
	.divider {
		.arc {
			stroke: #fafafa;
		}
	}
}
.content {
	display: flex;
	flex-direction: column;
	align-items: center;
	min-height: 40vh;
	padding-top: 4rem;
}
</style>