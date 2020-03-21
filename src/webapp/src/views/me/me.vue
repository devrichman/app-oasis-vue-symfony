<template>
  <dash-wrap :hide-tabs="false" :mobile-sidebar="false" mode="users" active="me" title="Compte">
    <div class="container text-left user-form mb-5">
      <template v-if="!$apollo.loading">
        <div class="user-change-icon profile-picture">
          <div v-if="uploadImage">
            <img class="user-icon" :src="uploadImage" />
          </div>
          <div v-else>
            <img
              v-if="me.profilePictureId"
              class="user-icon"
              :src="getImageUrl(me.profilePictureId)"
            />
            <img v-else class="user-icon" src="@/assets/img/user-primary.svg" />
          </div>
          <div class="edit-icon" v-if="canEdit">
            <input
              id="user-logo"
              class="d-none"
              type="file"
              accept=".png, .jpeg, .jpg, .gif"
              @change="uploadProfilePicture"
            />
            <label for="user-logo" class="m-0 p-0 btn">
              <img src="@/assets/img/edit-primary.svg" />
            </label>
          </div>
        </div>
        <div class="content flex-grow-1">
          <div class="d-flex justify-content-md-end justify-content-center mb-3">
            <button
                    class="btn btn-white-primary btn-cs px-4"
                    @click="updatePasswordDialogShow = true"
            >
              <img class="mr-1" src="@/assets/img/password-primary.svg" />
              Modifier le mot de passe
            </button>
          </div>
          <form>
            <fieldset>
              <legend>Profil</legend>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row">
                    <div class="col-md-5 form-group radio">
                      <label>Civilité</label>
                      <div class="radio-items">
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="CIVILITY_LIST.M_CODE.value"
                            v-model="me.civility"
                            :id="'model_' + CIVILITY_LIST.M_CODE.value"
                            :disabled="!canEdit"
                          />
                          <label
                            class="form-check-label"
                            :class="{disabled: !canEdit}"
                            :for="'model_' + CIVILITY_LIST.M_CODE.value"
                          >{{CIVILITY_LIST.M_CODE.label}}</label>
                        </div>
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="CIVILITY_LIST.MME_CODE.value"
                            v-model="me.civility"
                            :id="'model_' + CIVILITY_LIST.MME_CODE.value"
                            :disabled="!canEdit"
                          />
                          <label
                            class="form-check-label"
                            :class="{disabled: !canEdit}"
                            :for="'model_' + CIVILITY_LIST.MME_CODE.value"
                          >{{CIVILITY_LIST.MME_CODE.label}}</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-5">
                      <label>Nom*</label>
                      <input
                        :class="{ 'is-invalid': errors.has('lastName') }"
                        class="form-control"
                        id="lastName"
                        :disabled="!canEdit"
                        name="lastName"
                        placeholder="Nom..."
                        type="text"
                        v-model="me.lastName"
                        v-validate="'required'"
                      />

                      <div class="invalid-feedback" v-if="errors.has('firstName')">
                        <p>{{ " Le champ Nom est obligatoire " }}</p>
                      </div>
                    </div>
                    <div class="form-group col-md-5 offset-md-1">
                      <label>Prénom*</label>
                      <input
                        :class="{ 'is-invalid': errors.has('firstName') }"
                        class="form-control"
                        id="firstName"
                        name="firstName"
                        placeholder="Prénom..."
                        :disabled="!canEdit"
                        type="text"
                        v-model="me.firstName"
                        v-validate="'required'"
                      />
                      <div class="invalid-feedback" v-if="errors.has('lastName')">
                        <p>{{ " Le champ Prénom est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-5">
                      <label class>Fonction</label>
                      <input
                        :disabled="!canEdit"
                        class="form-control"
                        placeholder
                        type="text"
                        v-model="me.function"
                      />
                    </div>
                    <div class="form-group col-md-5 offset-md-1" v-if="isCandidate">
                      <label>Ancienneté dans la fonction*</label>

                      <datepicker
                        :disabled="!canEdit"
                        :bootstrap-styling="true"
                        :calendar-button="true"
                        :format="dateFormatter"
                        :language="fr"
                        :required="true"
                        v-model="me.seniorityDate"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset v-if="isCandidate">
              <legend>Réseau</legend>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row">
                    <div class="form-group col-md-12">
                      <label>LinkedIn</label>
                      <input
                        :disabled="!canEdit"
                        class="form-control"
                        placeholder
                        type="text"
                        v-model="me.linkedin"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset>
              <legend>Coordonnées</legend>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row">
                    <div class="form-group col-md-5">
                      <label>Email*</label>

                      <input
                        :class="{ 'is-invalid': errors.has('email') }"
                        class="form-control"
                        id="email"
                        name="email"
                        :disabled="!canEdit"
                        placeholder="you@example.com"
                        type="email"
                        v-model="emailInput"
                        v-validate="'required|email|uniqueEmail'"
                      />
                      <div
                        class="invalid-feedback"
                        v-if="errors.has('email') && errors.firstByRule('email', 'email')"
                      >
                        <p>{{ " Veuillez saisir une adresse email valide " }}</p>
                      </div>
                      <div class="invalid-feedback" v-else-if="errors.has('email')">
                        <p>{{ " L'email existe déjà" }}</p>
                      </div>
                    </div>
                    <div class="form-group col-md-5 offset-md-1">
                      <label>Téléphone*</label>

                      <input
                        :class="{ 'is-invalid': errors.has('phone') }"
                        class="form-control"
                        id="phone"
                        name="phone"
                        :disabled="!canEdit"
                        placeholder="you@example.com"
                        v-model="me.phone"
                        v-validate="{required: true, regex: '^(?:(?:\\+|00)33|0)\\s*[1-9](?:[\\s.-]*\\d{2}){4}$' }"
                      />
                      <div
                        class="invalid-feedback"
                        v-if="errors.has('phone') && errors.firstByRule('phone', 'required')"
                      >
                        <p>{{ " Le champ Téléphone est obligatoire " }}</p>
                      </div>
                      <div class="invalid-feedback" v-else-if="errors.has('phone')">
                        <p>{{ " Le numéro de téléphone est dans un format invalide " }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-11">
                      <label>Address</label>
                      <input
                        :disabled="!canEdit"
                        class="form-control"
                        rows="3"
                        v-model="me.address"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <div class="form-btn-wrap">
              <template v-if="canEdit">
                <button
                  @click="toggleEdit"
                  class="btn btn-outline-consultant-light"
                  type="button"
                >Annuler</button>
                <button
                  @click="handleSubmit"
                  class="btn btn-gradient-primary"
                  type="button"
                >Modifier</button>
              </template>
              <template v-else>
                <button
                  @click="toggleEdit"
                  class="btn btn-gradient-primary"
                  type="button"
                >Modifier profil</button>
              </template>
            </div>
          </form>
        </div>
      </template>
      <div class="row" v-else>
        <div class="col-md-12">Loading...</div>
      </div>
    </div>
    <mobile-modal
      class="d-block d-md-none"
      :active="updatePasswordDialogShow"
      @close="updatePasswordDialogShow = false"
    >
      <UpdatePasswordDialog :user="me" @close="updatePasswordDialogShow = false" />
    </mobile-modal>

    <div class="d-none d-md-block">
      <div class="modal" role="dialog" :class="{'d-block':updatePasswordDialogShow}">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <UpdatePasswordDialog :user="me" @close="updatePasswordDialogShow = false" />
          </div>
        </div>
      </div>
    </div>
  </dash-wrap>
</template>

<script>
import Datepicker from "vuejs-datepicker";
import fr from "vuejs-datepicker/dist/locale/translations/fr.js";
import izitoast from "izitoast";
import moment from "moment";
import { CIVILITY_LIST } from "@/enum/civilityEnum";
import UpdatePasswordDialog from "@/views/me/UpdatePasswordDialog";
import MobileModal from "@/components/utils/MobileModal";

import { UPLOAD_PICTURE } from "@/graphql/file/upload-picture-mutation";
import { ALL_USERS } from "@/graphql/user/all-users-query";
import { UPDATE_ME } from "@/graphql/user/update-me-mutation";
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import {FILE_PATH} from "@/enum/FilePathConstant";

export default {
  name: "me",
  components: {
    Datepicker,
    UpdatePasswordDialog,
    MobileModal
  },
  data() {
    return {
      fr: fr,
      me: '',
      emailInput: '',
      uploading: false,
      canEdit: false,
      updatePasswordDialogShow: false,
      uploadImage: null,
      CIVILITY_LIST: CIVILITY_LIST
    };
  },
  computed: {
    isCandidate() {
      return this.me.type === "candidate";
    }
  },
  apollo: {
    me: {
      query: LOGGED_USER,
      update(data) {
          this.emailInput = data.me.email;
          return data.me
      }
    },
  },
  methods: {
    toggleUpdatePasswordDialog() {
      this.updatePasswordDialogShow = !this.updatePasswordDialogShow;
    },
    toggleEdit() {
      this.canEdit = !this.canEdit;
    },
    uploadProfilePicture(e) {
      let file = e.target.files[0];
      this.saveProfilePicture(file);
      let fileReader = new FileReader();
      fileReader.onload = e => {
        this.uploadImage = e.target.result;
      };
      fileReader.readAsDataURL(file);
    },
    handleSubmit() {
      this.$validator.resume();
      this.$validator.validate().then(async valid => {
        if (!valid) {
          izitoast.error({
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          return;
        }

        if (this.uploading) {
          izitoast.error({
            position: "topRight",
            title: "Erreur",
            message: "Veuillez attendre la fin du téléchargement du fichier"
          });
          return;
        }

        let isEmailNew = false;
        if(this.emailInput !== this.me.email) {
            this.me.email = this.emailInput;
             isEmailNew = true;
        }

        let profilePictureId = this.me.profilePictureId;
        if (this.me.profilePictureFile) {
          let response = await this.$apollo.mutate({
            mutation: UPLOAD_PICTURE,
            variables: {
              file: this.me.profilePictureFile
            }
          });
          profilePictureId = response.data.uploadPicture.id;
        }
        this.$apollo
          .mutate({
            mutation: UPDATE_ME,
            variables: {
              ...this.me,
              profilePictureId: profilePictureId,
              phone: this.me.phone,
              seniorityDate: this.me.seniorityDate
                ? moment(this.me.seniorityDate, "D/M/YYYY").toISOString()
                : null
            },
            update(store, { data: { updateMe } }) {
              const data = store.readQuery({query: LOGGED_USER});
              data.me = updateMe;
              store.writeQuery({query: LOGGED_USER, data})
            }
          })
          .then(() => {
            this.canEdit = false;
            let message = "La modification a été effectuée avec succès";
            if(isEmailNew) {
              message += "<br>Un mail de confirmation vous a été envoyé sur cette nouvelle adresse";
            }

            izitoast.success({
                position: "topRight",
                title: "Succès",
                message: message
            });
          });
      });
    },
    uploadStart() {
      this.uploading = true;
    },
    saveProfilePicture(file) {
      this.uploading = false;
      this.me.profilePictureFile = file;
    },
    removeProfilePicture() {
      this.uploading = false;
      this.me.profilePicture = {};
      this.me.profilePictureId = "";
      this.me.profilePictureFile = null;
    },
    getImageUrl(profilePhotoId) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
        0,
        process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + profilePhotoId;
    },
    dateFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    }
  },
  beforeCreate() {
    this.$validator.pause();
    this.$validator.extend(
      "uniqueEmail",
      value => {
        return new Promise((resolve, reject) => {
          this.$apollo
            .query({
              query: ALL_USERS,
              variables: {
                search: value,
                limit: 1
              }
            })
            .then(response => {
              resolve(
                response.data.allUsers.items.length === 0 ||
                  response.data.allUsers.items[0].id === this.me.id
              );
            });
        });
      },
      {
        immediate: false
      }
    );
  }
};
</script>
