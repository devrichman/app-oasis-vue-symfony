import gql from 'graphql-tag'

export const USER_FRAGMENT = gql`
    fragment UserFragment on User {
        id,
        civility,
        firstName,
        lastName,
        status,
        phone,
        email,
        address,
        linkedin,
        function,
        seniorityDate,
        previousFunction,
        rolesByUsersRoles {
            id,
            name,
        },
        company {
            id,
            name,
        },
        type {
            id,
            label,
        },
        coach {
            id,
            firstName,
            lastName,
            profilePicture {
                id,
            },
        },
        profilePicture {
            id,
            name,
            size,
        },
    }
`;
