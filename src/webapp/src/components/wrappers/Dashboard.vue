
<template>
  <div class="wrapper wrapper--dash d-flex">
    <div
      class="sidebar align-items-center"
      :class="{'d-none d-sm-flex': !mobileSidebar, 'd-flex': mobileSidebar}"
    >
      <div class="nav-wrap nav-wrap--one">
        <ul class="nav main-nav">
          <template v-for="(link,i) in sidebarLinks">
            <li
              class="nav-item"
              v-if="link.enabled && !(link.key=='main-action' && !mobileMainAction.routeName)"
              :key="i"
              :class="[link.key]"
            >

              <router-link
                :to="link.key === 'main-action' && mobileMainAction.routeName !== null ? {name: mobileMainAction.routeName, params : mobileMainAction.routeParams} : {name: link.routeName, params:link.routeParams}"
                class="nav-link"
                :class="{'active': modeIs(link.key)}"
              >
                <!-- v-if="profilePicture && link.key==='avatar'" -->

                <!-- <img
                  class="avatar-img sb-img"
                  :src="profilePicture"
                  height="40"
                  width="40"
                  alt="avatar"
                />-->

                <!-- v-else -->
                <el-tooltip effect="light" :content="link.label" placement="right">
                <img
                  class="sb-img"
                  :src="require(`@/assets/img/${link.icon}${modeIs(link.key)?'-active':''}.svg`)"
                />
                </el-tooltip>
              </router-link>

            </li>
          </template>
        </ul>
      </div>
      <div class="nav-wrap nav-wrap--two">
        <ul class="nav bottom-nav">
          <li class="nav-item avatar">
            <span class="nav-link">
              <el-tooltip effect="light" content="Mon compte" placement="right">
                <button class="btn btn-avatar" @click="goToMyProfile()">
                  <img
                    class="avatar-img"
                    v-if="profilePicture"
                    :src="profilePicture"
                    height="40"
                    width="40"
                    alt="avatar"
                  />
                  <img
                    class="avatar-img"
                    v-else
                    src="@/assets/img/user-primary.svg"
                    height="40"
                    width="40"
                    alt
                  />
                <span class="d-block mt-1 mb-2 avatar-name">{{ me.firstName }}</span>
                </button>
              </el-tooltip>
            </span>
          </li>
          <li class="nav-item logout-nav-item mt-3">
            <el-tooltip effect="light" content="Déconnexion" placement="right">
              <button class="btn py-3" @click="logout">
                <img src="@/assets/img/deconnection.svg" alt />
              </button>
            </el-tooltip>
          </li>
        </ul>
      </div>
    </div>
    <div class="wrapper-content flex-grow-1 d-flex flex-column">
      <div class="wrapper-content--header container">
        <div class="d-flex align-items-center">
          <template v-if="modeIs('coache')">
            <span class="sub-txt text-white">
              Bonjour
              <span class="main-txt font-weight-light">Naomi</span>
            </span>
          </template>
          <template v-else>
            <span class="sub-txt text-white">
              {{article}}
              <span class="main-txt font-weight-light">{{title}}</span>
            </span>
          </template>
          <div class="ml-auto">
            <div class="d-flex">
              <template v-if="modeIs('coach')">
                <button class="btn btn-white-icon">
                  <img src="@/assets/img/prestation-add.svg" alt />
                  <span>Créer prestation</span>
                </button>
                <button class="btn btn-white-icon ml-3">
                  <img src="@/assets/img/icon-event-add_primary.svg" alt />
                  <span>Créer évenement</span>
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>
      <div class="wrapper-content--body flex-grow-1">
        <div class="container dash-body-container">
          <perfect-scrollbar style="width:100%">
            <ul
              class="admin-tabs nav nav-tabs"
              v-if="mode === 'admin' && (isAdmin || haveAccess) && !hideTabs"
            >
              <template v-for="(adminTab, index) in tabs">
                <li class="nav-item" v-if="adminTab.enabled" :key="index">
                  <router-link
                    :to="{name: adminTab.routeName}"
                    :class="{'nav-link': true, 'active': tab === adminTab.key}"
                  >
                    <img
                      class="nav-icon"
                      :src="require(`@/assets/img/${adminTab.key}${ tab === adminTab.key ? '-active' : ''}.svg`)"
                    />
                    <span>{{ adminTab.label }}</span>
                  </router-link>
                </li>
              </template>
            </ul>
          </perfect-scrollbar>

          <div class="tab-content">
            <div class="tab-pane fade" :class="{'show active': this.me}">
              <slot />
            </div>
            <div class="tab-pane fade show active" v-if="$apollo.loading">Loading...</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { FILE_PATH } from "@/enum/FilePathConstant";
import { PerfectScrollbar } from "vue2-perfect-scrollbar";
import { LOGGED_USER } from "@/graphql/security/logged-user-query";
import {LOGOUT} from "@/graphql/security/logout-mutation";

export default {
  name: "DashWrap",
  components: {
    PerfectScrollbar
  },
  props: {
    mode: {
      type: String,
      default: "admin"
    },
    title: {
      type: String,
      default: "Administration"
    },
    tab: {
      type: String,
      required: false
    },
    article: {
      type: String,
      required: false,
      default: "Mon"
    },
    hideTabs: {
      type: Boolean,
      default: false
    },
    mobileSidebar: {
      type: Boolean,
      default: true
    },
    mobileMainAction: {
      type: Object,
      default: () => {
        return {
          icon: "plus",
          routeName: null
        };
      }
    }
  },
  data() {
      return {
          me: {
              type: ''
          }
      }
  },
  apollo: {
    me: {
      query: LOGGED_USER
    }
  },
  computed: {
    sidebarLinks() {
      return [
        { key: "users", icon: "coache", routeName: "Me", enabled: !this.isAdmin },
        {
          key: "folders",
          icon: "folders",
          routeName: "DocumentsList",
          enabled: true,
          label: "Documents"
        },
        {
          key: "main-action",
          icon: this.mobileMainAction.icon
            ? this.mobileMainAction.icon
            : "plus",
          routeName: this.mobileMainAction.routeName,
          mobileOnly: true,
          enabled: true
        },
        {
          key: "admin",
          icon: "admin",
          routeName: "Users",
          label: "Administration",
          enabled: this.isAdmin || this.haveAccess
        },
        {
          key: "avatar",
          icon: "profil-bis",
          routeName: "Me",
          mobileOnly: true,
          enabled: true
        }
      ];
    },
    tabs() {
      return [
        {
          key: "users",
          label: "Utilisateurs",
          icon: "fa-users",
          routeName: "Users",
          enabled: true
        },
        {
          key: "companies",
          label: "Entreprises",
          icon: "fa-building",
          routeName: "CompaniesList",
          enabled:
            this.isAdmin ||
            this.me.rights.includes("ROLE_ACCESS_COMPANY_MENU")
        },
        {
          key: "roles",
          label: "Rôles",
          icon: "fa-tag",
          routeName: "RolesList",
          enabled: this.isAdmin
        },
        {
          key: "programs",
          label: "Prestations",
          icon: "fa-clipboard",
          routeName: "ProgramsList",
          enabled:
            this.isAdmin || this.me.rights.includes("ROLE_ACCESS_PROGRAM")
        },
        {
          key: "events",
          label: "Événements",
          icon: "fa-calendar-alt",
          routeName: "EventsList",
          enabled:
            this.isAdmin || this.me.rights.includes("ROLE_ACCESS_EVENT")
        }
      ];
    },
    profilePicture() {
      if (this.me && this.me.profilePictureId) {
        return (
          process.env.VUE_APP_GRAPHQL_HTTP.substr(
            0,
            process.env.VUE_APP_GRAPHQL_HTTP.lastIndexOf("/")
          ) +
          FILE_PATH +
          this.me.profilePictureId
        );
      }
      return null;
    },
    activeLinks() {
      return this.links.filter(link =>
        link.key === "admin" ? this.isAdmin || this.haveAccess : true
      );
    },
    isAdmin() {
      return this.me.type === "admin";
    },
    haveAccess() {
        return this.me.type === "support" || this.me.type === "coach";
    },
  },
  methods: {
    goToMyProfile() {
      if (this.$route.path !== "/me") {
        this.$router.push({ name: "Me" });
      }
    },
    modeIs(mode) {
      return this.mode.toLowerCase() === mode.toLowerCase();
    },
    logout() {
      this.$apollo.mutate({
          mutation: LOGOUT
      }).then(() => {
          this.$router.push("/connexion");
      });
    }
  }
};
</script>