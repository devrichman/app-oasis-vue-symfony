<template>
    <dash-wrap mode="admin">
        <h1>Admin dashboard</h1>
        <button @click="logout">Logout</button>
    </dash-wrap>
</template>

<script>
    import {LOGOUT} from "@/graphql/security/logout-mutation";

    export default {
        name: "AdminDashboard",
        data () {
            return {}
        },
        methods: {
            logout () {
                this.$apollo.mutate({
                    mutation: LOGOUT
                }).then(() => {
                    this.$router.push("/connexion");
                });
            }
        }
    }
</script>

<style scoped>

</style>
