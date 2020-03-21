<template>
    <dash-wrap active="admin" tab="events" :mobileMainAction="{routeName:'EventForm'}">
        <div class="row">
            <div class="col-md-12">
                <filters :filters="filterList"
                         name="events"
                         @filter="updateFilters($event)" />
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12" v-if="!filters.model">
                <list ref="list"
                      name="events"
                      :items="listItems"
                      :total="listItems.length"
                      :header="header"
                      no-items-message="Aucun événement ne correspond à ce filtre"
                      :loading="$apollo.loading"
                      @see="event => viewEvent(event)"
                      @edit="event => editEvent(event)"
                      @delete="event => showDeleteModal(event)"
                      @sort="updateSort($event)"
                      @paginate="updatePage($event)" />
            </div>
            <div class="col-md-12" v-else>
                <list ref="list"
                      name="eventModels"
                      :items="listModelItems"
                      :total="listModelItems.length"
                      :header="headerModel"
                      :loading="$apollo.loading"
                      no-items-message="Aucun modèle d'événement ne correspond à ce filtre"
                      @edit="event => editEventModel(event)"
                      @delete="event => showDeleteModal(event)"
                      @sort="updateModelSort($event)"
                      @paginate="updateModelPage($event)" />
            </div>
        </div>
        <confirm-dialog v-if="deleteModal.show"
                        title="Voulez-vous vraiment supprimer ce eventme?"
                        @close="closeDeleteModal()"
                        @confirm="deleteEvent()" />
    </dash-wrap>
</template>
<script>
    import List from '@/components/utils/List';
    import Filters from '@/components/filters/Filters';
    import ConfirmDialog from "@/components/utils/ConfirmDialog";
    import moment from 'moment';
    import iziToast from 'izitoast';
    import {ALL_EVENTS} from "@/graphql/event/all-events-query";
    import {ALL_EVENT_MODELS} from "@/graphql/event/all-event-models-query";
    import {EVENT_TYPES} from "@/enum/EventTypesEnum";
    import {LOGGED_USER} from "@/graphql/security/logged-user-query";
    import {DELETE_EVENT_MODEL} from "@/graphql/event/delete-event-model-mutation";
    import {DELETE_EVENT} from "@/graphql/event/delete-event-mutation";

    export default {
        name: "eventsList",
        components: {
            List,
            Filters,
            ConfirmDialog,
        },
        data: () => ({
            header: [
                {key: 'name', label: 'Nom', sortable: true},
                {key: 'description', label: 'Description', sortable: true},
                {key: 'type', label: 'Type', sortable: true},
                {key: 'status', label: 'Statut', sortable: true},
                {key: 'program', label: 'Prestation', sortable: true},
                {key: 'dateEvent', label: 'Date Evenement', sortable: true},
                {key: 'updatedAt', label: 'Date modification', sortable: true},
                {key: 'actions', label: 'Actions', actions: ['see', 'edit', 'delete']},
            ],
            headerModel: [
                {key: 'name', label: 'Nom', sortable: true},
                {key: 'description', label: 'Description', sortable: true},
                {key: 'type', label: 'Type', sortable: true},
                {key: 'updatedAt', label: 'Date modification', sortable: true},
                {key: 'actions', label: 'Actions', actions: ['edit', 'delete']},
            ],
            allEvents: {
                items: []
            },
            allEventModels: {
                items: []
            },
            filterList: [
                {key: 'search', type: 'text', label: 'Nom ou Description'},
                {key: 'status', type: 'select', label: 'Statut', attributes: {
                    options: [
                        {value: 'created', label: 'Sauvegardé'},
                        {value: 'inprogress', label: 'En cours'},
                        {value: 'completed', label: 'Terminé'},
                    ],
                }},
            ],
            filters: {},
            sortColumn: 'createdAt',
            sortDirection: 'desc',
            loading: false,
            offset: 0,
            sortModelColumn: 'createdAt',
            sortModelDirection: 'desc',
            offsetModel: 0,
            deleteModal: {
                event: {},
                show: false,
            },
            statusLabels: {
                created: 'Sauvegardé',
                inprogress: 'En cours',
                completed: 'Terminé',
            }
        }),
        created() {
            if (this.isAdmin) {
                this.filterList.push({ key: "model", type: "checkbox", label: "Modèle" });
            }
        },
        apollo: {
            me: {
                query: LOGGED_USER
            },
            allEvents: {
                query: ALL_EVENTS,
                variables() {
                    return {
                        ...this.filters,
                        limit: 10,
                        offset: this.offset,
                        sortColumn: this.sortColumn,
                        sortDirection: this.sortDirection,
                    }
                }
            },
            allEventModels: {
                query: ALL_EVENT_MODELS,
                variables() {
                    return {
                        ...this.filters,
                        limit: 10,
                        offset: this.offsetModel,
                        sortColumn: this.sortModelColumn,
                        sortDirection: this.sortModelDirection,
                    }
                }
            }
        },
        computed: {
            listItems () {
                return this.allEvents.items
                    .map(item => ({
                        id: item.id,
                        name: item.name,
                        description: item.description,
                        type: this.eventTypes.find(e => e.value ===item.type).label,
                        status: this.statusLabels[item.status],
                        program: item.program ? item.program.name : '',
                        programId: item.program ? item.program.id : null,
                        dateEvent:  moment(item.dateEvent).format('DD/MM/YYYY hh:mm'),
                        updatedAt: moment(item.updatedAt ? item.updatedAt : item.createdAt).format('DD/MM/YYYY hh:mm'),
                    }));
            },
            listModelItems () {
                return this.allEventModels.items
                    .map(item => ({
                        id: item.id,
                        name: item.name,
                        description: item.description,
                        type: this.eventTypes.find(e => e.value ===item.type).label,
                        updatedAt: moment(item.updatedAt ? item.updatedAt : item.createdAt).format('DD/MM/YYYY hh:mm'),
                        eventCount: item.events.count
                    }));
            },
            isAdmin() {
                return this.me && this.me.type === "admin";
            },
            eventTypes() {
                return EVENT_TYPES;
            },
        },
        methods: {
            updateSort ($event) {
                this.sortColumn = $event.column;
                this.sortDirection = $event.direction;
                this.resetOffset();
            },
            updatePage (page) {
                this.offset = (page - 1) * 10;
            },
            updateModelSort ($event) {
                this.sortModelColumn = $event.column;
                this.sortModelDirection = $event.direction;
                this.resetOffset();
            },
            updateModelPage (page) {
                this.offsetModel = (page - 1) * 10;
            },
            updateFilters (filters) {
                this.filters = {...filters};
                this.resetOffset();
            },
            resetOffset () {
                this.offset = 0;
                this.offsetModel = 0;
            },
            closeDeleteModal () {
                this.deleteModal.show = false;
            },
            showDeleteModal (event) {
                if (!event.status && event.eventCount) {
                    iziToast.error({
                        position: 'topRight',
                        title: 'Erreur',
                        message: "Nous ne pouvons pas supprimer car il a des events."
                    });
                    return;
                }
                this.deleteModal.event = event;
                this.deleteModal.show = true;
            },
            deleteEvent () {
                this.closeDeleteModal();
                let isModel = !this.deleteModal.event.status;
                
                this.$apollo.mutate({
                    mutation: isModel ? DELETE_EVENT_MODEL : DELETE_EVENT,
                    variables: isModel ? { eventModelId: this.deleteModal.event.id } : { eventId: this.deleteModal.event.id },
                }).then(() => {
                    this.$apollo.queries[isModel ? 'allEventModels' : 'allEvents'].refetch();
                    iziToast.success({
                        position: 'topRight',
                        title: 'Succès',
                        message: "L'événement a bien été supprimé",
                    });
                })
            },
            viewEvent (event) {
                if (event.programId) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: event.programId, event: 'event', eid: event.id, mode: 'view'}});
                } else {
                    this.$router.push({name: 'EventForm', params: {id: event.id, mode: 'view'}});
                }
            },
            editEvent (event) {
                if (event.programId) {
                    this.$router.push({name: 'ProgramEventForm', params: {id: event.programId, event: 'event', eid: event.id}});
                } else {
                    this.$router.push({name: 'EventForm', params: {id: event.id}});
                }
            },
            editEventModel (event) {
                this.$router.push({name: 'EventForm', params: {id: event.id, mode: 'model'}});
            }
        }
    };
</script>
