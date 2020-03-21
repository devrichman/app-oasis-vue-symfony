import gql from 'graphql-tag'

export const ME_FRAGMENT = gql`
    fragment MeFragment on SerializableUser {
        id
        email
        firstName
        lastName
        roles
        rights
        function
        civility
        linkedin
        address
        seniorityDate
        phone
        profilePictureId
        type
        cguAccepted
    }
`;
