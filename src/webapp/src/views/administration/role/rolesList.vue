<template>
    <dash-wrap active="admin" tab="roles" :mobileMainAction="{routeName:'RolesForm'}">
        <div class="d-flex">
            <filters :filters="filterList"
                     name="roles"
                     @filter="updateFilters($event)" />
            <div class="ml-auto d-none d-md-block">
                <button class="btn btn-gradient-primary" @click="() => $router.push({name: 'RolesForm'})">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Créer un Rôle
                </button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <list ref="list"
                      name="roles"
                      :items="listItems"
                      :total="allRoles.count"
                      :header="header"
                      :loading="loading"
                      no-items-message="Aucun rôle ne correspond à ce filtre"
                      default-sort="name"
                      default-sort-direction="asc"
                      @edit="role => $router.push({name: 'RolesForm', params: {id: role.id}})"
                      @delete="deleteRole($event)"
                      @sort="updateSort($event)"
                      @paginate="updatePage($event)" />
            </div>
        </div>
        <confirm-dialog v-if="deleteModal.show"
                        title="Voulez-vous vraiment supprimer ce rôle?"
                        @close="closeDeleteModal()"
                        @confirm="confirmDeleteRole()" />
    </dash-wrap>
</template>
<script>
    import {ALL_ROLES} from '@/graphql/role/all-roles-query';
    import List from '@/components/utils/List';
    import Filters from '@/components/filters/Filters';
    import izitoast from 'izitoast';
    import {DELETE_ROLE} from '@/graphql/role/delete-role-mutation';
    import deleteQueriesFromApolloCache from '@/utils/deleteQueriesFromApolloCache';
    import ConfirmDialog from '@/components/utils/ConfirmDialog';
    import {IZITOAST_CONSTANTS} from "@/enum/IzitoastConstants";

    export default {
        name: "rolesList",
        components: {
            List,
            Filters,
            ConfirmDialog,
        },
        data: () => ({
            header: [
                {key: 'name', label: 'Nom', sortable: true},
                {key: 'description', label: 'Description', sortable: false},
                {key: 'usersCount', label: 'Nombre d\'utilisateurs', sortable: false},
                {key: 'actions', label: 'Actions', actions: ['edit', 'delete']},
            ],
            filterList: [
                {key: 'search', type: 'text', label: 'Nom ou Description'},
            ],
            filters: {},
            sortColumn: 'name',
            sortDirection: 'asc',
            allRoles: {
                items: [],
                count: 0,
            },
            loading: false,
            offset: 0,
            limit: 10,
            deleteModal: {
                role: {},
                show: false,
            },
        }),
        apollo: {
            allRoles: {
                query: ALL_ROLES,
                variables () {
                    return this.allRolesQueryVariables;
                },
                watchLoading (isLoading) {
                    this.loading = isLoading;
                },
                result (result) {
                    this.loading = typeof result.data === 'undefined' && result.networkStatus === 1;
                },
            },
        },
        computed: {
            listItems () {
                return this.allRoles.items
                    .map(item => ({
                        id: item.id,
                        name: item.name,
                        description: item.description,
                        usersCount: item.usersCount,
                    }));
            },
            allRolesQueryVariables() {
                return {
                    ...this.filters,
                    offset: this.offset,
                    limit: this.limit,
                    sortColumn: this.sortColumn,
                    sortDirection: this.sortDirection,
                };
            }
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
            updateFilters (filters) {
                this.filters = {...filters};
                this.resetOffset();
            },
            resetOffset () {
                this.offset = 0;
                this.$refs.list.resetPage();
            },
            deleteRole (role) {
                if (role.usersCount) {
                    izitoast.error({
                        timeout: IZITOAST_CONSTANTS.TIME_OUT,
                        position: 'topRight',
                        title: 'Erreur',
                        message: 'Vous ne pouvez pas supprimer des rôles avec des utilisateurs',
                    });
                    return;
                }
                this.deleteModal.role = role;
                this.deleteModal.show = true;
            },
            closeDeleteModal () {
                this.deleteModal.show = false;
            },
            confirmDeleteRole () {
                this.closeDeleteModal();
                this.loading = true;
                this.$apollo.mutate({
                    mutation: DELETE_ROLE,
                    variables: {
                        roleId: this.deleteModal.role.id,
                    },
                    update (proxy) {
                        deleteQueriesFromApolloCache(proxy, 'allRoles');
                    }
                }).then(() => {
                    izitoast.success({
                        position: 'topRight',
                        title: 'Succès',
                        message: 'Le rôle a bien été supprimé',
                    });
                    this.$apollo.queries.allRoles.refresh();
                    this.loading = false;
                });
            }
        }
    };
</script>
