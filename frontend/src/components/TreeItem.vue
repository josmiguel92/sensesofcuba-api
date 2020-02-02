<template>
    <div @click.stop="toggle" class="tree-item">
        <div class="content d-flex flex-column p-2 justify-content-center">
            <div class="d-flex align-items-center">
                <div class="d-flex flex-grow-1 align-items-center">
                    <span class="label d-flex align-items-center ml-2 mr-2">
                        <i class="mr-2" :class="iconClass"></i> {{ item.title }}
                    </span>
                    <span v-if="!isParent" class="mr-2"> | </span>
                    <small v-if="!isParent && item.modified_on" class="d-none d-md-inline">{{ $t('general.updated') }}: {{ item.modified_on | date }}</small>
                </div>
                <div class="d-flex" v-if="item.file">
                    <SubscribeButton :item="item"></SubscribeButton>
                    <OpenDocumentButton :item="item"></OpenDocumentButton>
                    <a role="button" :href="`${item.file}`" class="btn btn-secondary btn-sm" :download="item.title">
                        <i class="fa fa-download"></i> <span class="d-none d-md-inline">Download</span>
                    </a>
                </div>
            </div>
            <div class="d-flex" v-if="item.description">
                <small class="ml-2 mt-2">
                    <i class="mr-2 fa fa-info-circle"></i> {{ item.description }}
                </small>
            </div>
        </div>
        <div class="tree" v-if="isParent" v-show="isOpen">
            <TreeItem v-for="(child, i) in item.children" :key="i" :item="child"></TreeItem>
        </div>
    </div>
</template>

<script>
import SubscribeButton from '~/components/SubscribeButton.vue';
import OpenDocumentButton from '~/components/OpenDocumentButton.vue';
import { mapActions } from 'vuex';

export default {
    name: 'TreeItem',
    props: ['item'],
    components: {
        SubscribeButton,
        OpenDocumentButton
    },
    data() {
        return {
            isOpen: false,
            subscribed: this.item.subscribed
        }
    },
    computed: {
        isParent() {
            return !!this.item.children;
        },
        iconClass() {
            if (this.isParent) {
                if (this.isOpen) {
                    return 'fa fa-chevron-up';
                } else {
                    return 'fa fa-chevron-down';
                }
            } else {
                return 'fa fa-tag';
            }
        }
    },
    methods: {
        ...mapActions(['subscribeToProduct', 'unsubscribeFromProduct']),
        toggle() {
            if (this.isParent) {
                this.isOpen = !this.isOpen;
            }
        },
        openDocument(title, file) {
            this.$pdfModal.show(title, file, this.$t('general.close'));
        },
        toggleSubscribe(productId) {
            if (this.subscribed) {
                this.unsubscribeFromProduct(productId);
                this.subscribed = false;
            } else {
                this.subscribeToProduct(productId);
                this.subscribed = true;
            }
        }
    },
    updated() {
        this.$parent.$forceUpdate();
    }
}
</script>

<style lang="scss" scoped>
.tree-item {
    margin-top: 4px;
    .content {
        background-color: #eee;
        border-radius: 3px;
        padding: 4px 8px;
        min-height: 46px;
        .label {
            font-weight: 500;
        }
    }
    .tree {
        margin-left: 16px;
    }
}

</style>