<template>
  <setup-wrap>
    <div class="container text-left" v-if="tokenIsValid">
      <template v-if="!displayMsg.success && !displayMsg.error">
        <div class="container">
          <div class="col-sm-11 col-md-8 col-lg-4 mx-auto">

            <p class="text-gris_44">
              Votre mot de passe doit contenir au minimum<br>
              - 8 caractères<br>
              - Une lettre majuscule<br>
              - Une lettre minuscule<br>
              - Un chiffre<br>
            </p>
          </div>
          <div class="col-sm-11 col-md-6 col-lg-8 mx-auto text-center">
            <h3 class="py-3 font-size-21 text-black font-weight-bold">Nouveau mot de passe</h3>
            <p class="text-gris_44">Mettre à jour mon mot de passe</p>
          </div>
        </div>
        <div class="row">
          <div v-if="displayMsg.success" class="col-sm-9 col-md-7 col-lg-5 mx-auto mt-3 text-center">
            <p class="alert alert-success">Le mot de passe a été mis a jour avec succès</p>
            <router-link to="/connexion" class="btn btn-primary text-center">Me connecter</router-link>
          </div>
          <div v-else class="col-sm-9 col-md-6 col-xs-11 col-lg-4 mx-auto mt-3">
            <form class="form-signin">
              <UpdatePasswordFields
                      ref="UpdatePasswordFields"
                      @submit="(password) => submit(password)"/>
              <button class="btn btn-white-primary wide my-4 btn-90" @click.prevent="$refs.UpdatePasswordFields.submit()">Mettre à jour</button>
            </form>
          </div>
          <!-- <div class="col-sm-12 text-center">
                    <template v-if="displayMsg.error">
                        <p class="alert alert-danger">Cette page a expiré, vous devez faire une demande de mot de passe à nouveau</p>
                        <router-link to="/mot-de-passe-oublie" class="btn btn-primary text-center">Refaire une demande</router-link>
                    </template>
          </div>-->
        </div>
      </template>

      <template v-if="displayMsg.success">
        <div class="row justify-content-center">
          <div class="col-sm-12 col-md-4 col-lg-3 text-center">
            <img src="@/assets/img/validation.svg" />
            <p
              class="text-center my-4"
            >Vous avez changé le mot de passe avec succès. Vous pouvez vous reconnecter à votre compte.</p>

            <router-link to="/connexion" tag="button" class="btn btn-white-primary wide">Se reconnecter</router-link>
          </div>
        </div>
      </template>

      <template v-if="displayMsg.error">
        <div class="d-flex flex-column align-items-center">
          <div class="text-center">
            <img src="@/assets/img/error.svg" />
            <p class="text-center my-4">
              Le lien à expiré.
              <br />Veuillez retenter avec un nouveau lien.
            </p>

            <button class="btn btn-white-primary wide px-5">Renvoyer un lien</button>
          </div>
        </div>
      </template>
    </div>
  </setup-wrap>
</template>

<script>
import SetupWrap from "@/components/wrappers/Setup";
import { apolloClient } from "@/apollo";
import { UPDATE_PASSWORD } from "@/graphql/security/update-password-mutation";
import { CHECK_VALID_TOKEN } from "@/graphql/security/check-token-query";
import UpdatePasswordFields from "@/components/utils/UpdatePasswordFields";


export default {
  name: "UpdatePassword",
  components: {
    SetupWrap,
    UpdatePasswordFields
  },
  data() {
    return {
      token: "",
      password: null,
      confirmPassword: null,
      displayMsg: {
        error: false,
        success: false
      },
      // RESET
      tokenIsValid: true
    };
  },
  created() {

      if (!this.$route.query.token) {
          this.$router.push({ name: 'ExpiredToken' });
      }
      this.token = this.$route.query.token;
      this.checkValidToken();

  },
  computed: {
    passwordNotMatch() {
      return this.password !== this.confirmPassword;
    }
  },
  methods: {
    checkValidToken() {
      apolloClient
        .query({
          query: CHECK_VALID_TOKEN,
          variables: { token: this.token }
        })
        .then(() => {
          this.tokenIsValid = true;
        })
        .catch(() => {
          this.$router.push({ name: 'ExpiredToken' });
          this.tokenIsValid = false;
        });
    },
    submit($password) {
      apolloClient
        .mutate({
          mutation: UPDATE_PASSWORD,
          variables: {
            token: this.token,
            password: $password
          }
        })
        .then(() => {
          this.displayMsg.success = true;
        })
        .catch(err => {
          let statusCode = err.networkError.statusCode;
          if (statusCode === 404 || statusCode === 400) {
            this.displayMsg.error = true;
          }
        });
    }
  }
};
</script>

<style scoped>
</style>
