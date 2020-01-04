<template>
	<div class="info-grid w-100 d-flex flex-wrap justify-content-center">
		<info-item v-for="(item, index) in items" :key="index" :item="item"></info-item>
	</div>
</template>

<script>
import InfoItem from '~/components/InfoItem.vue';

export default {
	name: 'info-grid',
	props: ['items'],
	components: {
		InfoItem
	},
	data() {
		return {
			modalOpen: false
		}
	}
}
</script>

<style lang="scss" scoped>

</style>