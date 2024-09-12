import { gql } from "graphql-request";

export default async function deleteRequest(ID: number) {
    const cookie = useCookie<string | null>("token")

    const header = {
        "Authorization": `Bearer ${cookie.value}`
    }

    interface variables {
        id: number
    }

    interface fetchData{
        deleteDiary:{
            id:number,
            title:string,
            text:string,
            user:{
                id:number
            }
        }
    }
    const DeleteDiaryQuery: string = gql`
    mutation($id:ID!){
      deleteDiary(id:$id){
        id
        title
        text
        user{
            id
        }
      }
    }
  `;

    const DiaryId: variables = {
        id: ID
    }

    const fetchData: fetchData = await gqlRequest<fetchData, variables>({
        query: DeleteDiaryQuery,
        variables: DiaryId,
        headers: header
    });
    
    return fetchData
}