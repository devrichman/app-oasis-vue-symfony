<template>
    <div class="container text-left">
      <div class="content">
        <page-title
          pre-title="document"
          :title="isModify ? `Modifier un document` : `Créer un document`"
          @back="$router.push({name: 'DocumentsList'})"
        />
        <div class="row" v-if="loading">
          <div class="col-md-12">Loading...</div>
        </div>
        <form v-if="!loading">
          <fieldset>
            <legend>Détails</legend>
            <div class="panel panel-default">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-4" v-if="!eventId">
                    <div
                      class="form-group select"
                      :class="{ 'is-invalid':  errors.has('visibility') }"
                    >
                      <label>Visibilité*</label>

                      <select
                        class="form-control"
                        name="visibility"
                        v-model="document.visibility"
                        :class="{ 'is-invalid':  errors.has('visibility') }"
                        :disabled="!canEdit"
                        v-validate="'required'"
                      >
                        <option value disabled>Sélectionner une visibilité</option>
                        <option
                          v-for="visibility in this.visibilityList"
                          :key="visibility.value"
                          :value="visibility.value"
                        >{{ visibility.label }}</option>
                      </select>
                      <div v-if=" errors.has('visibility')" class="invalid-feedback">
                        <p>{{ " Le champ visibility est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                  <div :class="{'col-md-8': !eventId, 'col-md-12': eventId}">
                    <div class="form-group">
                      <label>Nom*</label>
                      <input
                        v-model="document.name"
                        v-validate="'required'"
                        id="name"
                        name="name"
                        class="form-control"
                        type="text"
                        placeholder="Nom..."
                        :class="{ 'is-invalid':  errors.has('name') }"
                        :disabled="!canEdit"
                      />
                      <div v-if="errors.has('name')" class="invalid-feedback">
                        <p>{{ " Le champ Nom est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Description*</label>

                      <textarea
                              v-validate="'required'"
                              id="description"
                              :class="{ 'is-invalid':  errors.has('description') }"
                              name="description"
                              class="form-control"
                              v-model="document.description"
                              rows="3"
                              :disabled="!canEdit"
                      ></textarea>
                      <div v-if=" errors.has('description')" class="invalid-feedback">
                        <p>{{ " Le champ description est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row" v-if="!isCandidate">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Auteur*</label>
                      <user-autocomplete
                              v-if="canEdit"
                              :initial-query="document.author && document.author.firstName ? document.author.firstName + ' ' + document.author.lastName : ''"
                              v-model="document.authorId"
                              v-validate="'required'"
                              name="author"
                              :class-list="{'is-invalid':  errors.has('author')}"
                      />
                      <div class="row" v-if="!canEdit">
                        <div class="col-md-12">
                          <single-user :user="document.author" :has-action="false" />
                        </div>
                      </div>
                      <div v-if=" errors.has('author')" class="invalid-feedback d-block">
                        <p>{{ " Le champ auteur est obligatoire " }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Date d'élaboration</label>

                      <div class="row">
                        <div class="col-md-12">
                          <el-date-picker
                                  v-model="document.elaborationDate"
                                  format="dd/MM/yyyy"
                                  name="elaborationDate"
                                  :disabled="!canEdit"
                                  :calendar-button="true"
                                  :bootstrap-styling="true"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row" v-if="!isCandidate">
                  <div class="col-md-12">
                    <div class="form-group tag">
                      <label>Tags</label>
                      <div class="form-control d-flex" :class="{'has-focus': tagInputVisible}">
                        <el-tag
                                :key="index"
                                v-for="(tag, index) in document.tags"
                                :closable="canEdit"
                                :disable-transitions="false"
                                @close="handleRemoveTag(tag)"
                        >{{tag}}</el-tag>

                        <input
                                class="form-control-tag form-control form-control-sm"
                                v-model="tagInput"
                                v-if="tagInputVisible"
                                ref="saveTagInput"
                                @keyup.enter="handleAddTag"
                                @blur="handleAddTag"
                        />
                        <button
                                type="button"
                                v-if="canEdit"
                                class="btn btn-outline-primary btn-sm button-new-tag"
                                @click="showInputTag"
                        >
                          +
                          Nouveau Tag
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </fieldset>
          <div class="form-card">
            <div class="form-group">
              <upload-file-drop
                label="Cliquez ici pour ajouter votre document*"
                :is-picture="false"
                :disabled="!canEdit"
                :value="document.fileDescriptor"
                @select-file="saveFile"
                :error="document.fileDescriptorError"
                @remove-file="removeFile"
              />
              <div class="info-drop-file">
                Pour faciliter la lecture des documents, privilégier le format .pdf.
                Les fichiers de type .doc / .xlsx / .ppt et les liens internet sont acceptés.
                La taille des fichiers doit être inférieure à 10 Mo
              </div>
            </div>
          </div>

          <div class="form-btn-wrap">
            <button
              type="button"
              class="btn btn-outline-consultant-light"
              @click="cancel"
            >Annuler</button>
            <button
              type="button"
              class="btn btn-gradient-primary ml-2 px-5"
              @click="() => !canEdit ? enableEdit() : handleSubmit()"
            >Enregistrer</button>
          </div>
        </form>
      </div>
    </div>

</template>
<style>
  .el-date-editor input {
      border-radius: 20px;
  }
  .el-date-editor input:focus {
      border-color: #7f267b;
      box-shadow: none;
  }
</style>
<script>
import SingleUser from "@/components/utils/SingleUser";
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import fr from "vuejs-datepicker/dist/locale/translations/fr.js";
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";
import izitoast from "izitoast";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import UploadFileDrop from "@/components/file/UploadFileDrop";
import moment from "moment";
import { FORM_LIST } from "@/enum/visibilityEnum";

import { CREATE_DOCUMENT } from "@/graphql/document/create-document-mutation.ts";
import { CREATE_DOCUMENT_FROM_EVENT } from "@/graphql/document/create-document-from-event-mutation.ts";
import { UPDATE_DOCUMENT } from "@/graphql/document/update-document-mutation.ts";
import { DOCUMENT_BY_ID } from "@/graphql/document/document-by-id-query";
import { UPLOAD_FILE } from "@/graphql/file/upload-file-mutation";
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";

export default {
  name: "documentForm",
  components: {
    SingleUser,
    UserAutocomplete,
    UploadFileDrop
  },
  data() {
    return {
      fr: fr,
      me: {
        type: ''
      },
      document: {
        name: "",
        visibility: "",
        fileDescriptor: {},
        fileDescriptorId: "",
        uploadedFile: null,
        fileDescriptorError: null,
        description: "",
        tags: [],
        author: {},
        authorId: "",
        elaborationDate: "",
        document: ""
      },
      tagInputVisible: false,
      tagInput: "",
      visibilityList: FORM_LIST,
      uploading: false,
      loading: 0
    };
  },
  computed: {
    canEdit() {
      return this.$route.params.mode !== "view";
    },
    isModify () {
        if (this.$route.params.document === 'document' || this.$route.params.mode === 'document') {
            return false;
        }
        if (this.$route.params.id) {
            return true;
        }
        return false;
    },
    eventId () {
        if (this.$route.params.eid && this.$route.params.event) {
            return this.$route.params.eid;
        }
        if (!this.$route.params.event) {
            return this.$route.params.id;
        }
        return null;
    },
    isCandidate () {
      return this.me.type === 'candidate'
    }
  },
  methods: {
    handleSubmit(e) {
      const isNew = !this.isModify;
      this.$validator.resume();
      this.$validator.validate().then(async valid => {
        if (!valid || !this.document.fileDescriptorName) {
          if (!this.document.fileDescriptorName) {
            this.document.fileDescriptorError =
              "Le champ fichier est obligatoire";
          }
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          return;
        }

        if (this.uploading) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez attendre la fin du téléchargement du fichier"
          });
          return;
        }

        this.loading = true;

        let fileDescriptorId = this.document.fileDescriptorId;
        if (this.document.uploadedFile) {
          let response = await this.$apollo.mutate({
            mutation: UPLOAD_FILE,
            variables: {
              file: this.document.uploadedFile
            }
          });
          fileDescriptorId = response.data.uploadFile.id;
        }

        let createMutation = this.eventId ? CREATE_DOCUMENT_FROM_EVENT : CREATE_DOCUMENT;
        this.document.eventId = this.eventId;
        this.$apollo
          .mutate({
            mutation: this.isModify ? UPDATE_DOCUMENT : createMutation,
            variables: {
              ...this.document,
              fileDescriptorId: fileDescriptorId,
              name: this.document.name,
              description: this.document.description,
              authorId: this.document.authorId,
              tags: this.document.tags.join(),
              visibility: this.document.visibility,
              elaborationDate: this.document.elaborationDate
                ? moment(
                    this.document.elaborationDate,
                    "D/M/YYYY"
                  ).toISOString()
                : ""
            },
            update(proxy) {
              if (isNew) {
                deleteQueriesFromApolloCache(proxy, "allDocuments");
              }
            }
          })
          .then(() => {
            this.loading = false;
            if (this.$route.params.document === 'document' || this.$route.params.mode === 'document') {
                this.$emit('submit');
            } else {
                this.$router.push({name: 'DocumentsList'});
            }
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.isModify
                ? "Le document a été mis à jour avec succès"
                : "Le document a été créé avec succès"
            });
          });
      });
    },
    selectAuthor(authorId) {
      this.document.authorId = authorId;
    },
    uploadStart() {
      this.uploading = true;
    },
    handleAddTag() {
      let inputValue = this.tagInput;
      if (inputValue) {
        this.document.tags.push(inputValue);
      }
      this.tagInputVisible = false;
      this.tagInput = "";
    },
    handleRemoveTag(tag) {
      this.document.tags.splice(this.document.tags.indexOf(tag), 1);
    },
    showInputTag() {
      this.tagInputVisible = true;
      this.$nextTick(_ => {
        this.$refs.saveTagInput.focus();
      });
    },
    saveFile(file) {
      this.uploading = false;
      this.document.uploadedFile = file;
      this.document.fileDescriptorName = file.name;
    },
    removeFile() {
      this.uploading = false;
      this.document.fileDescriptor = {};
      this.document.fileDescriptorId = "";
      this.document.uploadedFile = null;
    },
    dateFormatter(date) {
      return moment(date).format("DD/MM/YYYY");
    },
    cancel () {
        if (this.$route.params.document === 'document' || this.$route.params.mode === 'document') {
            this.$emit('close');
        } else {
            this.$router.push({name: 'DocumentsList'});
        }
    },
    enableEdit () {
      this.$router.push({name: 'DocumentForm', params: {id: this.$route.params.id, mode: ''}});
    }
  },
  created() {
    this.$validator.pause();
    if (this.isModify) {
      this.$apollo.addSmartQuery("document", {
        query: DOCUMENT_BY_ID,
        variables: {
          id: this.$route.params.id
        },
        update: data => {
          return {
            ...data.documentById,
            tags: data.documentById.tags.split(",")[0]  ? data.documentById.tags.split(",") : [],
            elaborationDate: data.documentById.elaborationDate
              ? new Date(data.documentById.elaborationDate)
              : "",
            name:
              data.documentById.name.substr(0, 3) === "+33"
                ? data.documentById.name.substr(3)
                : data.documentById.name,
            description: data.documentById.description
              ? data.documentById.description
              : {},
            visibility: data.documentById.visibility,
            authorId: data.documentById.author
              ? data.documentById.author.id
              : "",
            fileDescriptorId: data.documentById.fileDescriptor
              ? data.documentById.fileDescriptor.id
              : "",
            fileDescriptor: data.documentById.fileDescriptor
              ? data.documentById.fileDescriptor
              : {},
            fileDescriptorName: data.documentById.fileDescriptor
              ? data.documentById.fileDescriptor.name
              : {}
          };
        },
        loadingKey: "loading"
      });
    }
  }
};
</script>
