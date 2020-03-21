import gql from 'graphql-tag'

export const CREATE_ROLE = gql`
    mutation createRole ($name: String!, $description: String!, $rights: [String!]!) {
        createRole (name: $name, description: $description, rights: $rights) {
            id,
        }
    }
`;
