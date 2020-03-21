<template>
  <dash-wrap active="admin" :hide-tabs="true">
    <page-title
      pre-title="Prestation"
      :title="$route.params.id ? 'Modifier une prestation' : 'Créer une prestation'"
      @back="$router.push({name: 'ProgramsList'})" />
    <div class="col-12 col-md-10 offset-md-1">
      <div class="content">
        <div class="row" v-if="$apollo.loading">
          <div class="col-md-11">Loading...</div>
        </div>
        <template v-else>
          <form>
            <div class="form-row" v-if="$route.params.id">
              <div class="col-md-12 text-right last-modified-at form-group mb-0">
                <label>Derniére modificaton</label> :
                <strong>
                  <router-link class="text-dark" :to="{name: 'UsersForm', params: {id: program.updatedBy !== null ? program.updatedBy.id : program.createdBy.id}}" target="_blank">
                    {{program.updatedBy ? program.updatedBy.firstName : program.createdBy.firstName}}
                  </router-link>
                  le {{ lastModifiedAt }}
                </strong>
              </div>
            </div>
            <fieldset>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row">
                    <div class="col-md-5 form-group radio" v-if="isAdmin">
                      <label>Est-ce que cette prestation est un modéle ?</label>
                      <div class="radio-items">
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="true"
                            v-model="isModel"
                            id="model_yes"
                            :disabled="$route.params.id || !canEdit"
                          />
                          <label class="form-check-label" for="model_yes">Oui</label>
                        </div>
                        <div class="radio-item">
                          <input
                            class="form-check-input"
                            type="radio"
                            :value="false"
                            v-model="isModel"
                            id="model_no"
                            :disabled="$route.params.id || !canEdit"
                          />
                          <label class="form-check-label" for="model_no">Non</label>
                        </div>
                      </div>
                    </div>
                    <div
                      class="col-md-5 form-group select"
                      :class="{'offset-md-1': isAdmin}"
                      v-if="!isModel"
                    >
                      <label for="model">Quel modéle souhaitez-vous utiliser ?</label>
                      <select
                        id="model"
                        class="form-control"
                        v-model="modelIdInput"
                        @change="updateModel"
                        :disabled="!canEdit"
                      >
                        <option :value="null">Sélectionnez le modèle approprié à votre prestation</option>
                        <option
                          v-for="model in programModels"
                          :key="model.id"
                          :value="model.id"
                        >{{ model.name }}</option>
                      </select>
                    </div>
                    <div
                      class="col-md-5 form-group select"
                      :class="{'offset-md-1': !isAdmin}"
                      v-if="!isModel"
                    >
                      <label for="type">Type de prestation*</label>
                      <select
                        id="type"
                        name="type"
                        class="form-control"
                        :disabled="!canEdit"
                        v-model="program.type"
                        v-validate="'required'"
                        :class="{'is-invalid': errors.has('type')}"
                      >
                        <option value>Sélectionnez un type de prestation</option>
                        <option value="individual">Coaching Individuel</option>
                        <option value="group">Coaching Collectif</option>
                      </select>
                      <div
                        v-if="errors.has('type')"
                        class="invalid-feedback"
                      >Le type est obligatoire.</div>
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
                      <label for="name">Nom*</label>
                      <input
                        class="form-control"
                        placeholder="Nom"
                        id="name"
                        name="name"
                        v-validate="'required'"
                        v-model="program.name"
                        :disabled="!canEdit"
                        :class="{'is-invalid': errors.has('name')}"
                      />
                      <div
                        v-if="errors.has('name')"
                        class="invalid-feedback"
                      >Le nom est obligatoire.</div>
                    </div>
                    <div class="col-md-5 offset-md-1 form-group" v-if="!isModel">
                      <label>Période*</label>
                      <div class="form-row">
                        <div class="col-md-1 mt-auto mb-auto text-center">Du</div>
                        <div class="col-md-5">
                          <el-date-picker
                            v-model="program.dateStart"
                            name="dateStart"
                            v-validate="'beforeFirstEvent'"
                            :disabled="!$route.params.id || !canEdit"
                            format="dd/MM/yyyy"
                            :calendar-button="true"
                            :required="true"
                            :bootstrap-styling="true" />
                        </div>
                        <div class="col-md-1 mt-auto mb-auto text-center">au</div>
                        <div class="col-md-5">
                          <el-date-picker
                            v-model="program.dateEnd"
                            name="dateEnd"
                            :disabled="!$route.params.id || !canEdit"
                            v-validate="'afterDateStart|afterLastEvent'"
                            format="dd/MM/yyyy"
                            :calendar-button="true"
                            :required="true"
                            :bootstrap-styling="true" />
                        </div>
                      </div>
                      <small
                        v-if="(errors.has('dateStart') || errors.has('dateEnd'))"
                        class="text-danger"
                      >La période s’étend de la première date d’événement à la dernière</small>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="col-md-11 form-group">
                      <label for="description">Description</label>
                      <textarea
                        class="form-control"
                        name="description"
                        id="description"
                        rows="5"
                        v-model="program.description"
                        :disabled="!canEdit"
                      />
                    </div>
                  </div>
                  <div class="form-row" v-if="!isModel">
                    <div class="col-md-5 form-group">
                      <label>Créateur</label>
                      <user-autocomplete
                        v-if="isAdmin && canEdit"
                        v-model="coachInput"
                        @item-selected="updateCoach"
                        :query-coaches="true"
                        :empty-query-after-selection="true"
                        name="coach"
                      />
                      <div>
                        <single-user
                          :user="program.coach && program.coach.id ? program.coach : me"
                          :has-action="isAdmin && program.coach && program.coach.id ? true : false"
                          @click="removeCoach"
                        />
                      </div>
                    </div>
                    <div class="col-md-5 offset-md-1 form-group" v-if="$route.params.id">
                      <label for="createdAt">Date de création</label>
                      <input
                        class="form-control created-date"
                        name="createdAt"
                        id="createdAt"
                        :value="dateFormatter(program.createdAt)"
                        disabled
                      />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
            <fieldset v-if="!isModel">
              <legend>Invités</legend>
              <div class="panel panel-default">
                <div class="panel-body">
                  <div class="form-row" v-if="canEdit">
                    <div class="col-md-5">
                      <user-autocomplete
                        v-model="inviteeInput"
                        @item-selected="addUser"
                        :class-list="{'is-invalid': program.users.length === 0 && submitted}"
                        :empty-query-after-selection="true"
                      />
                    </div>
                  </div>
                  <div class="form-row" v-if="program.users">
                    <div class="col-md-11">
                      <single-user
                        v-for="user in program.users"
                        :key="user.id"
                        :user="user"
                        :has-action="true"
                        @click="$event => removeUser(user)"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </fieldset>
          </form>
        </template>
      </div>
    </div>
    <div class="row" v-if="!$apollo.loading">
      <div class="col-md-12 mt-4" v-if="$route.params.id || eventsList.length">
          <list ref="list"
                  name="events"
                  :action-row="canEdit && !!$route.params.id"
                  action-icon="fa-plus"
                  :pagination="false"
                  no-items-message="Aucun événement n'a encore été créé"
                  :items="eventsList"
                  :total="eventsList.length"
                  :header="isModel || (modelIdInput && !routeParams.id) ? eventModelsListHeader : eventListHeader"
                  @action="addNewEvent"
                  @see="event => $router.push({name: this.eventFormRoute, params: {id: program.id ? program.id : modelIdInput, event: 'event', eid: event.id, mode: 'view'}})"
                  @edit="event => $router.push({name: this.eventFormRoute, params: {id: program.id ? program.id : modelIdInput, event: 'event', eid: event.id, mode: isModel || (modelIdInput && !routeParams.id) ? 'model' : null}})"
                  @delete="event => showDeleteModal(event)"
          />
      </div>
      <confirm-dialog v-if="deleteModal.show"
                  title="Voulez-vous vraiment supprimer ce eventme?"
                  @close="closeDeleteModal()"
                  @confirm="deleteEvent()" />
      <template v-if="canEdit">
        <div :class="!$route.params.id  || isModel ? 'container pr-4': 'col-md-12'">
          <div class="form-btn-wrap mt-1">
            <button
              type="button"
              class="btn btn-outline-consultant-light"
              @click="$router.push({name: 'ProgramsList'})"
            >Annuler</button>
            <button
              type="button"
              class="btn btn-gradient-primary"
              @click="handleSubmit"
            >Enregistrer</button>
          </div>
        </div>
      </template>
    </div>
  </dash-wrap>
</template>
<style>
.last-modified-at span {
  margin-right: 0;
}
.el-date-editor input {
    border-radius: 20px;
}
.el-date-editor input:focus {
    border-color: #7f267b;
    box-shadow: none;
}
.el-date-editor.el-input, .el-date-editor.el-input__inner  {
    width: 100%;
    border-radius: 20px;
}
.el-date-editor.is-disabled .el-input__inner {
  background-color: white;
}
.created-date:disabled{
  background-color: white;
}
</style>
<script>
import "vue-phone-number-input/dist/vue-phone-number-input.css";
import izitoast from "izitoast";
import moment from "moment";
import UserAutocomplete from "@/components/autocomplete/UserAutocomplete";
import SingleUser from "@/components/utils/SingleUser";
import deleteQueriesFromApolloCache from "@/utils/deleteQueriesFromApolloCache";
import List from "@/components/utils/List";
import ConfirmDialog from "@/components/utils/ConfirmDialog";

import { ALL_PROGRAM_MODELS } from "@/graphql/program/all-program-models-query";
import { PROGRAM_BY_ID } from "@/graphql/program/program-by-id-query";
import { CREATE_PROGRAM_MODEL } from "@/graphql/program/create-program-model-mutation";
import { UPDATE_PROGRAM_MODEL } from "@/graphql/program/update-program-model-mutation";
import { CREATE_PROGRAM } from "@/graphql/program/create-program-mutation";
import { PROGRAM_MODEL_BY_ID } from "@/graphql/program/program-model-by-id-query";
import { UPDATE_PROGRAM } from "@/graphql/program/update-program-mutation";
import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";
import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
import { EVENT_TYPES } from "@/enum/EventTypesEnum";
import {DELETE_EVENT_MODEL} from "@/graphql/event/delete-event-model-mutation";
import {DELETE_EVENT} from "@/graphql/event/delete-event-mutation";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";
import {USER_BY_ID} from "@/graphql/user/user-by-id-query";


export default {
  name: "programForm",
  components: {
    UserAutocomplete,
    SingleUser,
    ConfirmDialog,
    List
  },
  data() {
    return {
      me: '',
      modelIdInput: null,
      submitted: false,
      inviteeInput: '',
      coachInput: '',
      program: {
        updatedBy: '',
        createdBy: '',
        users: [],
        eventModels: {
            items: []
        },
        events: {
          items: []
        },
        programModel: {
            id: ''
        },
      },
      eventListHeader: [
        {key: 'name', label: 'Nom', sortable: false},
        {key: 'description', label: 'Description', sortable: false},
        {key: 'type', label: 'Type', sortable: false},
        {key: 'status', label: 'Statut', sortable: false},
        {key: 'dateEvent', label: ' Date Événement', sortable: false},
        {key: 'updatedAt', label: 'Date Modification', sortable: false},
        {key: 'actions', label: 'Actions', actions: ['see', 'edit', 'delete']},
      ],
      eventModelsListHeader: [
        {key: 'name', label: 'Nom', sortable: false},
        {key: 'description', label: 'Description', sortable: false},
        {key: 'type', label: 'Type', sortable: false},
        {key: 'updatedAt', label: 'Date Modification', sortable: false},
        ...(this.$route.params.id ? [{key: 'actions', label: 'Actions', actions: ['edit', 'delete']}] : []),
      ],
      deleteModal: {
        event: {},
        show: false,
      },
      routeName: this.$route.name,
      routeParams: this.$route.params,
      isModel: this.$route.name === 'ProgramModelForm'
    };
  },
  apollo: {
    me: {
        query: LOGGED_USER
    },
    programModels: {
      query: ALL_PROGRAM_MODELS,
      loadingKey: "loading",
      update: data => data.allProgramModels.items
    },
    program: {
        query() {
            return this.routeName === 'ProgramModelForm' ? PROGRAM_MODEL_BY_ID : PROGRAM_BY_ID;
        },
        variables() {
            return this.routeName === 'ProgramModelForm' ? {programModelId: this.routeParams.id} : {programId: this.routeParams.id};
        },
        update(data) {
            let r = data.programModelById || data.programById;
            this.modelIdInput = r.programModel && r.programModel.id;
            return r;
        },
        skip() {
            return !this.routeParams.id
        }
    },
    eventModels: {
        query: ALL_EVENT_MODELS,
        update: data => data.allEventModels
    }
  },
  computed: {
    eventsList() {
        let events = [];
        if (this.isModel || (this.modelIdInput && !this.routeParams.id)) {
            events = this.program.eventModels.items
        } else {
            events = this.program.events.items
        }

        return events.map(event => ({
            ...event,
            dateEvent: event.dateEvent ? moment(event.dateEvent).format('D/M/YYYY') : '',
            type: EVENT_TYPES.find(e => e.value === event.type).label,
            updatedAt: moment(event.updatedAt !== null ? event.updatedAt : event.createdAt).format('D/M/YYYY'),
        }))
    },
    isCandidate() {
      return this.user.typeId === "candidate";
    },
    eventFormRoute() {
        if (this.routeParams.id) {
            return this.isModel ? "ProgramModelEventForm" : "ProgramEventForm";
        } else {
            return "ProgramModelEventForm";
        }
    },
    isAdmin() {
      return this.me && this.me.type === "admin";
    },
    lastModifiedAt() {
      if (!this.$route.params.id) {
        return "";
      }

      let date =
        this.program.updatedAt !== null
          ? this.program.updatedAt
          : this.program.createdAt;

      return moment(date).format("D/M/YYYY");
    },
    canEdit() {
      return this.$route.params.mode !== "view";
    },
  },
  methods: {
    handleSubmit(e) {
      const isNew = !this.$route.params.id;
      this.$validator.resume();
      this.submitted = true;
      this.$validator.validate().then(valid => {
        if (
          !valid ||
          (!this.isModel && this.program.users.length === 0)
        ) {
          izitoast.error({
            timeout: IZITOAST_CONSTANTS.TIME_OUT,
            position: "topRight",
            title: "Erreur",
            message: "Veuillez vérifier le formulaire pour les erreurs"
          });
          return;
        }

        let mutation,
          variables = {
            ...this.program,
            isModel: this.isModel
          };
        if (this.isModel) {
          mutation = this.$route.params.id
            ? UPDATE_PROGRAM_MODEL
            : CREATE_PROGRAM_MODEL;
        } else {
          mutation = this.$route.params.id ? UPDATE_PROGRAM : CREATE_PROGRAM;
          variables.userIds = this.program.users.map(user => user.id);
          variables.modelId = this.modelIdInput;
          variables.coachId =
            this.program.coach && this.program.coach.id
              ? this.program.coach.id
              : null;
          variables.dateStart = this.program.dateStart
            ? moment(this.program.dateStart, "D/M/YYYY").toISOString()
            : null;
          variables.dateEnd = this.program.dateEnd
            ? moment(this.program.dateEnd, "D/M/YYYY").toISOString()
            : null;
        }

        this.$apollo
          .mutate({
            mutation: mutation,
            variables: variables,
            refetchQueries() {
                let refetch = [];
                if(!variables.isModel) {
                    for (let key in variables.userIds) {
                        refetch.push({
                            query: USER_BY_ID,
                            variables: {id: variables.userIds[key]}
                        })
                    }
                }
                return refetch;
            },
            update(proxy) {
              if (isNew) {
                deleteQueriesFromApolloCache(proxy, "allPrograms");
                deleteQueriesFromApolloCache(proxy, "allProgramModels");
              }
            }
          })
          .then(response => {
            izitoast.success({
              position: "topRight",
              title: "Succès",
              message: this.$route.params.id
                ? "Le prestation a été mis à jour avec succès"
                : "Le prestation a été créé avec succès"
            });
            if (!this.isModel || this.$route.params.id) {
              this.$router.push({name: "ProgramsList"});
            } else {
              this.$router.push({name: this.isModel ? "ProgramModelForm" : "ProgramForm", params: {id: response.data.createProgramModel.id}});
            }
          });
      });
    },
    dateFormatter(date) {
      return moment(date).format("D/M/YYYY");
    },
    updateCoach(coach) {
      this.program.coach = coach;
    },
    addUser(user) {
      this.program.users.push(user);
    },
    removeUser(user) {
      this.program.users = this.program.users.filter(u => u.id !== user.id);
    },
    removeCoach() {
      this.program.coach = {};
    },
    updateModel() {
      if (!this.isModel && this.modelIdInput) {
          let programModel = this.programModels.find(
          programModel => programModel.id === this.modelIdInput
        );
        this.program.name = programModel.name;
        this.program.description = programModel.description;
        this.program.eventModels = programModel.eventModels;
      } else if (!this.isModel) {
        this.program.name = "";
        this.program.description = "";
      }
    },
    closeDeleteModal () {
        this.deleteModal.show = false;
    },
    showDeleteModal (event) {
        this.deleteModal.event = event;
        this.deleteModal.show = true;
    },
    deleteEvent () {
        this.closeDeleteModal();
        this.$apollo.mutate({
            mutation: this.isModel ? DELETE_EVENT_MODEL : DELETE_EVENT,
            variables: this.isModel ? { eventModelId: this.deleteModal.event.id } : { eventId: this.deleteModal.event.id },
        }).then(() => {
            this.$apollo.queries.program.refetch();
            iziToast.success({
                position: 'topRight',
                title: 'Succès',
                message: "L'événement a bien été supprimé",
            });
        });
        // TODO : Implement correct error handling
        /* .catch((error) => {
         izitoast.error({
             position: 'topRight',
             title: 'Erreur',
             message: 'L\'événement ne peut être supprimé car il est utilisé par des événements',
         });
        });*/
    },
    addNewEvent () {
        this.$router.push({name: this.eventFormRoute, params: {id: this.$route.params.id}})
    },
  },
  beforeCreate() {
    this.$validator.extend("afterDateStart", value => {
      return new Promise(resolve => {
        resolve(
          !value ||
            !this.program.dateStart ||
            moment(value).isAfter(this.program.dateStart)
        );
      });
    });
    this.$validator.extend("beforeFirstEvent", value => {
      return new Promise(resolve => {
        let minimumDate = null;
        this.program.events.items.forEach(event => {
          if (
            minimumDate === null ||
            moment(event.dateEvent).isBefore(minimumDate)
          ) {
            minimumDate = event.dateEvent;
          }
        });
        resolve(
          minimumDate === null || moment(value).isSameOrAfter(minimumDate)
        );
      });
    });
    this.$validator.extend("afterLastEvent", value => {
      return new Promise(resolve => {
        let maximumDate = null;
        this.program.events.items.forEach(event => {
          if (
            maximumDate === null ||
            moment(event.dateEvent).isAfter(maximumDate)
          ) {
            maximumDate = event.dateEvent;
          }
        });
        resolve(
          maximumDate === null || moment(value).isSameOrBefore(maximumDate)
        );
      });
    });
  },
  created() {
    this.$validator.pause();
  }
};
</script>
