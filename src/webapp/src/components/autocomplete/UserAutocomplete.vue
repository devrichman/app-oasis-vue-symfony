<template>
    <autocomplete :initial-query="initialQuery"
                  :suggestions="suggestions"
                  :get-suggestion-value="suggestion => suggestion.item.firstName + ' ' + suggestion.item.lastName"
                  :loading="loading"
                  placeholder="Chercher"
                  :disabled="disabled"
                  :class-list="classList"
                  :empty-query-after-selection="emptyQueryAfterSelection"
                  @input="query => queryResults(query)"
                  @item-selected="$event => $emit('item-selected', $event)"
                  @selected="$event => $emit('input', $event)" />
</template>
<script>
    import Autocomplete from './Autocomplete';
    import {ALL_USERS} from '@/graphql/user/all-users-query';

    export default {
        name: "UserAutocomplete",
        props: {
            initialQuery: {
                type: String,
                required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            },
            value: {
                type: String,
                required: true,
            },
            name: {
                type: String,
                required: false,
                default: '',
            },
            classList: {
                type: Object,
                required: false,
            },
            queryCoaches: {
                type: Boolean,
                default: false,
            },
            emptyQueryAfterSelection: {
                type: Boolean,
                default: false,
            },
        },
        data: () => ({
            suggestions: [],
            loading: false,
            selectedValue: null,
        }),
        components: {
            Autocomplete,
        },
        methods: {
            queryResults (query) {
                if (query.length < 3) {
                    return;
                }

                this.$emit('input', '');
                this.loading = true;
                this.$apollo.query({
                    query: ALL_USERS,
                    variables: {
                        search: query,
                        limit: 10,
                        coachesOnly: this.queryCoaches,
                    },
                }).then(result => {
                    this.loading = false;
                    this.suggestions = [
                        {data: result.data.allUsers.items},
                    ];
                });
            },
        },
    };
</script>
