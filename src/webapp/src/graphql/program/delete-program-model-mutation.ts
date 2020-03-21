import gql from 'graphql-tag'

export const DELETE_PROGRAM_MODEL = gql`
    mutation deleteProgramModel ($programModelId: String!) {
        deleteProgramModel (programModelId: $programModelId) {
            id
        }
    }
`;
