import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import router from './router'
import { apolloProvider } from './apollo'
import 'bootstrap'
import DashWrap from "./components/wrappers/Dashboard.vue";
import SetupWrap from "./components/wrappers/Setup.vue";
import AuthWrap from "./components/wrappers/Auth.vue";
import PageTitle from './components/utils/PageTitle.vue';


// import 'bootstrap/dist/css/bootstrap.min.css'
import VeeValidate from 'vee-validate'
import 'izitoast/dist/css/iziToast.min.css'
import './themes/main.scss';
import {Tag, Input, Button, DatePicker, Tooltip} from 'element-ui'
import 'izitoast/dist/css/iziToast.min.css'
import 'material-design-icons/iconfont/material-icons.css';
import '@fortawesome/fontawesome-free/css/all.css';
import './themes/main.scss';
import lang from 'element-ui/lib/locale/lang/fr'
import locale from 'element-ui/lib/locale'


[DashWrap, SetupWrap, AuthWrap, PageTitle].forEach(element => {
  Vue.component(element.name, element);
});

[DashWrap, SetupWrap, AuthWrap, PageTitle].forEach(element => {
  Vue.component(element.name, element);
});

Vue.config.productionTip = false;
Vue.config.devtools = true;
Vue.use(VeeValidate, {
  fieldsBagName: 'formFields',
});


locale.use(lang)

Vue.use(Tag);
Vue.use(Input);
Vue.use(Button);
Vue.use(Tooltip);
Vue.use(DatePicker);



new Vue({
  router,
  apolloProvider,
  render: h => h(App)
}).$mount('#app');
