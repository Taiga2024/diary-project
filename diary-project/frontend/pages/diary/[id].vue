<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";

definePageMeta({
  middleware: ["auth"]
})

const isLoading: Ref<boolean> = ref(false);
const Diary: Ref<DiaryData | undefined> = ref<DiaryData>()
const isAuth: Ref<boolean | undefined> = ref()

interface AuthData {
  me: {
    id: number;
  }
}

interface DiaryData {
  id: number;
  title: string;
  text: string;
  user: {
    id: number;
    name: string;
  }
}

interface variables {
  id: string | string[]
}

interface fetchData {
  diary: {
    id: number;
    title: string;
    text: string;
    user: {
      id: number;
      name: string;
    }
  }
}

onMounted(async () => {
  const DiaryData = await fetchDiaryData()
  Diary.value = DiaryData
  const AuthData = await isAuthorized()
  console.log(AuthData)
  isAuth.value = AuthData
})

async function fetchDiaryData() {
  isLoading.value = true
  const cookie = useCookie<string | null>("token")
  const route = useRoute<string>()
  const DiaryID: string | string[] = route.params.id


  const header = {
    "Authorization": `Bearer ${cookie.value}`
  }

  const DiaryQuery: string = gql`
    query ($id:ID!) {
      diary(id: $id) {
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

  const DiaryInput: variables = { "id": DiaryID }

  const { diary }: fetchData = await gqlRequest<fetchData, variables>({
    query: DiaryQuery,
    variables: DiaryInput,
    headers: header
  });
  return diary
}

async function isAuthorized() {
  const cookie = useCookie<string | null>("token")
  const header = {
    "Authorization": `Bearer ${cookie.value}`
  }

  const AuthQuery: string = gql`
    query{
      me{
        id
      }
    }
  `;

  const { me } = await gqlRequest<AuthData>({
    query: AuthQuery,
    headers: header
  });

  return me.id === Diary.value?.user.id
}
</script>

<template>
  <header class="title">
    <h1>タイトル：{{ Diary?.title }}</h1>
    <NuxtLink :to="`/myPost/${Diary?.id}`" class="edit-button" v-if=isAuth>
      <svg class="edit-svgIcon" viewBox="0 0 512 512">
        <path
          d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z">
        </path>
      </svg>
    </NuxtLink>
  </header>
  <div v-html="Diary?.text" class="container">
  </div>
</template>

<style scoped>
h1 {
  margin-left: 1.3em;
}

.container {
  padding: 2em;
  border-top: 2px solid;
}

.title{
  display: flex;
  gap: 2em;
}
/* edit */
/* From Uiverse.io by aaronross1 */
.edit-button {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: rgb(20, 20, 20);
  border: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.164);
  cursor: pointer;
  transition-duration: 0.3s;
  overflow: hidden;
  position: relative;
  text-decoration: none !important;
}

.edit-svgIcon {
  width: 17px;
  transition-duration: 0.3s;
}

.edit-svgIcon path {
  fill: white;
}

.edit-button:hover {
  width: 120px;
  border-radius: 50px;
  transition-duration: 0.3s;
  background-color: rgb(255, 69, 69);
  align-items: center;
}

.edit-button:hover .edit-svgIcon {
  width: 20px;
  transition-duration: 0.3s;
  transform: translateY(60%);
  -webkit-transform: rotate(360deg);
  -moz-transform: rotate(360deg);
  -o-transform: rotate(360deg);
  -ms-transform: rotate(360deg);
  transform: rotate(360deg);
}

.edit-button::before {
  display: none;
  content: "Edit";
  color: white;
  transition-duration: 0.3s;
  font-size: 2px;
}

.edit-button:hover::before {
  display: block;
  padding-right: 10px;
  font-size: 13px;
  opacity: 1;
  transform: translateY(0px);
  transition-duration: 0.3s;
}
</style>
