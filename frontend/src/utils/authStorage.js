// Centraliza dónde vive la sesión: localStorage (remember = true, persiste
// entre cierres de navegador) o sessionStorage (remember = false, se borra
// al cerrar la pestaña/navegador). Todo lo que necesite leer o escribir
// token/user debe pasar por aquí para no desincronizar los 2 storages.

export function setAuth(token, user, remember) {

    const storage = remember ? localStorage : sessionStorage
    const other = remember ? sessionStorage : localStorage

    storage.setItem('token', token)
    storage.setItem('user', JSON.stringify(user))

    // por si había una sesión previa en el otro storage, evitamos
    // que quede un token/user viejo dando vueltas
    other.removeItem('token')
    other.removeItem('user')

}

export function getToken() {

    return localStorage.getItem('token') || sessionStorage.getItem('token') || null

}

export function getUser() {

    const raw = localStorage.getItem('user') || sessionStorage.getItem('user') || 'null'

    return JSON.parse(raw)

}

export function clearAuth() {

    localStorage.removeItem('token')
    localStorage.removeItem('user')

    sessionStorage.removeItem('token')
    sessionStorage.removeItem('user')

}
