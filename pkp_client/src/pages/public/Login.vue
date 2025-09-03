<script setup lang="ts">
import { ref } from "vue"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card, CardHeader, CardContent, CardFooter } from "@/components/ui/card"
import { Label } from "@/components/ui/label"
import { User, Lock, Eye, EyeOff } from "lucide-vue-next"

// form state
const username = ref("")
const password = ref("")
const showPassword = ref(false)

function handleLogin() {
  console.log("Logging in with:", username.value, password.value)
  // Add your authentication logic here
}

</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 relative overflow-hidden font-poppins">

    <!-- Card -->
    <Card class="w-full max-w-md shadow-2xl rounded-2xl bg-white/90 backdrop-blur">
      <CardHeader class="text-center space-y-2">
        <img src="@/assets/doh-logo.svg" alt="DOH Logo" class="w-20 mx-auto" />
        <h1 class="text-2xl font-bold">PuroKalusugan</h1>
        <p class="text-sm text-gray-600">
          Department of Health - Center for Health Development <br />
          Cordillera Administrative Region
        </p>
      </CardHeader>

      <CardContent>
        <p class="text-center text-gray-700 mb-4 font-medium">Welcome back</p>
        <form @submit.prevent="handleLogin" class="space-y-4">
          <!-- Username -->
          <div>
            <Label for="username">Username</Label>
            <div class="flex items-center border rounded-md px-2 mt-2">
              <User class="w-4 h-4 text-gray-400 mr-2" />
              <Input id="username" v-model="username" placeholder="Enter your username"
                class="border-0 focus:ring-0 flex-1" />
            </div>
          </div>

          <!-- Password -->
          <div>
            <Label for="password">Password</Label>
            <div class="flex items-center border rounded-md px-2 relative mt-2">
              <Lock class="w-4 h-4 text-gray-400 mr-2" />
              <Input id="password" :type="showPassword ? 'text' : 'password'" v-model="password"
                placeholder="Enter your password" class="border-0 focus:ring-0 flex-1" />
              <button type="button" class="absolute right-2 text-gray-500 hover:text-gray-700"
                @click="showPassword = !showPassword">
                <Eye v-if="!showPassword" class="w-4 h-4" />
                <EyeOff v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Submit -->
          <Button type="submit" class="w-full bg-blue-600 hover:bg-blue-700">
            Sign In
          </Button>
        </form>
      </CardContent>

      <CardFooter class="text-center text-xs text-gray-500">
        <div class="w-full flex flex-col justify-center items-center p-2">
          <p class="text-xs italic">© 2025 Department of Health</p>
          <p class="text-xs italic"><strong>Center for Health Development</strong> - Cordillera Administrative
            Region</p>
          <p class="text-xs italic">Developed By: ICTMU</p>
        </div>
      </CardFooter>
    </Card>
  </div>
</template>
