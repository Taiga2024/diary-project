export default defineNuxtRouteMiddleware((to, from) => {
    const cookie = useCookie<string>("token");
    if (!cookie.value) {
        return navigateTo("/register")
    }
})

