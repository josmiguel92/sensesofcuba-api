<template>
	<nav id="navbar" class="navbar sticky-top navbar-expand-lg navbar-dark">
		<div class="container">
			<div class="navbar-left">
				<div class="navbar-nav">
					<div class="nav-item">
						<a href="#" class="nav-link nav-link-icon" v-if="user">
							<i class="fa fa-user"></i>
							<span @click="showModal" class="nav-link-inner--text">{{user.username}}</span>
							<a v-if="isUserAdmin" class="badge badge-primary" href="/sensesofcuba/admin">Admin</a>
							<modal :show.sync="modalOpen" modalClasses="modal-md">
								<h6 slot="header" class="modal-title" id="modal-title-default">{{ user.username }}</h6>
								<div class="user-data container" slot="default">
									<form-input label="Email" placeholder="Email"></form-input>
									<form-input label="Password" placeholder="Password"></form-input>
								</div>
								<template slot="footer">
									<b-button class="ml-auto" @click="modalOpen = false">Close
									</b-button>
								</template>
							</modal>
						</a>
					</div>
				</div>
			</div>
			<router-link to="/" class="navbar-brand">
				<!--<img src="~/assets/images/soc-logo2.png" alt="">-->
				SENSES OF CUBA
			</router-link>
			<button 
				class="navbar-toggler" 
				type="button" 
				:data-toggle="toggled" 
				data-target="menu"
				aria-controls="menu"
				:aria-expanded="toggled"
				@click.stop="toggled = !toggled"
				aria-label="Toggle Navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<slide-y-up-transition>
				<div class="collapse navbar-collapse" 
					id="menu" 
					v-show="toggled" 
					:class="{show: toggled}"
					v-click-outside="closeMenu">
					<div class="navbar-collapse-header">
						<dropdown tag="div" title="en" icon="">
							<template #title>
								<a class="dropdown-toggle nav-link text-nowrap"
									data-toggle="dropdown">
									<i class="fa fa-language"></i>
									<span>{{ currentLocale }}</span>
								</a>
							</template>
							<span @click="toggleLocale('en')" class="dropdown-item"><i class="fa fa-language"></i> English</span>
							<span @click="toggleLocale('es')" class="dropdown-item"><i class="fa fa-language"></i> Spanish</span>
							<span @click="toggleLocale('de')" class="dropdown-item"><i class="fa fa-language"></i> German</span>
						</dropdown>
					</div>
					<div class="navbar-nav">
						<div class="nav-item">
							<a href="#" class="nav-link text-nowrap">{{$t('navbar.products')}}</a>							
						</div>
						<div class="nav-item">
							<a href="#info" class="nav-link text-nowrap">{{$t('navbar.info')}}</a>
						</div>
						<div class="nav-item">
							<a href="#" class="nav-link text-nowrap">{{$t('navbar.contact')}}</a>
						</div>
					</div>
				</div>
			</slide-y-up-transition>
		</div>
	</nav>
</template>

<script>
import { SlideYUpTransition } from 'vue2-transitions';
import Dropdown from '~/components/Dropdown';
import {setLocale} from '~/_i18n';

export default {
	props: {
		user: {
			type: Object,
			default: {}
		}
	},
	data() {
		return {
			toggled: false,
			modalOpen: false
		}
	},
	components: {
		SlideYUpTransition,
		Dropdown
	},
	computed: {
		currentLocale() {
			return this.$i18n.locale;
		},
		isUserAdmin() {
			return true;
		}
	},
	methods: {
		showModal() {
			//this.modalOpen = true;
			this.$modal.show('');
		},
		closeMenu() {
			this.toggled = false;
		},
		toggleLocale(locale) {
			setLocale(locale);
			this.$router.go();
		}
	}
}
</script>

<style lang="scss" scoped>
#navbar {
	background-color: rgba(0,0,0,.3) !important;
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	z-index: 999;
	.navbar-brand {
		font-weight: 700;
		color: var(--primary);
	}
	.dropdown-toggle {
		font-weight: 700;
		color: #fafafa;
	}
	.dropdown-item {
		cursor: pointer;
		&:hover {
			background-color: transparent;
		}
	}
	.nav-link {
		font-weight: 700;
		color: #fafafa;
	}
}
@media (min-width: 992px) {
	.navbar-left, .navbar-brand, .navbar-collapse {
		width: 33.3333333%;
		text-align: center;
	}
	.navbar-left {
		display: flex;
		justify-content: center;
	}
	.navbar-collapse {
		flex-grow: 0;
		justify-content: center;
	}
}
</style>