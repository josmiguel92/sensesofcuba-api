<template>
  <div v-if="succeed">
	  <h3>Your account was created successfully.</h3>
	  <p>An confirmation email have beed sended to your inbox</p>
  </div>
  <card id="card" shadow v-else>
    <div slot="header" class="card-header-content">
			<img class="img" src="~/assets/images/logo.png" alt="">
			<h3 class="title">Sign Up</h3>
		</div>
    <form-input v-model="credentials.name" placeholder="Full Name" addonLeftIcon="fa fa-user"></form-input>
    <form-input
      v-model="credentials.enterprise"
      placeholder="Enterprice"
      addonLeftIcon="fa fa-user"
    ></form-input>
    <div class="select form-group input-group w-100">
      <div class="input-group-prepend">
        <span class="input-group-text">
          <i class="fa fa-user"></i>
        </span>
      </div>
      <select class="form-control" v-model="credentials.travelAgency">
        <option value="travel_agency" selected>Travel Agency</option>
        <option value="tour_operator">Tour Operator</option>
      </select>
    </div>
    <form-input v-model="credentials.web" placeholder="Company Website" addonLeftIcon="fa fa-link"></form-input>
    <form-input v-model="credentials.country" placeholder="Country" addonLeftIcon="fa fa-globe"></form-input>
    <form-input type="email" v-model="credentials.email" placeholder="Email" addonLeftIcon="fa fa-envelope"></form-input>
    <form-input required type="password" v-model="credentials.password" placeholder="Password" addonLeftIcon="fa fa-lock"></form-input>
    <form-input type="password" v-model="passwordRepeat" placeholder="Repeat Password" addonLeftIcon="fa fa-lock"></form-input>
    <b-button @click="onSubmit" type="primary">Sign Up</b-button>
    <template #footer>
      <span>
        Have an account?
        <router-link :to="{name: 'sign-in'}">Sign In</router-link>
      </span>
    </template>
  </card>
</template>

<script>
import { mapActions } from "vuex";
export default {
  data() {
    return {
      credentials: {
        name: "",
        enterprise: "",
        travelAgency: "travel_agency",
        web: "",
        country: "",
        email: "",
        password: "",
        _token: undefined
      },
      passwordRepeat: "",
      succeed: false
    };
  },
  methods: {
    ...mapActions(["signUp", "getCSFRCode"]),
    validate() {
      return true;
    },
    onSubmit() {
      this.signUp(this.credentials)
        .then(() => {
          this.succeed = true;
        })
        .catch(e => {
          console.error(e);
        });
    }
  },
  mounted() {
    this.getCSFRCode().then(code => {
      this.credentials._token = code;
    });
  }
};
</script>

<style>
#card {
  margin-top: 32px;
  margin-bottom: 32px;
}
</style>