<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";

definePageMeta({
  middleware: ["auth"]
})

const isLoading: Ref<boolean> = ref(false);
const Diary:Ref<DiaryData|undefined> =ref<DiaryData>()



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
})

async function fetchDiaryData() {
  isLoading.value = true
  const cookie = useCookie<string | null>("token")
  const route = useRoute<string>()
  const DiaryID:string | string[]=route.params.id
  

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

  const DiaryInput: variables = {"id": DiaryID}

  const { diary }: fetchData = await gqlRequest<fetchData, variables>({
    query: DiaryQuery,
    variables: DiaryInput,
    headers: header
  });
  return diary
}
</script>

<template>
  <h1>タイトル：{{ Diary?.title }}</h1>
  <div v-html="Diary?.text" class="container">
  </div>
</template>

<style scoped>
  h1{
    margin-left: 1.3em;
  }
  .container{
    padding: 2em;
    border-top: 2px solid;
  }
</style>
