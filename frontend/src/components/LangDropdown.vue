<template>
    <div class="dropdown"
        :class="{show: isOpen}"
        aria-haspopup="true"
        :aria-expanded="isOpen"
        @click="toggle"
        v-click-outside="close">
        <slot name="title">
            <a class="dropdown-toggle nav-link"
                data-toggle="dropdown">
                <i :class="currentLocaleIconPrev + title"></i>
                <span class="no-icon">{{ title }}</span>
            </a>
        </slot>
        <ul class="dropdown-menu" :class="{show: isOpen}">
            <slot>
                <li class="dropdown-item" v-for="(opt, i) in options" :key="i" @click="select(opt.value)">
                    <span><i class="mr-2" v-if="opt.icon" :class="opt.icon"></i> {{ opt.title }}</span>
                </li> 
            </slot>
        </ul>
    </div>
</template>

<script>
export default {
    props: {
        icon: {
            type: String,
            default: ''
        },
        title: {
            type: String,
            default: ''
        },
        options: {
            type: Array,
            default: () => [
                {
                    title: 'en',
                    value: 'en',
                    icon: 'flag-icon flag-icon-en'
                },
                {
                    title: 'de',
                    value: 'de',
                    icon: 'flag-icon flag-icon-de'
                }
            ]            
        },        
    },

    data() {
        return {
            isOpen: false,
            currentLocaleLang: 'de',
            currentLocaleIconPrev: 'flag-icon flag-icon-',
            currentLocaleIcon: ''
        }
    },
    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },
        select(value) {
            this.currentLocaleLang = value;
            this.currentLocaleIcon = this.currentLocaleIconPrev + this.currentLocaleLang;
            console.log(this.currentLocaleIcon)
            this.$emit('select', value);
            
        },
        close() {
            this.isOpen = false;
        },
        
    }
}
</script>

<style scoped>
.dropdown {
  list-style-type: none;
}
.dropdown .dropdown-toggle {
  cursor: pointer;
}
.dropdown-item {
  cursor: pointer;
}
</style>