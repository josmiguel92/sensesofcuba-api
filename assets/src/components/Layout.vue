<template>
	<div id="app">
		<header>
			<slot name="header"></slot>
		</header>
		<main>
			<slot></slot>
		</main>
		<footer>
			<slot name="footer"></slot>
		</footer>
	</div>
</template>

<script>
export default {
	name: 'layout'
}
</script>

<style lang="scss">
</style>