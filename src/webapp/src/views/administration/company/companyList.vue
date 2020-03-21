<template>
  <dash-wrap active="admin" tab="companies" :mobileMainAction="{routeName:'CompanyForm'}">
    <div class="d-flex">
        <filters name="companies" :filters="filterList" @filter="updateFilters($event)" />
        <div class="ml-auto d-none d-md-block" v-if="rights.includes('ROLE_CREATE_COMPANY')">
            <button class="btn btn-gradient-primary px-4" @click="$router.push({name: 'CompanyForm'})">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Créer une entreprise
            </button>
        </div>
    </div>
    <div class="row mt-2">
      <div class="col-md-12">
        <list
          name="companies"
          ref="list"
          :items="listItems"
          no-items-message="Aucune entreprise ne correspond à ce filtre"
          :total="allCompanies.count"
          :header="header"
          :loading="loading"
          @delete="company => showDeleteCompanyModal(company)"
          @edit=" company => $router.push({name: 'CompanyForm', params: {id: company.id}})"
          @sort="updateSort($event)"
          @paginate="updatePage($event)"
        />
      </div>
    </div>

    <confirm-dialog
      v-if="deleteModal.show"
      title="Voulez-vous supprimer cette entreprise ?"
      @close="closeDeleteModal()"
      @confirm="deleteCompany()"
    />
  </dash-wrap>
</template>

<script>
import { ALL_COMPANIES } from "@/graphql/company/all-companies-query";
import List from "@/components/utils/List";
import { DELETE_COMPANY } from "@/graphql/company/delete-company-mutation";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import izitoast from "izitoast";
import Filters from "@/components/filters/Filters";
import {LOGGED_USER} from "@/graphql/security/logged-user-query";

export default {
  name: "companyList",
  components: {
    List,
    Filters,
    ConfirmDialog
  },
  computed: {
    rights() {
        return this.me.rights;
    },
    listItems() {
      return this.allCompanies.items.map(item => ({
        id: item.id,
        name: item.name,
        code: item.code,
        salesforceLink: item.salesforceLink,
        _actions: {
          edit: this.rights.includes('ROLE_DELETE_COMPANY'),
          delete: this.rights.includes('ROLE_UPDATE_COMPANY'),
        }
      }));
    },
    allCompanyQueryVariables() {
      return {
        ...this.filters,
        offset: this.offset,
        limit: this.limit,
        sortColumn: this.sortColumn,
        sortDirection: this.sortDirection,
      };
    }
  },
  data() {
    return {
      allCompanies: {
        items: [],
        count: 0
      },
      me: {
          rights: []
      },
      searchText: "",
      header: [
        { key: "name", label: "Nom", sortable: true },
        //{ key: "code", label: "Code", sortable: true },
        //{ key: "salesforceLink", label: "Lien Salesforce", sortable: true },
        { key: "actions", label: "Actions", actions: ["edit", "delete"] }
      ],
      filterList: [{ key: "search", type: "text", label: "Nom entreprise" }],
      loading: false,
      filters: {},
      sortColumn: "createdAt",
      sortDirection: "desc",
      offset: 0,
      limit: 10,
      deleteModal: {
        company: {},
        show: false
      }
    };
  },
  apollo: {
    me: {
        query: LOGGED_USER
    },
    allCompanies: {
      query: ALL_COMPANIES,
      variables() {
        return this.allCompanyQueryVariables;
      },
      watchLoading(isLoading) {
        this.loading = isLoading;
      },
      result(result) {
        this.loading =
          typeof result.data === "undefined" && result.networkStatus === 1;
      }
    }
  },
  methods: {
    updateSort($event) {
      this.sortColumn = $event.column;
      this.sortDirection = $event.direction;
      this.resetOffset();
    },
    updateFilters(filters) {
      this.filters = { ...filters };
      this.resetOffset();
    },
    resetOffset() {
      this.offset = 0;
      this.$refs.list.resetPage();
    },
    updatePage(page) {
      this.offset = (page - 1) * 10;
    },
    closeDeleteModal() {
      this.deleteModal.show = false;
    },
    showDeleteCompanyModal(company) {
      this.deleteModal.company = company;
      this.deleteModal.show = true;
    },
    deleteCompany() {
      this.closeDeleteModal();
      let variables = this.allCompanyQueryVariables,
        optimisticResponse = {
          deleteCompany: {
            __typename: "company",
            id: this.deleteModal.company.id
          }
        };
      this.$apollo
        .mutate({
          mutation: DELETE_COMPANY,
          variables: {
            id: this.deleteModal.company.id
          },
          update(store, result) {
            const data = store.readQuery({
              query: ALL_COMPANIES,
              variables: variables
            });
            data.allCompanies.items = data.allCompanies.items.filter(
              company => company.id !== result.data.deleteCompany.id
            );
            store.writeQuery({
              query: ALL_COMPANIES,
              variables: variables,
              data
            });
          },
          optimisticResponse: optimisticResponse
        })
        .then(() => {
          izitoast.success({
            position: "topRight",
            title: "Succès",
            message: "L'entreprise a bien été supprimée"
          });
        });
    }
  }
};
</script>