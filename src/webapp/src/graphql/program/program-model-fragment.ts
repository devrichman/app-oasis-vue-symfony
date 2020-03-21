import gql from 'graphql-tag'

export const PROGRAM_MODEL_FRAGMENT = gql`
    fragment ProgramModelFragment on ProgramModel {
        id,
        name,
        description,
        createdAt,
        createdBy {
            id,
            firstName,
            lastName,
        },
        updatedAt,
        updatedBy {
            id,
            firstName,
            lastName,
        },
        eventModels {
            items {
                id,
                name,
                description,
                type,
                createdAt,
                updatedAt,
            },
            count
        },
    }
`;
