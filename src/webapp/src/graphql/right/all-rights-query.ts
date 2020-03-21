import gql from 'graphql-tag'

export const ALL_RIGHTS = gql`
    query allRights {
        allRights {
            id,
            name,
            code,
        }
    }
`;
