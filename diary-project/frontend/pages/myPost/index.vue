<script setup lang="ts">
import { ref } from "vue";
import type { Ref } from 'vue'
import { gql } from "graphql-request";


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
    me: {
        diaries: {
            data: [
                {
                    id: number;
                    title: string;
                }
            ],
            paginatorInfo: {
                count: number;
                currentPage: number;
                lastPage: number;
            }
        }
    }
}

watch(currentPage, async () => {
    const { me } = await loadDiaryData()
    Diaries.value = me.diaries.data
})


onMounted(async () => {
    const { me } = await loadDiaryData()
    pageCount.value = me.diaries.paginatorInfo.lastPage
    Diaries.value = me.diaries.data
})

async function loadDiaryData(): Promise<fetchData> {
    isLoading.value = true
    const cookie = useCookie<string | null>("token")

    const header = {
        "Authorization": `Bearer ${cookie.value}`
    }

    const DiaryQuery: string = gql`
    query($page:Int!){
      me{
        diaries(page:$page){
            data{
                id
                title
            }
            paginatorInfo{
                currentPage
                count
                lastPage
            }
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

async function deleteDiary(id:number) {
    isLoading.value = true
    await deleteRequest(id)
    Diaries.value=Diaries.value.filter((Diary:any)=>Diary.id !== id)
    isLoading.value=false
}
</script>

<template>
    <Loading v-if="isLoading" />
    <!-- 日記 -->
    <div class="card" v-for="Diary in Diaries" v-if="!isLoading" :key="Diary.id">
        <NuxtLink :to="`/diary/${Diary.id}`" class="card__title">{{ Diary.title }}
        </NuxtLink>
        <button class="btn" @click="deleteDiary(Diary.id)">
            <svg viewBox="0 0 15 17.5" height="17.5" width="15" xmlns="http://www.w3.org/2000/svg" class="icon">
                <path transform="translate(-2.5 -1.25)"
                    d="M15,18.75H5A1.251,1.251,0,0,1,3.75,17.5V5H2.5V3.75h15V5H16.25V17.5A1.251,1.251,0,0,1,15,18.75ZM5,5V17.5H15V5Zm7.5,10H11.25V7.5H12.5V15ZM8.75,15H7.5V7.5H8.75V15ZM12.5,2.5h-5V1.25h5V2.5Z"
                    id="Fill"></path>
            </svg>
        </button>
        <div class="card__arrow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="15" width="15">
                <path fill="#fff"
                    d="M13.4697 17.9697C13.1768 18.2626 13.1768 18.7374 13.4697 19.0303C13.7626 19.3232 14.2374 19.3232 14.5303 19.0303L20.3232 13.2374C21.0066 12.554 21.0066 11.446 20.3232 10.7626L14.5303 4.96967C14.2374 4.67678 13.7626 4.67678 13.4697 4.96967C13.1768 5.26256 13.1768 5.73744 13.4697 6.03033L18.6893 11.25H4C3.58579 11.25 3.25 11.5858 3.25 12C3.25 12.4142 3.58579 12.75 4 12.75H18.6893L13.4697 17.9697Z">
                </path>
            </svg>
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

/* deleteButton */
/* From Uiverse.io by boryanakrasteva */ 
.btn {
  background-color: transparent;
  position: relative;
  border: none;
  margin-left: 10px;
}

.btn::after {
  content: 'delete';
  position: absolute;
  top: -130%;
  left: 50%;
  transform: translateX(-50%);
  width: fit-content;
  height: fit-content;
  background-color: rgb(168, 7, 7);
  padding: 4px 8px;
  border-radius: 5px;
  transition: .2s linear;
  transition-delay: .2s;
  color: white;
  text-transform: uppercase;
  font-size: 12px;
  opacity: 0;
  visibility: hidden;
}

.icon {
  transform: scale(1.2);
  transition: .2s linear;
}

.btn:hover > .icon {
  transform: scale(1.5);
}

.btn:hover > .icon path {
  fill: rgb(168, 7, 7);
}

.btn:hover::after {
  visibility: visible;
  opacity: 1;
  top: -160%;
}

</style>
