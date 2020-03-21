<template>
  <auth-wrapper page="forget-pass">
    <div class="auth-form-wrap forgot-password-wrap">
      <div class="text-center" v-if="displayConfirmMsg">
        <div class="col-sm-12 text-center">
          <img src="@/assets/img/validation.svg" />
          <p class="text-center my-4">
            Un email vous a été envoyé avec le lien de réinitialisation.
          </p>
        </div>
      </div>
      <div class="text-center" v-if="emailError">
          <div class="col-sm-12 text-center">
            <img src="@/assets/img/error.svg" />
            <p
                    class="text-center my-4"
            >{{ msg }}</p>
          </div>
        </div>

      <div v-if="!displayConfirmMsg">
        <h3 class="py-3 font-size-21 font-weight-bold forgot-password-title text-center">Mot de passe oublié ?</h3>
        <p class="forgot-password-p font-size-18 forget-pass-txt text-center">
          Indiquez votre email pour réinitialiser votre mot de passe.
        </p>
        <form class="form-signin">
          <!-- <label for="email">Adresse email:</label> -->
          <div class="form-group">
            <input
              type="text"
              class="form-control"
              :class="{'is-invalid': errors.has('email') }"
              v-validate="'required|email'"
              id="email"
              name="email"
              placeholder="Mon adresse email"
              v-model="email"
            />
            <img class="form-control-icon" v-if="errors.has('email')" src="@/assets/img/profil-rouge.svg" />
            <img class="form-control-icon" v-else src="@/assets/img/profil-gray.svg" />

            <div class="invalid-feedback">
              <p
                v-if="errors.firstByRule('email', 'required')"
              >Ce champ est obligatoire</p>
              <p v-if="errors.firstByRule('email', 'email')">Veuillez saisir une adresse email valide</p>
            </div>
          </div>
          <div class="text-center">
            <button class="btn btn-white-primary wide mb-2" @click.prevent="submit">Récupérer</button>
          </div>
        </form>
      </div>
    </div>
  </auth-wrapper>
</template>

<script>
import AuthWrapper from "@/components/wrappers/Auth";
import { apolloClient } from "@/apollo";
import { RESET_PASSWORD } from "@/graphql/security/reset-password-mutation";
export default {
  name: "ForgetPassword",
  components: {
    AuthWrapper
  },
  data() {
    return {
      passwordSent: false,
      msg: "Échec d'envoi du mot de passe de récupération.",
      email: "",
      emailError: false,
      displayConfirmMsg: false
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
          apolloClient
            .mutate({
              mutation: RESET_PASSWORD,
              variables: {
                email: this.email
              }
            })
            .then(() => {
              this.displayConfirmMsg = true;
              this.emailError = false;
            })
            .catch(() => {
              this.displayConfirmMsg = false;
              this.emailError = true;
            });
        }
      });
    }
  }
};
</script>

<style scoped lang="scss">
.auth-form-wrap {
  // width: 480px;
}
.forget-pass-txt {
  line-height: normal;
}
</style>
