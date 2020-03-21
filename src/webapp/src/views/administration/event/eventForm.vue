<template>
    <dash-wrap active="admin" :hide-tabs="true">
        <page-title
            :pre-title="programModel ? 'PRESTATION MODELE / MODIFIER UN MODELE' : 'PRESTATION / MODIFIER UN PRESTATION'"
            :title="(isModify ? 'Modifier un Événement' : 'Créer un Événement') + (programModel ? ' Modele' : '')"
            @back="$router.push(previousPage)"
        ></page-title>
        <div class="container">
            <div class="content">
                <div class="row" v-if="$apollo.loading">
                    <div class="col-md-11">Loading...</div>
                </div>
                <template v-else>
                    <div class="col-md-12 mb-4">
                        <div class="float-left mr-3">
                            <img :src="require(`@/assets/img/prestation.svg`)" width="40" height="40" >
                            <span class="Bien-tre-au-travail">
                                {{event.program ? event.program.name : program.name}}
                            </span>
                        </div>
                        <div class="ml-3">
                            <img :src="require(`@/assets/img/cal.svg`)" width="40" height="40" >
                            <span class="Bien-tre-au-travail">
                                {{event.program ? dateFormatter(event.program.dateStart) : dateFormatter(program.dateStart)}} au {{event.program ? dateFormatter(event.program.dateEnd) : dateFormatter(program.dateEnd)}}
                            </span>
                        </div>
                    </div>
                    <div class="form-row" v-if="isModify">
                        <div class="col-md-12 text-right last-modified-at form-group mb-0">
                            <label>Derniére modificaton</label> : 
                            <strong>
                                <router-link class="text-dark" :to="{name: 'UsersForm', params: {id: event.updatedBy !== null ? event.updatedBy.id : event.createdBy.id}}" target="_blank">
                                    {{event.updatedBy ? event.updatedBy.firstName : event.createdBy.firstName}}
                                </router-link> 
                                le {{ lastModifiedAt }}
                            </strong>
                        </div>
                    </div>
                    <form>
                        <fieldset>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row">
                                        <div class="col-md-5 form-group radio" v-if="isAdmin">
                                            <label>Est-ce que cet événement est un modèle ?</label>
                                            <div class="radio-items">
                                                <div class="radio-item">
                                                    <input class="form-check-input" type="radio" :value="true" v-model="event.isModel" id="model_yes" :disabled="isModify || !canEdit || isProgramForm" />
                                                    <label class="form-check-label" for="model_yes">Oui</label>
                                                </div>
                                                <div class="radio-item">
                                                    <input class="form-check-input" type="radio" :value="false" v-model="event.isModel" id="model_no" :disabled="isModify || !canEdit || isProgramForm" />
                                                    <label class="form-check-label" for="model_no">Non</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5 form-group select" :class="{'offset-md-1': isAdmin}" v-if="!event.isModel && event.eventModel">
                                            <label for="model">Quel modéle souhaitez-vous utiliser ?</label>
                                            <select id="model" class="form-control" v-model="event.eventModel.id" @change="updateModel" :disabled="!canEdit">
                                                <option value="">Sélectionnez un modèle d'événement</option>
                                                <option v-for="model in eventModels" :key="model.id" :value="model.id">
                                                    {{ model.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-5 form-group select" :class="{'offset-md-1': !isAdmin}">
                                            <label for="type">Type d'événement</label>
                                            <select id="type" name="type" class="form-control"
                                                    :disabled="!canEdit"
                                                    v-model="event.type"
                                                    v-validate="'required'"
                                                    :class="{'is-invalid': errors.has('type')}">
                                                <option v-for="type in eventTypes" :key="type.id" :value="type.value">
                                                    {{ type.label }}
                                                </option>
                                            </select>
                                            <div v-if="errors.has('type')" class="invalid-feedback">
                                                Le champ type est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Informations</legend>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row">
                                        <div class="col-md-5 form-group">
                                            <label for="name">Nom</label>
                                            <input class="form-control" placeholder="Nom" id="name" name="name" v-validate="'required'"
                                                    v-model="event.name"
                                                    :disabled="!canEdit"
                                                    :class="{'is-invalid': errors.has('name')}" />
                                            <div v-if="errors.has('name')" class="invalid-feedback">
                                                Le champ nom est obligatoire.
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="!event.isModel">
                                            <label>Date évènement</label>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <el-date-picker
                                                        name="dateEvent"
                                                        v-model="event.dateEvent"
                                                        v-validate="'afterToday|required'"
                                                        type="date"
                                                        format="dd/MM/yyyy"
                                                        :disabled="!canEdit"
                                                    />
                                                </div>
                                            </div>
                                            <small v-if="errors.has('dateEvent')" class="text-danger">
                                                <span v-if="errors.items.find(e=>e.field==='dateEvent').rule==='required'">Le champ date est obligatoire.</span>
                                                <span v-else>date d’événement ne peut pas être dans le passé</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-11 form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" name="description" id="description"
                                                        rows="5"
                                                        v-validate="'required'"
                                                        v-model="event.description"
                                                        :required="true"
                                                        :disabled="!canEdit"
                                                        :class="{'is-invalid': errors.has('description')}" />
                                            <div v-if="errors.has('description')" class="invalid-feedback">
                                                Le champ Description est obligatoire.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="!event.isModel">
                                        <div class="col-md-5 form-group">
                                            <label>Organisateur</label>
                                            <user-autocomplete v-if="isAdmin && canEdit"
                                                                v-model="organizerInput"
                                                                @item-selected="updateOrganizer"
                                                                :query-coaches="true"
                                                                :empty-query-after-selection="true"
                                                                name="coach" />
                                            <div>
                                                <single-user :user="event.organizer && event.organizer.id ? event.organizer : me"
                                                                :has-action="event.organizer && event.organizer.id ? true : false"
                                                                @click="removeOrganizer" />
                                            </div>
                                        </div>
                                        <div class="col-md-5 offset-md-1 form-group" v-if="isModify">
                                            <label for="createdAt">Date de création</label>
                                            <input class="form-control created-date" name="createdAt" id="createdAt" :value="dateFormatter(event.createdAt)" disabled />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset v-if="!event.isModel">
                            <legend>Invités</legend>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-row" v-if="canEdit">
                                        <div class="col-md-5">
                                            <user-autocomplete v-model="inviteeInput"
                                                                @item-selected="addUser"
                                                                :class-list="{'is-invalid': event.users.length === 0 && submitted}"
                                                                :empty-query-after-selection="true" />
                                        </div>
                                    </div>
                                    <div class="form-row" v-if="event.users">
                                        <div class="col-md-11">
                                            <single-user v-for="user in event.users" :key="user.id" :user="user" :has-action="true" @click="$event => removeUser(user)" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset v-if="!event.isModel && isModify">
                            <legend>Documents</legend>
                            <div class="panel panel-default">
                                <div class="panel-header">
                                    <button class="btn btn-primary float-right" @click.prevent="addNewDocument">
                                        + Ajouter un document
                                    </button>
                                </div>
                                <div class="panel-body">
                                    <div class="form-row">
                                        <div class="col-md-10" v-for="document in event.documents" :key="document.id">
                                            <router-link :to="{name: 'DocumentForm', params: {id: document.id}}" target="_blank">
                                                {{document.name}}
                                            </router-link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <document-dialog v-if="$route.params.document==='document' || $route.params.mode==='document'" @close="closeDocumentDialog" @submit="submitDocumentDialog" />
                        <div class="row" v-if="canEdit">
                            <div class="col-md-12 mt-1">
                                <div class="form-btn-wrap">
                                    <button type="button" class="btn btn-outline-consultant-light" @click="cancel">
                                        Annuler
                                    </button>
                                    <button type="button" class="btn btn-gradient-primary" @click="handleSubmit">
                                        Enregistrer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </dash-wrap>
</template>
<style>
    .last-modified-at span {
        margin-right: 0;
    }
    .el-date-editor.el-input, .el-date-editor.el-input__inner  {
        width: 100%
    }
    .el-date-editor input {
        border-radius: 20px;
    }
    .el-date-editor input:focus {
        border-color: #7f267b;
        box-shadow: none;
    }
    .Bien-tre-au-travail {
        margin-left: -25px;
        width: 165px;
        height: 26px;
        font-family: Calibri;
        font-size: 21px;
        font-weight: bold;
        font-stretch: normal;
        font-style: normal;
        line-height: normal;
        letter-spacing: normal;
        color: var(--gris_70);
    }
    .el-date-editor.is-disabled .el-input__inner {
        background-color: white;
    }
    .created-date:disabled{
        background-color: white;
    }
</style>
<script>
    import 'vue-phone-number-input/dist/vue-phone-number-input.css';
    import izitoast from 'izitoast';
    import moment from 'moment';
    import UserAutocomplete from '@/components/autocomplete/UserAutocomplete';
    import SingleUser from '@/components/utils/SingleUser';
    import DocumentDialog from '@/views/document/documentFormDialog';
    import {EVENT_BY_ID} from "@/graphql/event/event-by-id-query";
    import {EVENT_MODEL_BY_ID} from "@/graphql/event/event-model-by-id-query";
    import {PROGRAM_BY_ID} from "@/graphql/program/program-by-id-query";
    import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import { EVENT_TYPES } from "@/enum/EventTypesEnum";
    import {PROGRAM_MODEL_BY_ID} from "@/graphql/program/program-model-by-id-query";
    import {UPDATE_EVENT_MODEL} from "@/graphql/event/update-event-model-mutation";
    import {CREATE_EVENT_MODEL} from "@/graphql/event/create-event-model-mutation";
    import {UPDATE_EVENT} from "@/graphql/event/update-event-mutation";
    import {CREATE_EVENT} from "@/graphql/event/create-event-mutation";


    export default {
        name: "Event",
        components : {
            UserAutocomplete,
            SingleUser,
            DocumentDialog
        },
        data() {
            return {
                me: {},
                event: {
                    name: '',
                    isModel: this.$route.name.startsWith('ProgramModel'),
                    dateEvent: '',
                    eventModel: {
                        id: ''
                    },
                    description: '',
                    organizerId: '',
                    organizer: {},
                    updatedBy: '',
                    createdBy: '',
                    createdAt: '',
                    users: [],
                    type: '',
                },
                program: {

                },
                eventModels: [],
                inviteeInput: '',
                organizerInput: '',
                loading: 0,
                submitted: false,
                routeName: this.$route.name,
                routeMode: this.$route.params.mode,
                routeParams: this.$route.params,
                programModel: this.$route.name.startsWith('ProgramModel'),
                isProgramForm: this.$route.name.startsWith('Program')
            };
        },
        mounted: function() {
            this.event.users = this.program.users ? this.program.users : []
        },
        computed: {
            isAdmin () {
                return this.me && this.me.type === 'admin';
            },
            previousPage() {
                if(this.isProgramForm) {
                    return {name: this.programModel ? 'ProgramModelForm' : 'ProgramForm', params: {id: this.$route.params.id}}
                } else {
                    return {name: 'EventsList'}
                }
            },
            lastModifiedAt () {
                if (!this.isModify) {
                    return '';
                }
                let date = this.event.updatedAt !== null ? this.event.updatedAt : this.event.createdAt;
                return moment(date).format('DD/MM/YYYY');
            },
            canEdit () {
                return this.$route.params.mode !== 'view';
            },
            isModify () {
                if (this.isProgramForm) {
                    return this.$route.params.eid ? true : false;
                } else {
                    return this.$route.params.id ? true : false;
                }
            },
            eventTypes() {
                return EVENT_TYPES;
            },
        },
        apollo: {
            me: {
                query: LOGGED_USER,
            },
            event: {
                query()  {
                    return this.routeMode === 'model' ? EVENT_MODEL_BY_ID : EVENT_BY_ID;
                },
                variables() {
                    let id;
                    let variableName = this.routeMode === 'model' ? 'eventModelId' : 'eventId';
                    if(this.routeName === 'ProgramEventForm' || this.routeName === 'ProgramEventDocumentForm' || this.routeName === 'ProgramModelEventForm') {
                        id = this.routeParams.eid;
                    } else {
                        id = this.routeParams.id
                    }
                    return {
                       [variableName]: id,
                    }
                },
                update(data) {
                    let result = data.eventById || data.eventModelById;
                    result.isModel = this.routeMode === 'model';
                    if (result.isModel) {
                        result.users = [];
                    }
                    return result
                },
                skip() {
                    if (this.isProgramForm) {
                        return !this.routeParams.eid;
                    } else {
                        return !this.routeParams.id;
                    }
                }
            },
            eventModels: {
                query: ALL_EVENT_MODELS,
                update: data => data.allEventModels.items
            },
            program: {
                query() {
                    return this.routeName.startsWith('ProgramModel') ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID;
                },
                variables() {
                    let variableName = this.routeName.startsWith('ProgramModel') ? 'programModelId' : 'programId';
                    return {
                        [variableName]: this.routeParams.id
                    }
                },
                update: data => data.programModelById || data.programById,
                skip() {
                    return !this.routeName.startsWith('Program');
                }
            }
        },
        methods: {
            handleSubmit (e) {
                this.$validator.resume();
                this.submitted = true;
                this.$validator.validate().then(valid => {
                    if (!valid || (this.event.users.length === 0 && !this.event.isModel)) {
                        izitoast.error({
                            position: 'topRight',
                            title: 'Erreur',
                            message: 'Veuillez vérifier le formulaire pour les erreurs',
                        });
                        return;
                    }

                    this.loading = true;

                    let mutation,
                        variables = {
                            ...this.event,
                        };
                    if (this.event.isModel) {
                        mutation = this.isModify ? UPDATE_EVENT_MODEL : CREATE_EVENT_MODEL;
                        if (this.isProgramForm) {
                            variables.programModelId = this.program.id;
                        }
                    } else {
                        mutation = this.isModify ? UPDATE_EVENT : CREATE_EVENT;
                        variables.userIds = this.event.users.map(user => user.id);
                        variables.organizerId = this.event.organizer && this.event.organizer.id ? this.event.organizer.id : this.me.id;
                        variables.programId = this.isProgramForm ? this.$route.params.id : this.event.program.id;
                        variables.dateEvent = moment(this.event.dateEvent).format('YYYY-MM-DD HH:mm:ss');
                    }

                    this.$apollo.mutate({
                        mutation: mutation,
                        variables: variables,
                        refetchQueries: [{
                            query: this.programModel ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID,
                            variables: {
                                [this.programModel ? 'programModelId' :  'programId']: this.routeParams.id
                            },
                            skip() {
                                return !this.isProgramForm;
                            }
                        },{
                            query: ALL_EVENT_MODELS
                        }],
                    }).then(res => {
                        izitoast.success({
                            position: 'topRight',
                            title: 'Succès',
                            message: this.isModify ? 'L\'événement a été mis à jour avec succès' : 'L\'événement a été créé avec succès',
                        });
                        this.$router.push(this.previousPage);
                    });
                });
            },
            dateFormatter (date) {
                return moment(date).format('DD/MM/YYYY');
            },
            updateOrganizer (organizer) {
                this.event.organizer = organizer;
            },
            addUser (user) {
                this.event.users.push(user);
            },
            removeUser (user) {
                this.event.users = this.event.users.filter(u => u.id !== user.id);
            },
            removeOrganizer () {
                this.event.organizer = {};
            },
            updateModel () {
                if (!this.event.isModel && this.event.eventModel && this.event.eventModel.id) {
                    let eventModel = this.eventModels.find(eventModel => eventModel.id === this.event.eventModel.id);
                    this.event.name = eventModel.name;
                    this.event.description = eventModel.description;
                    this.event.type = eventModel.type;
                } else if (!this.event.isModel) {
                    this.event.name = '';
                    this.event.description = '';
                    this.event.type = '';
                }
            },
            cancel () {
                this.$router.push(this.previousPage);
            },
            addNewDocument () {
                if (this.isProgramForm) {
                    this.$router.push({name: 'ProgramEventDocumentForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event', document: 'document'}})
                } else {
                    this.$router.push({name: 'EventDocumentForm', params: {id: this.$route.params.id, document: 'document' }})
                }
            },
            closeDocumentDialog () {
                if (this.isProgramForm) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event'}})
                } else {
                    this.$router.push({name: 'EventForm', params: {id: this.$route.params.id}})
                }
            },
            async submitDocumentDialog () {
                await this.closeDocumentDialog();
                await this.$apollo.queries.event.refetch();
            },
        },
        beforeCreate () {
            this.$validator.extend('afterToday', value => {
                return new Promise(resolve => {
                    let today = new Date();
                    resolve(value && moment(value).isSameOrAfter(today));
                });
            });
        },
        created () {
            this.$validator.pause();
            if (this.program && this.program.isModel) {
                this.event.isModel = true;
            }
        },
    }
</script>
