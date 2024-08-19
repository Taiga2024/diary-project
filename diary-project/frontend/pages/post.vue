<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";
import gqlRequest from "../utils/gqlRequest";

definePageMeta({
  middleware: ["auth"]
})

const title: Ref<string> = ref("");
const text: Ref<string> = ref("");
const isLoading: Ref<boolean> = ref(false);


interface variables {
  input: {
    title: string;
    text: string;
  }
}

interface fetchData {
    post:{
      id:number;
      title:string;
      text:string;
      user:{
        id:number;
        name:string;
      }
    }
}

async function sendDiaryData() {
  isLoading.value = true
  const cookie = useCookie<string | null>("token")

  const header = {
    "Authorization": `Bearer ${cookie.value}`
  }
  
  const PostQuery: string = gql`
    mutation ($input: DiaryInput!) {
      post(input: $input) {
        user {
          id
          name
        }
        id
        title
        text
      }
    }
  `;

  const DiaryInput: variables = {
    "input": {
      "title": title.value,
      "text": text.value
    }
  }

  const {post}: fetchData = await gqlRequest<fetchData, variables>({
    query: PostQuery,
    variables: DiaryInput,
    headers: header
  });

  navigateTo('/')
}
</script>

<template>
  <Loading v-if="isLoading"/>

  <div class="flex" v-if="!isLoading">
    <div class="container">
      <div class="heading">Diary</div>
      <form class="form" @submit.prevent="sendDiaryData">
        <input required class="input" type="text" placeholder="title" v-model="title">
        <textarea required class="textarea" placeholder="text" v-model="text"></textarea>
        <input class="submit-button" type="submit" value="Post">
      </form>
    </div>
  </div>
</template>

<style scoped>
.flex {
  display: flex;
  justify-content: center;
  align-items: center;
  height: calc(100vh - 5rem);
}

/* From Uiverse.io by Smit-Prajapati */
.container {
  max-width: 80%;
  height: auto;
  width: 700px;
  background: #F8F9FD;
  background: linear-gradient(0deg, rgb(255, 255, 255) 0%, rgb(244, 247, 251) 100%);
  border-radius: 40px;
  padding: 25px 35px;
  border: 5px solid rgb(255, 255, 255);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 30px 30px -20px;
  margin: 20px;
}

.heading {
  text-align: center;
  font-weight: 900;
  font-size: 30px;
  color: rgb(16, 137, 211);
}

.form {
  margin-top: 20px;
}

.form .input {
  width: 100%;
  background: white;
  border: none;
  padding: 15px 20px;
  border-radius: 20px;
  margin-top: 15px;
  box-shadow: #cff0ff 0px 10px 10px -5px;
  border-inline: 2px solid transparent;
}

.form .input::-moz-placeholder {
  color: rgb(170, 170, 170);
}

.form .input::placeholder {
  color: rgb(170, 170, 170);
}

.form .input:focus {
  outline: none;
  border-inline: 2px solid #12B1D1;
}

.form .textarea {
  width: 100%;
  height: 20em;
  background: white;
  border: none;
  padding: 15px 20px;
  border-radius: 20px;
  margin-top: 15px;
  box-shadow: #cff0ff 0px 10px 10px -5px;
  border-inline: 2px solid transparent;
}

.form .textarea::-moz-placeholder {
  color: rgb(170, 170, 170);
}

.form .textarea::placeholder {
  color: rgb(170, 170, 170);
}

.form .textarea:focus {
  outline: none;
  border-inline: 2px solid #12B1D1;
}

.form .submit-button {
  display: block;
  width: 100%;
  font-weight: bold;
  background: linear-gradient(45deg, rgb(16, 137, 211) 0%, rgb(18, 177, 209) 100%);
  color: white;
  padding-block: 15px;
  margin: 20px auto;
  border-radius: 20px;
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 20px 10px -15px;
  border: none;
  transition: all 0.2s ease-in-out;
}

.form .submit-button:hover {
  transform: scale(1.03);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 23px 10px -20px;
}

.form .submit-button:active {
  transform: scale(0.95);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 15px 10px -10px;
}
</style>
