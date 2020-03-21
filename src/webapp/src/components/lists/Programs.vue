<template>
  <div class="list list--profile">
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
                        @click.prevent="$emit(action, item)">

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
    <div class="table-wrap-sm">
      <template v-if="loading">
        <span>Loading</span>
      </template>
      <div
        class="custom-table-row flex-column profile-list"
        v-for="(item, index) in items"
        :key="index"
      >
        <div class="d-flex justify-content-end">
          <!-- status -->
          <span
            class="cell cell-status ml-2"
            :class="`cell-${statusStyle(item.status)}`"
          >{{ item.status }}</span>
        </div>
        <div>
          <!-- name -->
          <span class="font-size-14 font-weight-bold">{{item.name}}</span>
          <!-- date -->
          <span class="text-gris_44">{{item.period}}</span>

          <!-- tag respsonsable -->
          <span class="tag-name">Responsable</span>
          <!-- val -->
          <span class="tag-val">{{item.coachName}}</span>

          <div class="d-flex">
            <div>
              <!-- tag nombre -->
              <span class="tag-name">Nombre d’évenements</span>
              <!-- val -->
              <span class="tag-val">{{item.eventCount}}</span>
            </div>
            <div class="ml-auto">
              <button
                class="btn liste-action-btn mr-2"
                v-for="(action, index) in header[header.length - 1].actions"
                @click.prevent="$emit(action, item)"
                :key="index"
              >
                <img :src="require(`@/assets/img/${actions[action].icon}.svg`)" alt />
              </button>
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
          page-link-class="page-link"
          prev-link-class="page-link previous md-icon"
          next-link-class="page-link next md-icon"
        />
      </div>
    </div>
  </div>
</template>
<script>
import listMixin from '../utils/listMixin';

export default {
  name: "ProfileList",
  mixins: [listMixin],
};
</script>