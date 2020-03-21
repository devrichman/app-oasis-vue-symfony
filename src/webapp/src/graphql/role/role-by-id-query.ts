import gql from 'graphql-tag'

export const ROLE_BY_ID = gql`
    query roleById ($id: String!) {
        roleById (id: $id) {
            id,
            name,
            description,
            usersCount,
            rights {
                id,
                code,
            },
        }
    }
`;
