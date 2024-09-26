<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";
import gqlRequest from "../utils/gqlRequest";

definePageMeta({
  middleware: ["auth"]
})

const isLoading: Ref<boolean> = ref(false);
const currentPage: Ref<number> = ref(1);
const pageCount: Ref<number | undefined> = ref();
const Diaries = ref()

interface variables {
  page: number
}

interface fetchData {
  allDiaries: {
    data: [
      {
        id: number;
        title: string;
        text: string;
        isFavorite: boolean;
        favoriteCount: number;
      }
    ]
    paginatorInfo: {
      count: number;
      currentPage: number;
      lastPage: number;
    }
  }
}

watch(currentPage, async () => {
  const { allDiaries } = await loadDiaryData()
  Diaries.value = allDiaries.data
})


onMounted(async () => {
  const { allDiaries } = await loadDiaryData()
  pageCount.value = allDiaries.paginatorInfo.lastPage
  Diaries.value = allDiaries.data
})

async function loadDiaryData(): Promise<fetchData> {
  isLoading.value = true
  const cookie = useCookie<string | null>("token")

  const header = {
    "Authorization": `Bearer ${cookie.value}`
  }

  const DiaryQuery: string = gql`
    query($page:Int!){
      allDiaries(page:$page){
        data{
          id
          title
          text
          isFavorite
          favoriteCount
        }
        paginatorInfo{
          count
          currentPage
          lastPage
        }
      }
    }
  `;

  const pageCount: variables = {
    page: currentPage.value
  }

  const fetchData: fetchData = await gqlRequest<fetchData, variables>({
    query: DiaryQuery,
    variables: pageCount,
    headers: header
  });
  isLoading.value = false
  return fetchData
}

async function storeFavoriteData(id:number) {
    isLoading.value = true
    await storeFavorite(id)
    Diaries.value.forEach((Diary: any) => {
      if (Diary.id==id) {
        Diary.favoriteCount+=1
        Diary.isFavorite=!Diary.isFavorite
      }
    });
    isLoading.value=false
}

async function deleteFavoriteData(diary_id: number) {
    isLoading.value = true
    await deleteFavorite(diary_id)
    Diaries.value.forEach((Diary: any) => {
      if (Diary.id==diary_id) {
        Diary.favoriteCount-=1
        Diary.isFavorite=!Diary.isFavorite
      }
    });
    isLoading.value=false
}
</script>

<template>
  <Loading v-if="isLoading" />
  <!-- 日記 -->
  <div class="card" v-for="Diary in Diaries" v-if="!isLoading">
    <NuxtLink :to="`/diary/${Diary.id}`" class="card__title">{{ Diary.title }}
    </NuxtLink>
    <div class="card__arrow">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="15" width="15">
        <path fill="#fff"
          d="M13.4697 17.9697C13.1768 18.2626 13.1768 18.7374 13.4697 19.0303C13.7626 19.3232 14.2374 19.3232 14.5303 19.0303L20.3232 13.2374C21.0066 12.554 21.0066 11.446 20.3232 10.7626L14.5303 4.96967C14.2374 4.67678 13.7626 4.67678 13.4697 4.96967C13.1768 5.26256 13.1768 5.73744 13.4697 6.03033L18.6893 11.25H4C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75H18.6893L13.4697 17.9697Z">
        </path>
      </svg>
    </div>
    <!-- いいね -->
    <div class="favorite-container">
      <div class="heart-container" title="Like" v-if=Diary.isFavorite>
        <input type="checkbox" class="checkbox" id="Give-It-An-Id" checked @change="deleteFavoriteData(Diary.id)">
        <div class="svg-container">
          <svg viewBox="0 0 24 24" class="svg-outline" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Zm-3.585,18.4a2.973,2.973,0,0,1-3.83,0C4.947,16.006,2,11.87,2,8.967a4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,11,8.967a1,1,0,0,0,2,0,4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,22,8.967C22,11.87,19.053,16.006,13.915,20.313Z">
            </path>
          </svg>
          <svg viewBox="0 0 24 24" class="svg-filled" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Z">
            </path>
          </svg>
          <svg class="svg-celebrate" width="100" height="100" xmlns="http://www.w3.org/2000/svg">
            <polygon points="10,10 20,20"></polygon>
            <polygon points="10,50 20,50"></polygon>
            <polygon points="20,80 30,70"></polygon>
            <polygon points="90,10 80,20"></polygon>
            <polygon points="90,50 80,50"></polygon>
            <polygon points="80,80 70,70"></polygon>
          </svg>
        </div>
      </div>
      <div class="heart-container" title="Like" v-else=!Diary.isFavorite>
        <input type="checkbox" class="checkbox" id="Give-It-An-Id" @change="storeFavoriteData(Diary.id)">
        <div class="svg-container">
          <svg viewBox="0 0 24 24" class="svg-outline" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Zm-3.585,18.4a2.973,2.973,0,0,1-3.83,0C4.947,16.006,2,11.87,2,8.967a4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,11,8.967a1,1,0,0,0,2,0,4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,22,8.967C22,11.87,19.053,16.006,13.915,20.313Z">
            </path>
          </svg>
          <svg viewBox="0 0 24 24" class="svg-filled" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Z">
            </path>
          </svg>
          <svg class="svg-celebrate" width="100" height="100" xmlns="http://www.w3.org/2000/svg">
            <polygon points="10,10 20,20"></polygon>
            <polygon points="10,50 20,50"></polygon>
            <polygon points="20,80 30,70"></polygon>
            <polygon points="90,10 80,20"></polygon>
            <polygon points="90,50 80,50"></polygon>
            <polygon points="80,80 70,70"></polygon>
          </svg>
        </div>
      </div>
      {{ Diary.favoriteCount }}
    </div>
  </div>

  <!-- ぺジネーション -->
  <div v-if="!isLoading">
    <div class="radio-inputs">
      <label class="radio" v-for="page in pageCount" :key="page">
        <input type="radio" name="radio" v-model="currentPage" :value="page">
        <span class="name">{{ page }}</span>
      </label>
    </div>
  </div>
</template>

<style scoped>
/* From Uiverse.io by Yaya12085 */
/* this card is inspired form this - https://georgefrancis.dev/ */

/* 日記 */
.card {
  --border-radius: 0.75rem;
  --primary-color: #7257fa;
  --secondary-color: #3c3852;
  max-width: 80%;
  height: auto;
  width: 800px;
  font-family: "Arial";
  padding: 1rem;
  cursor: pointer;
  border-radius: var(--border-radius);
  background: #f1f1f3;
  box-shadow: 0px 8px 16px 0px rgb(0 0 0 / 3%);
  position: relative;
  margin: auto;
  margin-bottom: 10px;
}

.card>*+* {
  margin-top: 1.1em;
}

.card .card__content {
  color: var(--secondary-color);
  font-size: 0.86rem;
}

.card .card__title {
  padding: 0;
  font-size: 1.3rem;
  font-weight: bold;
  text-decoration: none;
}

.card .card__date {
  color: #6e6b80;
  font-size: 0.8rem;
}

.card .card__arrow {
  position: absolute;
  background: rgb(255, 65, 65);
  padding: 0.4rem;
  border-top-left-radius: var(--border-radius);
  border-bottom-right-radius: var(--border-radius);
  bottom: 0;
  right: 0;
  transition: 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
}

.card svg {
  transition: 0.2s;
}

/* hover */
.card:hover .card__title {
  color: rgb(255, 65, 65);
  text-decoration: underline;
}

.card:hover .card__arrow {
  background: #111;
}

.card:hover .card__arrow svg {
  transform: translateX(3px);
}

/* ぺジネーション */
.radio-inputs {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  border-radius: 0.5rem;
  background-color: #EEE;
  box-sizing: border-box;
  box-shadow: 0 0 0px 1px rgba(0, 0, 0, 0.06);
  padding: 0.25rem;
  width: 150px;
  font-size: 14px;
  margin: auto;
}

.radio-inputs .radio {
  flex: 1 1 auto;
  text-align: center;
}

.radio-inputs .radio input {
  display: none;
}

.radio-inputs .radio .name {
  display: flex;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  border-radius: 0.5rem;
  border: none;
  padding: .5rem 0;
  color: rgba(51, 65, 85, 1);
  transition: all .15s ease-in-out;
}

.radio-inputs .radio input:checked+.name {
  background-color: #fff;
  font-weight: 600;
}

/* いいねボタン */
.favorite-container{
  display: flex;
  gap: 10px;
}
/* From Uiverse.io by catraco */
.heart-container {
  --heart-color: rgb(255, 91, 137);
  position: relative;
  width: 20px;
  height: 20px;
  transition: .3s;
}

.heart-container .checkbox {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  z-index: 20;
  cursor: pointer;
}

.heart-container .svg-container {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.heart-container .svg-outline,
.heart-container .svg-filled {
  fill: var(--heart-color);
  position: absolute;
}

.heart-container .svg-filled {
  animation: keyframes-svg-filled 1s;
  display: none;
}

.heart-container .svg-celebrate {
  position: absolute;
  animation: keyframes-svg-celebrate .5s;
  animation-fill-mode: forwards;
  display: none;
  stroke: var(--heart-color);
  fill: var(--heart-color);
  stroke-width: 2px;
}

.heart-container .checkbox:checked~.svg-container .svg-filled {
  display: block
}

.heart-container .checkbox:checked~.svg-container .svg-celebrate {
  display: block
}

@keyframes keyframes-svg-filled {
  0% {
    transform: scale(0);
  }

  25% {
    transform: scale(1.2);
  }

  50% {
    transform: scale(1);
    filter: brightness(1.5);
  }
}

@keyframes keyframes-svg-celebrate {
  0% {
    transform: scale(0);
  }

  50% {
    opacity: 1;
    filter: brightness(1.5);
  }

  100% {
    transform: scale(1.4);
    opacity: 0;
    display: none;
  }
}
</style>
