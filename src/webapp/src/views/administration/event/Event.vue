<template>
    <div class="container text-left">
        <div class="content my-5">
            <page-title
                pre-title="Événement"
                :title="isModify ? 'Modifier un événement' : 'Créer un événement'"
                @back="$router.push({name: 'ProgramsList'})" />
            <div class="row" v-if="loading">
                <div class="col-md-11">
                    Loading...
                </div>
            </div>
            <form v-if="!loading">
                <fieldset>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-row">
                                <div class="col-md-5 form-group" v-if="isAdmin">
                                    <label>Est-ce que cet événement est un modèle ?</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" :value="true" v-model="event.isModel" id="model_yes" :disabled="isModify || !canEdit" />
                                            <label class="form-check-label" for="model_yes">Oui</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" :value="false" v-model="event.isModel" id="model_no" :disabled="isModify || !canEdit" />
                                            <label class="form-check-label" for="model_no">Non</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5 form-group" :class="{'offset-md-1': isAdmin}" v-if="!event.isModel">
                                    <label for="model">Quel modéle souhaitez-vous utiliser ?</label>
                                    <select id="model" class="form-control" v-model="event.eventModelId" @change="updateModel" :disabled="!canEdit">
                                        <option value="">Sélectionnez un modèle d'événement</option>
                                        <option v-for="model in eventModels" :key="model.id" :value="model.id">
                                            {{ model.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-5 form-group" :class="{'offset-md-1': !isAdmin}">
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
                                        <div class="col-md-5">
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
                                                        v-model="event.organizerId"
                                                        @item-selected="updateOrganizer"
                                                        :query-coaches="true"
                                                        :empty-query-after-selection="true"
                                                        name="coach" />
                                    <div>
                                        <single-user :user="event.organizer && event.organizer.id ? event.organizer : currentUser"
                                                        :has-action="event.organizer && event.organizer.id ? true : false"
                                                        @click="removeOrganizer" />
                                    </div>
                                </div>
                                <div class="col-md-5 offset-md-1 form-group" v-if="isModify">
                                    <label for="createdAt">Date de création</label>
                                    <input class="form-control" name="createdAt" id="createdAt" :value="dateFormatter(event.createdAt)" disabled />
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
                                    <user-autocomplete v-model="event.inviteeInput"
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
                    <div class="col-md-6 offset-4">
                        <button type="button" class="btn btn-outline-danger" @click="cancel">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-success ml-2" @click="handleSubmit">
                            {{ isModify ? 'Modifier' : 'Créer' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<style>
    .last-modified-at span {
        margin-right: 0;
    }
    .el-date-editor input {
        border-radius: 20px;
    }
</style>
<script>
    import 'vue-phone-number-input/dist/vue-phone-number-input.css';
    import izitoast from 'izitoast';
    import moment from 'moment';
    import UserAutocomplete from '@/components/autocomplete/UserAutocomplete';
    import SingleUser from '@/components/utils/SingleUser';
    import DocumentDialog from '@/views/document/documentFormDialog';
    import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
    import {EVENT_MODEL_BY_ID} from "@/graphql/event/event-model-by-id-query";
    import {EVENT_BY_ID} from "@/graphql/event/event-by-id-query";


    export default {
        name: "Event",
        components : {
            UserAutocomplete,
            SingleUser,
            DocumentDialog
        },
        data() {
            return {
                event: {
                    isModel: false,
                    modelId: '',
                    name: '',
                    dateEvent: '',
                    description: '',
                    organizerId: '',
                    organizer: {},
                    createdAt: '',
                    users: [],
                    inviteeInput: '',
                    type: '',
                },
                loading: 0,
                submitted: false,
            };
        },
        mounted: function() {
            this.$store.dispatch('event/getAllEventModels');
            this.event.users = this.program.users ? this.program.users : []
        },
        apollo: {
            event: {
                query()  {
                    return this.routeMode === 'model' ? EVENT_MODEL_BY_ID : EVENT_BY_ID;
                },
                variables() {
                    let id;
                    let variableName = this.routeMode === 'model' ? 'eventModelId' : 'eventId';
                    if(this.routeName === 'ProgramEventForm' || this.routeName === 'ProgramEventDocumentForm') {
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
                    if (this.programEvent) {
                        return !this.routeParams.eid;
                    } else {
                        return !this.routeParams.id;
                    }
                }
            },
        },
        computed: {
            program () {
                return this.$store.getters['program/PROGRAM'];
            },
            eventModels () {
                return this.$store.getters['event/EVENT_MODELS'];
            },
            currentUser () {
                return this.$store.getters['auth/LOGGED_USER'] ? this.$store.getters['auth/LOGGED_USER'] : {};
            },
            isAdmin () {
                return this.currentUser && this.currentUser.type === 'admin';
            },
            lastModifiedAt () {
                if (this.isModify) {
                    return '';
                }

                let date = this.event.updatedAt !== null ? this.event.updatedAt : this.event.createdAt;

                return moment(date).format('DD/MM/YYYY');
            },
            canEdit () {
                return this.$route.params.mode !== 'view';
            },
            isModify () {
                if (this.$route.params.event) {
                    return this.$route.params.eid ? true : false;
                } else {
                    return this.$route.params.id ? true : false;
                }
            },
            eventTypes() {
                return this.$store.getters['event/EVENT_TYPES'];
            }
        },
        watch: {
            event: {
                deep: true,
                handler () {
                    if (this.submitted) {
                        this.$validator.validate();
                    }
                    
                },
            }
        },
        methods: {
            handleSubmit (e) {
                const isNew = !this.isModify;
                this.submitted = true;
                this.$validator.resume();
                this.$validator.validate().then(valid => {
                    if (!valid || (this.event.users.length === 0 && !this.event.isModel)) {
                        izitoast.error({
                            timeout: IZITOAST_CONSTANTS.TIME_OUT,
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
                        mutation = this.isModify ? 'UPDATE_EVENT_MODEL' : 'CREATE_EVENT_MODEL';
                    } else {
                        mutation = this.isModify ? 'UPDATE_EVENT' : 'CREATE_EVENT';
                        variables.userIds = this.event.users.map(user => user.id);
                        variables.organizerId = this.event.organizer && this.event.organizer.id ? this.event.organizer.id : this.currentUser.id;
                    }
                    variables.dateEvent = moment(this.event.dateEvent).format('YYYY-MM-DD HH:mm:ss');
                    variables.programId = this.$route.params.event ? this.$route.params.id : this.event.program.id;
                    
                    this.$store.dispatch('event/postEvent', {
                        variables: variables,
                        mutation: mutation
                    }).then(res => {
                        this.loading = false;
                        izitoast.success({
                            position: 'topRight',
                            title: 'Succès',
                            message: this.isModify ? 'Le Événement a été mis à jour avec succès' : 'Le Événement a été créé avec succès',
                        });
                        if (this.$route.params.event) {
                            if (mutation==='CREATE_EVENT') {
                                this.$store.commit('program/setAddEventToProgram', res.createEvent)
                            }
                            this.$router.push({name: 'ProgramForm', params: {id: this.$route.params.id}});
                        } else {
                            this.$router.push({name: 'EventsList'})
                        }
                    })
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
                if (!this.event.isModel && this.event.modelId) {
                    let eventModel = this.eventModels.find(eventModel => eventModel.id === this.event.modelId);
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
                if (this.$route.params.event) {
                    this.$router.push({name: 'ProgramForm', params: {id: this.$route.params.id}});
                } else {
                    this.$router.push({name: 'EventsList'});
                }
            },
            addNewDocument () {
                if (this.$route.params.event) {
                    this.$router.push({name: 'ProgramEventDocumentForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event', document: 'document'}})
                } else {
                    this.$router.push({name: 'EventDocumentForm', params: {id: this.$route.params.id, document: 'document' }})
                }
            },
            closeDocumentDialog () {
                if (this.$route.params.event) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: this.$route.params.id, eid: this.$route.params.eid, event: 'event'}})
                } else {
                    this.$router.push({name: 'EventForm', params: {id: this.$route.params.id}})
                }
            },
            async submitDocumentDialog () {
                await this.closeDocumentDialog();
                this.loadData();                
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
        },
    }
</script>
