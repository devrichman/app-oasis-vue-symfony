<template>
  <div class="bg-primary flex-column h-100 d-flex align-items-center justify-content-center">
    <img alt="logo" src="@/assets/img/logo.svg">

    <ul class="nav mt-5">
      <li class="nav-item">
        <router-link class="btn btn-white-primary nav-link " :to="{name:'login'}">Connexion</router-link>
      </li>
    </ul>
  </div>
    
</template>

<script>
export default {
  name: 'home',

}
</script>
