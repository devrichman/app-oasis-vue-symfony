<template>
  <div class="list-table-wrap">
    <div class="table-wrap-md">
      <table class="table list-table table-hover">
        <thead class="thead-dark">
          <tr>
            <th
              v-for="(column, index) in header"
              :key="index"
              :class="{
                        'sortable': column.sortable,
                        'font-weight-bold': column.key === sortColumn,
                        'sort-asc': column.key === sortColumn && sortDirection === 'asc',
                        'sort-desc': column.key === sortColumn && sortDirection === 'desc',
                        ...(column.classList ? column.classList : {}),
                    }"
              @click="headerClick(index)"
              :style="{'width': widths[column.key]}"
            >
              <div class="d-flex">
                <span class="cell">{{ column.label }}</span>
                <img
                  v-if="column.sortable"
                  class="ml-3"
                  :class="{'inactive-sort': column.key !== sortColumn}"
                  :src="require(`@/assets/img/filtre-${ column.key === sortColumn && sortDirection === 'asc' ? 'up' : 'down'}.svg`)"
                />
              </div>
            </th>
          </tr>
        </thead>
        <tbody v-if="loading">
          <tr>
            <td :colspan="header.length" class="text-center">Loading...</td>
          </tr>
        </tbody>
        <tbody v-else-if="!items.length">
          <tr v-if="actionRow">
            <td :colspan="header.length" class="h3 text-center">
              <a href="#" @click.prevent="$emit('action')">
                <i class="fa" :class="actionIcon" />
              </a>
            </td>
          </tr>
          <tr class="text-center">
            <td :colspan="header.length">
              {{noItemsMessage}}
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr
            v-for="(item, index) in items"
            :key="index"
            @click="$emit('itemClick', item)"
            :class="{
                    ...(item._classList ? item._classList : {}),
                }"
          >
            <td
              v-for="(column, columnIndex) in header"
              :key="columnIndex"
              :class="{'align-middle': column.key == 'status'}"
            >
              <template v-if="column.actions">
                <div class="d-flex">
                  <template v-for="action in column.actions">
                    <el-tooltip :key="action" effect="light" :content="actions[action].labelToolTip" placement="top">
                      <button
                        v-if="!item._actions || typeof item._actions[action] === 'undefined' || item._actions[action]"
                        :class="{
                                          'btn liste-action-btn ml-2': true,
                                          ...(action.buttonClassList ? action.buttonClassList : {}),
                                      }"
                        @click.prevent="$emit(action, item)"
                      >

                          <span v-if="actions[action].label">{{ actions[action].label }}</span>
                          <span v-else-if="actions[action].icon">
                          <img
                                  class="action-icon"
                                  :src="require(`@/assets/img/${actions[action].icon}.svg`)"
                          />
                        </span>
                      </button>
                    </el-tooltip>

                  </template>
                </div>
              </template>
              <template v-else>
                <div class="d-flex">
                  <img
                    class="table-avatar mr-3"
                    v-if="showAvatar && column.key == 'name'"
                    :src="require(`@/assets/img/${ item.status === 'Oui'? 'user-primary': 'profil-bis'}.svg`)"
                  />
                  <span
                    class="cell"
                    :class="`cell-${column.key} cell-${statusStyle(item[column.key])}`"
                  >{{ item[column.key] }}</span>
                </div>
              </template>
            </td>
          </tr>
          <tr v-if="actionRow">
            <td :colspan="header.length" class="h3 text-center">
              <a href="#" @click.prevent="$emit('action')">
                <i class="fa" :class="actionIcon" />
              </a>
            </td>
          </tr>
        </tbody>
      </table>

    </div>

    <!-- moved custom mobile templates to new components and pages -->

    <!-- general lists for mobile, works for most other lists -->

    <div class="table-wrap-sm">
      <template v-if="loading">
        <span>Loading</span>
      </template>
      <div class="custom-table-row flex-column p-2" v-for="(item, index) in items" :key="index">
        <div>
          <div class="d-flex align-items-center">
            <span
              class="font-size-18 text-uppercase font-weight-bold text-consultant-light"
            >{{item.name}}</span>
            <div class="d-flex" v-if="item.type">
              <span class="mx-2 font-weight-bold">-</span>
              <span class="font-weight-bold">{{item.type}}</span>
            </div>
            <div class="ml-auto">
              <span
                v-if="item.status"
                class="cell cell-status ml-2"
                :class="`cell-${statusStyle(item.status)}`"
              >{{ item.status }}</span>

              <span
                v-if="item.visibility"
                class="cell cell-status cell-tag ml-2"
              >{{item.visibility}}</span>
            </div>
          </div>
        </div>
        <div>
          <div class="d-flex">
            <div class="flex-grow-1">
              <p class="font-weight-semibold mb-1">{{item.program}}</p>
              <p class="font-weight-light font-size-12 mb-1">{{item.description}}</p>

              <div class="d-flex flex-wrap" v-if="item.tags">
                <div class="tag" v-for="(item, index) in stripTags(item.tags)" :key="index">
                  <span class="cell cell-status bg-primary text-white mr-2" v-if="item">{{item}}</span>
                </div>
              </div>
            </div>
            <div class="flex-grow-1 d-flex flex-column">
              <span v-if="item.dateEvent" class="text-gris_44 font-size-14">Date: {{item.dateEvent}}</span>
            </div>
          </div>
        </div>
        <div>
          <div class="d-flex justify-content-end align-items-center">
            <span
              v-if="item.updatedAt"
              class="text-gris_44 font-size-14 mr-2"
            >Updated: {{item.updatedAt}}</span>

            <span v-if="item.createdAt" class="text-gris_44 font-size-14">Création{{item.createdAt}}</span>

            <span class="text-gris_44" v-if="item.usersCount">
              <i class="fa fa-user-circle"></i>
              {{item.usersCount}}
            </span>
            <div class="d-flex ml-auto">
              <button
                class="btn liste-action-btn mr-2"
                @click.prevent="$emit(action, item)"
                v-for="(action, index) in header[header.length - 1].actions"
                :key="index"
              >
                <img :src="require(`@/assets/img/${actions[action].icon}.svg`)" alt />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4 text-center" v-if="pagination">
      <paginate
        v-if="items.length"
        :value="page"
        :page-count="totalPages"
        :click-handler="$event => paginate($event)"
        container-class="pagination justify-content-center"
        prev-text="chevron_left"
        next-text="chevron_right"
        page-class="page-item"
        disabled-class="page-link-disabled"
        page-link-class="page-link"
        prev-link-class="page-link previous md-icon"
        next-link-class="page-link next md-icon"
      />
    </div>
  </div>
</template>
<script>
import listMixin from './listMixin';

export default {
  name: "List",
  mixins: [listMixin],
};
</script>
<style scoped>
th .inactive-sort {
  opacity: 0.4;
}
th.sortable {
  cursor: pointer;
}
</style>
