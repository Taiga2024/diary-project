<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";
import gqlRequest from "../utils/gqlRequest";

const name:Ref<string> = ref("");
const email:Ref<string> = ref("");
const password:Ref<number|undefined> = ref();
const confirmPassword:Ref<number|undefined>= ref();

interface variables {
  input: {
    name: string;
    email: string;
    password: number | undefined;
  }
}

interface fetchData {
  register:{
    token:string,
    user:{
      id:number,
      name:string,
      email:string,
    }
  }
}

async function sendUserData() {
  const registerQuery: string = gql`
    mutation ($input: RegisterInput!) {
      register(input: $input) {
        user {
          id
          name
          email
        }
        token
      }
    }
  `;

  const RegisterInput: variables = {
    "input": {
      "name": name.value,
      "email": email.value,
      "password": password.value
    }
  }

  const fetchData:fetchData = await gqlRequest<fetchData,variables>({
    query: registerQuery,
    variables: RegisterInput,
  });
  console.log(fetchData.register.token)
}
</script>

<template>
  <div class="flex">
    <form class="form" @submit.prevent="sendUserData">
      <p class="title">Register</p>
      <p class="message">Signup now and get full access to our app.</p>
      <label>
        <input required type="text" class="input" v-model="name" />
        <span>Name</span>
      </label>

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
      <p class="signin">Already have an acount ? <a href="#">Signin</a></p>
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

.signin a {
  color: royalblue;
}

.signin a:hover {
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
