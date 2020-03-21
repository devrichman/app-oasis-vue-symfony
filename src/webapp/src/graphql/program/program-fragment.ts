import gql from 'graphql-tag'

export const PROGRAM_FRAGMENT = gql`
    fragment ProgramFragment on Program {
        id,
        name,
        description,
        status,
        type,
        dateStart,
        dateEnd,
        programModel {
            id,
            name,
            description,
        },
        events {
            items {
                id,
                name,
                description,
                status,
                type,
                dateEvent,
                updatedAt,
                updatedBy {
                    ...PartialUserFragment,
                },
            },
            count,
        }
        users {
            ...PartialUserFragment,
        },
        coach {
            ...PartialUserFragment
        },
        createdAt,
        createdBy {
            ...PartialUserFragment
        },
        updatedAt,
        updatedBy {
            ...PartialUserFragment
        },
    }
    fragment PartialUserFragment on User {
        id,
        firstName,
        lastName,
        profilePicture {
            id
        },
    }
`;
