<template>
  <dash-wrap
    article="Liste de"
    title="Documents"
    mode="folders"
    :mobileMainAction="{routeName:'DocumentForm'}"
  >
    <div class="container text-left my-5">
      <!-- <div class="row mt-3">
                <div class="col-md-6">
                    <h1>Liste des documents</h1>
                </div>
      </div>-->
      <div class="row">
        <div class="col-md-8">
          <filters name="documents" :filters="filterList" @filter="updateFilters($event)" />
        </div>
        <div class="col-md-4 d-flex justify-content-end">
          <button
                  class="btn btn-gradient-primary px-4"
                  @click="() => $router.push({name: 'DocumentForm'})"
          >
            <i class="fa fa-plus" aria-hidden="true"></i>
            Créer un document
          </button>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-12">
          <list
            name="documents"
            ref="list"
            :items="listItems"
            :total="allDocuments.count"
            :header="header"
            no-items-message="Aucun document ne correspond à ce filtre"
            :loading="loading"
            @edit="document => $router.push({name: 'DocumentForm', params: {id: document.id}})"
            @see="document => $router.push({name: 'DocumentForm', params: {id: document.id, mode: 'view'}})"
            @delete="document => showDeleteDocumentModal(document)"
            @sort="updateSort($event)"
            @paginate="updatePage($event)"
          />
        </div>
      </div>
      <confirm-dialog
        v-if="deleteModal.show"
        title="Etes-vous sûr de vouloir supprimer ce document ?"
        @close="closeDisableModal()"
        @confirm="deleteDocument()"
      />
    </div>
  </dash-wrap>
</template>
<script>
import List from "@/components/utils/List";
import moment from "moment";
import { FILTER_LIST } from "@/enum/visibilityEnum";
import Filters from "@/components/filters/Filters";
import ConfirmDialog from "@/components/utils/ConfirmDialog";
import { ALL_DOCUMENTS } from "@/graphql/document/all-documents-query";
import { DELETE_DOCUMENT } from "@/graphql/document/delete-document-mutation";
import izitoast from "izitoast";

export default {
  name: "documentsList",
  components: {
    List,
    Filters,
    ConfirmDialog
  },
  data: () => ({
    header: [
      { key: "name", label: "Nom", sortable: true },
      { key: "description", label: "Description" },
      { key: "tags", label: "Tags" },
      { key: "visibility", label: "Visibilité" },
      { key: "createdAt", label: "Date de création", sortable: true },
      { key: "actions", label: "Actions", actions: ["see", "edit", "delete"] }
    ],
    filterList: [
      { key: "search", type: "text", label: "Nom, description" },
      {
        key: "visibility",
        type: "select",
        label: "Visibilité",
        attributes: {
          options: FILTER_LIST
        }
      }
    ],
    filters: {},
    sortColumn: "createdAt",
    sortDirection: "desc",
    allDocuments: {
      items: [],
      count: 0
    },
    loading: false,
    offset: 0,
    limit: 10,
    deleteModal: {
      document: {},
      show: false
    }
  }),
  apollo: {
    allDocuments: {
      query: ALL_DOCUMENTS,
      variables() {
        return this.allDocumentsQueryVariables;
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
  computed: {
    listItems() {
      return this.allDocuments.items.map(item => ({
        id: item.id,
        name: item.name,
        tags: item.tags.split(",").join(", "),
        createdAt: moment(item.createdAt).format("DD/MM/YYYY"),
        description: item.description,
        visibility: FILTER_LIST[item.visibility].label
      }));
    },
    allDocumentsQueryVariables() {
      return {
        ...this.filters,
        offset: this.offset,
        limit: this.limit,
        sortColumn: this.sortColumn,
        sortDirection: this.sortDirection
      };
    }
  },
  methods: {
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
    deleteDocument() {
      this.closeDisableModal();
      let variables = this.allDocumentsQueryVariables,
        optimisticResponse = {
          deleteDocument: {
            __typename: "Document",
            id: this.deleteModal.document.id
          }
        };
      this.$apollo
        .mutate({
          mutation: DELETE_DOCUMENT,
          variables: {
            id: this.deleteModal.document.id
          },
          update(store, result) {
            const data = store.readQuery({
              query: ALL_DOCUMENTS,
              variables: variables
            });
            data.allDocuments.items = data.allDocuments.items.filter(
              document => document.id !== result.data.deleteDocument.id
            );
            store.writeQuery({
              query: ALL_DOCUMENTS,
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
            message: "Le document a bien été supprimé"
          });
        });
    },
    resetOffset() {
      this.offset = 0;
      this.$refs.list.resetPage();
    },
    closeDisableModal() {
      this.deleteModal.show = false;
    },
    showDeleteDocumentModal(document) {
      this.deleteModal.document = document;
      this.deleteModal.show = true;
    }
  }
};
</script>