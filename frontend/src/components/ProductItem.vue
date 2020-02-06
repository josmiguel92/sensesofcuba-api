<template>
    <div class="item mr-1 ml-1" :class="{selected: active}">
        <div class="content h-100 d-flex flex-column align-items-center justify-content-end" v-lazy:background-image="`${item.image}`">
            <span class="title">{{ item.title }}</span>
        </div>
        <div class="panel p-4" v-active="active">
            <div class="d-flex align-items-center mb-4">
                <h3 class="panel-title text-dark mr-2">{{ item.title }}</h3>
                <SubscribeButton :item="item"></SubscribeButton>
                <OpenDocumentButton v-if="item.file" :item="item"></OpenDocumentButton>
                <a v-if="item.file" role="button" :href="`${item.file}`" class="ml-2 btn btn-primary btn-sm btn-icon" :download="item.title"><i class="fa fa-download"></i>
                    <span class="d-none d-md-inline">Download</span>
                </a>
            </div>
            <TreeItem v-for="(child, i) in item.children" :key="i" :item="child"></TreeItem>
        </div>
    </div>
</template>

<script>
import TreeItem from "~/components/TreeItem.vue";
import SubscribeButton from '~/components/SubscribeButton.vue';
import OpenDocumentButton from '~/components/OpenDocumentButton.vue';

export default {
  props: ["item", "active"],
  components: {
    TreeItem,
    SubscribeButton,
    OpenDocumentButton
  },
  directives: {
    active: {
      bind: function(el, binding, vnode) {
        vnode.context.$nextTick(() => {
          el.parentElement.style.marginBottom = "8px";
        });
      },
      update: function(el, binding, vnode) {
        const active = binding.value;
        if (active) {
          el.style.display = "flex";
          vnode.context.$nextTick(() => {
            const height = el.clientHeight + 12;
            el.parentElement.style.marginBottom = `${height}px`;
          });
        } else {
          el.style.display = "none";
          vnode.context.$nextTick(() => {
            el.parentElement.style.marginBottom = "8px";
          });
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.item {
    cursor: pointer;
    min-width: 400px; 
    height: 200px;
    opacity: 0.95;
    transition: opacity 0.35s;
    .content {
        position: relative;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;  
        border-radius: 1px;
        .title {
            color: #fafafa;
            width: 100%;
            text-align: center;
            background:rgba(0,0,0,0.4);
            padding: 2%;
            text-transform: none;
            z-index: 99;
            transition: all .2s ease;
        }
        &:hover {
            &::before {
                background-color: transparent;
            }
        }
    }
    .content::before {
        position: absolute;
        content: " ";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,.2);
        transition: all .2s ease;    
    }
    .panel {
        display: none;
        position: absolute;
        background-color: #fafafa;
        top: auto;
        margin-top: 6px;
        left: 1%;
        right: 1%;
        min-height: 200px;
        border-radius: 3px;
        padding: 4px;
        flex-direction: column;
        .panel-title {
            font-weight: 700;
            margin-bottom: 2px !important;
        }
    }
}
.item.selected .content .title {
        color: #f9ce1c;
        padding: 3%;
        background-color: rgba(0,0,0,0.6);
}
</style>