<template>
    <div class="container">
        <div id="documents-grid" class="d-flex flex-wrap justify-content-center">
            <DocItem class="m-2" v-for="(item, i) in items" :key="i" :item="item">
            </DocItem>
        </div>
    </div>
</template>

<script>
import DocItem from '~/components/DocItem.vue';
export default {
    props: {
        items: {
            type: Array,
            default: () => []
        }
    },
    components: {
        DocItem
    }
}
</script>

<style scoped>
</style>