import gql from 'graphql-tag'

export const DELETE_ROLE = gql`
    mutation deleteRole ($roleId: String!) {
        deleteRole (roleId: $roleId) {
            name,
        },
    }
`;
