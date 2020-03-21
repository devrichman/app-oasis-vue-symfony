<template>
  <auth-wrapper>
    <form action method>
      <div class="auth-form-wrap">
        <div class="text-center" v-if="loginError">
          <div class="col-sm-12 text-center">
            <img src="@/assets/img/error.svg" />
            <p class="text-center error-message my-4">{{ errorMessage }}</p>
          </div>
        </div>
        <div class="card card-signin my-5 border-none">
          <div class="card-body">
            <h1 class="text-center py-2 mb-3 font-weight-bold d-none d-lg-block">Connexion</h1>
            <form class="form-signin">
              <div class="form-group">
                <input
                  type="text"
                  class="form-control"
                  :class="{'is-invalid': errors.has('email') }"
                  v-validate="'required|email'"
                  name="email"
                  placeholder="Email"
                  v-model="email"
                />
                <img v-if="errors.has('email')" class="form-control-icon" src="@/assets/img/profil-rouge.svg" />
                <img v-else class="form-control-icon" src="@/assets/img/profil-primary.svg" />
                
                <div class="invalid-feedback">
                  <p v-if="errors.firstByRule('email', 'required')">Ce champ est obligatoire</p>
                  <p v-if="errors.firstByRule('email', 'email')">Veuillez saisir une adresse email valide</p>
                </div>
              </div>
              <div class="form-group">
                <input
                  type="password"
                  class="form-control"
                  :class="{'is-invalid': errors.has('password') }"
                  v-validate="'required'"
                  name="password"
                  placeholder="Mot de passe"
                  v-model="password"
                />

                <img
                  class="form-control-icon"
                  v-if="errors.has('password')"
                  src="@/assets/img/profil-rouge.svg"
                />
                <img class="form-control-icon" v-else src="@/assets/img/profil-primary.svg" />

                <div class="invalid-feedback">
                  <p v-if="errors.firstByRule('password', 'required')"
                  >Ce champ est obligatoire</p>
                </div>
              </div>
              <div class="mt-4">
                <button
                  class="btn btn-white-primary wide mb-2"
                  @click.prevent="submit"
                >Connexion</button>
              </div>

              <div class="text-center mt-3">
                <router-link
                  to="/mot-de-passe-oublie"
                  class="forgot-password-text font-size-12"
                >Mot de passe oublié ?</router-link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </form>
  </auth-wrapper>
</template>

<script>
import AuthWrapper from "@/components/wrappers/Auth";
import { LOGIN } from "@/graphql/security/login-mutation";
export default {
  name: "LoginPage",
  components: {
    AuthWrapper
  },
  data() {
    return {
      email: "",
      password: "",
      loginError: false,
      errorMessage: "Connexion impossible avec ces identifiants, veuillez réessayer"
    };
  },
  created() {
      this.$validator.pause();
  },
  methods: {
    submit() {
      this.$validator.resume();
      this.$validator.validateAll().then(result => {
        if (result) {
          this.$apollo.mutate({
              mutation: LOGIN,
              variables: {
                  userName: this.email,
                  password: this.password
              }
          }).then(res => {
              const login = res.data.login;
              if (login && login.roles) {
                  if (login.roles.includes("ROLE_CREATE_USER")) {
                      this.$router.push({ name: 'Users' });
                  }
              }
          })
          .catch(() => {
              this.loginError = true;
          });
        }
      });
    }
  }
};
</script>

<style scoped>
</style>
