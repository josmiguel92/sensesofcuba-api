<template>
    <div class="select" @click="toggle" :class="{ 'expanded': isOpen }" v-click-outside="close">
        <span class="selection">
            {{ value.title ? value && value.title : placeholder }}
        </span>
        <div class="menu">
            <option
                v-for="(opt, index) in options"
                :key="index"
                :selected="selectedOption(opt)"
                :value="opt.value"
                @click="changeSelection">
                {{ opt.title }}
            </option>
        </div>
    </div>
</template>
<script>
export default {
    name: 'b-select',
    props: ['value', 'options', 'placeholder'],
    data() {
        return {
            isOpen: false
        }
    },
    methods: {
        selectedOption(option) {
            if (this.value) {
                return option.value === this.value.value;
            }
            return false;
        },
        changeSelection(e) {
            const selectedValue = e.target.value;
            const opt = this.options.find(opt => selectedValue == opt.value);
            this.$emit('input', opt.value);
        },
        toggle() {
            this.isOpen = !this.isOpen;
		},
		close() {
			if (this.isOpen) {
				this.isOpen = false;
			}
		}
    }
}
</script>
<style lang="scss" scoped>
.select {
    height: 34px;
    background-color: #fff;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    border: 1px solid rgb(250, 235, 223);
    border-radius: 5px;
    cursor: pointer;
    &::after {
        position: absolute;
        width: 24px;
        height: 24px;
        background-image: url('~/assets/icons/expand_more-24px.svg');
        background-repeat: no-repeat;
        background-position: center;
        content: " ";
        right: 8px;
    }
    .selection {
        user-select: none;
        font-size: 14px;
        padding-left: 8px;
        color: #212121;
    }
    .menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        flex-wrap: nowrap;
        background: #fafafa;
        border-radius: 5px;
        display: none;
        opacity: 0;
        transition: all .3s ease;
        z-index: 10;
        box-shadow: 0px 2px 4px rgba(0,0,0,0.3);
        option {
            cursor: pointer;
            text-align: left;
            width: 100%;
            height: 34px;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background-clip: border-box;
            &:hover {
                background-color: #eee;
            }
            &:active {
                background-color: #eee;
            }
        }
    }
    &.expanded {
        &::after {
            background-image: url('~/assets/icons/expand_less-24px.svg');
        }
        .menu {
            display: flex;
            opacity: 1;
        }
    }
}
</style>