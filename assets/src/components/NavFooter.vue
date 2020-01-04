<template>
	<div class="footer-container w-100 p-4 bg-dark">
		<div class="container">
			<div class="content d-flex flex-column align-items-center">
				<h3 class="title">Contact <br> <em>Us</em></h3>
				<div class="row mt-4">
					<div class="col">
						<div class="nav flex-column text-right">
							<strong class="nav-item">Address</strong>
							<div class="nav-item">Edificio Bacardi, Oficina 404, Calle Monerrate 261, CP 10100, La Habana Vieja, Habana, Cuba</div>
							<strong class="nav-item">Phone</strong>
							<div class="nav-item">+53 7866 4734</div>
							<strong class="nav-item">Emergency Phone</strong>
							<div class="nav-item">+53 7866 4734</div>
						</div>
					</div>
					<div class="col">
						<div class="nav flex-column text-left">
							<strong class="nav-item">General Contact</strong>
							<div class="nav-item">info@sensesofcuba.com</div>
							<strong class="nav-item">Sales</strong>
							<div class="nav-item">info@sensesofcuba.com</div>
							<strong class="nav-item">Product Management</strong>
							<div class="nav-item">info@sensesofcuba.com</div>
						</div>
					</div>
				</div>
				<div class="row mt-2">
					<div class="col">
						<div class="nav flex-column text-center">
							<strong class="nav-item">Opening Times</strong>
							<div class="nav-item">Monday - Sunday: 9:00 - 17:00</div>
							<div class="nav-item">Saturday - Sunday (and public holidays): 10:00 - 16:00</div>							
						</div>
					</div>
				</div>
				<div class="footer nav">
					<div class="nav-item">
						<a href="#" class="nav-link">Inprint</a>
					</div>
					<div class="nav-item">
						<a href="#" class="nav-link">Cooperations</a>
					</div>
					<div class="nav-item">
						<a href="#" class="nav-link">General Terms of Service</a>
					</div>
					<div class="nav-item">
						<a href="#" class="nav-link">Privacy Policy</a>
					</div>
				</div>
				<small class="copyright mt-4">Copyright <i class="fa fa-copyright"></i> 2019. Tourcom</small>
			</div>
		</div>
	</div>
</template>

<script>
export default {

}
</script>

<style lang="scss" scoped>
.footer-container {
	color: #fafafa;
	position: relative;
	.content {
		padding-top: 4rem;
		strong {
			color: #eee;
		}
	}
	.title {
		font-weight: 700;
		text-transform: uppercase;
		color: #fafafa;
		text-align: center;
		position: absolute;
		top: -10px;
		em {
			color: #f9ce1c;
			font-style: normal;
		}
	}
	.footer {
		margin-top: 4rem;
	}
}
</style>