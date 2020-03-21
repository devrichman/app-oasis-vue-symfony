<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
      pre-title="Rôles"
      :title="$route.params.id ? 'Modifier le Rôle' : 'Créer un Rôle'"
      @back="$router.push({name: 'RolesList'})"
    />
    <div class="col-12 col-md-10 offset-md-1">
      <div class="row" v-if="loading">Loading...</div>
      <form v-if="!loading">
        <div class="form-card">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="name">Nom*</label>
              <input
                class="form-control"
                type="text"
                v-validate="'required|uniqueRoleName'"
                v-model="role.name"
                name="name"
                id="name"
                :class="{'is-invalid': errors.has('name')}"
              />
              <div
                v-if="errors.has('name') && errors.firstByRule('name', 'required')"
                class="invalid-feedback">
                <p>{{ " Le champ Nom est obligatoire " }}</p>
              </div>
              <div v-else-if="errors.has('name')" class="invalid-feedback">
                <p>{{ " Le Nom existe déjà" }}</p>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="description">Description*</label>
              <textarea
                name="description"
                id="description"
                v-validate="'required'"
                class="form-control"
                v-model="role.description"
                :class="{'is-invalid': errors.has('description')}"
              />
              <div v-if="errors.has('description')" class="invalid-feedback">
                <p>{{ " Le champ Description est obligatoire " }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="form-row form-card d-none d-md-block">
          <div class="form-group col-md-12">
            <!-- <h3>Droits du rôle</h3> -->
            <ul class="nav nav-tabs custom-nav">
              <li class="nav-item" v-for="(category, index) in rightsByCategories" :key="index">
                <a
                  class="nav-link"
                  @click="selectCategory(index)"
                  :class="{'active': activeCategory === index}"
                >
                  <i class="fa" :class="category.icon" />
                  <span>{{ category.label }}</span>
                </a>
              </li>
            </ul>
            <div class="tab-content">
              <div
                v-for="(category, index) in rightsByCategories"
                class="tab-pane fade"
                :class="{'show active': activeCategory === index}"
                :key="index"
              >
                <div class="row">
                  <div
                    class="col-sm-6 mt-4"
                    v-for="(right, index) in category.rights"
                    :key="index"
                    :class="{'mt-4': index === 0}"
                  >
                    <div class="form-group custom-form custom-check col-md-12">
                      <input
                        class="form-check-input"
                        type="checkbox"
                        :checked="role.rights.includes(right.code)"
                        @change="updateRight($event, right)"
                        :id="right.code"
                      />
                      <label class="form-check-label" :for="right.code">{{ right.name }}</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-block d-md-none">
          <mobile-tab
            v-for="(category, index) in rightsByCategories"
            :key="index"
            :active="activeCategory === index"
            :title="{name:category.label, icon:category.icon}"
            :index="index"
            @toggle="toggleMobileTab"
          >
            <div class="div" v-for="(right, index) in category.rights" :key="index">
              <div class="form-group custom-form custom-check col-md-12">
                <input
                  class="form-check-input"
                  type="checkbox"
                  :checked="role.rights.includes(right.code)"
                  @change="updateRight($event, right)"
                  :id="right.code"
                />
                <label class="form-check-label" :for="right.code">{{ right.name }}</label>
              </div>
            </div>
          </mobile-tab>
        </div>
        <div class="form-btn-wrap">
          <button
            class="btn btn-outline-consultant-light"
            @click.prevent="$router.push({name: 'RolesList'})"
          >Annuler</button>
          <button
            class="btn btn-gradient-primary"
            @click.prevent="handleSubmit()"
          >{{ $route.params.id ? 'Enregistrer' : 'Enregistrer'}}</button>
        </div>
      </form>
    </div>
  </dash-wrap>
</template>
<script>
import { ROLE_BY_ID } from "@/graphql/role/role-by-id-query";
import { ALL_RIGHTS } from "@/graphql/right/all-rights-query";
import { CREATE_ROLE } from "@/graphql/role/create-role-mutation";
import { UPDATE_ROLE } from "@/graphql/role/update-role-mutation";
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import MobileTab from "@/components/form/MobileTab";
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import {ROLE_NAME_UNIQUE} from "@/graphql/role/role-name-unique-query";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

export default {
  name: "rolesForm",
  components: {
    MobileTab
  },
  data: () => ({
    me: {
        type: '',
        rights: []
    },
    role: {
      id: "",
      name: "",
      description: "",
      rights: []
    },
    validationErrors: {
      name: false,
      description: false
    },
    rights: [],
    loading: false,
    activeCategory: 0
  }),
  computed: {
    roleQueryVariable() {
      return {
        id: this.$route.params.id
      };
    },
    rightCategories() {
      return [
        {
            key: "user",
            codes: ["USER", "USERS"],
            label: "Utilisateurs",
            icon: "fa-users",
            enabled:
                this.userIsAdmin ||
                this.user.rights.includes("ROLE_ACCESS_COMPANY_MENU")
        },
        {
            key: "company",
            codes: ["COMPANY", "COMPANIES"],
            label: "Entreprises",
            icon: "fa-building",
            enabled: this.userIsAdmin
        },
        {
          key: "role",
          codes: ["ROLE", "ROLES"],
          label: "Rôles",
          icon: "fa-tag",
          enabled: true
        },
        {
            key: "program",
            codes: ["PROGRAM", "PROGRAMS"],
            label: "Prestations",
            icon: "fa-clipboard",
            enabled:
                this.userIsAdmin || this.user.rights.includes("ROLE_ACCESS_EVENT")
        },
        {
          key: "event",
          codes: ["EVENT", "EVENTS"],
          label: "Événements",
          icon: "fa-calendar-alt",
          enabled:
            this.userIsAdmin || this.me.rights.includes("ROLE_ACCESS_PROGRAM")
        },
        {
            key: "document",
            codes: ["DOCUMENT"],
            label: "Documents",
            icon: "fa-clipboard",
            enabled:
                this.userIsAdmin
        }
      ];
    },
    rightsByCategories() {
      let categories = [];
      this.rightCategories.forEach(category => {
        let rights = [];
        this.rights.forEach(right => {
          let matches = false;
          category.codes.forEach(code => {
            if (right.code.substr(5).indexOf(code) > -1) {
              matches = true;
            }
          });
          if (matches) {
            rights.push(right);
          }
        });
        categories.push({
          ...category,
          rights: rights
        });
      });
      return categories;
    },
    userIsAdmin() {
      return this.me.type === "admin";
    }
  },
  apollo: {
    me: {
        query: LOGGED_USER
    },
    rights: {
      query: ALL_RIGHTS,
      loadingKey: "loading",
      update: data => data.allRights
    }
  },
  methods: {
    selectCategory(index) {
      this.activeCategory = index;
    },
    updateRight($event, right) {
      if (this.role.rights.includes(right.code)) {
        this.role.rights = this.role.rights.filter(r => r !== right.code);
      } else {
        this.role.rights.push(right.code);
      }
    },
    handleSubmit() {
      const isNew = !this.$route.params.id;
      this.$validator.resume();
      this.$validator.validate().then(async valid => {
        if (!valid) {
            izitoast.error({
                timeout: IZITOAST_CONSTANTS.TIME_OUT,
                position: "topRight",
                title: "Erreur",
                message: "Veuillez vérifier le formulaire pour les erreurs"
            });
            return;
        }

        this.loading = true;
        this.$apollo
            .mutate({
                mutation: !this.$route.params.id ? CREATE_ROLE : UPDATE_ROLE,
                variables: {
                    ...this.role
                },
                loadingKey: "loading",
                update(proxy) {
                    if (isNew) {
                        deleteQueriesFromApolloCache(proxy, "allRoles");
                    }
                }
            })
            .then(() => {
                this.loading--;
                this.$router.push({name: "RolesList"});
                izitoast.success({
                    position: "topRight",
                    title: "Succès",
                    message: this.$route.params.id
                        ? "Le rôle a été mis à jour avec succès"
                        : "Le rôle a été créé avec succès"
                });
            });
      });
    },

    toggleMobileTab(index) {
      this.activeCategory = this.activeCategory === index ? null : index;
    }
  },
  beforeCreate() {
      this.$validator.extend(
          "uniqueRoleName",
          value => {
              return new Promise((resolve, reject) => {
                  this.$apollo
                      .query({
                          query: ROLE_NAME_UNIQUE,
                          variables: {
                              name: value,
                              ...(this.$route.params.id ? {roleId: this.$route.params.id} : {})
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
      this.$apollo.addSmartQuery("role", {
        query: ROLE_BY_ID,
        variables() {
          return this.roleQueryVariable;
        },
        update: data => {
          return {
            ...data.roleById,
            rights: data.roleById.rights.map(right => right.code)
          };
        },
        loadingKey: "loading"
      });
    }
  }
};
</script>
