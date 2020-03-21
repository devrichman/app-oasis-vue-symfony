import gql from 'graphql-tag'

export const DELETE_COMPANY = gql`
    mutation deleteCompany($id: String!) {
        deleteCompany(id: $id) {
            id,
        }
    }
`;
