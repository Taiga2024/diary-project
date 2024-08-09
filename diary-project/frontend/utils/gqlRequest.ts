import { GraphQLClient } from "graphql-request";


interface RequestOptions<V = Record<string, unknown>> {
  query: string;
  variables?: V;
  headers?: Record<string, string>;
}

export default async function gqlRequest<T=unknown,V = Record<string, unknown>>({
  query,
  variables,
  headers,
}: RequestOptions<V>):Promise<T> {

  const runtimeConfig = useRuntimeConfig();
  const apiUrl: string = runtimeConfig.public.apiUrl as string;
  const client = new GraphQLClient(apiUrl);

  if (headers) {
    client.setHeaders(headers);
  }
  
  try {
    if (variables) {
      const data = await client.request<T>(query, variables);
      return data;
    } else {
      const data = await client.request<T>(query);
      return data;
    }
  } catch (error) {
    console.error(error)
    throw error;
  }
}
