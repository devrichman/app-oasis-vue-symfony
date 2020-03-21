<template>
    <div>
        <div class="input-group">
            <div class="input-group-prepend" v-if="value && value.id">
                <button class="btn btn-outline-secondary" disabled>{{ getFileExtension(value.name) }}</button>
            </div>
            <div class="custom-file" :class="{'file-selected': value && value.id, 'file-in-progress': loading}" @click="fileLabelClick">
                <input type="file" class="custom-file-input" lang="fr" :accept="accept" @change="selectFile"
                       :disabled="loading || (value && value.id)"
                       :class="{'is-invalid': error && error.length > 0}" />
                <label class="custom-file-label" v-if="!loading">
                    {{ value && value.name ? value.name : (file ? file.name : '') }}
                </label>
                <label class="custom-file-label" v-else>
                    Téléchargement...
                </label>
                <div class="loader" v-show="loading"></div>
            </div>
            <div class="input-group-append" v-if="value && value.id && !disabled">
                <button class="btn btn-danger" @click="fileLabelClick">X</button>
            </div>
        </div>
        <small class="text-danger" v-if="error && error.length > 0">
            {{ error }}
        </small>
    </div>
</template>
<style scoped>
    .custom-file.file-in-progress .custom-file-label {
        padding-left: 32px;
    }
    .custom-file .custom-file-label {
        overflow: hidden;
    }
    .custom-file .loader {
        right: 0;
        left: 4px;
        z-index: 100;
    }
    .custom-file.file-selected .custom-file-label:after {
        content: "";
        padding: 0;
        border: 0;
    }
    .custom-file-input:disabled~.custom-file-label, .custom-file-input[disabled]~.custom-file-label {
        background: #fff;
    }
</style>
<script>
    import {UPLOAD_PICTURE} from '@/graphql/file/upload-picture-mutation';
    import {UPLOAD_FILE} from '@/graphql/file/upload-file-mutation';
    import izitoast from 'izitoast';

    export default {
        name: 'UploadFile',
        props: {
            value: {
                type: Object,
                required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            },
            isPicture: {
                type: Boolean,
                default: false,
            },
            isExcel: {
                type: Boolean,
                default: false,
            },
            error: {
                type: String,
                required: false,
            },
        },
        data: () => ({
            loading: false,
            file: null,
        }),
        computed: {
            accept () {
                return this.isPicture ? '.png,.jpeg,.jpg,.gif' : (this.isExcel ? '.xls,.xlsx' : '');
            },
        },
        methods: {
            selectFile ($event) {
                let file = $event.target.files[0];
                if (!file || !file.size) {
                    return;
                }

                this.file = file;
                this.$emit('select-file', file);
            },
            fileLabelClick () {
                if (this.value && this.value.id) {
                    this.file = null;
                    this.$emit('remove-file');
                }
            },
            getFileExtension (filename) {
                return filename.substr(filename.lastIndexOf('.') + 1);
            },
        },
    }
</script>