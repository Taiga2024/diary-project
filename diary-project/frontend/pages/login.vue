<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";
import gqlRequest from "../utils/gqlRequest";


const email: Ref<string> = ref("");
const password: Ref<number | undefined> = ref();
const confirmPassword: Ref<number | undefined> = ref();
const isLoading: Ref<boolean> = ref(false);

definePageMeta({
  layout: false
})

interface variables {
  input: {
    email: string;
    password: number | undefined;
  }
}

interface fetchData {
  login: {
    token: string,
    user: {
      id: number,
      name: string,
      email: string,
    }
  }
}

async function sendUserData() {
  isLoading.value = true
  const LoginQuery: string = gql`
    mutation ($input: LoginInput!) {
      login(input: $input) {
        user {
          id
          name
          email
        }
        token
      }
    }
  `;

  const LoginInput: variables = {
    "input": {
      "email": email.value,
      "password": password.value
    }
  }

  const fetchData: fetchData = await gqlRequest<fetchData, variables>({
    query: LoginQuery,
    variables: LoginInput,
  });

  const cookie = useCookie<string>("token", {
    maxAge: 60 * 60 * 24,
  })
  cookie.value = fetchData.login.token

  navigateTo('/')
}
</script>

<template>
  <!-- ローディング -->
  <Loading v-if="isLoading"/>

  <!-- フォーム -->
  <div class="flex" v-if="!isLoading">
    <form class="form" @submit.prevent="sendUserData">
      <p class="title">Login</p>
      <p class="message">Sign in to continue</p>

      <label>
        <input required type="email" class="input" v-model="email" />
        <span>Email</span>
      </label>

      <label>
        <input required type="password" class="input" v-model="password" />
        <span>Password</span>
      </label>
      <label>
        <input required type="password" class="input" v-model="confirmPassword" />
        <span>Confirm password</span>
      </label>
      <button class="submit">Submit</button>
      <p class="signin">Don't have an account? <NuxtLink to="/register" class="signinLink"> Sign up </NuxtLink>
      </p>
    </form>
  </div>
</template>

<style scoped>
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 80%;
  height: auto;
  width: 400px;
  background: rgb(255, 255, 255);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 20px;
  padding: 20px;
  position: relative;
}

.title {
  font-size: 28px;
  color: royalblue;
  font-weight: 600;
  letter-spacing: -1px;
  position: relative;
  display: flex;
  align-items: center;
  padding-left: 30px;
}

.title::before,
.title::after {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  border-radius: 50%;
  left: 0px;
  background-color: royalblue;
}

.title::before {
  width: 18px;
  height: 18px;
  background-color: royalblue;
}

.title::after {
  width: 18px;
  height: 18px;
  animation: pulse 1s linear infinite;
}

.message,
.signin {
  color: rgba(88, 87, 87, 0.822);
  font-size: 14px;
}

.signin {
  text-align: center;
}

.signin .signinLink {
  color: royalblue;
}

.signin .signinLink:hover {
  text-decoration: underline royalblue;
}

.form label {
  position: relative;
}

.form label .input {
  width: 100%;
  padding: 10px 10px 20px 10px;
  outline: 0;
  border: 1px solid rgba(105, 105, 105, 0.397);
  border-radius: 10px;
}

.form label .input+span {
  position: absolute;
  left: 10px;
  top: 15px;
  color: grey;
  font-size: 0.9em;
  cursor: text;
  transition: 0.3s ease;
}

.form label .input:placeholder-shown+span {
  top: 15px;
  font-size: 0.9em;
}

.form label .input:focus+span,
.form label .input:valid+span {
  top: 30px;
  font-size: 0.7em;
  font-weight: 600;
}

.form label .input:valid+span {
  color: green;
}

.submit {
  border: none;
  outline: none;
  background-color: royalblue;
  padding: 10px;
  border-radius: 10px;
  color: #fff;
  font-size: 16px;
  transform: 0.3s ease;
}

.submit:hover {
  background-color: rgb(56, 90, 194);
}

@keyframes pulse {
  from {
    transform: scale(0.9);
    opacity: 1;
  }

  to {
    transform: scale(1.8);
    opacity: 0;
  }
}

</style>
