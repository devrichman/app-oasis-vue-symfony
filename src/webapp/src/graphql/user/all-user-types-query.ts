import gql from 'graphql-tag'

export const ALL_USER_TYPES = gql`
    query allUserTypes {
        allUserTypes {
            id,
            label,
            description,
        }
    }
`;
