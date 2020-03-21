<template>
  <div class="dropzone custom-form" :class="{'dropzone--disabled':disabled, 'has-file': hasFile}">
    <template v-if="!hasFile && !disabled">
      <label :for="id" class="p-4" :class="{'border-danger': error, 'text-danger': error, 'alert-danger': error}">
        <div class="dropzone-file d-flex flex-column align-items-center w-100">
          <img src="@/assets/img/default-file.svg" />
          <div
            class="custom-file"
            :class="{'file-selected': value && value.id, 'file-in-progress': loading}"
            @click="fileLabelClick"
          >
            <input
              type="file"
              class="custom-file-input"
              lang="fr"
              :id="id"
              :accept="accept"
              @change="selectFile"
              :disabled="loading || (value && value.id)"
              :class="{'is-invalid': error && error.length > 0}"
            />

            <span class="d-block dropzone-label mt-3 text-center">
              <template v-if="error && error.length > 0">{{error}}</template>
              <template v-else>{{label}}</template>
            </span>
            <div class="loader" v-show="loading"></div>
          </div>
        </div>
      </label>
    </template>
    <template v-else-if="hasFile">
      <div class="dropzone-file">
        <div class="d-flex align-items-center p-3">
          <img height="48px" width="38px" src="@/assets/img/present-file.svg" />
          <a v-if="value" target="_blank" rel="noopener noreferrer" class="font-size-18 text-gris_44 d-block mx-3" :href="getFileLinkUrl(value)" download>
            {{fileName}}
          </a>
          <span class="font-size-18 text-gris_44 d-block mx-3" v-else>
            {{fileName}}
          </span>
          <button v-if="!disabled" type="button" @click="removeFile" class="btn liste-action-btn bg-gris_94">
            <img src="@/assets/img/edit.svg" alt />
          </button>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import izitoast from "izitoast";
import {FILE_PATH} from "@/enum/FilePathConstant";

export default {
  name: "UploadFileDrop",
  props: {
    value: {
      type: Object,
      required: false
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    },
    isPicture: {
      type: Boolean,
      default: false
    },
    isExcel: {
      type: Boolean,
      default: false,
    },
    error: {
      type: String,
      required: false
    },
    label: {
      type: String,
      default: null
    },
    legend: {
      type: String,
      default: null
    },
  },
  data() {
    return {
      loading: false,
      file: this.value,
    };
  },
  computed: {
    accept () {
      return this.isPicture ? '.png,.jpeg,.jpg,.gif' : (this.isExcel ? '.xls,.xlsx' : '');
    },
    id() {
      return "dropzone-" + Date.parse(new Date());
    },
    hasFile() {
      return this.file && this.file.name != null;
    },
    fileName() {
      return this.file.name ? this.file.name : 'no-file'
    }
  },
  methods: {
    getFileLinkUrl(file) {
      let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(
              0,
              process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
      );
      return baseUrl + FILE_PATH + file.id;
    },
    selectFile($event) {

      let file = $event.target.files[0];
      if (!file || !file.size) {
        return;
      }
      // > to 10MB
      if (file.size > 10000000 ) {
        izitoast.error({
          position: "topRight",
          title: "Erreur",
          message: "Ce fichier dépasse la limite de 10Mo"
        });
        return;
      }
      var ext = file.name.split('.').pop();
      if(this.accept){
        if(!this.accept.split(','.includes(`.${ext}`))){
          return false
        }
      }
      
      this.file = file;
      this.$emit("select-file", file);
      this.$emit("input", file);
    },
    fileLabelClick() {
      if (this.value && this.value.id) {
        this.file = null;
        this.$emit("remove-file");
      }
    },
    getFileExtension(filename) {
      return filename.substr(filename.lastIndexOf(".") + 1);
    },
    removeFile() {
      this.file = null;
    }
  }
};
</script>