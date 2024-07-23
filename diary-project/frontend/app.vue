<template>
  <div>
    <h1>Hello Nuxt3</h1>
    <h2>{{fetchData}}</h2>
  </div>
</template>

<script setup lang="ts">
  import { ref } from "vue";
  import { GraphQLClient } from 'graphql-request';

  const fetchData=ref();

  const runtimeConfig = useRuntimeConfig();

  console.log(runtimeConfig.public.apiUrl)
  const client = new GraphQLClient(runtimeConfig.public.apiUrl);

  const query = `{
  user(id: 1) {
    id
    name
    email
  }
}
`;

  async function fetchUserData() {
    try {
      const data = await client.request(query);
      fetchData.value=data.user
    } catch (error) {
      console.error(error);
    }
  };

  fetchUserData();
</script>