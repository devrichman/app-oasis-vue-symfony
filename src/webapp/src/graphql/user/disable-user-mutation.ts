import gql from 'graphql-tag'

export const DISABLE_USER = gql`
    mutation DisableUser($id: String!) {
        disableUser(id: $id) {
            id,
            status,
        }
    }
`;
