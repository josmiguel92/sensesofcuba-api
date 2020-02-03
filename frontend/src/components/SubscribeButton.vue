<template>
    <button class="btn btn-secondary btn-sm btn-icon"
        @click.stop="toggleSubscribe">
        <i class="fa fa-bell" :class="{'text-primary': subscribed}"></i>
        <span>{{ buttonText }}</span>
    </button>
</template>

<script>
import { mapActions } from 'vuex';
export default {
    props: ['item'],
    data() {
        return {
            subscribed: this.item.subscribed
        }
    },
    computed: {
        buttonText() {
            if (this.subscribed) return this.$t('general.subscribed');
            else return this.$t('general.subscribe');
        }
    },
    methods: {
        ...mapActions(['subscribeToProduct', 'unsubscribeFromProduct']),
        toggleSubscribe() {
            if (this.subscribed) {
                this.unsubscribeFromProduct(this.item.id);
                this.subscribed = false;
            } else {
                this.subscribeToProduct(this.item.id);
                this.subscribed = true;
            }
        }
    }
}
</script>

<style>

</style>