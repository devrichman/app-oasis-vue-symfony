import gql from 'graphql-tag'

export const DELETE_DOCUMENT = gql`
    mutation deleteDocument (
        $id: String!
    ) {
        deleteDocument (
            id: $id,
        ) {
          id
        }
    }
`;
