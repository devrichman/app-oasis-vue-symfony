<template>
  <div class="filters">
    <div class="filter-group filter-group--l">
      <span class="text-gris_44 filters-label">Filtres :</span>
      <div
        v-for="filter in filters"
        :key="filter.key"
        :class="{
                    ...(filter.classList ? filter.classList : {})
                 }"
      >
        <component
          :is="filterTypes[filter.type]"
          class="ml-2 mb-0"
          :label="filter.label"
          @enter="applyFilters"
          :attributes="filter.attributes ? filter.attributes : {}"
          v-model="filterValues[filter.key]"
          @input="$event => updateFilterValue(filter.key, $event)"
        />
      </div>
      <div class="ml-2">
        <button class="btn btn-search-round btn-gris_80" @click="applyFilters">
          <img src="@/assets/img/search-white.svg" />
        </button>
        <button class="btn btn-outline-dark btn-sm ml-2 btn-search-round" @click="resetFilters()">
          <i class="fa fa-undo" aria-hidden="true"></i>
        </button>
      </div>
    </div>
    <div class="filter-group filter-group--sm justify-content-end">
      <button
        @click="filterModalActive = true"
        type="button"
        class="btn bg-transparent text-primary btn-outline-primary ml-4 btn-no-hover btn-no-focus"
      >
        <img class="mr-3" src="@/assets/img/filtre.svg" />
        Filtres
      </button>

      <mobile-modal :active="filterModalActive" @close="filterModalActive = false">
        <div>
          <span class="font-weight-bold font-size-18">Filtres :</span>
          <div
            v-for="filter in filters"
            :key="filter.key"
            :class="{
                    ...(filter.classList ? filter.classList : {})
                 }"
          >
            <component
              :is="filterTypes[filter.type]"
              class
              :label="filter.label"
              :attributes="filter.attributes ? filter.attributes : {}"
              :value="filterValues[filter.key]"
              @input="$event => updateFilterValue(filter.key, $event)"
            />
          </div>
          <button class="btn btn-white-primary btn-block" @click="applyFilters">filtrer</button>
          <button
            class="btn btn-outline-dark btn-block mt-2 btn-white-secondary btn-uppercase"
            @click="resetFilters()"
          >Réinitialiser</button>
        </div>
      </mobile-modal>
    </div>
  </div>
</template>
<script>
    import text from './text';
    import select from './select';
    import checkbox from './checkbox';
    import MobileModal from "@/components/utils/MobileModal";
    import {ALL_FILTERS} from "@/graphql/filters/filter";

export default {
  name: "List",
  components: {
    MobileModal
  },
  props: {
    name: {
      type: String,
      required: true
    },
    filters: {
      type: Array,
      required: true
    }
  },
  apollo: {
      filterList: {
          query: ALL_FILTERS,
          update: data => data.filters
      }
  },
  data() {
    return {
      filterList: [],
      filterValues: {},
      filterTypes: {
        text: text,
        select: select,
        'checkbox': checkbox
      },
      filterModalActive: false
    };
  },
  mounted() {
      const data = this.filterList.find(item => item.id === this.name);

      if  (data) {
          for (let [key, value] of Object.entries(data.filters)) {
              this.filterValues = {
                  ...this.filterValues,
                  [value.key]: value.value
              }
          }
      }
      this.$emit('filter', this.filterValues);
  },
  methods: {
    resetFilters() {
      this.filters.forEach(filter => {
        this.filterValues[filter.key] = null;
      });

      this.updateFilterCache(this.name, this.filterValues);
      this.$emit('filter', this.filterValues);
      this.updateFilterBox();
      },
      updateFilterValue (key, value) {
      this.filterValues[key] = value;
      this.updateFilterBox();
      },
      applyFilters () {
      this.updateFilterCache(this.name, this.filterValues);
      this.$emit('filter', this.filterValues);
      },
      jsonToCache (json) {
        let array = []
        for (let [key, value] of Object.entries(json)) {
            array.push({
                __typename: 'FilterValue',
                id: key + "_" + this.name,
                key: key,
                value: value
            })
        }
        return array;
      },
      updateFilterCache(key, filterValues) {
        let data = this.$apollo.getClient().cache.readQuery({query: ALL_FILTERS});
        const currentFilter = data.filters.find(item => item.id === key);
        if(currentFilter) {
            currentFilter.filters = this.jsonToCache(filterValues)
        } else {
            data.filters.push({
                __typename: 'Filter',
                id: key,
                filters: this.jsonToCache(filterValues)
            })
        }
        this.$apollo.getClient().cache.writeQuery({query: ALL_FILTERS, data})
      },
      updateFilterBox () {
          this.filterList = this.filterList.map(filter => {
              return {...filter, value: this.filterValues[filter.key]}
          })
      }
  },
};
</script>

