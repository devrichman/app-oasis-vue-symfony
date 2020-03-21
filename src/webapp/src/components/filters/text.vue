<template>
    <div class="form-group">
        <input type="text" class="form-control"
               :placeholder="label"
               :value="value"
               v-on:keyup.enter="$event => $emit('enter')"
               @input="$event => $emit('input', $event.target.value)" />
    </div>
</template>
<script>
    export default {
        name: "List",
        props: {
            value: {
                required: true,
            },
            label: {
                type: String,
                required: true,
            },
            attributes: {
                type: Object,
                required: false,
            },
        },
    };
</script>

