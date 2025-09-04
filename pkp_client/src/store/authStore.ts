import { defineStore } from 'pinia'
import axios from '../axios/axios'

interface User {
  first_name: string;
  last_name: string;
  [key: string]: any
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem('token') || '',
    isAuthenticated: false,
    isAdmin: false
  }),

  actions: {
    setUser(user: User) {
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

