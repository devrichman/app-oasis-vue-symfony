<template>
  <dash-wrap mode="admin" active="admin" tab="users" :mobileMainAction="{routeName:'UsersForm'}">
    <div class="d-flex justify-content-end"></div>
    <div class="d-flex">
      <filters name="users" class="flex-grow-1" :filters="filterList" @filter="updateFilters($event)" />
      <div class="ml-auto d-none d-md-block" v-if="rights.includes('ROLE_CREATE_USER')">
        <button
          class="ml-auto btn btn-gradient-primary"
          @click="() => $router.push({name: 'UsersForm'})"
        >
          <i class="fa fa-plus" aria-hidden="true"></i>
          Créer un utilisateur
        </button>
        <button
          class="ml-3 btn btn-outline-secondary"
          @click="() => $router.push({name: 'ImportUsers'})"
        >
          <i class="fa fa-file" aria-hidden="true"></i>
          Importer
        </button>
      </div>
    </div>
    <div class="row mt-2">
      <div class="col-md-12">


        <!-- special list -->
        <list-users
          name="users"
          ref="list"
          :items="listItems"
          :total="allUsers.count"
          :header="header"
          no-items-message="Aucun utilisateur ne correspond à ce filtre"
          :loading="loading"
          :show-avatar="true"
          @see="user => $router.push({name: 'ProfileUser', params: {id: user.id}})"
          @edit="user => $router.push({name: 'UsersForm', params: {id: user.id}})"
          @disable="user => showDisableUserModal(user)"
          @enable="user => enableUser(user)"
          @delete="user => showDeleteUserModal(user)"
          @sort="updateSort($event)"
          @paginate="updatePage($event)"
        />
      </div>
    </div>
    <confirm-dialog
      v-if="disableModal.show"
      title="Êtes-vous sûr de vouloir désactiver cet utilisateur ?"
      @close="closeDisableModal()"
      @confirm="disableUser()"
    />

    <confirm-dialog
            v-if="deleteModal.show"
            title="Êtes-vous sûr de vouloir archiver cet utilisateur ?"
            @close="closeDeleteModal()"
            @confirm="deleteUser()"
    />
  </dash-wrap>
</template>
<script>
import { ALL_USERS } from "@/graphql/user/all-users-query";
import { DISABLE_USER } from "@/graphql/user/disable-user-mutation";
import { ENABLE_USER } from "@/graphql/user/enable-user-mutation";
import { DELETE_USER } from "@/graphql/user/delete-user-mutation";

import ListUsers from "@/components/lists/Users";
import Filters from "@/components/filters/Filters";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import {ALL_ROLES} from "@/graphql/role/all-roles-query";
import izitoast from "izitoast";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

export default {
  name: "usersList",
  components: {
    ListUsers,
    Filters,
    ConfirmDialog
  },
  data() {
    return {
      header: [
        { key: "name", label: "Nom", sortable: true },
        { key: "role", label: "Rôles", sortable: false },
        { key: "company", label: "Entreprise", sortable: true },
        { key: "status", label: "Actif", sortable: true },
        {
          key: "actions",
          label: "Actions",
          actions: ["see", "edit", "enable", "disable", "delete"]
        }
      ],
      filters: {},
      sortColumn: "createdAt",
      sortDirection: "desc",
      allUsers: {
        items: [],
        count: 0
      },
      allRoles: {
        items: [],
        count: 0
      },
      me: {
          rights: []
      },
      loading: false,
      offset: 0,
      limit: 10,
      disableModal: {
        user: {},
        show: false
      },
      deleteModal: {
          user: {},
          show: false
      }
    };
  },
  apollo: {
    me: {
        query: LOGGED_USER
    },
    allUsers: {
      query: ALL_USERS,
      variables() {
        return this.allUsersQueryVariables;
      },
      watchLoading(isLoading) {
        this.loading = isLoading;
      },
      result(result) {
        this.loading =
          typeof result.data === "undefined" && result.networkStatus === 1;
      }
    },
    allRoles: {
      query: ALL_ROLES,
      watchLoading(isLoading) {
        this.loading = isLoading;
      },
      result(result) {
        this.loading =
          typeof result.data === "undefined" && result.networkStatus === 1;
      }
    }
  },
  computed: {
    rights() {
      return this.me.rights;
    },
    listItems() {
      return this.allUsers
        ? this.allUsers.items.map(item => ({
            id: item.id,
            name: item.firstName + " " + item.lastName.toUpperCase(),
            role: item.rolesByUsersRoles.map(role => role.name).join(", "),
            status: item.status ? "Oui" : "Non",
            company: item.company ? item.company.name : "",
            _actions: {
              enable: !item.status && this.rights.includes('ROLE_DISABLE_USER'),
              disable: item.status && this.rights.includes('ROLE_DISABLE_USER'),
              edit: this.rights.includes('ROLE_UPDATE_USER'),
              delete: this.rights.includes('ROLE_DELETE_USER'),
            }
          }))
        : [];
    },
    filterList() {
      return [
        { key: "search", type: "text", label: "Nom, Prénom ou Email" },
        { key: "companyName", type: "text", label: "Entreprise" },
        {
          key: "role",
          type: "select",
          label: "Rôle",
          attributes: {
            options: this.listRoles
          }
        }
      ]
    },
    listRoles() {
      return this.allRoles ? this.allRoles.items.map(item => ({
        value: item.id,
        label: item.name
      })) : [];
    },
    allUsersQueryVariables() {
      return {
        ...this.filters,
        companyId: this.$route.query.companyId,
        offset: this.offset,
        limit: this.limit,
        sortColumn: this.sortColumn,
        sortDirection: this.sortDirection
      };
    },
    userIsAdmin() {
      return this.me.type === 'admin';
    },
  },
  methods: {
    closeDeleteModal() {
        this.deleteModal.show = false;
    },
    showDeleteUserModal(user) {
        this.deleteModal.user = user;
        this.deleteModal.show = true;
    },
    updateSort($event) {
      this.sortColumn = $event.column;
      this.sortDirection = $event.direction;
      this.resetOffset();
    },
    updatePage(page) {
      this.offset = (page - 1) * 10;
    },
    updateFilters(filters) {
      this.filters = { ...filters };
      this.resetOffset();
    },
    resetOffset() {
      this.offset = 0;
      this.$refs.list.resetPage();
    },
    disableUser() {
      this.toggleUserStatus(
        DISABLE_USER,
        "disableUser",
        this.disableModal.user
      );
      this.closeDisableModal();
    },
    closeDisableModal() {
      this.disableModal.show = false;
    },
    showDisableUserModal(user) {
      this.disableModal.user = user;
      this.disableModal.show = true;
    },
    enableUser(user) {
      this.toggleUserStatus(ENABLE_USER, "enableUser", user);
    },
    deleteUser() {
        this.closeDeleteModal();
        let user = this.deleteModal.user;
        let variables = this.allUsersQueryVariables,
        optimisticResponse = {
            deleteUser: {
                __typename: "User",
                id: user.id,
                deleted: true
            }
        };
        this.$apollo.mutate({
            mutation: DELETE_USER,
            variables: {
                id: user.id
            },
            update(store, result) {
                const data = store.readQuery({
                    query: ALL_USERS,
                    variables: variables
                });
                let userIndex = data.allUsers.items.findIndex(
                    user => user.id === result.data.deleteUser.id
                );
                data.allUsers.items.splice(userIndex, 1);
                store.writeQuery({ query: ALL_USERS, variables: variables, data });
            },
            optimisticResponse: optimisticResponse
        }).then(() => {
            izitoast.success({
                position: "topRight",
                title: "Succès",
                message: "L'utilisateur a bien été supprimé"
            });
        });
    },
    toggleUserStatus(mutation, type, user) {
      let variables = this.allUsersQueryVariables,
        optimisticResponse = {
          __typename: "Mutation"
        };
      optimisticResponse[type] = {
        __typename: "User",
        id: user.id,
        status: type !== "disableUser"
      };
      this.$apollo.mutate({
        mutation: mutation,
        variables: {
          id: user.id
        },
        update(store, result) {
          const data = store.readQuery({
            query: ALL_USERS,
            variables: variables
          });
          let userIndex = data.allUsers.items.findIndex(
            user => user.id === result.data[type].id
          );
          data.allUsers.items[userIndex].status = result.data[type].status;
          store.writeQuery({ query: ALL_USERS, variables: variables, data });
        },
        optimisticResponse: optimisticResponse
      });
    }
  }
};
</script>
