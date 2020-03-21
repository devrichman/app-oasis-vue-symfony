<template>
  <dash-wrap article="Importer" title="Des Utilisateurs" mode="admin" :hide-tabs="true">
    <div class="container text-left">
      <div class="content">
        <page-title pre-title="Utilisateurs" title="Importer" @back="$router.push({name: 'Users'})" />
        <div class="row" v-if="loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <div class="form-card mt-3">
          <legend>Modèle</legend>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="form-row">
                <div class="form-btn-wrap">
                  <a
                          type="button"
                          class="btn btn-outline-primary"
                          :href="getModeleUrl()">
                    <i class="fa fa-file" aria-hidden="true"></i>
                    Télécharger fichier modèle d'import
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <form v-show="!loading">
          <div class="form-card">
            <div class="form-group">
              <div>
                <upload-file-drop
                        label="Cliquez ici pour ajouter votre fichier*"
                        :is-excel="true"
                        @select-file="savefile"
                        @remove-file="removefile" />
              </div>
              <small class="text-danger" v-if="submitted && errorMessage" v-html="errorMessage" />
            </div>
          </div>
          <div class="form-btn-wrap">
            <button
                    type="button"
                    class="btn btn-outline-consultant-light"
                    @click="$router.push({name: 'Users'})"
            >Annuler</button>
            <button
                    type="button"
                    class="btn btn-gradient-primary"
                    @click="handleSubmit"
                    :disabled="!file"
            >Importer</button>
          </div>
        </form>
      </div>
    </div>
  </dash-wrap>
</template>
<script>
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import UploadFileDrop from "@/components/file/UploadFileDrop";

import { IMPORT_USERS } from '@/graphql/user/import-users-mutation';
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import { MODEL_IMPORT_FILE_PATH } from "@/enum/FilePathConstant";

export default {
  name: "importUsers",
  components: {
    UploadFileDrop
  },
  data() {
    return {
      submitted: false,
      loading: false,
      errorMessage: '',
      file: null,
    };
  },
  methods: {
    getModeleUrl() {
        let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
            0,
            process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
        );
        return baseUrl + MODEL_IMPORT_FILE_PATH;
    },
    handleSubmit(e) {
      this.submitted = true;
      this.loading = true;
      this.$apollo
        .mutate({
          mutation: IMPORT_USERS,
          variables: {
            file: this.file,
          },
          update(proxy) {
            deleteQueriesFromApolloCache(proxy, "allUsers");
          }
        })
        .then(response => {
          this.loading = false;
          this.$router.push({ name: "Users" });
          izitoast.success({
            position: "topRight",
            title: "Succès",
            message: "Les utilisateurs ont été importés avec succès",
          });
        })
        .catch(response => {
          this.loading = false;
          this.errorMessage = response.networkError.result.errors[0].message;
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Une erreur s'est produite lors de l'importation d'utilisateurs, veuillez consulter le formulaire",
          });
        });
    },
    savefile(file) {
      let filename = file.name.toLowerCase();
      if (filename.slice(-3) !== 'xls' && filename.slice(-4) !== 'xlsx') {
        izitoast.error({
          timeout: IZITOAST_CONSTANTS.TIME_OUT,
          position: "topRight",
          title: "Erreur",
          message: "Le fichier doit être xls et xlsx",
        });
        return;
      }
      this.file = file;
      this.submitted = false;
      this.errorMessage = '';
    },
    removefile() {
      this.file = null;
    },
  },
};
</script>
