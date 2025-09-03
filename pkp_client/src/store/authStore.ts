import { defineStore } from 'pinia'
import axios from '../axios/axios'


export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || '',
    isAuthenticated: false,
    isAdmin:false
  }),

  actions: {
    setUser(user) {
        this.user = user
        this.isAuthenticated = true
        console.log(user)
    },

    setToken(token: string) {
      this.token = token
      localStorage.setItem('token', token)
    },

    clearUser() {
     
      this.token = ''
      localStorage.removeItem('token')
    },

    async fetchUser() {
      try {
        const response = await axios.get('user')
        this.setUser(response.data)
        console.log(this.isAuthenticated)
      }
      catch (error) {
        this.clearUser()
      }
    },

    async initAuth() {
      if (this.token) {
        await this.fetchUser()
      }
    }
  }
})

