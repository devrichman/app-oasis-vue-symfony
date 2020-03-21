<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
            pre-title="entreprise"
            :title="$route.params.id ? `Modifier une entreprise` : `Créer une entreprise`"
            @back="$router.push({name: 'CompaniesList'})"
    />
    <div class="col-12 col-md-10 offset-md-1">
      <div class="content">
        <div class="row" v-if="loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-if="!loading">
          <fieldset>
            <legend>Détails</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="form-row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Code</label>
                      <div class="col-sm-8">
                        <input
                          v-model="company.code"
                          name="code"
                          class="form-control"
                          type="text"
                          disabled
                        />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="col-md-5">
                    <div class="form-group row">
                      <label class="col-sm-4 col-form-label">Nom*</label>
                      <div class="col-sm-8">
                        <input
                          v-model="company.name"
                          v-validate="'required|uniqueName'"
                          id="name"
                          name="name"
                          class="form-control"
                          :class="{ 'is-invalid': errors.has('name') }"
                          type="text"
                          placeholder="Nom..."
                        />
                        <div
                          v-if="errors.has('name') && errors.firstByRule('name', 'required')"
                          class="invalid-feedback"
                        >
                          <p>{{ " Le champ nom est obligatoire " }}</p>
                        </div>
                        <div v-else-if="errors.has('name')" class="invalid-feedback">
                          <p>{{ " Le nom existe déjà" }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5 offset-md-1">
                    <div class="form-group row">
                      <label class="col-sm-6 col-form-label">Lien vers Salesforce</label>
                      <div class="col-sm-6">
                        <input
                          v-model="company.salesforceLink"
                          id="lastName"
                          name="lastName"
                          class="form-control"
                          type="text"
                          placeholder="Salesforce..."
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <fieldset v-if="$route.params.id">
            <legend>Utilisateurs</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div v-if="$route.params.id" class="form-row">
                  <div class="form-btn-wrap">
                    <router-link
                      class="btn btn-outline-primary"
                      :to="{name: 'Users', query: {companyId: company.id, companyName: company.name}}"
                      target="_blank"
                    >Voir les utilisateurs</router-link>
                    <router-link
                      v-if="$route.params.id"
                      class="btn btn-gradient-primary"
                      :to="{name: 'UsersForm', query: {companyId: company.id, companyName: company.name}}"
                      tag="button"
                    >Créer un utilisateur</router-link>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>

          <div class="form-btn-wrap">
            <button
              type="button"
              class="btn btn-outline-consultant-light"
              @click.prevent="$router.push({name: 'CompaniesList'})"
            >Annuler</button>
            <button
              type="button"
              class="btn btn-gradient-primary"
              @click="handleSubmit"
            >Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </dash-wrap>
</template>

<script>
import izitoast from "izitoast";
import { CREATE_COMPANY } from "@/graphql/company/create-companies-mutation";
import { COMPANY_BY_ID } from "@/graphql/company/company-by-id-query";
import { UPDATE_COMPANY } from "@/graphql/company/update-company-mutation";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import { ALL_COMPANIES } from "@/graphql/company/all-companies-query";
import { IZITOAST_CONSTANTS } from "@/enum/IzitoastConstants";

export default {
  name: "companyForm",
  data() {
    return {
      company: {
        code: "",
        name: "",
        salesforceLink: ""
      },
      uploading: false,
      loading: 0
    };
  },
  apollo: {},
  computed: {},
  methods: {
    handleSubmit() {
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
            mutation: this.$route.params.id ? UPDATE_COMPANY : CREATE_COMPANY,
            variables: {
              ...this.company
            },
            update(store, result) {
              if (result.data.createCompany) {
                deleteQueriesFromApolloCache(store, "allCompanies");
              }
            }
          })
          .then(() => {
            this.loading = false;
            this.$router.push({ name: "CompaniesList" });
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.$route.params.id
                ? "L'entreprise a été modifiée avec succès"
                : "L'entreprise a été créée avec succès"
            });
          })
          .catch(e => {
            izitoast.error({
              timeout: IZITOAST_CONSTANTS.TIME_OUT,
              position: "topRight",
              title: "Erreur",
              message: e
            });
          });
      });
    }
  },
  beforeCreate() {
    this.$validator.extend(
      "uniqueName",
      value => {
        return new Promise((resolve, reject) => {
          this.$apollo
            .query({
              query: ALL_COMPANIES,
              variables: {
                search: value,
                limit: 1
              }
            })
            .then(response => {
              resolve(
                response.data.allCompanies.items.length === 0 ||
                  response.data.allCompanies.items[0].id ===
                    this.$route.params.id
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
      this.$apollo.addSmartQuery("company", {
        query: COMPANY_BY_ID,
        variables: {
          id: this.$route.params.id
        },
        update: data => {
          return {
            ...data.companyById,
            name: data.companyById.name,
            salesforceLink: data.companyById.salesforceLink,
            code: data.companyById.code
          };
        },
        loadingKey: "loading"
      });
    }
  }
};
</script>
<style>
</style>
