import { gql } from "graphql-request";

export async function storeFavorite(ID: number) {
    const cookie = useCookie<string | null>("token")
    const header = {
        "Authorization": `Bearer ${cookie.value}`
    }

    interface variables {
        id: number
    }

    interface fetchData {
        status: boolean
    }
    const CreataFavoriteQuery: string = gql`
    mutation($id:ID!){
      createFavorite(diary_id:$id){
        status
      }
    }
  `;

    const DiaryId: variables = {
        id: ID
    }

    const fetchData: fetchData = await gqlRequest<fetchData, variables>({
        query: CreataFavoriteQuery,
        variables: DiaryId,
        headers: header
    });

    return fetchData
}

export async function deleteFavorite(diary_id: number) {
    const cookie = useCookie<string | null>("token")

    const Header = {
        "Authorization": `Bearer ${cookie.value}`
    }

    interface variables {
        input: {
            diary_id:number;
        }
    }

    interface fetchData {
        status: boolean
    }
    const DeleteFavoriteQuery: string = gql`
    mutation($input: DeleteFavoriteInput!){
      deleteFavorite(input: $input){
        status
      }
    }
  `;

    const Ids: variables = {
        "input":{
            "diary_id":diary_id,
        } 
    }
    const fetchData: fetchData = await gqlRequest<fetchData, variables>({
        query: DeleteFavoriteQuery,
        variables: Ids,
        headers: Header
    });

    return fetchData
}

