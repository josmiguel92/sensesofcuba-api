<template>
	<layout>
		<template #default>
			<div class="wrapper">
				<section class="section container d-flex flex-direction-column align-items-center justify-content-center">
					<fade-transition origin="center" mode="out-in" :duration="150">
						<router-view></router-view>
					</fade-transition>
				</section>
			</div>
		</template>
	</layout>
</template>

<script>
import { FadeTransition } from 'vue2-transitions';
export default {
	name: 'access',
	components: {
		FadeTransition
	}
}
</script>

<style lang="scss" scoped>
.wrapper {
	background-color: #f9ce1c;
}
.section {
	min-height: 100vh;
}
</style>