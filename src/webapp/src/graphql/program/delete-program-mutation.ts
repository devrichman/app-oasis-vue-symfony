import gql from 'graphql-tag'

export const DELETE_PROGRAM = gql`
    mutation deleteProgram ($programId: String!) {
        deleteProgram (programId: $programId) {
            id
        }
    }
`;
