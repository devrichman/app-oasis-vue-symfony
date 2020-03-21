<template>
  <div class="modal--content">
    <h5 class="text-center custom-modal--title font-size-18 font-weight-bold">Entrez un nouveau mot de passe</h5>
    <div class="col-sm-11 mx-auto">
      <p class="text-gris_44">
        Votre mot de passe doit contenir au minimum<br>
        - 8 caractères<br>
        - Une lettre majuscule<br>
        - Une lettre minuscule<br>
        - Un chiffre<br>
      </p>
    </div>
    <div class="modal-body pb-0">
      <UpdatePasswordFields
        ref="UpdatePasswordFields"
        @submit="(password) => updatePassword(password)"
      />
    </div>
    <div class="custom-modal--footer form-btn-wrap d-none d-md-flex">
      <button type="button" class="btn btn-outline-consultant-light" @click="closeModal()">Annuler</button>
      <button
        type="button"
        class="btn btn-gradient-primary"
        @click="$refs.UpdatePasswordFields.submit()"
      >Modifier</button>
    </div>
    <div class="custom-modal--footer form-btn-wrap d-flex d-md-none">
      
      <button
        type="button"
        class="btn btn-white-primary"
        @click="$refs.UpdatePasswordFields.submit()"
      >Confirmer</button>
    </div>
  </div>
</template>
<script>
import izitoast from "izitoast";
import { UPDATE_MY_PASSWORD } from "@/graphql/security/update-my-password-mutation";
import UpdatePasswordFields from "@/components/utils/UpdatePasswordFields";
import {LOGIN} from "@/graphql/security/login-mutation";

export default {
  name: "UpdatePasswordDialog",
  components: {
    UpdatePasswordFields
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  },
  methods: {
    updatePassword(password) {
      this.$apollo
        .mutate({
          mutation: UPDATE_MY_PASSWORD,
          variables: {
            password: password
          }
        })
        .then(() => {
          this.$emit("close");
          this.$apollo.mutate({
              mutation: LOGIN,
              variables: {
                  userName: this.user.email,
                  password: password
              }
          });
          izitoast.success({
            position: "topRight",
            title: "Succès",
            message: "Le mot de passe a bien été modifié"
          });
        });
    },
    closeModal(){
        this.$emit('close')
        this.$emit('closed')
    }
  }
};
</script>
<style>
.modal {
  background: rgba(0, 0, 0, 0.3);
}
</style>