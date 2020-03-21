import gql from 'graphql-tag'

export const ENABLE_USER = gql`
    mutation EnableUser($id: String!) {
        enableUser(id: $id) {
            id,
            status,
        }
    }
`;
