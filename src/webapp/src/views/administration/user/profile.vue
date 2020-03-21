<template>
  <dash-wrap :hideTabs="true" :mobileMainAction="{routeName:'UsersForm', icon:'edit-white', routeParams: {id: $route.params.id}}">
    <div class="profile-wrap">
      <page-title pre-title="Utilisateurs" title="Fiche utilisateur" @back="$router.push({name: 'Users'})"></page-title>
      <div class="row" v-if="$apollo.loading">
          <div class="col-md-11">
              Loading...
          </div>
      </div>

      <template v-else>
        <div class="form-btn-top">
          <button type="button" @click="toEdit" class="btn btn-gradient-primary px-5 ml-2">
            <i class="fa fa-pen"></i>
            Modifier
          </button>
        </div>
        <div class="d-flex align-items-start user-box">
          <div class="profile-avatar">
            <div v-if="user.profilePicture">
              <img :src="getImageUrl(user.profilePicture)" alt /></div>
            <div v-else >
              <img src="@/assets/img/user-primary.svg" alt /></div>
            <span class="avatar-status bg-vert-light" v-if="user.status"></span>
            <span class="avatar-status bg-danger" v-else></span>
          </div>
          <div class="profile-container">
            <div class="pl-5 ml-3 mb-3 d-none d-md-block">
              <div class="d-flex align-items-end">
                <h1 class="profile-name">{{user.firstName}} {{user.lastName}}</h1>
                <div class="profile-label font-size-21 ml-2 mb-2">-
                  <span >{{user.type.label}} </span>
                </div>
              </div>
            </div>
            <div class="form-card profile-card">
              <div class="d-block d-md-none text-center">

                <div class="">
                  <h1 class="profile-name">{{user.firstName}} {{user.lastName}}</h1>
                  <div class="profile-label font-size-21">
                    <span class="d-block" >{{user.type.label}} </span>
                  </div>
                </div>
                <div class="font-size-18px text-gris_44 font-italic">
                  <span v-for="role in user.rolesByUsersRoles" :key="role.id">{{role.name}} </span>
                </div>

              </div>
              <div class="d-flex border-bottom border-gris_44 pb-2 profile-details profile-details--one" >
                <div class="section-1">
                  <div class="item-detail">
                    <span class="profile-label">Entreprise :</span>
                    <span class="profile-value">{{user.company ? user.company.name : ''}}</span>
                  </div>
                  <div class="item-detail">
                    <span class="profile-label">Fonction :</span>
                    <span class="profile-value">{{user.function}}</span>
                  </div>
                </div>
                <div v-if="user.type.id === 'candidate'" class="section-2">
                  <div class="item-detail">
                    <span class="profile-label">Ancienneté :</span>
                    <span class="profile-value">{{dateFormat(user.seniorityDate)}}</span>
                  </div>
                  <div class="item-detail">
                    <span class="profile-label">Ancienneté dans la fonction :</span>
                    <span class="profile-value">{{user.previousFunction}}</span>
                  </div>
                </div>
                <div v-if="user.linkedin" class="section-3">
                  <a :href="getLinkedinURL(user.linkedin)" target="_blank"><img src="@/assets/img/linkedin.svg" height="40px" width="40px" alt /></a>
                </div>
              </div>
              <div class="row profile-details profile-details--two">
                <div :class="['coach', 'admin', 'support'].indexOf(user.type.id) ? 'col-md-5 section-1 offset-md-1' : 'col-md-12'" class="col-sm-12 pt-0 mt-0 offset-0">
                  <span class="profile-section-title">Coordonnées</span>
                  <div class="profile-section-content">
                    <div class="profile-contact-tag">
                      <img src="@/assets/img/mail-primary.svg" alt />
                      <span>{{user.email}}</span>
                    </div>
                    <div class="profile-contact-tag">
                      <img src="@/assets/img/phone-primary.svg" alt />
                      <span>{{user.phone}}</span>
                    </div>
                    <div class="profile-contact-tag">
                      <img src="@/assets/img/home-primary.svg" alt />
                      <span>{{user.address}}</span>
                    </div>
                  </div>
                </div>
                <div v-if="['coach', 'admin', 'support'].indexOf(user.type.id)" class="col-sm-12 col-md-5 pt-0 mt-0 section-2">
                  <span class="profile-section-title">Coach - Consultant référent</span>
                  <div class="d-flex align-items-center px-3" v-if="user.coach">
                    <img :src="getImageUrl(user.coach.profilePicture)" class="rounded-circle" height="72px" width="72px" alt v-if="user.coach.profilePicture" />
                    <img src="@/assets/img/user-primary.svg" class="rounded-circle" height="72px" width="72px" alt v-else />
                    <span class="profile-label font-size-21 ml-2">{{user.coach.firstName}} {{user.coach.lastName}}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="content">
          <div class="row mt-2">
            <div class="col-md-12">
              <!-- special list -->
              <list-programs ref="list"
                             name="programs"
                             :items="listPrograms"
                             :total="listPrograms.length"
                             :header="header"
                             :loading="$apollo.loading"
                             no-items-message="Aucune prestation ne correspond à ce filtre"
                             :pagination="false"
                             @edit="program => $router.push({name: 'ProgramForm', params: {id: program.id}})"
              />
            </div>
          </div>
        </div>
      </template>
    </div>
  </dash-wrap>
</template>

<script>
import moment from 'moment';
import ListPrograms from '@/components/lists/Programs';
import {FILE_PATH} from "@/enum/FilePathConstant";
import {USER_BY_ID} from "@/graphql/user/user-by-id-query";
export default {
  name: "profileUserForm",
  components: {
    ListPrograms,
  },
  data() {
    return {
      user: {
          id: '',
          name: '',
          type: {
              id: ''
          },
          programsByProgramsUsers: null,
      },
      header: [
          {key: 'name', label: 'Nom', sortable: false},
          {key: 'coachName', label: 'Responsable', sortable: false},
          {key: 'period', label: 'Période', sortable: false},
          {key: 'status', label: 'Statut', sortable: false},
          {key: 'eventCount', label: 'Nombre d\'événements', sortable: false},
          {key: 'actions', label: 'Actions', actions: ['edit']},
      ],
      routeParams: this.$route.params,
      statusLabels: {
          created: 'Sauvegardé',
          inprogress: 'En cours',
          completed: 'Terminé',
      }
    };
  },
  computed: {
    listPrograms () {
        return this.user.programsByProgramsUsers ? this.user.programsByProgramsUsers
            .map(item => ({
                id: item.id,
                name: item.name,
                coachName: item.coach ? item.coach.firstName + ' ' + item.coach.lastName : '',
                status: this.statusLabels[item.status],
                period: item.dateStart ? this.dateFormat(item.dateStart) + ' au ' + this.dateFormat(item.dateEnd) : '',
                eventCount: item.events.count,
            })) : []
    },
  },
  apollo: {
    user: {
        query: USER_BY_ID,
        variables() {
            return {id: this.routeParams.id}
        },
        update: data => data.userById
    }
  },
  methods: {
    getImageUrl (profilePhoto) {
        let baseUrl = process.env.VUE_APP_GRAPHQL_HTTP.substr(0, process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf('/'));
        return baseUrl + FILE_PATH + profilePhoto.id;
    },
    toEdit () {
      this.$router.push({name: 'UsersForm', params: {id: this.$route.params.id}})
    },
    dateFormat (date) {
      return date ? moment(date).format('DD/MM/YYYY') : '';
    },
    getLinkedinURL(linkedin) {
      return linkedin.indexOf('http')>-1 ? (linkedin) : ('https://' + linkedin)
    }
  },
};
</script>

<style scoped>
  .user-box {
    /* width: 95%; */
  }
</style>
