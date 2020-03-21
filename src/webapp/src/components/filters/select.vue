<template>
    <div class="form-group select no-label">
        <select class="form-control" @input="$event => $emit('input', $event.target.value)">
            <option :value="null">{{ label }}</option>
            <option v-for="(option, index) in attributes.options" :value="option.value" :key="index">
                {{ option.label }}
            </option>
        </select>
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
                required: true,
            },
        },
    };
</script>

