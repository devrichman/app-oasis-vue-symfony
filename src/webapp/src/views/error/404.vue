<template>
    <div class="d-flex flex-column text-white bg-secondary justify-content-center align-items-center h-100">
        <div class="text-center">
            <h1 class="display-1">404</h1>
            <p class="lead">Page not found.</p>
            <br>
            <router-link class="btn btn-white-primary" :to="{name:'home'}">Go Home</router-link>
        </div>
    </div>
</template>
<script>
export default {
    name:"FourOFour"
}
</script>