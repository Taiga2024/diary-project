<script setup lang="ts">
import { gql } from "graphql-request";
import gqlRequest from "../utils/gqlRequest";

const isLoading: Ref<boolean> = ref(false);


interface fetchData {
  logout: {
    status: boolean,
    user: {
      id: number,
      name: string,
      email: string,
    }
  }
}


async function Logout() {
  isLoading.value = true
  const cookie = useCookie<string | null>("token")

  const header = {
    "Authorization": `Bearer ${cookie.value}`
  }


  const LogoutQuery: string = gql`
    mutation{
      logout{
        user {
          id
          name
          email
        }
        status
      }
    }
  `;

  const fetchData: fetchData = await gqlRequest<fetchData>({
    query: LogoutQuery,
    headers: header
  });

  cookie.value = null
  navigateTo('/login')
}
</script>

<template>
  <div>
    <header>
      <h1><NuxtLink to="/" class="title"> EnglishDiary </NuxtLink></h1>
      <nav>
        <NuxtLink to="/post" class="postBtn"> Post </NuxtLink>
        <button class="logout" @click.prevent="Logout">Logout</button>
      </nav>
    </header>
    <Loading v-if="isLoading" />
    <slot v-if="!isLoading"/>
  </div>
</template>

<style scoped>
header {
  width: 100%;
  height: 5rem;
  display: flex;
  justify-content: space-between;
  padding: 1vh 10vw;
}

h1 {
  font-weight: 100;
}

h1 .title{
  text-decoration: none;
  color: black;
}

.logout {
  width: 100px;
  border-radius: 10px;
  height: 45px;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: rgb(255, 65, 65);
  font-size: 1em;
  font-weight: 300;
  color: white;
}

nav{
  display: flex;
  justify-content: space-between;
  gap: 20px;
}
.postBtn {
  text-decoration: none;
  display:inline-block;
  width: 100px;
  border-radius: 10px;
  height: 45px;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
  background-color: white;
  font-size: 1em;
  font-weight: 300;
  text-align: center;
  color: black;
  padding-top: 12px;
}
</style>
