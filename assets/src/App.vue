<template>
	<fade-transition origin="center" mode="out-in" :duration="250">
		<router-view></router-view>
	</fade-transition>
</template>

<script>
import { FadeTransition } from 'vue2-transitions';
export default {
	name: 'app',
	components: {
		FadeTransition
	}
}
</script>

<style lang="scss">
#app {
	display: flex;
	flex-direction: column;
	font-size: 14px;
	min-height: 100vh;
	main {
		flex: 1 0 auto;
		//background-color: #f9ce1c;
	}
	svg.divider {
		position: absolute;
		bottom: -1px;
		left: 0;
		height: 8vw;
		width: 100%;
		.arc {
			stroke-width: 2px;
		}
	}
}

.form-group {
	width: 100%;
	.input-group-text {
		min-width:48px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.form-control {
		width: 100%;
	}
}
</style>