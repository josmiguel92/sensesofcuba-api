window.darkMode = function () {
    return {
        isDark: (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)),
        setDark() {
            localStorage.theme = 'dark';
            this.updateTheme()
        },
        setLight() {
            localStorage.theme = 'light';
            this.updateTheme()
        },
        setDefault() {
            localStorage.removeItem('theme');
            this.updateTheme()
        },
        updateTheme() {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
    }
}
