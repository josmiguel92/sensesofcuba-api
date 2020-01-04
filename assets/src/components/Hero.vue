<template>
	<section id="hero" v-lazy:background-image="bgHero" class="jumbotron jumbotron-fluid">
		<svg class="divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
			<path class="bg" fill="transparent" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
			<path class="arc" fill="#fdc416" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
		</svg>
		<div class="container">
			<h1>Cuba is... <br>Our Passion</h1>
    		<!--<h1 class="display-5">{{ $t('hero.1') }}</h1>-->
    		<p class="lead">{{$t('hero.2')}}</p>
  		</div>
	</section>
</template>

<script>
import bgHero from '~/assets/images/hero.jpg';

export default {
	name: 'hero',
	data() {
		return {
			bgHero
		}
	}
}
</script>

<style lang="scss">
#hero {
	//background-image: url('~/assets/images/hero.jpg');
	background-position: left;
	background-size: cover;
	background-repeat: no-repeat;
	color: #fafafa;
	padding-top: 14rem;
	padding-bottom: 10rem;
	margin-bottom: 0;
	position: relative;
}
</style>