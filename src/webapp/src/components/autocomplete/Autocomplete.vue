<template>
    <vue-autosuggest ref="autosuggest"
                     v-model="query"
                     :suggestions="suggestions"
                     :input-props="props"
                     :get-suggestion-value="getSuggestionValue"
                     @selected="selectSuggestion"
                     @input="() => $emit('input', query)">
        <template slot="after-input">
            <div class="loader" v-show="loading"></div>
        </template>
        <template slot-scope="{suggestion}">
            <div v-if="renderSuggestion" v-html="renderSuggestion(suggestion)" />
            <div v-else-if="getSuggestionValue">{{ getSuggestionValue(suggestion) }}</div>
            <div v-else>{{ suggestion.item }}</div>
        </template>
    </vue-autosuggest>
</template>
<script>
    import {VueAutosuggest} from 'vue-autosuggest';

    export default {
        name: "Autocomplete",
        props: {
            initialQuery: {
                type: String,
                required: false,
            },
            placeholder: {
              type: String,
              required: false,
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            },
            suggestions: {
                type: Array,
                required: false,
            },
            getSuggestionValue: {
                type: Function,
                required: false,
            },
            renderSuggestion: {
                type: Function,
                required: false,
            },
            loading: {
                type: Boolean,
                required: false,
                default: false,
            },
            classList: {
                type: Object,
                required: false,
            },
            emptyQueryAfterSelection: {
                type: Boolean,
                default: false,
            },
        },
        data: () => ({
            query: '',
            props: {},
        }),
        components: {
            VueAutosuggest,
        },
        watch: {
            classList () {
                this.props = {
                    class: {
                        'form-control': true,
                        ...(this.classList ? this.classList : {}),
                    },
                    disabled: this.disabled,
                    placeholder: 'Chercher',
                };
            },
        },
        mounted () {
            this.$refs.autosuggest.$watch('loading', value => {
                if (value) {
                    this.$emit('changeLoading', false);
                }
            });
            if (this.initialQuery) {
                this.query = this.initialQuery;
            }
            this.props = {
                class: {
                    'form-control': true,
                    ...(this.classList ? this.classList : {}),
                },
                placeholder: this.placeholder,
                disabled: this.disabled,
            };
        },
        methods: {
            selectSuggestion (suggestion) {
                this.$emit('selected', suggestion.item.id);
                this.$emit('item-selected', suggestion.item);
                if (this.emptyQueryAfterSelection) {
                    this.query = '';
                }
            }
        },
    };
</script>
<style>
    .loader {
        border: 3px solid #fff;
        animation: spin 1s linear infinite;
        border-top: 3px solid #bbb;
        border-radius: 16px;
        width: 24px;
        height: 24px;
        position: absolute;
        top: 8px;
        right: 24px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .autosuggest__results-container {
        position: relative;
        width: 100%;
    }

    .autosuggest__results {
        font-weight: 300;
        margin: 0;
        position: absolute;
        z-index: 10000001;
        width: 100%;
        border: 1px solid #e0e0e0;
        border-bottom-left-radius: 4px;
        border-bottom-right-radius: 4px;
        background: white;
        padding: 0px;
        max-height: 400px;
        overflow-y: scroll;
    }

    .autosuggest__results ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .autosuggest__results .autosuggest__results-item {
        cursor: pointer;
        padding: 5px;
        padding-left: 10px;
    }

    #autosuggest ul:nth-child(1) > .autosuggest__results_title {
        border-top: none;
    }

    .autosuggest__results .autosuggest__results-before {
        color: gray;
        font-size: 11px;
        margin-left: 0;
        padding: 15px 13px 5px;
        border-top: 1px solid lightgray;
    }

    .autosuggest__results .autosuggest__results-item:active,
    .autosuggest__results .autosuggest__results-item:hover,
    .autosuggest__results .autosuggest__results-item:focus,
    .autosuggest__results
    .autosuggest__results-item.autosuggest__results-item--highlighted {
        background-color: #f6f6f6;
    }
</style>