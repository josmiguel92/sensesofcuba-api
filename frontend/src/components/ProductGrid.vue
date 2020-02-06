<template>
    <div class="container-fluid">
        <div id="grid" class="d-flex align-items-start justify-content-center flex-wrap">
            <ProductItem 
                v-for="(item, i) in items"
                :key="i"
                @click.native="toggle(i)"
                :item="item"
                :active="currentItem === i" />
        </div>
    </div>
</template>

<script>
import ProductItem from '~/components/ProductItem.vue';

export default {
    components: {
        ProductItem
    },
    props: ['items'],
    data() {
        return {
            currentItem: -1 // No item selected
        }
    },
    methods: {
        toggle(i) {
            if (this.currentItem === i) {
                this.currentItem = -1;
            } else {
                this.currentItem = i;
            }
        }
    }
}
</script>

<style lang="scss" scoped>
#grid {
    position: relative;
}
</style>