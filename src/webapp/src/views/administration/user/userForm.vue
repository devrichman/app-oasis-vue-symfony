<template>
  <dash-wrap active="admin" :hide-tabs="true" :mobile-main-action="{}">
    <page-title :title="'Utilisateurs / '+ (isEdit ? 'Modification' : 'Création' ) +' d’un utilisateur'"
                @back="$router.push({name: 'Users'})" />
    <div class="container text-left user-form my-5">
      <div class="user-change-icon profile-picture" v-if="!loading">
        <div v-if="uploadeImage">
          <img class="user-icon" :src="uploadeImage"/>
        </div>
        <div v-else>
          <img class="user-icon" :src="getImageUrl(user.profilePicture)" v-if="user.profilePicture.id && isEdit" />
          <img class="user-icon" src="@/assets/img/user-primary.svg" v-else />
        </div>
        <div class="edit-icon">
          <input id="user-logo" class="d-none" type="file" accept=".png,.jpeg,.jpg,.gif" @change="uploadProfilePicture">
          <label for="user-logo" class="m-0 p-0 btn">
            <img src="@/assets/img/edit-primary.svg" />
          </label>
        </div>
      </div>
      <div class="content flex-grow-1">
        <div class="row" v-if="loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-if="!loading">
          <div class="form-row">
            <div class="form-group col-md-6">
              <div class="custom-control custom-switch" v-if="!loading">
                <input
                  type="checkbox"
                  class="custom-control-input"
                  id="status"
                  v-model="user.status"
                />
                <label class="custom-control-label" for="status">Actif</label>
              </div>
            </div>
          </div>
          <fieldset>
            <legend>Profil</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="form-row">
                  <div class="col-md-5">
                    <div class="form-group select">
                      <label>Type*</label>
                      
                        <select
                          class="form-control"
                          name="type"
                          :disabled="$route.query.companyId || me.type === 'coach'"
                          v-model="user.typeId"
                          v-validate="'required'"
                          :class="{ 'is-invalid': errors.has('type') }"
                        >
                          <option value disabled>Sélectionner un type</option>
                          <option
                            v-for="type in getTypes"
                            :key="type.id"
                            :value="type.id"
                          >{{ type.label }}</option>
                        </select>
                        <div v-if="errors.has('type')" class="invalid-feedback">
                          <p>{{ " Le champ Type est obligatoire " }}</p>
                        </div>
                      
                    </div>
                  </div>
                  <div class="col-md-5 offset-md-1 form-group radio" v-if="hasType">
                    <label>Civilité</label>
                    <div class="radio-items">
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" value="m" v-model="user.civility" id="civility_m" />
                        <label class="form-check-label" for="civility_m">M.</label>
                      </div>
                      <div class="radio-item">
                        <input class="form-check-input" type="radio" value="mme" v-model="user.civility" id="civility_mme" />
                        <label class="form-check-label" for="civility_mme">Mme</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-row" v-if="hasType">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>Prénom*</label>

                      <input
                              v-model="user.firstName"
                              v-validate="'required'"
                              id="firstName"
                              name="firstName"
                              class="form-control"
                              :class="{ 'is-invalid': errors.has('firstName') }"
                              type="text"
                              placeholder="Prénom..."
                      />
                      <div v-if="errors.has('firstName')" class="invalid-feedback">
                        <p>{{ " Le champ Prénom est obligatoire " }}</p>
                      </div>
                      
                    </div>
                  </div>
                  <div class="col-md-5 offset-md-1" v-if="hasType">
                    <div class="form-group ">
                      <label>Nom*</label>

                      <input
                              v-model="user.lastName"
                              v-validate="'required'"
                              id="lastName"
                              name="lastName"
                              class="form-control"
                              :class="{ 'is-invalid': errors.has('lastName') }"
                              type="text"
                              placeholder="Nom..."
                      />
                      <div v-if="errors.has('lastName')" class="invalid-feedback">
                        <p>{{ " Le champ Nom est obligatoire " }}</p>
                      </div>

                    </div>
                  </div>
                </div>
               
                <div class="form-row" v-if="isCandidate">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>Coach-Consultant Référent*</label>
                      
                        <user-autocomplete
                          :initial-query="user.coach && user.coach.firstName ? user.coach.firstName + ' ' + user.coach.lastName : ''"
                          v-model="user.coachId"
                          :queryCoaches="true"
                          :disabled="me.type === 'coach'"
                          v-validate="'required'"
                          name="coach"
                          :class-list="{'is-invalid': errors.has('coach')}"
                        />
                        <div
                          v-if="errors.has('coach')"
                          class="invalid-feedback d-block"
                        >
                          <p>{{ " Le champ Coach est obligatoire " }}</p>
                        </div>
                      
                    </div>
                  </div>
                </div>
                <div class="form-row" v-if="hasType">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>Societé*</label>
                      
                        <company-autocomplete
                              :disabled="$route.query.companyId != null"
                          :initial-query="user.company ? user.company.name : ''"
                          v-model="user.companyId"
                          v-validate="'required'"
                          name="company"
                          @query="query => getInputCompanyField(query)"
                          :class-list="{'is-invalid': errors.has('company')}"
                        />
                        <div
                          v-if="errors.has('company') && (!user.companyField || 0 === user.companyField.length)"
                          class="invalid-feedback d-block">
                          <p>{{ " Le champ Entreprise est obligatoire " }}</p>
                        </div>

                        <div
                          v-else-if="errors.has('company') && user.companyField"
                          class="invalid-feedback d-block">
                        <p>{{ " L'entreprise n'existe pas. Vous devez créer l'entreprise dans le menu &lt;&lt; Entreprise &gt;&gt; " }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5 offset-md-1">
                    <div class="form-group">
                      <label class>Fonction</label>
                      <input class="form-control" type="text" placeholder="Fonction" v-model="user.function" />
                    </div>
                  </div>
                </div>
                <div class="form-row" v-if="isCandidate">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>Date d'ancienneté dans l'entreprise*</label>

                      <el-date-picker
                              v-model="user.seniorityDate"
                              format="dd/MM/yyyy"
                              placeholder="Date d'ancienneté dans l'entreprise"
                              :calendar-button="true"
                              :required="true"
                              :bootstrap-styling="true"
                      />
                    </div>

                  </div>
                  <div class="col-md-5 offset-md-1">
                    <div class="form-group">
                      <label>Ancienneté dans la fonction</label>
                      
                        <input
                          class="form-control"
                          type="text"
                          placeholder="En nombre d'années"
                          v-model="user.previousFunction"
                        />
                      </div>
                    
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
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>LinkedIn</label>
                        <input class="form-control" type="text" placeholder="https://www.linkedin.com/company/oasys-consultants/" v-model="user.linkedin" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset v-if="hasType">
            <legend>Coordonnées</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="form-row">
                  <div class="col-md-5">
                    <div class="form-group">
                      <label>Email*</label>
                      
                        <input
                          v-model="user.email"
                          v-validate="'required|email|uniqueEmail'"
                          id="email"
                          name="email"
                          :class="{ 'is-invalid': errors.has('email') }"
                          type="email"
                          class="form-control"
                          placeholder="you@example.com"
                        />
                        <div
                          v-if="errors.has('email') && errors.firstByRule('email', 'email')"
                          class="invalid-feedback"
                        >
                          <p>{{ " Veuillez saisir une adresse email valide " }}</p>
                        </div>
                        <div v-else-if="errors.has('email')" class="invalid-feedback">
                          <p>{{ " L'email existe déjà" }}</p>
                        </div>
                     
                    </div>
                  </div>
                  <div class="col-md-5 offset-md-1">
                    <div class="form-group">
                      <label>Téléphone*</label>

                      <input
                              :class="{ 'is-invalid': errors.has('phone') }"
                              class="form-control"
                              id="phone"
                              name="phone"
                              placeholder="01 05 33 16 60..."
                              v-model="user.phone"
                              v-validate="{required: true, regex: '^(?:(?:\\+|00)33|0)\\s*[1-9](?:[\\s.-]*\\d{2}){4}$' }" />
                        <div
                          v-if="errors.has('phone') && errors.firstByRule('phone', 'required')"
                          class="invalid-feedback"
                        >
                          <p>{{ " Le champ Téléphone est obligatoire " }}</p>
                        </div>
                        <div v-else-if="errors.has('phone')" class="invalid-feedback">
                          <p>{{ " Le numéro de téléphone est dans un format invalide " }}</p>
                        </div>
                     
                    </div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-11">
                    <div class="form-group">
                      <label>Adresse</label>
                        <input class="form-control" placeholder="1 rue de test, 75008 Paris" v-model="user.address"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset v-if="hasType">
            <legend>Rôles*</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="form-row">
                  <div class="custom-form form-check" v-for="role in roles" :key="role.id">
                    <input
                      type="checkbox"
                      class="form-check-input"
                      :checked="user.roleIds.includes(role.id)"
                      @change="selectRole(role)"
                      :id="role.id"
                    />
                    <label class="form-check-label" :for="role.id">{{ role.name }}</label>
                  </div>
                  <div
                    v-if="user.roleIds.length === 0 && submitted"
                    class="invalid-feedback d-block"
                  >
                    <p>{{ "Au moins un rôle doit être sélectionné" }}</p>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <div class="form-btn-wrap" v-if="hasType">
              <button
                type="button"
                class="btn btn-outline-consultant-light"
                @click="$router.push({name: 'Users'})"
              >Annuler</button>
              <button
                type="button"
                class="btn btn-gradient-primary ml-2"
                @click="handleSubmit"
              >{{ 'Enregistrer' }}</button>
            </div>
          
        </form>
      </div>
    </div>
  </dash-wrap>
</template>

<script>
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";
import CompanyAutocomplete from "@/components/autocomplete/CompanyAutocomplete";
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import moment from "moment";
import {FILE_PATH} from "@/enum/FilePathConstant";

import { ALL_USER_TYPES } from "@/graphql/user/all-user-types-query";
import { ALL_ROLES } from "@/graphql/role/all-roles-query";
import { CREATE_USER } from "@/graphql/user/create-user-mutation";
import { UPDATE_USER } from "@/graphql/user/update-user-mutation";
import { USER_BY_ID } from "@/graphql/user/user-by-id-query";
import { UPLOAD_PICTURE } from "@/graphql/file/upload-picture-mutation";
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import { EMAIL_UNIQUE } from "@/graphql/user/email-unique-query";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

export default {
  name: "userForm",
  components: {
    UserAutocomplete,
    CompanyAutocomplete
  },
  data() {
    return {
      me: '',
      user: {
        firstName: "",
        lastName: "",
        email: "",
        phone: "",
        typeId: "",
        companyId: "",
        companyField: "",
        company: {},
        status: true,
        coach: {},
        coachId: "",
        roleIds: [],
        linkedin: "",
        address: "",
        function: "",
        seniorityDate: "",
        previousFunction: "",
        profilePicture: {},
        profilePictureId: "",
        profilePictureFile: null,
      },
      submitted: false,
      uploading: false,
      uploadeImage: null,
      loading: 0
    };
  },
  apollo: {
    me: {
        query: LOGGED_USER,
        update(data) {
            if(data.me.type === 'coach') {
                this.user.typeId = 'candidate';
                this.user.coach.lastName = data.me.lastName;
                this.user.coach.firstName = data.me.firstName;
                this.user.coachId = data.me.id;
            }
            return data.me;
        }
    },
    types: {
      query: ALL_USER_TYPES,
      loadingKey: "loading",
      update: data => data.allUserTypes
    },
    roles: {
      query: ALL_ROLES,
      loadingKey: "loading",
      update: data => data.allRoles.items
    }
  },
  computed: {
    isCandidate() {
      return this.user.typeId === "candidate";
    },
    isEdit() {
      return this.$route.params.id ? true : false;
    },
    hasType() {
        return this.user.typeId !== "";
    },
    getTypes() {
        let tmp = this.types;
        if (this.me.type !== "admin") {
            tmp = tmp.filter(function (item) {
                return item.id !== "admin"
            });
        }
        return tmp;
    }
  },
  methods: {
      getInputCompanyField(query) {
        this.user.companyField = query;
    },
    handleSubmit(e) {
      const isNew = !this.isEdit;
      this.submitted = true;
      this.$validator.resume();
      this.$validator.validate().then(async valid => {
        if (!valid || this.user.roleIds.length === 0) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          this.loading = false;
          return;
        }

        if (this.uploading) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez attendre la fin du téléchargement du fichier"
          });
          this.loading = false;
          return;
        }

        this.loading = true;

        let profilePictureId = this.user.profilePictureId;
        if (this.user.profilePictureFile) {
          let response = await this.$apollo.mutate({
            mutation: UPLOAD_PICTURE,
            variables: {
              file: this.user.profilePictureFile
            }
          });
          profilePictureId = response.data.uploadPicture.id;
        }
        this.$apollo
          .mutate({
            mutation: this.isEdit ? UPDATE_USER : CREATE_USER,
            variables: {
              ...this.user,
              profilePictureId: profilePictureId,
              phone: this.user.phone,
              seniorityDate: this.user.seniorityDate
                ? moment(this.user.seniorityDate, "D/M/YYYY").toISOString()
                : null
            },
            update(proxy) {
              if (isNew) {
                deleteQueriesFromApolloCache(proxy, "allUsers");
              }
            }
          })
          .then(() => {
            this.loading = false;
            if (this.$route.query.companyId) {
              this.$router.push({ name: "CompanyForm", params: {id: String(this.$route.query.companyId)} });
            } else {
              this.$router.push({ name: "Users" });
            }
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.$route.params.id
                ? "La modification a été effectuée avec succès"
                : "L'utilisateur a été créé avec succès"
            });
          });
      });
    },
    selectCoach(userId) {
      this.user.coachId = userId;
    },
    selectCompany(companyId) {
      this.user.companyId = companyId;
    },
    selectRole(role) {
      if (this.user.roleIds.includes(role.id)) {
        this.user.roleIds = this.user.roleIds.filter(r => r !== role.id);
      } else {
        this.user.roleIds.push(role.id);
      }
    },
    uploadStart() {
      this.uploading = true;
    },
    uploadProfilePicture(e) {
      let file = e.target.files[0];
      // > to 10MB
      if (file.size > 10000000 ) {
        izitoast.error({
          position: "topRight",
          title: "Erreur",
          message: "Cette image dépasse la limite de 2Mo"
        });
        return;
      }

      this.saveProfilePicture(file);
      let fileReader = new FileReader();
      fileReader.onload = (e) => {
        this.uploadeImage = e.target.result;
      };
      fileReader.readAsDataURL(file);
    },
    saveProfilePicture(file) {
      this.uploading = false;
      this.user.profilePictureFile = file;
    },
    removeProfilePicture() {
      this.uploading = false;
      this.user.profilePicture = {};
      this.user.profilePictureId = "";
      this.user.profilePictureFile = null;
    },
    getImageUrl(profilePhoto) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
        0,
        process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + profilePhoto.id;
    },
    dateFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    }
  },
  beforeCreate() {
    this.$validator.extend(
      "uniqueEmail",
      value => {
        return new Promise((resolve, reject) => {
          this.$apollo
            .query({
              query: EMAIL_UNIQUE,
              variables: {
                email: value,
                ...(this.$route.params.id ? {userId: this.$route.params.id} : {})
              }
            })
            .then(response => {
              resolve(
                true
              );
            }).catch(() => {
              resolve(
                  false
              );
          });
        });
      },
      {
        immediate: false
      }
    );
  },
  created() {
    this.$validator.pause();
    if (this.$route.params.id) {
      this.$apollo.addSmartQuery("user", {
        query: USER_BY_ID,
        variables: {
          id: this.$route.params.id
        },
        update: data => {
          return {
            ...data.userById,
            seniorityDate: data.userById.seniorityDate
              ? new Date(data.userById.seniorityDate)
              : "",
            phone:
              data.userById.phone.substr(0, 3) === "+33"
                ? data.userById.phone.substr(3)
                : data.userById.phone,
            profilePicture: data.userById.profilePicture
              ? data.userById.profilePicture
              : {},
            profilePictureId: data.userById.profilePicture
              ? data.userById.profilePicture.id
              : "",
            companyId: data.userById.company ? data.userById.company.id : "",
            coachId: data.userById.coach ? data.userById.coach.id : "",
            roleIds: data.userById.rolesByUsersRoles.map(role => role.id),
            typeId: data.userById.type.id
          };
        },
        loadingKey: "loading"
      });
    } else if(this.$route.query.companyId) {
      this.user.typeId = 'candidate';
      this.user.company = {
        name: this.$route.query.companyName
      };
      this.user.companyId = this.$route.query.companyId;
    }
  }
};
</script>
<style>
  .el-date-editor input {
      border-radius: 20px;
  }
</style>
