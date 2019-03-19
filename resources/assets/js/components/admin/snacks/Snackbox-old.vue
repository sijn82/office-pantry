<template>
    <div>
        <p> {{ this.order }} </p>
        <p> {{ product }} </p>
        <p> {{ quantity }} </p>
    </div>

</template>

<style>

</style>

<script>
export default {
    props: ['product', 'quantity'],
    data() {
        return {
            order: 'empty',
        }
    },
    methods: {
        addProductToOrder($event) {
            alert($event);
            this.order = 'one item';
        }
    }
}
</script>