<template>
	<div class="backdrop" v-show="isOpen">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ title }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<iframe :src="src"></iframe>
				</div>
			</div>
		</div>
	</div>
</template>

<script>

export default {
	name: 'pdf-modal',
	components: {
		
	},
	data() {
		return {
			isOpen: false,
			title: '',
			src: ''
		}
	},
	methods: {
		show(title, src) {
			this.title = title;
			this.src = src;
			this.isOpen = true;
		},
		close() {
			this.src = '',
			this.isOpen = false;
		}
	}
}
</script>

<style lang="scss">
.backdrop {
	position: fixed;
	z-index: 9998;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background-color: rgba(0, 0, 0, .5) !important;
	display: table;
	transition: opacity .3s ease;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	.modal-dialog {
		max-width: 90vw;
		height: 90vh;
		.modal-content {
			width: 90vw;
			height: 90vh;
		}
		iframe {
			width: 100%;
			height: 100%;
		}
	}
}
</style>