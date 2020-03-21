import gql from 'graphql-tag'

export const UPDATE_ROLE = gql`
    mutation updateRole ($id: String!, $name: String!, $description: String!, $rights: [String!]!) {
        updateRole (id: $id, name: $name, description: $description, rights: $rights) {
            id,
            name
            description
            usersCount
        }
    }
`;
