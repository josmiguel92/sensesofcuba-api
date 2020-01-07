<template>
    <div id="app">
        <router-view name="header"></router-view>
        <main>
            <router-view></router-view>
        </main>
        <router-view name="footer"></router-view>
    </div>
</template>

<script>
export default {
    
}
</script>

<style lang="scss">
section .title,
footer .title {
    text-transform: uppercase;
    font-weight: bold;
    text-align: center;
    em {
        font-style: normal;
    }
}
.section-divider {
    height: 8vw;
    width: 100%;
}
</style>