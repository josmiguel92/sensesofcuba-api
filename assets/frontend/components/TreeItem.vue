<template>
    <div @click.stop="toggle" class="tree-item"  v-if="item.children || item.files || item.description">
        <div class="content d-flex flex-column p-2 justify-content-center">
            <div class="d-flex align-items-center">
                <div class="d-flex flex-grow-1 align-items-center justify-content-between">
                    <span class="label d-flex align-items-center ml-2 mr-2">
                        <i class="mr-2" :class="iconClass"></i> {{ item.title }}

                        <span class="badge badge-pill text-sm ml-2"
                              v-bind:class="[!isOpen ? 'badge-primary' : 'badge-secondary text-muted']"
                              v-if="isParent">
                            {{ item.children.length }}
                        </span>
                    </span>

                    <small v-if="item.modified_on" class="d-none d-md-inline pr-2 pl-1 text-muted">
                        {{ $t('general.updated') }}: {{ item.modified_on | date }}
                    </small>

                </div>

                <a v-if="currentUser.roles === 0" role="button" :href="'admin/product/'+item.id+'/edit'" target="_blank" class="btn btn-sm d-md-inline">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <SubscribeButton :item="item"></SubscribeButton>

            </div>
            <div class="d-flex flex-row-reverse">
                <div class="docs-list d-flex flex-column mt-1 flex-shrink-0 w-20" v-if="item.files">
                    <div class="btn-group  mt-1" role="group" aria-label="Product Document" v-for="(file, i) in item.files" :key="i" :item="file.title">
                        <OpenDocumentButton :item="file" :title="item.title"  v-if="file.file"></OpenDocumentButton>
                        <a role="button" :href="`${file.file}`" class="btn btn-secondary btn-sm flex-grow-0" :download="file.title"  v-if="file.file">
                            <i class="fa fa-download"></i>
                        </a>
                    </div>
                </div>
                <div class="flex-grow-1 d-flex" v-if="item.description">
                    <small class="ml-2 mt-2">
                        <i class="mr-2 fa fa-info-circle"></i> {{ item.description }}
                    </small>
                </div>
            </div>

        </div>
        <div class="tree" v-if="isParent" v-show="isOpen">
            <TreeItem v-for="(child, i) in item.children" :key="i" :item="child"></TreeItem>
        </div>
    </div>
</template>

<script>
import SubscribeButton from './SubscribeButton.vue';
import OpenDocumentButton from './OpenDocumentButton.vue';
import {mapActions, mapGetters} from 'vuex';

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
        },
        ...mapGetters(['currentUser']),
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
    .w-20{
        max-width: 20%;
    }
}

</style>
